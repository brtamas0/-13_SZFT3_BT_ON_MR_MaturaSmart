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
            'description' => 'A geometria legfontosabb összefüggése: elmélet, bizonyítás és gyakorlati számítások mesterfokon.',
            'xp' => 150,
            'order' => 1,
            'content' => <<<EOT
                <div class="space-y-12 text-gray-300 font-sans">
                    
                    <div class="relative bg-gradient-to-br from-blue-900/40 to-indigo-900/40 border border-blue-500/20 rounded-2xl p-8 shadow-xl overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl"></div>
                        <h3 class="text-2xl font-bold text-white mb-4 relative z-10">Miért ez a legfontosabb tétel?</h3>
                        <p class="leading-relaxed relative z-10 text-lg">
                            A Pitagorasz-tétel nem csupán egy képlet. Ez az alapja a távolságmérésnek, az építészetnek, a GPS rendszereknek, sőt, még a videojátékok 3D grafikájának is. Ez a tétel teremt hidat az algebra (számok) és a geometria (alakzatok) között.
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white font-bold text-xl">1</span>
                            <h2 class="text-3xl font-bold text-white">Elméleti alapok</h2>
                        </div>
                        
                        <p class="mb-6 leading-relaxed">
                            A tétel kizárólag <strong>derékszögű háromszögekre</strong> vonatkozik. Az alábbi ábrán láthatod a pontos elnevezéseket. Jegyezd meg a színeket, segíteni fognak!
                        </p>

                        <div class="bg-[#0f172a] border border-white/5 rounded-2xl p-8 mb-8 flex justify-center shadow-inner">
                            <svg width="300" height="200" viewBox="0 0 300 200" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50,150 L80,150 L80,120" fill="none" stroke="#475569" stroke-width="2" />
                                <circle cx="65" cy="135" r="2" fill="#475569" />
                                
                                <path d="M50,20 L50,150 L250,150 Z" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                
                                <line x1="50" y1="20" x2="50" y2="150" stroke="#60a5fa" stroke-width="4" />
                                <text x="30" y="95" fill="#60a5fa" font-family="sans-serif" font-weight="bold" font-size="16">a</text>
                                
                                <line x1="50" y1="150" x2="250" y2="150" stroke="#818cf8" stroke-width="4" />
                                <text x="150" y="175" fill="#818cf8" font-family="sans-serif" font-weight="bold" font-size="16">b</text>
                                
                                <line x1="50" y1="20" x2="250" y2="150" stroke="#facc15" stroke-width="4" />
                                <text x="160" y="70" fill="#facc15" font-family="sans-serif" font-weight="bold" font-size="18">c (átfogó)</text>
                            </svg>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-[#1e293b] p-6 rounded-xl border-l-4 border-blue-400 hover:bg-[#253045] transition-colors">
                                <h4 class="text-blue-400 font-bold text-lg mb-2 uppercase tracking-wide">Befogók (a, b)</h4>
                                <p>Azok az oldalak, amelyek a <strong>derékszöget (90°)</strong> közrefogják. A fenti ábrán a kék és lila oldalak.</p>
                            </div>
                            <div class="bg-[#1e293b] p-6 rounded-xl border-l-4 border-yellow-400 hover:bg-[#253045] transition-colors">
                                <h4 class="text-yellow-400 font-bold text-lg mb-2 uppercase tracking-wide">Átfogó (c)</h4>
                                <p>A derékszöggel <strong>szemben</strong> lévő oldal. Ez mindig a háromszög <strong>leghosszabb</strong> oldala. Az ábrán sárgával jelölve.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-black/20 rounded-3xl p-8 border border-white/10">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-600 text-white font-bold text-xl">2</span>
                            <h2 class="text-3xl font-bold text-white">A Tétel Kimondása</h2>
                        </div>

                        <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border-l-4 border-blue-500 p-6 rounded-r-xl mb-8">
                            <p class="text-xl italic text-white font-medium">
                                "Bármely derékszögű háromszögben a befogók hosszának négyzetösszege egyenlő az átfogó hosszának négyzetével."
                            </p>
                        </div>

                        <div class="flex flex-col items-center justify-center py-8">
                            <div class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-yellow-400 tracking-wider font-mono drop-shadow-2xl">
                                a² + b² = c²
                            </div>
                            <p class="mt-4 text-gray-400 text-sm uppercase tracking-widest">Ahol 'c' mindig az átfogó!</p>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-600 text-white font-bold text-xl">3</span>
                            <h2 class="text-3xl font-bold text-white">Típusfeladatok</h2>
                        </div>

                        <div class="space-y-8">
                            <div class="bg-[#1e293b] rounded-2xl overflow-hidden border border-white/5">
                                <div class="bg-white/5 p-4 border-b border-white/5 flex justify-between items-center">
                                    <h4 class="font-bold text-white text-lg">A) Ha az átfogót keressük</h4>
                                    <span class="bg-green-500/20 text-green-400 text-xs font-bold px-2 py-1 rounded">Alap eset</span>
                                </div>
                                <div class="p-6">
                                    <p class="mb-4"><strong>Feladat:</strong> A két befogó 6 cm és 8 cm. Mekkora az átfogó?</p>
                                    <div class="space-y-3 font-mono text-sm md:text-base">
                                        <div class="flex items-center gap-4 bg-black/20 p-3 rounded-lg">
                                            <span class="text-gray-500 w-6">1.</span>
                                            <span class="text-gray-300">Írjuk fel:</span>
                                            <span class="text-white">6² + 8² = c²</span>
                                        </div>
                                        <div class="flex items-center gap-4 bg-black/20 p-3 rounded-lg">
                                            <span class="text-gray-500 w-6">2.</span>
                                            <span class="text-gray-300">Négyzetre emelés:</span>
                                            <span class="text-white">36 + 64 = c²</span>
                                        </div>
                                        <div class="flex items-center gap-4 bg-black/20 p-3 rounded-lg">
                                            <span class="text-gray-500 w-6">3.</span>
                                            <span class="text-gray-300">Összeadás:</span>
                                            <span class="text-white">100 = c²</span>
                                        </div>
                                        <div class="flex items-center gap-4 bg-green-900/20 border border-green-500/30 p-3 rounded-lg">
                                            <span class="text-gray-500 w-6">4.</span>
                                            <span class="text-gray-300">Gyökvonás:</span>
                                            <span class="text-green-400 font-bold">c = √100 = 10 cm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-[#1e293b] rounded-2xl overflow-hidden border border-white/5">
                                <div class="bg-white/5 p-4 border-b border-white/5 flex justify-between items-center">
                                    <h4 class="font-bold text-white text-lg">B) Ha az egyik befogót keressük</h4>
                                    <span class="bg-yellow-500/20 text-yellow-400 text-xs font-bold px-2 py-1 rounded">Figyelj!</span>
                                </div>
                                <div class="p-6">
                                    <p class="mb-4"><strong>Feladat:</strong> Az átfogó 13 cm, az egyik befogó 5 cm. Mekkora a másik?</p>
                                    <div class="bg-yellow-500/10 border-l-4 border-yellow-500 p-3 mb-4 text-sm text-yellow-200">
                                        ⚠️ Vigyázat! Itt <strong>ki kell vonni</strong> a kisebb négyzetet a nagyobbból!
                                        <div class="font-mono mt-1 font-bold">a² = c² - b²</div>
                                    </div>
                                    <div class="space-y-3 font-mono text-sm md:text-base">
                                        <div class="flex items-center gap-4 bg-black/20 p-3 rounded-lg">
                                            <span class="text-gray-500 w-6">1.</span>
                                            <span class="text-white">a² + 5² = 13²</span>
                                        </div>
                                        <div class="flex items-center gap-4 bg-black/20 p-3 rounded-lg">
                                            <span class="text-gray-500 w-6">2.</span>
                                            <span class="text-white">a² = 169 - 25</span>
                                        </div>
                                        <div class="flex items-center gap-4 bg-green-900/20 border border-green-500/30 p-3 rounded-lg">
                                            <span class="text-gray-500 w-6">3.</span>
                                            <span class="text-green-400 font-bold">a = √144 = 12 cm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-900/10 p-8 rounded-3xl border border-blue-500/20">
                        <div class="grid md:grid-cols-2 gap-8 items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-white mb-4">A Létra-probléma 🪜</h3>
                                <p class="leading-relaxed mb-4">
                                    Ez a leggyakoribb szöveges feladat. Adott egy fal és egy létra.
                                </p>
                                <ul class="space-y-2 text-gray-300 text-sm mb-6">
                                    <li class="flex items-center gap-2"><div class="w-3 h-3 bg-gray-500 rounded-full"></div> <strong>A fal:</strong> Függőleges befogó (a)</li>
                                    <li class="flex items-center gap-2"><div class="w-3 h-3 bg-green-500 rounded-full"></div> <strong>A talaj:</strong> Vízszintes befogó (b)</li>
                                    <li class="flex items-center gap-2"><div class="w-3 h-3 bg-yellow-500 rounded-full"></div> <strong>A létra:</strong> Átfogó (c)</li>
                                </ul>
                                <div class="bg-[#0b1029] p-4 rounded-xl border border-white/10">
                                    <p class="text-sm italic text-blue-200 mb-2">
                                        "Egy 5 méteres létrát a falhoz támasztunk úgy, hogy az alja 3 méterre van a faltól. Milyen magasra ér a létra?"
                                    </p>
                                    <div class="font-mono text-green-400 font-bold">x = √(5² - 3²) = 4 méter</div>
                                </div>
                            </div>
                            
                            <div class="flex justify-center">
                                <svg width="240" height="240" viewBox="0 0 240 240" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="20" y="220" width="200" height="4" fill="#22c55e" />
                                    <rect x="40" y="20" width="10" height="200" fill="#64748b" />
                                    <path d="M40,50 L50,50 M40,80 L50,80 M40,110 L50,110" stroke="#475569" stroke-width="1" />
                                    
                                    <line x1="50" y1="60" x2="180" y2="220" stroke="#eab308" stroke-width="6" stroke-linecap="round" />
                                    <line x1="65" y1="80" x2="75" y2="90" stroke="#eab308" stroke-width="4" />
                                    <line x1="90" y1="110" x2="100" y2="120" stroke="#eab308" stroke-width="4" />
                                    <line x1="115" y1="140" x2="125" y2="150" stroke="#eab308" stroke-width="4" />
                                    
                                    <text x="100" y="235" fill="#22c55e" font-size="12" font-weight="bold">3 m (távolság)</text>
                                    <text x="140" y="120" fill="#eab308" font-size="12" font-weight="bold">5 m (létra)</text>
                                    <text x="10" y="120" fill="#cbd5e1" font-size="12" font-weight="bold" transform="rotate(-90, 10, 120)">? (magasság)</text>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="text-2xl">🧠</span>
                            <h2 class="text-2xl font-bold text-white">Pro Tipp: Pitagoraszi Számhármasok</h2>
                        </div>
                        <p class="mb-4">Ha ezeket a számokat megjegyzed, számológép nélkül is azonnal tudni fogod az eredményt az érettségin!</p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-white/5 p-4 rounded-xl text-center hover:bg-white/10 transition-colors cursor-pointer group">
                                <div class="text-sm text-gray-400 mb-1">Klasszikus</div>
                                <div class="text-xl font-bold text-white group-hover:text-blue-400 transition-colors">3, 4, 5</div>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl text-center hover:bg-white/10 transition-colors cursor-pointer group">
                                <div class="text-sm text-gray-400 mb-1">Gyakori</div>
                                <div class="text-xl font-bold text-white group-hover:text-blue-400 transition-colors">5, 12, 13</div>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl text-center hover:bg-white/10 transition-colors cursor-pointer group">
                                <div class="text-sm text-gray-400 mb-1">Nagyobb</div>
                                <div class="text-xl font-bold text-white group-hover:text-blue-400 transition-colors">8, 15, 17</div>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl text-center hover:bg-white/10 transition-colors cursor-pointer group">
                                <div class="text-sm text-gray-400 mb-1">Ritkább</div>
                                <div class="text-xl font-bold text-white group-hover:text-blue-400 transition-colors">7, 24, 25</div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-white/10 pt-8 mt-12">
                        <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-6 flex gap-4 items-start">
                            <div class="text-3xl">🚫</div>
                            <div>
                                <h4 class="text-red-400 font-bold text-lg">Gyakori hibák – Ne kövesd el őket!</h4>
                                <ul class="list-disc list-inside mt-2 text-red-200/80 space-y-1">
                                    <li>Ne felejts el <strong>gyököt vonni</strong> a végén! (c² nem a végeredmény)</li>
                                    <li>Ne keverd össze a befogót az átfogóval. Mindig a <strong>leghosszabb</strong> az átfogó.</li>
                                    <li>Soha ne használd nem derékszögű háromszögre!</li>
                                </ul>
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

        Flashcard::create([
            'topic_id' => $pythagoras->id,
            'front' => 'Mi a Pitagorasz-tétel képlete?',
            'back' => 'a² + b² = c²',
        ]);

        Flashcard::create([
            'topic_id' => $pythagoras->id,
            'front' => 'Milyen háromszögre igaz a tétel?',
            'back' => 'Csak és kizárólag a derékszögű háromszögre.',
        ]);

        Flashcard::create([
            'topic_id' => $pythagoras->id,
            'front' => 'Mit nevezünk átfogónak?',
            'back' => 'A derékszöggel szembeni oldalt (ez a leghosszabb oldal).',
        ]);

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