<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ShopItem;
use App\Models\Achievement;
use App\Models\UserInventory;
use App\Models\Flashcard;

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

        // ==========================================
        // MATEMATIKA
        // ==========================================
        $math = Subject::create([
            'name' => 'Matematika',
            'slug' => 'matematika',
            'icon' => '🧮',
            'description' => 'Algebra, Geometria és minden, ami számolás.'
        ]);

        // 1. Mappa: Geometria
        $geoUnit = Unit::create([
            'subject_id' => $math->id,
            'title' => 'Geometria',
            'order' => 1
        ]);

        $pythagoras = Topic::create([
            'subject_id' => $math->id,
            'unit_id' => $geoUnit->id,
            'title' => 'A Pitagorasz-tétel',
            'slug' => 'pitagorasz-tetel',
            'description' => 'A geometria legfontosabb összefüggése.',
            'year' => -500,
            'year_label' => 'i.e. 500',
            'xp' => 150,
            'order' => 1,
            'content' => <<<EOT
                <div class="space-y-12 text-gray-300 font-sans">
                    <div class="relative bg-gradient-to-br from-blue-900/40 to-indigo-900/40 border border-blue-500/20 rounded-2xl p-8 shadow-xl overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl"></div>
                        <h3 class="text-2xl font-bold text-white mb-4 relative z-10">Miért ez a legfontosabb tétel?</h3>
                        <p class="leading-relaxed relative z-10 text-lg">
                            A Pitagorasz-tétel nem csupán egy képlet. Ez az alapja a távolságmérésnek, az építészetnek, a GPS rendszereknek.
                        </p>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white font-bold text-xl">1</span>
                            <h2 class="text-3xl font-bold text-white">Elméleti alapok</h2>
                        </div>
                        <p class="mb-6 leading-relaxed">
                            A tétel kizárólag <strong>derékszögű háromszögekre</strong> vonatkozik.
                        </p>
                        <div class="bg-black/20 rounded-3xl p-8 border border-white/10 text-center">
                             <div class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-yellow-400 tracking-wider font-mono drop-shadow-2xl">
                                a² + b² = c²
                            </div>
                        </div>
                    </div>
                </div>
EOT,
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

        Flashcard::create(['topic_id' => $pythagoras->id, 'front' => 'Mi a Pitagorasz-tétel képlete?', 'back' => 'a² + b² = c²']);
        Flashcard::create(['topic_id' => $pythagoras->id, 'front' => 'Milyen háromszögre igaz?', 'back' => 'Csak a derékszögű háromszögre.']);
        Flashcard::create(['topic_id' => $pythagoras->id, 'front' => 'Mit nevezünk átfogónak?', 'back' => 'A derékszöggel szembeni oldalt.']);

        // ==========================================
        // TÖRTÉNELEM
        // ==========================================
        $history = Subject::create([
            'name' => 'Történelem',
            'slug' => 'tortenelem',
            'icon' => '⚔️',
            'description' => 'Magyarország és a nagyvilág története.'
        ]);

        $arpadUnit = Unit::create([
            'subject_id' => $history->id,
            'title' => 'Az Államalapítás kora',
            'order' => 1
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $arpadUnit->id,
            'title' => 'Az Államalapítás',
            'slug' => 'az-allamalapitas',
            'description' => 'Szent István király és a kereszténység felvétele.',
            'year' => 1000,
            'year_label' => '1000',
            'xp' => 150,
            'order' => 1,
            'content' => '<p>István király 1000-ben történt koronázásával...</p>'
        ]);
        
        // --- 2. Mappa---
        $torokUnit = Unit::create([
            'subject_id' => $history->id,
            'title' => 'Török hódoltság kora',
            'order' => 2
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $torokUnit->id,
            'title' => 'A Nándorfehérvári diadal',
            'slug' => 'nandorfehervar',
            'description' => 'Hunyadi János győzelme a túlerővel szemben.',
            'year' => 1456, // Timeline teszt
            'year_label' => '1456',
            'xp' => 120,
            'content' => '<p>A déli harangszó a győzelem emlékét őrzi...</p>'
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $torokUnit->id,
            'title' => 'A Mohácsi vész',
            'slug' => 'mohacs',
            'description' => 'A középkori Magyar Királyság bukása.',
            'year' => 1526,
            'year_label' => '1526',
            'xp' => 100,
            'content' => '<p>Csele patak, II. Lajos király halála...</p>'
        ]);

        // --- 3. Mappa: Újkor és Forradalmak ---
        $revolutionsUnit = Unit::create([
            'subject_id' => $history->id,
            'title' => 'Forradalmak kora',
            'order' => 3
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $revolutionsUnit->id,
            'title' => 'Rákóczi-szabadságharc',
            'slug' => 'rakoczi-szabadsagharc',
            'description' => 'Cum Deo pro Patria et Libertate - Istennel a hazáért és szabadságért.',
            'year' => 1703,
            'year_label' => '1703-1711',
            'xp' => 140,
            'content' => '<p>A brezáni kiáltvánnyal kezdődött...</p>'
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $revolutionsUnit->id,
            'title' => 'Az 1848-49-es forradalom',
            'slug' => '1848-forradalom',
            'description' => 'Talpra magyar, hí a haza! Petőfi és a márciusi ifjak.',
            'year' => 1848,
            'year_label' => '1848',
            'xp' => 160,
            'content' => '<p>Március 15., Pilvax kávéház, 12 pont...</p>'
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $revolutionsUnit->id,
            'title' => 'Kiegyezés',
            'slug' => 'kiegyezes',
            'description' => 'Az Osztrák-Magyar Monarchia létrejötte.',
            'year' => 1867,
            'year_label' => '1867',
            'xp' => 110,
            'content' => '<p>Deák Ferenc, a haza bölcse és Ferenc József megállapodása.</p>'
        ]);

        // --- 4. Mappa: A 20. század viharai ---
        $modernUnit = Unit::create([
            'subject_id' => $history->id,
            'title' => 'A 20. század viharai',
            'order' => 4
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $modernUnit->id,
            'title' => 'A Trianoni békediktátum',
            'slug' => 'trianon',
            'description' => 'Magyarország területének kétharmadának elvesztése.',
            'year' => 1920,
            'year_label' => '1920',
            'xp' => 130,
            'content' => '<p>A versailles-i Nagy Trianon kastélyban írták alá...</p>'
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $modernUnit->id,
            'title' => 'Az 1956-os forradalom',
            'slug' => '1956-forradalom',
            'description' => 'Harc a szovjet elnyomás ellen. A lyukas zászló.',
            'year' => 1956,
            'year_label' => '1956',
            'xp' => 180,
            'content' => '<p>Október 23., a Budapesti Műszaki Egyetem diákjainak felvonulása...</p>'
        ]);
        
        // --- 5. Mappa (időrend teszteléshez) ---
        $worldUnit = Unit::create([
            'subject_id' => $history->id,
            'title' => 'Egyetemes kitekintő',
            'order' => 5
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $worldUnit->id,
            'title' => 'A Római Birodalom bukása',
            'slug' => 'roma-bukasa',
            'description' => 'Az ókor vége és a középkor kezdete.',
            'year' => 476,
            'year_label' => '476',
            'xp' => 120,
            'content' => '<p>Romulus Augustulus lemondatása...</p>'
        ]);

        Topic::create([
            'subject_id' => $history->id,
            'unit_id' => $worldUnit->id,
            'title' => 'Amerika felfedezése',
            'slug' => 'amerika-felfedezese',
            'description' => 'Kolumbusz Kristóf útja és az Újvilág.',
            'year' => 1492,
            'year_label' => '1492',
            'xp' => 130,
            'content' => '<p>Santa Maria, Pinta, Nina...</p>'
        ]);
        
        // Kérdés Törihez

        // ==========================================
        // IRODALOM
        // ==========================================
        $lit = Subject::create([
            'name' => 'Irodalom',
            'slug' => 'irodalom',
            'icon' => '📖',
            'description' => 'Versek, novellák és kötelező olvasmányok.'
        ]);
        
        Unit::create([
            'subject_id' => $lit->id,
            'title' => 'Költészet',
            'order' => 1
        ]);

        // ==========================================
        // ANGOL
        // ==========================================
        $eng = Subject::create([
            'name' => 'Angol nyelv',
            'slug' => 'angol',
            'icon' => '🇬🇧',
            'description' => 'Grammar, Vocabulary és érettségi felkészítő.'
        ]);

        Unit::create([
            'subject_id' => $eng->id,
            'title' => 'Grammar',
            'order' => 1
        ]);


        // ==========================================
        // NYELVTAN
        // ==========================================
        $grammar = Subject::create([
            'name' => 'Magyar nyelvtan',
            'slug' => 'nyelvtan',
            'icon' => '✍️',
            'description' => 'Helyesírás, szófajtan és mondattan.'
        ]);

        $partsUnit = Unit::create([
            'subject_id' => $grammar->id,
            'title' => 'Szófajtan',
            'order' => 1
        ]);

        $partsOfSpeech = Topic::create([
            'subject_id' => $grammar->id,
            'unit_id' => $partsUnit->id,
            'title' => 'A szófajok rendszere',
            'slug' => 'szofajok',
            'description' => 'Alapszófajok, viszonyszók és mondatszók.',
            'xp' => 120,
            'order' => 1,
            'year' => null,
            'content' => '
                <h3>A szófajok csoportosítása</h3>
                <p>A magyar nyelv szavait jelentésük és mondatbeli szerepük alapján három fő kategóriába soroljuk.</p>
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

        echo "Adatok feltöltve Unitokkal és Timeline adatokkal! \n";
        echo "Tomi: tomi@maturasmart.hu\n";
        echo "Nati: nati@maturasmart.hu\n";
        echo "Roli: roli@maturasmart.hu\n";
    }
}