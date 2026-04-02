<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentGenController extends Controller
{
    public function generate(Request $request)
    {
        try {
            $validated = $request->validate([
                'source_text' => ['nullable', 'string', 'max:50000'],
                'source_file' => ['nullable', 'file', 'max:10240', 'mimetypes:text/plain,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png,image/webp'],
                'topic_title' => ['nullable', 'string', 'max:255'],
                'subject_name' => ['nullable', 'string', 'max:255'],
            ]);

            $textSource = trim($validated['source_text'] ?? '');
            $fileContext = $this->extractFileContext($request->file('source_file'));

            if ($textSource === '' && ($fileContext['content'] ?? '') === '') {
                return response()->json(['error' => 'Adj meg szöveges forrást vagy tölts fel fájlt.'], 422);
            }

            $apiKey = trim((string) env('OPENROUTER_API_KEY', ''));
            if (!$apiKey) {
                return response()->json(['error' => 'Hiányzik az OPENROUTER_API_KEY a backend környezetből.'], 500);
            }

            $topicTitle = $validated['topic_title'] ?? 'Ismeretlen lecke';
            $subjectName = $validated['subject_name'] ?? 'Ismeretlen tantárgy';

            $messages = [
                ['role' => 'system', 'content' => $this->buildSystemPrompt()],
                ['role' => 'user', 'content' => $this->buildUserPrompt($textSource, $fileContext, $topicTitle, $subjectName)],
            ];

            $requestBody = [
                'model' => 'nvidia/nemotron-3-super-120b-a12b:free',
                'messages' => $messages,
                'temperature' => 0.45,
                'top_p' => 0.9,
                'max_tokens' => 3000,
                'stream' => false,
            ];

            $response = Http::baseUrl('https://openrouter.ai/api/v1')
                ->acceptJson()
                ->asJson()
                ->withToken($apiKey)
                ->withHeaders([
                    'Referer' => config('app.url', 'https://maturasmart.hu'),
                    'HTTP-Referer' => config('app.url', 'https://maturasmart.hu'),
                    'X-Title' => 'MaturaSmart Course Builder',
                ])
                ->withOptions([
                    'verify' => filter_var(env('OPENROUTER_SSL_VERIFY', true), FILTER_VALIDATE_BOOL),
                ])
                ->timeout(90)
                ->retry(2, 700)
                ->post('/chat/completions', $requestBody);

            if (!$response->successful()) {
                Log::warning('OpenRouter lesson generation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'error' => 'Hiba történt a tananyag-generálás közben.',
                    'details' => $response->json() ?: $response->body(),
                ], 500);
            }

            $responseJson = $response->json();
            $content = data_get($responseJson, 'choices.0.message.content', '');

            if (!is_string($content) || trim($content) === '') {
                $content = (string) data_get($responseJson, 'output.0.content.0.text', '');
            }

            if (trim($content) === '') {
                Log::warning('OpenRouter returned empty generation content', [
                    'response' => $responseJson,
                ]);

                return response()->json([
                    'error' => 'Az AI válasz üres volt.',
                    'details' => $responseJson,
                ], 502);
            }

            $html = $this->extractHtml($content);

            return response()->json([
                'html' => $html,
                'raw' => $content,
            ]);
        } catch (\Throwable $e) {
            Log::error('Content generation fatal error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Váratlan hiba történt a generálás során.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildSystemPrompt(): string
    {
        return <<<PROMPT
Te egy professzionális magyar nyelvű érettségi felkészítő tanár vagy. A feladatod egy magasan strukturált, pedagógiailag átgondolt tananyag generálása.

Használj tiszta HTML kódot (Inline stílusok nélkül, a megadott CSS osztályokat használva).

A tananyag tartalmazzon: logikus alcímeket, kiemeléseket (bold), felsorolásokat, egy 'Axel tippje' keretes megjegyzést, és a végén egy rövid összefoglaló táblázatot.

A hangnem legyen bátorító, modern és közérthető.

Kimeneti szabályok:
- KIZÁRÓLAG HTML kódot adj vissza, markdown kódfence nélkül.
- Használj reszponzív és theme-kompatibilis utility osztályokat (Tailwind-szerű osztályok rendben vannak).
- Ne használj inline style attribútumot.
- A színekhez és felületekhez preferáld a projektben használt osztályokat/var() változókat.
PROMPT;
    }

    private function buildUserPrompt(string $textSource, array $fileContext, string $topicTitle, string $subjectName): string
    {
        $filePart = $fileContext['content'] ?? '';
        $fileMeta = $fileContext['meta'] ?? 'Nincs fájl metaadat.';

        return <<<PROMPT
Készíts tananyag HTML-t az alábbi témához.

Tantárgy: {$subjectName}
Lecke címe: {$topicTitle}

Projekt design rendszer és téma logika (kötelezően igazodj hozzá):
- A projekt globális CSS változókat használ, data-theme attribútummal.
- [data-theme="dark"] fő változók:
  --bg-color: #0f172a
  --text-primary: #f8fafc
  --text-secondary: #94a3b8
  --glass-bg: rgba(30, 41, 59, 0.7)
  --glass-border: rgba(255, 255, 255, 0.08)
  --accent: #6366f1
- [data-theme="light"] fő változók:
  --bg-color: #e4f1fa
  --text-primary: #1a3550
  --text-secondary: #42627b
  --surface-1: #feffff
  --surface-2: #e4f1fa
  --surface-3: #aedae1
  --brand-blue: #2d72b6
  --brand-pink: #d94e78
  --accent: var(--brand-blue)
- Jelentés:
  --bg-color: oldal háttér
  --text-primary: fő szöveg
  --text-secondary: másodlagos magyarázó szöveg
  --glass-bg / --glass-border: kártya-szerű panelek
  --accent: CTA/kiemelés
- Tartalomhoz használj olyan osztályokat, amelyek light/dark módban is olvashatóak: pl. text-white/text-gray-200 + light mode override kompatibilis szerkezet, bg-[#131b3d], bg-[#0b102e], border-gray-700 jelleg.
- Kimenet legyen mobilon is jól tördelődő.

Forrás szöveg:
"""
{$textSource}
"""

Fájl metaadat:
{$fileMeta}

Fájlból kinyert tartalom / kivonat:
"""
{$filePart}
"""

Kimenet: csak HTML.
PROMPT;
    }

    private function extractHtml(string $content): string
    {
        $trimmed = trim($content);
        if (Str::startsWith($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:html)?\s*/', '', $trimmed);
            $trimmed = preg_replace('/\s*```$/', '', $trimmed);
        }

        return trim($trimmed);
    }

    private function extractFileContext($file): array
    {
        if (!$file) {
            return ['meta' => 'Nem lett fájl feltöltve.', 'content' => ''];
        }

        $mime = $file->getMimeType() ?? 'ismeretlen';
        $meta = sprintf('Név: %s | MIME: %s | Méret: %d byte', $file->getClientOriginalName(), $mime, $file->getSize());

        if ($mime === 'text/plain') {
            return [
                'meta' => $meta,
                'content' => mb_substr((string) file_get_contents($file->getRealPath()), 0, 15000),
            ];
        }

        if ($mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            return [
                'meta' => $meta,
                'content' => mb_substr($this->extractDocxText($file->getRealPath()), 0, 15000),
            ];
        }

        return [
            'meta' => $meta.' | Megjegyzés: ebből a fájltípusból a backend nem végez teljes szövegfelismerést, csak metaadatot továbbít.',
            'content' => '',
        ];
    }

    private function extractDocxText(string $path): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        if ($xml === '') {
            return '';
        }

        $plain = strip_tags(str_replace('</w:p>', "\n", $xml));

        return preg_replace('/\s+/', ' ', $plain) ?? '';
    }
}
