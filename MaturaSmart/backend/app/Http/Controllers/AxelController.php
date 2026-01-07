<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AxelController extends Controller
{
    public function ask(Request $request)
    {
        try {
            //Adatok fogadása
            $message = $request->input('message');
            $subject = $request->input('subject', 'Általános');
            $topic = $request->input('topic', 'Általános');
            $notes = $request->input('notes') ?: "Nincs megadva konkrét tananyag.";

            //Alap prompt, Ai tanítás
            $systemPrompt = <<<EOT
            SZEREP:
Te Axel vagy, a MaturaSmart érettségi felkészítő oldal intelligens, fiatalos és türelmes kabalája, aki segít a tanulónak, ha kérdése lenne, segítségre lenne szüksége. 🤖🎓

KONTEXTUS:
Tantárgy: $subject
Témakör: $topic

Az alábbi tananyagra / tananyag alapján válaszolj!

""" MaturaSmart Tananyag leírás:
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

EOT;

            //API Kulcs
            $apiKey = env('GROQ_API_KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'Hiányzik a GROQ_API_KEY!'], 500);
            }

            // GROQ API Request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',  //model
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1024
            ]);

            //Válasz a chatre
            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['choices'][0]['message']['content'] ?? 'Nem kaptam választ.';
                return response()->json(['answer' => $answer]);
            } else {
                return response()->json([
                    'error' => 'Groq API Hiba',
                    'details' => $response->json()
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Szerver Hiba', 'msg' => $e->getMessage()], 500);
        }
    }
}