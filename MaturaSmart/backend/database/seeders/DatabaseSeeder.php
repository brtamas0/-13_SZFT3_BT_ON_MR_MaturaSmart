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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'email' => 'tomi@maturasmart.hu',
            'full_name' => 'Bíró Tamás Attila',
            'password' => Hash::make('jelszo1234'),
            'role' => 'admin',
            'xp' => 100,
            'gems' => 50,
        ]);
        $user = User::create([
            'email' => 'nati@maturasmart.hu',
            'full_name' => 'Ocskó Natasa',
            'password' => Hash::make('jelszo123'),
            'role' => 'admin',
            'xp' => 100,
            'gems' => 50,
        ]);
        $user = User::create([
            'email' => 'roli@maturasmart.hu',
            'full_name' => 'Maródi Roland',
            'password' => Hash::make('jelszo5342'),
            'role' => 'admin',
            'xp' => 100,
            'gems' => 50,
        ]);

        $math = Subject::create([
            'name' => 'Matematika',
            'slug' => 'matematika',
            'icon' => '🧮',
        ]);

        $history = Subject::create([
            'name' => 'Történelem',
            'slug' => 'tortenelem',
            'icon' => '🏛️',
        ]);

        $pythagoras = Topic::create([
            'subject_id' => $math->id,
            'title' => 'A Pitagorasz-tétel',
            'slug' => 'pitagorasz-tetel',
            'description' => 'Ismerd meg a derékszögű háromszögek titkát.',
            
            'xp' => 100,

            'content' => '
        <p>A Pitagorasz-tétel a geometria egyik alappillére. Azt mondja ki, hogy derékszögű háromszögben a két befogó négyzetének összege egyenlő az átfogó négyzetével.</p>
        
        <h3 class="text-xl font-bold text-white mt-4">A képlet</h3>
        <p class="font-mono bg-black/30 p-4 rounded inline-block mt-2 text-blue-300">a² + b² = c²</p>
        
        <p class="mt-4">Ez a tétel csak és kizárólag <strong>derékszögű</strong> háromszögekre igaz.</p>
    ',

            'order' => 1,
        ]);

        $q1 = Question::create([
            'topic_id' => $pythagoras->id,
            'type' => 'multiple_choice',
            'content' => 'Melyik háromszögre igaz a Pitagorasz-tétel?',
            'points' => 10,
            'difficulty' => 1,
        ]);

        Answer::create(['question_id' => $q1->id, 'text' => 'Derékszögű', 'is_correct' => true]);
        Answer::create(['question_id' => $q1->id, 'text' => 'Egyenlő szárú', 'is_correct' => false]);
        Answer::create(['question_id' => $q1->id, 'text' => 'Bármilyen', 'is_correct' => false]);

        $q2 = Question::create([
            'topic_id' => $pythagoras->id,
            'type' => 'multiple_choice',
            'content' => 'Ha a befogók 3 és 4 cm hosszúak, mennyi az átfogó?',
            'points' => 20,
            'difficulty' => 2,
        ]);

        Answer::create(['question_id' => $q2->id, 'text' => '5', 'is_correct' => true]);
        Answer::create(['question_id' => $q2->id, 'text' => '7', 'is_correct' => false]);
        Answer::create(['question_id' => $q2->id, 'text' => '12', 'is_correct' => false]);

        ShopItem::create([
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

        Achievement::create([
            'name' => 'Első Lépések',
            'description' => 'Oldj meg egy feladatot hibátlanul.',
            'xp_reward' => 50,
            'icon' => '🥇',
        ]);

        \App\Models\UserInventory::create([
            'user_id' => $user->id,
            'shop_item_id' => 1,
            'quantity' => 2,
        ]);

        echo "Adatok feltöltve! Belépés: admin@teszt.hu / password\n";
    }
}
