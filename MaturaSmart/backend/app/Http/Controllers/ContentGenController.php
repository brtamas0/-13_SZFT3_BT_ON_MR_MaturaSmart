<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentGenController extends Controller
{
    public function generate(Request $request)
    {
        $requestTimeout = (int) env('CONTENTGEN_TIMEOUT_SECONDS', 180);
        $requestTimeout = max(30, $requestTimeout);

        if (function_exists('set_time_limit')) {
            set_time_limit($requestTimeout + 10);
        }
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

        $apiKey = env('OPENROUTER_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'Hiányzik az OPENROUTER_API_KEY a backend környezetből.'], 500);
        }

        $topicTitle = $validated['topic_title'] ?? 'Ismeretlen lecke';
        $subjectName = $validated['subject_name'] ?? 'Ismeretlen tantárgy';

        $messages = [
            ['role' => 'system', 'content' => $this->buildSystemPrompt()],
            ['role' => 'user', 'content' => $this->buildUserPrompt($textSource, $fileContext, $topicTitle, $subjectName)],
        ];


        $referer = $request->headers->get('origin')
            ?: config('app.url')
            ?: $request->getSchemeAndHttpHost();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => $referer,
                'X-Title' => 'MaturaSmart Course Builder',
            ])
                ->connectTimeout(20)
                ->timeout($requestTimeout)
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'nvidia/nemotron-3-super-120b-a12b:free',
                    'messages' => $messages,
                    'temperature' => 0.45,
                    'top_p' => 0.9,
                    'max_tokens' => 3000,
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Content generation timeout/connection error', [
                'timeout_seconds' => $requestTimeout,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Az AI szolgáltatás túl lassan válaszolt. Próbáld újra, vagy növeld a CONTENTGEN_TIMEOUT_SECONDS értékét.',
            ], 504);
        }

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Hiba történt a tananyag-generálás közben.',
                'details' => $response->json() ?: $response->body(),
            ], 500);
        }

        $content = data_get($response->json(), 'choices.0.message.content', '');
        $html = $this->extractHtml($content);

        return response()->json([
            'html' => $html,
            'raw' => $content,
        ]);
    }

    private function buildSystemPrompt(): string
    {
        return <<<PROMPT
Te egy professzionális magyar nyelvű érettségi felkészítő tanár és oktatási tartalomtervező vagy. A feladatod pedagógiailag átgondolt, jól strukturált, modern és könnyen tanulható tananyag generálása HTML formátumban.

Elsődleges céljaid:
- A tananyag legyen pontos, közérthető, logikusan felépített és érettségi felkészülésre alkalmas.
- A magyarázatok legyenek didaktikusak, fokozatosan építkezők, és segítsék a megértést, nem csak a bemagolást.
- A tartalom legyen vizuálisan jól tagolt, könnyen olvasható, mobilbarát, és illeszkedjen a projekt meglévő theme rendszeréhez.

Fontos:
- Ne csak információt sorolj fel, hanem segítsd a megértést.
- A HTML szerkezet támogassa a vizuális tagolást.
- Mobilon is jól tördelődő tartalmat adj.
PROMPT;
    }

    private function buildUserPrompt(string $textSource, array $fileContext, string $topicTitle, string $subjectName): string
    {
        $filePart = $fileContext['content'] ?? '';
        $fileMeta = $fileContext['meta'] ?? 'Nincs fájl metaadat.';

        return <<<PROMPT
Készíts részletes, jól tanulható, érettségi felkészítésre alkalmas HTML tananyagot az alábbi témához.

Tantárgy: {$subjectName}
Lecke címe: {$topicTitle}

Kötelező tartalmi elvárások:
- Legyen világos főcím utáni rövid bevezető.
- Használj logikus alcímeket.
- Használj jól elkülönülő bekezdéseket.
- Használj felsorolásokat ott, ahol a tananyag szerkezete ezt indokolja.
- Használj kiemeléseket (<strong>) a legfontosabb fogalmakhoz.
- Tartalmazzon egy külön, jól észrevehető „Axel tippje” blokkot.
- A végén legyen egy rövid, áttekintő összefoglaló táblázat.
- Ha indokolt, szerepeljen benne rövid példa, összehasonlítás vagy tipikus vizsgacsapda.

Kötelező formai és technikai szabályok:
- KIZÁRÓLAG HTML-t adj vissza.
- Ne használj markdown kódfence-et.
- Ne írj magyarázó szöveget a HTML-en kívül.
- Ne használj inline style attribútumot.
- Ne használj script taget.
- Ne használj külső CSS-re vagy JS-re utalást.
- A HTML legyen szemantikusan rendezett és tiszta.
- A kimenet legyen közvetlenül beilleszthető egy Vue komponens v-html tartalmába.

Theme és design rendszer szabályok:
- A projekt data-theme alapú light/dark megjelenítést használ.
- A generált HTML-nek ehhez kompatibilisnek kell lennie.
- Ne találj ki új globális CSS változókat.
- Ne használj fix színlogikát inline formában.
- Olyan osztálystruktúrát használj, amely jól együttműködik a projekt theme-aware stílusaival.
- A tartalom legyen jól olvasható dark és light módban is.
- Az osztályok lehetnek Tailwind-szerű utility osztályok és/vagy értelmes szemantikus class nevek.
- Törekedj tiszta, újrahasznosítható, jól olvasható HTML szerkezetre.

Preferált HTML szerkezet:
- külső wrapper
- rövid bevezető blokk
- több tartalmi szekció alcímekkel
- Axel tippje blokk
- összegző táblázat a végén

Preferált elemek:
- section
- div
- h2
- h3
- p
- ul / ol / li
- strong
- table / thead / tbody / tr / th / td
- blockquote vagy külön div a kiemelt tipphez

Pedagógiai stílus:
- magyar nyelven írj
- légy közérthető, de szakmailag pontos
- a hangnem legyen bátorító, modern és magyarázó
- ne legyen túl szószátyár
- ne legyen túl tömör sem
- úgy fogalmazz, mintha egy kiváló tanár magyarázná el a témát diákoknak

Kötelező design és theme kompatibilitási szabályok:
A projekt globális CSS változókat használ data-theme attribútummal.

Elérhető globális változók:

[data-theme="dark"]
- --bg-color: #0f172a
- --text-primary: #f8fafc
- --text-secondary: #94a3b8
- --glass-bg: rgba(30, 41, 59, 0.7)
- --glass-border: rgba(255, 255, 255, 0.08)
- --accent: #6366f1

[data-theme="light"]
- --bg-color: #e4f1fa
- --text-primary: #1a3550
- --text-secondary: #42627b
- --surface-1: #feffff
- --surface-2: #e4f1fa
- --surface-3: #aedae1
- --brand-blue: #2d72b6
- --brand-pink: #d94e78
- --accent: var(--brand-blue)

A változók jelentése:
- --bg-color: oldal háttér
- --text-primary: fő szöveg
- --text-secondary: másodlagos szöveg
- --glass-bg: üveghatású panel háttér
- --glass-border: üveghatású panel keret
- --accent: kiemelés, hangsúly, fontos rész
- --surface-1 / --surface-2 / --surface-3: világos mód felületei
- --brand-blue / --brand-pink: világos mód márkaszínei

Kötelező kimeneti szabályok:
- Csak HTML-t adj vissza.
- Ne használj markdownot.
- Ne használj inline style attribútumot.
- Ne használj script taget.
- Ne használj külső assetre hivatkozást.
- Ne találj ki új globális CSS változókat.
- A HTML legyen theme-kompatibilis.
- A tartalom mobilon is jól tördelődjön.
- A szerkezet legyen jól olvasható light és dark módban is.
- Használj értelmes class neveket és/vagy Tailwind-szerű utility classokat.
- Az osztályok ne ütközzenek a theme logikával.
- Olyan szerkezetet használj, amit a projekt theme-aware stílusai könnyen formáznak.

Javasolt classnév irányok:
- content-root
- content-intro
- content-section
- content-title
- content-subtitle
- content-text
- content-list
- content-tip
- content-tip-title
- content-summary
- content-table
- content-highlight

Kötelező tartalmi szerkezet:
1. Rövid bevezető
2. 3-6 jól elkülönülő tartalmi blokk alcímekkel
3. Fontos fogalmak kiemelése
4. Legalább egy felsorolás
5. Egy „Axel tippje” blokk
6. A végén rövid összefoglaló táblázat

Tartalmi minőségi elvárások:
- A magyarázat legyen pontos és közérthető.
- A tananyag ne csak definíciókat soroljon, hanem magyarázzon is.
- Segítse a megértést és a memorizálást.
- Ha indokolt, térjen ki tipikus hibákra, félreértésekre vagy vizsgahelyzetben fontos megkülönböztetésekre.
- Ne legyen túl hosszú, de legyen érdemben használható.
- Kerüld az üres frázisokat.

A HTML felépítésére mintaként gondolj ilyen szerkezetben:
- külső wrapper div vagy article
- bevezető section
- több tartalmi section
- külön Axel tippje panel
- végén összefoglaló section egy táblázattal

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

Feladat:
A fenti információk alapján generálj egy kész, tiszta, beilleszthető HTML tananyagot a megadott leckéhez.

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
