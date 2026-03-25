# 🎓 MaturaSmart - Érettségi Felkészítő Platform

![Project Status](https://img.shields.io/badge/status-development-orange)
![Laravel](https://img.shields.io/badge/Laravel-11-red)
![Vue.js](https://img.shields.io/badge/Vue.js-3-green)
![Docker](https://img.shields.io/badge/Docker-Enabled-blue)

A **MaturaSmart** egy modern, interaktív oktatási platform, amely gamifikációs elemekkel és mesterséges intelligenciával támogatja a középiskolások érettségi felkészülését.

## 🚀 Funkciók

* **📚 Tananyagok & Leckék:** Strukturált tantárgyak (Matek, Töri, stb.), fejezetek és leckék.
* **🤖 Axel AI Mentor:** Beépített AI asszisztens (Llama-3 alapú), amely segít a tananyag megértésében, de nem oldja meg a leckét a diák helyett.
* **🎮 Gamification:**
    * XP gyűjtés, Szintek (Leveling system).
    * Ranglista (Leaderboard).
    * Streak (Sorozat) rendszer.
* **🧠 Tanulókártyák (Flashcards):** Ismétlő kártyák a hatékony memorizáláshoz.
* **📹 Videó Integráció:** YouTube oktatóvideók beágyazása (GDPR-barát módon).
* **📊 Progress Tracking:** Haladás nyomon követése százalékos vizualizációval.
* **🔒 Biztonság:** Role-based access control (Admin/Student), biztonságos jelszókezelés és API tokenek.

## 🛠️ Technológiák

A projekt modern technológiai stackre épül:

* **Backend:** Laravel 11 (PHP 8.2+)
* **Frontend:** Vue.js 3 (Composition API) + Tailwind CSS
* **Adatbázis:** MySQL (Production) / SQLite (Testing)
* **Környezet:** Docker & Docker Compose
* **AI API:** Groq (Llama-3 modellek)

## ⚙️ Telepítés és Futtatás (Docker)

A fejlesztői környezet elindításához kövesd az alábbi lépéseket:

### 1. Repository klónozása, futtatása
```bash
git clone [https://gitlab.neumann-bp.edu.hu/72590478549/13_szft3_bt_on_mr_vizsgaremek.git](https://gitlab.neumann-bp.edu.hu/72590478549/13_szft3_bt_on_mr_vizsgaremek.git)
cd maturasmart
#(.env bemásolása)
chmod +x start.sh
./start.sh