<script setup>
import { ref, onMounted, watch } from "vue"
import { useRouter, useRoute } from "vue-router"

const props = defineProps({
  mode: {
    type: String,
    default: "landing",
  },
})

const router = useRouter()
const route = useRoute()
const mobileOpen = ref(false)

const theme = ref("dark")
const user = ref({ full_name: "" })

// --- KERESŐ VÁLTOZÓK ---
const searchQuery = ref("")
const searchResults = ref([])
const showResults = ref(false)
const isSearching = ref(false)
let searchTimeout = null

// --- ÉLETCIKLUS ---
onMounted(() => {
  const savedTheme = localStorage.getItem("theme") || "dark"
  theme.value = savedTheme
  document.documentElement.setAttribute("data-theme", savedTheme)

  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (e) {
      console.error("Hiba a felhasználói adatok betöltésekor:", e)
    }
  }

  // Kattintás figyelése a kereső bezárásához
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-container')) {
      showResults.value = false
    }
  })
})

// --- METÓDUSOK ---

const toggleTheme = () => {
  const next = theme.value === "dark" ? "light" : "dark"
  theme.value = next
  document.documentElement.setAttribute("data-theme", next)
  localStorage.setItem("theme", next)
}

const scrollToId = (id) => {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: "smooth" })
}

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push("/login")
}

// --- KERESŐ LOGIKA (Clean Code) ---

// 1. Debounce (Késleltetés): Csak akkor indít keresést, ha abbahagytad a gépelést
const handleSearchInput = () => {
  if (searchTimeout) clearTimeout(searchTimeout)

  if (searchQuery.value.length < 2) {
    searchResults.value = []
    showResults.value = false
    return
  }

  isSearching.value = true
  
  // 300ms várakozás
  searchTimeout = setTimeout(async () => {
    try {
      const token = localStorage.getItem('token')
      // API Hívás
      const response = await fetch(`http://backend.vm1.test/api/search?q=${searchQuery.value}`, {
         headers: { 'Authorization': `Bearer ${token}` }
      })
      const data = await response.json()
      searchResults.value = data.results || []
      showResults.value = true
    } catch (error) {
      console.error("Keresési hiba:", error)
    } finally {
      isSearching.value = false
    }
  }, 300)
}

// 2. Találat kiválasztása
const selectResult = (url) => {
  router.push(url)
  searchQuery.value = ""
  showResults.value = false
  mobileOpen.value = false
}

// Útvonal váltáskor zárjon be mindent
watch(() => route.path, () => {
  mobileOpen.value = false
  showResults.value = false
})
</script>

<template>
  <header
    class="fixed top-0 left-0 w-full z-50 backdrop-blur-xl border-b transition-all duration-300"
    :class="theme === 'dark'
      ? 'bg-[#0b102e]/80 border-white/10'
      : 'bg-white/80 border-black/10'"
  >
    <div class="max-w-7xl mx-auto px-6 md:px-10 h-20 flex items-center justify-between gap-4">

      <RouterLink
        to="/"
        class="text-2xl font-bold tracking-wide shrink-0"
        :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
      >
        Matura<span class="accent-text">Smart</span>
      </RouterLink>

      <div v-if="props.mode !== 'landing'" class="hidden md:block flex-1 max-w-md relative search-container mx-4">
          <div class="relative">
             <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
             </span>
             <input 
                v-model="searchQuery"
                @input="handleSearchInput"
                @focus="showResults = searchQuery.length >= 2"
                type="text" 
                class="w-full pl-10 pr-4 py-2 rounded-xl text-sm transition-colors border focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="theme === 'dark' 
                   ? 'bg-[#1a1f40] border-white/10 text-white placeholder-gray-400' 
                   : 'bg-gray-100 border-gray-200 text-gray-800 placeholder-gray-500'"
                placeholder="Keresés tantárgyak, leckék között..."
             >
             <div v-if="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
             </div>
          </div>

          <div v-if="showResults && searchResults.length > 0" 
               class="absolute mt-2 w-full rounded-xl shadow-2xl overflow-hidden z-50 border"
               :class="theme === 'dark' ? 'bg-[#1e293b] border-white/10' : 'bg-white border-gray-200'"
          >
             <ul class="max-h-80 overflow-y-auto">
                <li v-for="item in searchResults" :key="item.id + item.type">
                   <button @click="selectResult(item.url)" 
                           class="w-full text-left px-4 py-3 flex items-center gap-3 transition-colors border-b last:border-0"
                           :class="theme === 'dark' ? 'hover:bg-white/5 border-white/5 text-gray-200' : 'hover:bg-gray-50 border-gray-100 text-gray-700'"
                   >
                      <span class="text-xl">{{ item.icon || (item.type === 'subject' ? '📘' : '📄') }}</span>
                      <div>
                         <div class="text-sm font-bold">{{ item.title }}</div>
                         <div class="text-[10px] uppercase font-bold opacity-60">{{ item.type === 'subject' ? 'Tantárgy' : 'Lecke' }}</div>
                      </div>
                   </button>
                </li>
             </ul>
          </div>
          
          <div v-if="showResults && searchResults.length === 0 && !isSearching" 
               class="absolute mt-2 w-full p-4 text-center text-sm rounded-xl shadow-xl border"
               :class="theme === 'dark' ? 'bg-[#1e293b] border-white/10 text-gray-400' : 'bg-white border-gray-200 text-gray-500'"
          >
             Nincs találat erre: "{{ searchQuery }}"
          </div>
      </div>

      <nav class="hidden lg:flex items-center gap-4 lg:gap-8 shrink-0">

        <template v-if="props.mode === 'landing'">
          <a @click.prevent="scrollToId('home')" class="nav-item" style="color: var(--accent); font-weight: 600;">Kezdőlap</a>
          <a @click.prevent="scrollToId('features')" class="nav-item">Funkciók</a>
          <a @click.prevent="scrollToId('mission')" class="nav-item">Célunk</a>
          <a @click.prevent="scrollToId('faq')" class="nav-item">GYIK</a>
        </template>

        <template v-else>
          <RouterLink to="/main" class="nav-link" :class="{ 'active': route.path === '/main' }">Vezérlőpult</RouterLink>
          <RouterLink to="/tantargyak" class="nav-link" :class="{ 'active': route.path.startsWith('/tantargyak') }">Tantárgyak</RouterLink>
          <RouterLink to="/calendar" class="nav-link" :class="{ 'active': route.path === '/calendar' }">Naptár 📅</RouterLink>
          <RouterLink to="/leaderboard" class="nav-link" :class="{ 'active': route.path === '/leaderboard' }">Ranglista 🏆</RouterLink>
        </template>
      </nav>

      <div class="flex items-center gap-4 shrink-0">

        <button
          @click="toggleTheme"
          class="text-xl hover:opacity-80 transition theme-btn"
          :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
        >
          🌓
        </button>

        <template v-if="props.mode === 'landing'">
          <RouterLink
            to="/login"
            class="px-4 py-2 rounded-xl font-semibold shadow-md transition text-sm"
            style="background: var(--accent); color: black;"
          >
            Belépés
          </RouterLink>
        </template>

        <template v-else>
          <div
            class="hidden sm:flex px-4 py-2 rounded-2xl shadow-lg text-sm items-center gap-2"
            :style="theme === 'dark'
              ? 'background: rgba(0,0,0,0.4); color: white;'
              : 'background: rgba(0,0,0,0.05); color: #333;'"
          >
            🔥 <span>{{ 32 }}</span> <!-- később streak változó kell ide-->
          </div>

          <RouterLink
            to="/profile"
            class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md hover:scale-105 transition-transform"
            style="background: linear-gradient(to bottom right, #2563eb, #4f46e5); border: 2px solid #0b1029;"
          >
            {{ user.full_name ? user.full_name.charAt(0).toUpperCase() : 'U' }}
          </RouterLink>

          <button
            @click="handleLogout"
            class="hidden lg:flex items-center justify-center px-4 py-2 rounded-xl transition shadow-md font-medium cursor-pointer text-sm"
            :class="theme === 'dark'
              ? 'bg-red-600/20 text-red-400 hover:bg-red-600/30 hover:text-red-300'
              : 'bg-red-100 text-red-600 hover:bg-red-200'"
            title="Kijelentkezés"
          >
            Kilépés
          </button>
        </template>

        <button
          class="lg:hidden text-3xl"
          @click="mobileOpen = !mobileOpen"
          :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
        >
          ☰
        </button>
      </div>
    </div>

    <transition name="fade">
      <div
        v-if="mobileOpen"
        class="lg:hidden px-6 py-6 space-y-4 shadow-xl max-h-[90vh] overflow-y-auto"
        :class="theme === 'dark'
          ? 'bg-[#0b102e]/95 border-white/10 text-white'
          : 'bg-white/95 border-black/10 text-gray-800'"
      >
        <div v-if="props.mode !== 'landing'" class="relative pb-4 border-b border-gray-500/20">
             <input 
                v-model="searchQuery"
                @input="handleSearchInput"
                type="text" 
                class="w-full px-4 py-3 rounded-xl text-sm transition-colors border focus:outline-none"
                :class="theme === 'dark' 
                   ? 'bg-[#1a1f40] border-white/10 text-white placeholder-gray-400' 
                   : 'bg-gray-100 border-gray-200 text-gray-800 placeholder-gray-500'"
                placeholder="Keresés..."
             >
             <ul v-if="searchResults.length > 0" class="mt-2 rounded-xl border overflow-hidden" 
                 :class="theme === 'dark' ? 'bg-[#1e293b] border-white/10' : 'bg-white border-gray-200'">
                 <li v-for="item in searchResults" :key="item.id">
                     <button @click="selectResult(item.url)" class="w-full text-left px-4 py-3 border-b last:border-0 text-sm font-medium"
                             :class="theme === 'dark' ? 'border-white/5 hover:bg-white/5' : 'border-gray-100 hover:bg-gray-50'">
                         {{ item.title }}
                     </button>
                 </li>
             </ul>
        </div>

        <template v-if="props.mode === 'landing'">
          <a @click="scrollToId('home'); mobileOpen=false" class="block nav-item">Kezdőlap</a>
          <a @click="scrollToId('features'); mobileOpen=false" class="block nav-item">Funkciók</a>
          <a @click="scrollToId('mission'); mobileOpen=false" class="block nav-item">Célunk</a>
          <a @click="scrollToId('faq'); mobileOpen=false" class="block nav-item">GYIK</a>
        </template>

        <template v-else>
          <RouterLink to="/main" class="block nav-item" @click="mobileOpen = false">Vezérlőpult</RouterLink>
          <RouterLink to="/tantargyak" class="block nav-item" @click="mobileOpen = false">Tantárgyak</RouterLink>
          <RouterLink to="/calendar" class="block nav-item" @click="mobileOpen = false">Naptár 📅</RouterLink>
          <RouterLink to="/results" class="block nav-item" @click="mobileOpen = false">Eredmények</RouterLink>
          <RouterLink to="/leaderboard" class="block nav-item" @click="mobileOpen = false">Ranglista 🏆</RouterLink>
          <RouterLink to="/profile" class="block nav-item" @click="mobileOpen = false">Profil</RouterLink>
        </template>
        
        <button v-if="props.mode !== 'landing'"
          @click="handleLogout"
          class="w-full text-left px-4 py-3 rounded-xl font-medium cursor-pointer transition
          bg-red-600/20 text-red-400 hover:bg-red-600/30 hover:text-red-300"
        >
          Kijelentkezés
        </button>

      </div>
    </transition>
  </header>
</template>

<style scoped>
.nav-item {
  cursor: pointer;
  transition: 0.2s;
  color: inherit; 
  padding: 8px 0;
}
.nav-item:hover {
  opacity: 0.8;
  padding-left: 5px;
}
.nav-link {
  transition: all 0.2s;
  color: inherit;
  font-weight: 500;
}
.nav-link:hover {
  color: #60a5fa;
}
.nav-link.active {
  color: #60a5fa;
  font-weight: 600;
}

/* Vue transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>