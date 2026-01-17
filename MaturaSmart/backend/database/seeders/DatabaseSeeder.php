<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ShopItem;
use App\Models\Achievement;
use App\Models\UserInventory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- FELHASZNÁLÓK ---
        $tomi = User::create([
            'email' => 'tomi@maturasmart.hu',
            'full_name' => 'Bíró Tamás Attila',
            'password' => Hash::make('jelszo1234'),
            'role' => 'admin',
            'xp' => 3250,
            'gems' => 50,
            'level' => 4,
        ]);

        $nati = User::create([
            'email' => 'nati@maturasmart.hu',
            'full_name' => 'Ocskó Natasa',
            'password' => Hash::make('jelszo123'),
            'role' => 'admin',
            'xp' => 2850,
            'gems' => 120,
            'level' => 3,
        ]);

        $roli = User::create([
            'email' => 'roli@maturasmart.hu',
            'full_name' => 'Maródi Roland',
            'password' => Hash::make('jelszo5342'),
            'role' => 'admin',
            'xp' => 850,
            'gems' => 50,
            'level' => 5,
        ]);

        // --- MATEMATIKA ---
        $math = Subject::create([
            'name' => 'Matematika',
            'slug' => 'matematika',
            'icon' => '🧮',
            'description' => 'Algebra, Geometria és minden, ami számolás.'
        ]);

        $pythagoras = Topic::create([
            'subject_id' => $math->id,
            'title' => 'A Pitagorasz-tétel',
            'slug' => 'pitagorasz-tetel',
            'description' => 'Ismerd meg a derékszögű háromszögek titkát.',
            'xp' => 100,
            'order' => 1,
            'content' => '
                <p>A Pitagorasz-tétel a geometria egyik alappillére. Azt mondja ki, hogy derékszögű háromszögben a két befogó négyzetének összege egyenlő az átfogó négyzetével.</p>
                <h3 class="text-xl font-bold text-white mt-4">A képlet</h3>
                <p class="font-mono bg-black/30 p-4 rounded inline-block mt-2 text-blue-300">a² + b² = c²</p>
                <p class="mt-4">Ez a tétel csak és kizárólag <strong>derékszögű</strong> háromszögekre igaz.</p>
            ',
        ]);

        // Kérdések a Matekhoz
        $q1 = Question::create(['topic_id' => $pythagoras->id, 'type' => 'multiple_choice', 'content' => 'Melyik háromszögre igaz a Pitagorasz-tétel?', 'xp' => 10, 'difficulty' => 1]);
        Answer::create(['question_id' => $q1->id, 'text' => 'Derékszögű', 'is_correct' => true]);
        Answer::create(['question_id' => $q1->id, 'text' => 'Egyenlő szárú', 'is_correct' => false]);
        Answer::create(['question_id' => $q1->id, 'text' => 'Bármilyen', 'is_correct' => false]);

        $q2 = Question::create(['topic_id' => $pythagoras->id, 'type' => 'multiple_choice', 'content' => 'Ha a befogók 3 és 4 cm hosszúak, mennyi az átfogó?', 'xp' => 20, 'difficulty' => 2]);
        Answer::create(['question_id' => $q2->id, 'text' => '5', 'is_correct' => true]);
        Answer::create(['question_id' => $q2->id, 'text' => '7', 'is_correct' => false]);
        Answer::create(['question_id' => $q2->id, 'text' => '12', 'is_correct' => false]);


        // --- TÖRTÉNELEM ---
        $history = Subject::create([
            'name' => 'Történelem',
            'slug' => 'tortenelem',
            'icon' => '⚔️',
            'description' => 'Magyarország és a nagyvilág története.'
        ]);

        $stateFoundation = Topic::create([
            'subject_id' => $history->id,
            'title' => 'Az Államalapítás',
            'slug' => 'az-allamalapitas',
            'description' => 'Szent István király és a kereszténység felvétele.',
            'xp' => 150,
            'order' => 1,
            'content' => '<p>István király 1000-ben történt koronázásával Magyarország csatlakozott a keresztény Európához...</p>'
        ]);
        
        // Kérdés Törihez
        $q3 = Question::create(['topic_id' => $stateFoundation->id, 'type' => 'multiple_choice', 'content' => 'Mikor koronázták meg Szent Istvánt?', 'xp' => 10, 'difficulty' => 1]);
        Answer::create(['question_id' => $q3->id, 'text' => '1000-ben', 'is_correct' => true]);
        Answer::create(['question_id' => $q3->id, 'text' => '896-ban', 'is_correct' => false]);


        // --- IRODALOM ---
        Subject::create([
            'name' => 'Irodalom',
            'slug' => 'irodalom',
            'icon' => '📖',
            'description' => 'Versek, novellák és kötelező olvasmányok.'
        ]);


        // --- ANGOL ---
        Subject::create([
            'name' => 'Angol nyelv',
            'slug' => 'angol',
            'icon' => '🇬🇧',
            'description' => 'Grammar, Vocabulary és érettségi felkészítő.'
        ]);


        // --- NYELVTAN ---
        $grammar = Subject::create([
            'name' => 'Magyar nyelvtan',
            'slug' => 'nyelvtan',
            'icon' => '✍️',
            'description' => 'Helyesírás, szófajtan és mondattan.'
        ]);

        $partsOfSpeech = Topic::create([
            'subject_id' => $grammar->id,
            'title' => 'A szófajok rendszere',
            'slug' => 'szofajok',
            'description' => 'Alapszófajok, viszonyszók és mondatszók.',
            'xp' => 120,
            'order' => 1,
            'content' => '
                <h3>A szófajok csoportosítása</h3>
                <p>A magyar nyelv szavait jelentésük és mondatbeli szerepük alapján három fő kategóriába soroljuk:</p>
                <ul>
                    <li><strong>Alapszófajok:</strong> (pl. főnév, ige)</li>
                    <li><strong>Viszonyszók:</strong> (pl. névelő, kötőszó)</li>
                    <li><strong>Mondatszók:</strong> (pl. indulatszó)</li>
                </ul>
            '
        ]);

        // Kérdés Nyelvtanhoz
        $q4 = Question::create(['topic_id' => $partsOfSpeech->id, 'type' => 'multiple_choice', 'content' => 'Milyen szófaj a "fut" szó?', 'xp' => 10, 'difficulty' => 1]);
        Answer::create(['question_id' => $q4->id, 'text' => 'Ige', 'is_correct' => true]);
        Answer::create(['question_id' => $q4->id, 'text' => 'Főnév', 'is_correct' => false]);


        // --- SHOP ITEMS ---
        $freeze = ShopItem::create([
            'name' => 'Streak Freeze',
            'cost' => 50,
            'type' => 'streak_freeze',
            'icon' => '🧊',
        ]);

        ShopItem::create([
            'name' => 'Arany Keret',
            'cost' => 200,
            'type' => 'avatar_frame',
            'icon' => '🪙',
        ]);

        // --- ACHIEVEMENTS ---
        Achievement::create([
            'name' => 'Első Lépések',
            'description' => 'Oldj meg egy feladatot hibátlanul.',
            'xp_reward' => 50,
            'icon' => '🥇',
        ]);

        // --- INVENTORY ---
        UserInventory::create([
            'user_id' => $tomi->id,
            'shop_item_id' => $freeze->id,
            'quantity' => 2,
        ]);

        echo "Adatok feltöltve! \n";
        echo "Tomi: tomi@maturasmart.hu\n";
        echo "Nati: nati@maturasmart.hu\n";
        echo "Roli: roli@maturasmart.hu\n";
    }
}