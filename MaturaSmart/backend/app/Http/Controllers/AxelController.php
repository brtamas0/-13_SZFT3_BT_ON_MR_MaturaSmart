<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AxelController extends Controller
{
    public function ask(Request $request)
    {
        try {
            // 1. Adatok fogadása
            $message = $request->input('message');
            $subject = $request->input('subject', 'Általános');
            $topic = $request->input('topic', 'Általános');
            
            $rawNotes = $request->input('notes');
            $notes = $rawNotes ? $rawNotes : "Nincs megadva konkrét tananyag, használd az általános tudásodat.";

            // Alap prompt
            $systemPrompt = <<<EOT
SZEREP:
Te Axel vagy, a MaturaSmart érettségi felkészítő oldal intelligens, fiatalos és türelmes, tanuló segédje. 🤖🎓

KONTEXTUS:
Tantárgy: $subject
Témakör: $topic

Az alábbi tananyagra / tananyag alapján válaszolj! Ha a válasz megtalálható ebben a szövegben, akkor ezt használd elsődleges forrásként, és egészítsd ki a saját tudásoddal.

"""
$notes
"""

INSTRUKCIÓK:
1. Stílus: Tegeződj, légy közvetlen, használj emojikat.
2. Pedagógia: Ne csak a megoldást mondd meg! Magyarázd el úgy, mintha a fenti jegyzetet értelmeznéd a diáknak.
3. Matek esetén: Vezesd le lépésről lépésre.
4. Nyelvtan esetén: Adj példákat a szabályokra.
5. Történelem esetén: Helyezd el a kontextusban az eseményeket.
6. Ha a kérdés nem kapcsolódik a fenti tantárgyhoz vagy témakörhöz, udvariasan jelezd, hogy ebben nem tudsz segíteni.
7. Formázás: Használj Markdown-t (félkövér, listák, stb.) a válaszodban.

A diák kérdése:
EOT;

            // 3. API Kulcs
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'Hiányzik a GEMINI_API_KEY!'], 500);
            }

            // 4. Kérés küldése
            // $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying() // <--- "ideiglenes", SSL kikerülésre
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $systemPrompt . "\n\n" . $message]
                            ]
                        ]
                    ]
                ]);

            // 5. Válasz feldolgozása
            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sajnos nem kaptam értékelhető választ.';
                return response()->json(['answer' => $answer]);
            } else {
                return response()->json([
                    'error' => 'Google API Hiba',
                    'details' => $response->json()
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Szerver Hiba',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}