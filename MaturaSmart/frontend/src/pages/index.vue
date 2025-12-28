<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '@/layouts/BaseLayout.vue'

const router = useRouter()

// Téma váltás logika (Vue verzió)
const toggleTheme = () => {
  const html = document.documentElement
  const currentTheme = html.getAttribute('data-theme')
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark'
  
  html.setAttribute('data-theme', newTheme)
  
  // Tailwind v4 class alapú dark mód kezelése
  if (newTheme === 'dark') {
    html.classList.add('dark')
  } else {
    html.classList.remove('dark')
  }
  
  localStorage.setItem('theme', newTheme)
}

onMounted(() => {
  const saved = localStorage.getItem('theme') || 'dark'
  const html = document.documentElement
  html.setAttribute('data-theme', saved)
  if (saved === 'dark') html.classList.add('dark')
})

const goToLogin = () => {
  router.push('/login')
}
</script>

<template>
  <BaseLayout>
    
    <button
      @click="toggleTheme"
      class="fixed top-5 right-5 w-10 h-10 rounded-full border border-gray-300 dark:border-white/20
             bg-white/70 dark:bg-white/5 flex items-center justify-center
             hover:bg-gray-200 dark:hover:bg-white/10 transition z-50 shadow-sm cursor-pointer">
      🌓
    </button>

    <nav class="w-full py-4 flex justify-between items-center px-6
                bg-white/80 dark:bg-[#0b1220]/80 backdrop-blur-md
                border-b border-black/5 dark:border-white/5 rounded-2xl mt-4 mb-10">

      <div class="text-xl font-bold tracking-tight">
        Matura<span class="text-indigo-500">Smart</span>
      </div>

      <div class="space-x-6 hidden md:flex text-sm font-medium text-slate-600 dark:text-slate-300">
        <a href="#" class="hover:text-indigo-500 transition-colors">Kezdőlap</a>
        <a href="#features" class="hover:text-indigo-500 transition-colors">Funkciók</a>
        <a href="#faq" class="hover:text-indigo-500 transition-colors">GYIK</a>
      </div>

      <button
        @click="goToLogin"
        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-semibold transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40">
        Belépés
      </button>
    </nav>

    <section class="max-w-6xl mx-auto px-4">
      <div class="bg-gradient-to-br
                  from-indigo-50 via-white to-indigo-50
                  dark:from-[#1e1b4b] dark:via-[#312e81]/50 dark:to-[#1e1b4b]
                  rounded-3xl p-8 md:p-16 border border-indigo-100 dark:border-white/10
                  shadow-2xl dark:shadow-indigo-900/20 relative overflow-hidden">
        
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <h1 class="text-5xl md:text-7xl font-extrabold leading-tight relative z-10">
          A sikeres érettségi<br>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
            itt kezdődik.
          </span>
        </h1>

        <p class="mt-6 text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-xl relative z-10">
          Felejtsd el a magolást. Tanulj <span class="font-bold text-indigo-500">Axel</span> társaságában, játékos leckékkel és személyre szabott AI tervekkel.
        </p>

        <div class="mt-10 flex flex-col sm:flex-row gap-4 relative z-10">
          <button @click="goToLogin" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-bold text-white shadow-xl shadow-indigo-500/30 transition-transform hover:-translate-y-1">
            🚀 Kezdjük el ingyen!
          </button>
          <button class="px-8 py-4 border border-slate-200 dark:border-white/10 rounded-xl font-semibold hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
            Funkciók felfedezése
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
        <div class="glass-panel p-8 rounded-2xl text-center">
          <p class="text-4xl md:text-5xl font-black text-indigo-600 dark:text-indigo-400">1.2k+</p>
          <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">Sikeres érettségi</p>
        </div>
        <div class="glass-panel p-8 rounded-2xl text-center">
          <p class="text-4xl md:text-5xl font-black text-purple-600 dark:text-purple-400">8.5k+</p>
          <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">Megoldott feladat</p>
        </div>
        <div class="glass-panel p-8 rounded-2xl text-center">
          <p class="text-4xl md:text-5xl font-black text-emerald-500">4.8</p>
          <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">Átlagos osztályzat</p>
        </div>
      </div>
    </section>

    <section id="features" class="max-w-6xl mx-auto mt-24 px-4">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Miért a MaturaSmart?</h2>
        <p class="text-slate-500 dark:text-slate-400 text-lg">Minden eszköz, amire szükséged van a jeles érettségihez.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-[#1e293b]/50 backdrop-blur border border-slate-200 dark:border-slate-700 p-6 rounded-2xl hover:border-indigo-500/50 transition-colors group">
          <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">👾</div>
          <h3 class="text-xl font-bold mb-2">Virtuális Tanulótárs</h3>
          <p class="text-slate-600 dark:text-slate-400">Axel végigkísér a felkészülésen, motivál és segít, ha elakadsz.</p>
        </div>

        <div class="bg-white dark:bg-[#1e293b]/50 backdrop-blur border border-slate-200 dark:border-slate-700 p-6 rounded-2xl hover:border-indigo-500/50 transition-colors group">
          <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">📅</div>
          <h3 class="text-xl font-bold mb-2">Okos Tervező</h3>
          <p class="text-slate-600 dark:text-slate-400">Automatikus tanulási ütemezés a vizsga dátuma alapján.</p>
        </div>

        <div class="bg-white dark:bg-[#1e293b]/50 backdrop-blur border border-slate-200 dark:border-slate-700 p-6 rounded-2xl hover:border-indigo-500/50 transition-colors group">
          <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">🎮</div>
          <h3 class="text-xl font-bold mb-2">Gamifikáció</h3>
          <p class="text-slate-600 dark:text-slate-400">Gyűjts XP-t, tartsd meg a streak-et és kerülj fel a ranglistára!</p>
        </div>
        
        <div class="bg-white dark:bg-[#1e293b]/50 backdrop-blur border border-slate-200 dark:border-slate-700 p-6 rounded-2xl hover:border-indigo-500/50 transition-colors group">
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">🃏</div>
            <h3 class="text-xl font-bold mb-2">Szókártyák</h3>
            <p class="text-slate-600 dark:text-slate-400">Tanulj fogalmakat és évszámokat interaktív kártyákkal.</p>
        </div>

        <div class="bg-white dark:bg-[#1e293b]/50 backdrop-blur border border-slate-200 dark:border-slate-700 p-6 rounded-2xl hover:border-indigo-500/50 transition-colors group">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">📱</div>
            <h3 class="text-xl font-bold mb-2">Mobilbarát</h3>
            <p class="text-slate-600 dark:text-slate-400">A felület reszponzív, így a buszon ülve is gyakorolhatsz.</p>
        </div>

        <div class="bg-white dark:bg-[#1e293b]/50 backdrop-blur border border-slate-200 dark:border-slate-700 p-6 rounded-2xl hover:border-indigo-500/50 transition-colors group">
            <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">♿</div>
            <h3 class="text-xl font-bold mb-2">Akadálymentes</h3>
            <p class="text-slate-600 dark:text-slate-400">Kontrasztos módok és képernyőolvasó támogatás.</p>
        </div>
      </div>
    </section>

    <section id="faq" class="max-w-3xl mx-auto mt-24 px-4 text-center">
      <h2 class="text-3xl font-bold mb-8">Gyakori Kérdések</h2>

      <div class="space-y-4 text-left">
        <details class="group bg-white dark:bg-white/5 rounded-xl px-6 py-4 cursor-pointer border border-slate-200 dark:border-white/5 hover:border-indigo-500/30 transition-all open:ring-2 open:ring-indigo-500/20">
          <summary class="flex justify-between items-center font-semibold list-none select-none">
            Ingyenes a használata?
            <span class="transition-transform group-open:rotate-90 text-slate-400">▶</span>
          </summary>
          <p class="text-slate-600 dark:text-slate-300 mt-4 text-sm leading-relaxed">
            Igen! A MaturaSmart alapverziója minden diák számára ingyenesen elérhető, ami tartalmazza a tananyagok nagy részét.
          </p>
        </details>

        <details class="group bg-white dark:bg-white/5 rounded-xl px-6 py-4 cursor-pointer border border-slate-200 dark:border-white/5 hover:border-indigo-500/30 transition-all open:ring-2 open:ring-indigo-500/20">
          <summary class="flex justify-between items-center font-semibold list-none select-none">
            Milyen tantárgyak érhetők el?
            <span class="transition-transform group-open:rotate-90 text-slate-400">▶</span>
          </summary>
          <p class="text-slate-600 dark:text-slate-300 mt-4 text-sm leading-relaxed">
            Jelenleg a fő érettségi tárgyak: Matematika, Történelem, Magyar nyelv és irodalom, Angol és Informatika. A lista folyamatosan bővül a felhasználói visszajelzések alapján.
          </p>
        </details>

        <details class="group bg-white dark:bg-white/5 rounded-xl px-6 py-4 cursor-pointer border border-slate-200 dark:border-white/5 hover:border-indigo-500/30 transition-all open:ring-2 open:ring-indigo-500/20">
            <summary class="flex justify-between items-center font-semibold list-none select-none">
              Ki az az Axel?
              <span class="transition-transform group-open:rotate-90 text-slate-400">▶</span>
            </summary>
            <p class="text-slate-600 dark:text-slate-300 mt-4 text-sm leading-relaxed">
              Axel a rendszer mesterséges intelligenciája és kabalája. Ő elemzi a teljesítményedet, és javaslatokat tesz, hogy mit gyakorolj a legjobb eredmény eléréséhez.
            </p>
          </details>
      </div>
    </section>

    <section class="mt-24 px-4 text-center mb-20">
      <div class="max-w-4xl mx-auto p-12 rounded-3xl bg-indigo-600 text-white shadow-2xl relative overflow-hidden">
        
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cube-coat.png')]"></div>
        
        <h2 class="text-3xl md:text-4xl font-bold mb-6 relative z-10">Készen állsz az ötösre?</h2>
        <p class="mb-8 opacity-90 max-w-lg mx-auto relative z-10">Csatlakozz több ezer diákhoz, és tedd élménnyé a felkészülést még ma.</p>
        
        <button @click="goToLogin" class="px-10 py-4 bg-white text-indigo-600 hover:bg-indigo-50 rounded-xl text-lg font-bold shadow-lg transition-transform hover:-translate-y-1 relative z-10">
          Csatlakozom most
        </button>
      </div>
    </section>

  </BaseLayout>
</template>