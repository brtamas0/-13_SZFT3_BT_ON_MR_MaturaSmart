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
            'email' => 'admin@teszt.hu',
            'full_name' => 'Admin Ádám',
            'password' => Hash::make('jelszo123'),
            'role' => 'admin',
            'xp' => 100,
            'gems' => 50,
        ]);

        $math = Subject::create([
            'name' => 'Matematika',
            'slug' => 'matematika',
            'icon' => 'calculator-outline',
        ]);

        $history = Subject::create([
            'name' => 'Történelem',
            'slug' => 'tortenelem',
            'icon' => 'book-outline',
        ]);

        $pythagoras = Topic::create([
            'subject_id' => $math->id,
            'title' => 'A Pitagorasz-tétel',
            'description' => 'Ismerd meg a derékszögű háromszögek titkát.',
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
            'icon' => 'ice-cube',
        ]);

        ShopItem::create([
            'name' => 'Arany Keret',
            'cost' => 200,
            'type' => 'avatar_frame',
            'icon' => 'frame-gold',
        ]);

        Achievement::create([
            'name' => 'Első Lépések',
            'description' => 'Oldj meg egy feladatot hibátlanul.',
            'xp_reward' => 50,
            'icon' => 'medal-first',
        ]);

        \App\Models\UserInventory::create([
            'user_id' => $user->id,
            'shop_item_id' => 1,
            'quantity' => 2,
        ]);
        
        echo "Adatok feltöltve! Belépés: admin@teszt.hu / password\n";
    }
}