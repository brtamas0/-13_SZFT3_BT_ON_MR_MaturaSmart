MaturaSmart – Rendszer és Fejlesztői Dokumentáció
=================================================

1. Rendszer Áttekintés
-----------------------

### 1.1. Projekt Célja

A **MaturaSmart** egy modern oktatási platform, amelynek célja, hogy a diákok számára interaktív, gamifikált módon segítse a felkészülést az érettségire. A rendszer mesterséges intelligencia (AI) alapú mentorálást, személyre szabott tanulási útvonalakat és közösségi versenyélményt kínál.

### 1.2. Főbb Funkciók

*   **Tartalomkezelés:** Hierarchikus struktúra (Tantárgyak → Unitok → Témák).
    
*   **Tanulás:** Interaktív leckék, tesztek, tanulókártyák (Flashcards).
    
*   **AI Mentor (Axel):** Kontextus-érzékeny segítségnyújtás a leckékhez.
    
*   **Gamifikáció:** XP pontok, szintek, ranglista, streak (folyamatosság) követés.
    
*   **Adminisztráció:** Teljes körű tartalomkezelő (CMS) és felhasználókezelő felület.
    
*   **Kommunikáció:** Globális rendszerüzenetek (Popup).
    

2. Tech Stack
----------------------

### 2.1. Backend

*   **Keretrendszer:** Laravel 11.x
    
*   **Nyelv:** PHP 8.2+
    
*   **Adatbázis:** MySQL 8.0+
    
*   **API:** RESTful API (Laravel Sanctum autentikációval)
    
*   **Integrációk:**
    
    *   _Google OAuth:_ Google fiókos bejelentkezés.
        
    *   _Groq API:_ AI mentor (Llama-3 modell).
        

### 2.2. Frontend

*   **Keretrendszer:** Vue.js 3 (Composition API)
    
*   **Build tool:** Vite
    
*   **Stílus:** Tailwind CSS
    
*   **HTTP Kliens:** Axios
    

### 2.3. Fejlesztői Eszközök

*   **Verziókezelés:** Git
    
*   **Csomagkezelők:** Composer (PHP), npm (JS)
    
*   **Környezet:** Docker
    

3. Adatbázis Struktúra
-----------------------

### 3.1. Entitás Kapcsolatok

Subject (Tantárgy)
  └─ hasMany → Unit (Mappa/Korszak)
       └─ hasMany → Topic (Téma/Lecke/Teszt)
            ├─ hasMany → Question (Kérdés)
            │    └─ hasMany → Answer (Válasz)
            └─ hasMany → Flashcard (Tanulókártya)
            
### 3.2. Kulcsfontosságú Táblák

#### users

A felhasználók (diákok és adminok) adatai.

*   id, email, password, full_name, avatar_url
    
*   google_id: Google OAuth azonosító.
    
*   role: student vagy admin.
    
*   xp, level: Gamifikációs adatok.
    
*   last_topic_id: Utoljára megnyitott lecke.
    
*   current_streak, last_activity: Aktivitás követése.
    

#### topics

A tananyag legkisebb egységei.

*   type: lesson (tananyag) vagy test (vizsga).
    
*   content: A lecke szöveges tartalma (HTML/Markdown).
    
*   passing_percentage: Teszt esetén a sikeres teljesítés küszöbe.
    
*   reading_weight: Olvasás vs. feladatmegoldás aránya a progress számításhoz.
    

#### question_user

A felhasználók válaszainak naplózása (Pivot tábla).

*   user_id, question_id
    
*   is_correct: Helyes volt-e a válasz.
    
*   **Logika:** Egy felhasználó egy kérdésért csak egyszer kaphat XP-t.
    

#### global_messages

Rendszerszintű értesítések kezelése.

*   title, message, type (info, warning, danger, success).
    
*   is_active: Csak az aktív üzenet jelenik meg.
    
*   expires_at: Opcionális lejárati idő.
    

4. API Architektúra és Végpontok
---------------------------------

### 4.1. Publikus Végpontok

*   POST /login: Bejelentkezés.
    
*   POST /register: Regisztráció.
    
*   GET /auth/google: Google OAuth indítása.
    
*   POST /forgot-password, /reset-password: Jelszókezelés.
    
*   GET /global-message: Aktuális rendszerüzenet lekérése.
    

### 4.2. Védett Végpontok (Sanctum Auth)

#### Felhasználó és Dashboard

*   GET /dashboard: Főoldali statisztikák, utolsó lecke, tantárgyi progress.
    
*   GET /profile: Profil adatok és statisztikák.
    
*   PUT /profile/update: Név módosítása (Rate limited).
    
*   GET /leaderboard: Toplista lekérése.
    
*   GET /search: Globális kereső (Tantárgyak és Témák).
    

#### Tananyag és Gamifikáció

*   GET /tantargyak: Tantárgyak listája.
    
*   GET /tantargyak/{slug}: Tantárgy részletei unitokkal és progressel.
    
*   GET /topics/{subject}/{topic}: Lecke tartalom, kérdések, flashcardok.
    
*   POST /gamification/complete-topic: Teszt/Lecke beküldése, XP jóváírás.
    
*   POST /ask-axel: AI mentor kérdezése az adott lecke kontextusában.
    

#### Adminisztráció (admin middleware)

*   **Statisztika:** GET /admin/stats, GET /admin/users.
    
*   **Tartalom:** CRUD műveletek subjects, units, topics, questions, flashcards entitásokra.
    
*   **Rendszer:** POST /admin/system-message (Globális üzenet küldése).
    
*   **Sorrend:** POST /admin/topics/reorder.
    

5. Frontend Architektúra (Vue.js)
----------------------------------

### 5.1. Könyvtárszerkezet

*   src/pages/: Az oldal nézetei (Main, Login, Admin/*).
    
*   src/layouts/: Keretrendszer komponensek (BaseLayout, BaseHeader).
    
*   src/components/: Újrafelhasználható elemek (GlobalNotification).
    

### 5.2. Layout Rendszer

A rendszer központi eleme a **BaseLayout.vue**, amely kezeli a megjelenítést a bejelentkezett és nem bejelentkezett felhasználók számára.

#### BaseLayout.vue

*   **Props:** mode ('app' vagy 'landing').
    
*   **Logika:**
    
    *   Ha mode === 'landing', elrejti a BaseHeader-t (menüt).
        
    *   **Global Notification:** Itt fut a popup logika. Betöltéskor ellenőrzi a tokent. Ha van token, lekéri az üzenetet a backendről.
        
    *   **LocalStorage:** Ellenőrzi, hogy a felhasználó bezárta-e már az adott üzenetet (hide_msg_{msgId}_u{userId}).
        

#### BaseHeader.vue

*   Tartalmazza a navigációt, a keresőt, a streak számlálót és a profil legördülőt.
    
*   Csak mode !== 'landing' esetén jelenik meg.
    

#### GlobalNotification.vue

*   Önálló komponens, amely a BaseLayout-ba van importálva.
    
*   Kezeli a rendszerüzenetek megjelenítését és a bezárás eseményt.
    

6. Üzleti Logika és Algoritmusok
---------------------------------

### 6.1. Gamifikáció (XP Rendszer)

A GamificationController felel a pontok kiosztásáért.

*   **Szabály:** Minden kérdésnek van egy XP értéke (alapértelmezett: 10).
    
*   **Duplikáció szűrés:** A rendszer a question_user táblában rögzíti a helyes válaszokat. Ha egy felhasználó újra kitölt egy tesztet, a már korábban helyesen megválaszolt kérdésekért **nem kap újra pontot**.
    
*   **Vizsga:** Ha a lecke típusa test, a rendszer ellenőrzi a passing_percentage-et. Ha a felhasználó nem éri el a százalékot, a teszt sikertelennek minősül, és 0 XP jár.
    

### 6.2. AI Mentor (Axel)

*   **Kontextus:** Az AI megkapja a felhasználó által éppen olvasott lecke tartalmát (notes).
    
*   **Guardrails:** A System Prompt szigorúan korlátozza az AI-t:
    
    1.  Csak az adott tananyagból válaszolhat.
        
    2.  Nem oldhatja meg a házi feladatot, csak rávezethet.
        
    3.  Fiatalos, emojikkal tűzdelt stílusban kell kommunikálnia.
        

### 6.3. Globális Értesítési Rendszer

*   **Backend:** Az admin új üzenetet küld (is_active = true). A régebbi aktív üzenetek automatikusan inaktívvá válnak.
    
*   **Frontend:** A felhasználó minden oldalbetöltéskor (vagy Layout mountoláskor) lekéri az aktív üzenetet.
    
*   **Adatok:** A bezárás ténye a böngésző localStorage-ában tárolódik, összekötve a felhasználó ID-jával és az üzenet ID-jával, így egy gépen több felhasználó is láthatja az üzenetet saját fiókjában.
    

7. Telepítés és Üzembe Helyezés
--------------------------------

### 7.1. Backend (Laravel)

Bash

# 1. Klónozás
git clone https://gitlab.neumann-bp.edu.hu/72590478549/13_szft3_bt_on_mr_vizsgaremek

# 2. Konfiguráció
cp .env.example .env
# Állítsd be: DB_*, GROQ_API_KEY, GOOGLE_*, FRONTEND_URL

# 3. Inicializálás
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan install:api
php artisan db:seed

# 4. Futtatás
./start.sh

8. Biztonság
-------------

*   **Autentikáció:** Laravel Sanctum token alapú védelem minden védett végponton.
    
*   **Jogosultság:** AdminMiddleware védi az adminisztrációs felületeket.
    
*   **Rate Limiting:** A ProfileController korlátozza a névváltás gyakoriságát (1 perc / kérés), az AuthController védi a login végpontot brute-force ellen.
    
*   **Adatvédelem:** A jelszavak Bcrypt hasheléssel tárolódnak. Az AI felé küldött adatokból a rendszer kiszűri a HTML tegeket és limitálja a hosszt.