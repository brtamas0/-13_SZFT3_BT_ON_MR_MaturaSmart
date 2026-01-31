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
            $history = $request->input('history', []);
            $subject = $request->input('subject', 'Ismeretlen tárgy');
            $topic = $request->input('topic', 'Ismeretlen témakör');
            
            // HTML tagek törlése a token spóroláshoz + limitálás
            $rawNotes = $request->input('notes') ?: "";
            $cleanNotes = strip_tags($rawNotes);
            // Ha nagyon hosszú a lecke, levágjuk az első 8000 karakterre (kb 2-3k token), ai kiegészíti saját tudásból, ha valami lemaradt
            $cleanNotes = mb_substr($cleanNotes, 0, 8000); 

            // Szigorított System Prompt
            $systemPrompt = <<<EOT
SZEREP:
Te Axel vagy, a MaturaSmart oktatási platform AI mentora. 
Célod: Kizárólag a megadott tananyag megértésében segíteni a diákot.
Stílusod: Fiatalos, tegező, bátorító, emojikat használó (de nem túlzásba vive).

KONTEXTUS ADATOK:
- Tantárgy: $subject
- Témakör: $topic

JELENLEGI TANANYAG TARTALMA (Forrás):
"""
$cleanNotes
"""

SZIGORÚ SZABÁLYOK (GUARDRAILS):
1. KIZÁRÓLAG a fenti "Jelenlegi Tananyag Tartalma" és a "$subject" tárgykörében válaszolj.
2. HA a kérdés NEM kapcsolódik a tananyaghoz vagy a tantárgyhoz (pl. "Mi a kedvenc színed?", "Írj egy receptet", "Ki nyerte a meccset?"):
   - VÁLASZOD: "Bocsi, de én csak a(z) $subject tantárggyal és a(z) $topic leckével kapcsolatban tudok segíteni! 📚 Térjünk vissza a tanuláshoz!"
   - NE válaszolj a kérdésre, még akkor sem, ha tudod a választ.
3. Ne oldd meg a házifeladatot helyette, hanem vezesd rá a megoldásra.
4. Ha a tananyagban nincs benne a válasz, de szorosan kapcsolódik a témához (pl. történelemnél egy évszám), akkor válaszolhatsz általános tudásodból, de jelezd, hogy ez kiegészítés.

VÁLASZ FORMÁTUM:
- Használj Markdown formázást (félkövér kiemelések).
- Légy tömör és lényegretörő.
EOT;

            // API Kulcs ellenőrzés
            $apiKey = env('GROQ_API_KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'Hiányzik a GROQ_API_KEY!'], 500);
            }
            
            $messagesPayload = [];
            $messagesPayload[] = ['role' => 'system', 'content' => $systemPrompt];

            // Előzmények hozzáadása (Max 6 üzenet)
            if (!empty($history) && is_array($history)) {
                $historySubset = array_slice($history, -6);
                foreach ($historySubset as $msg) {
                    if (isset($msg['role'], $msg['content'])) {
                        $messagesPayload[] = [
                            'role' => $msg['role'], 
                            'content' => mb_substr($msg['content'], 0, 500) // User input limitálása
                        ];
                    }
                }
            }

            $messagesPayload[] = ['role' => 'user', 'content' => $message];

            // Küldés a GROQ API-nak
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // 'mixtral-8x7b-32768' gyorsabb lehet, ha később AI-val validálnánk a felhasználó kérdését, hogy a topichoz tartozik e
                'messages' => $messagesPayload,
                'temperature' => 0.6,
                'max_tokens' => 800,
                'top_p' => 0.9
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['choices'][0]['message']['content'] ?? 'Ezt most nem tudtam feldolgozni.';
                return response()->json(['answer' => $answer]);
            } else {
                return response()->json([
                    'error' => 'Hiba az AI szolgáltatásban.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Szerver Hiba'], 500);
        }
    }
}