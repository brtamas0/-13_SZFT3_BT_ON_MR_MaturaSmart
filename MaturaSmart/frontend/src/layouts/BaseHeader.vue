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
const user = ref({ full_name: "", current_streak: 0 })

// --- KERESŐ VÁLTOZÓK ---
const searchQuery = ref("")
const searchResults = ref([])
const showResults = ref(false)
const isSearching = ref(false)
let searchTimeout = null

onMounted(async () => {
  // Téma betöltése
  const savedTheme = localStorage.getItem("theme") || "dark"
  theme.value = savedTheme
  document.documentElement.setAttribute("data-theme", savedTheme)

  // User betöltése localStorage-ból
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (e) {
      console.error("Hiba a felhasználói adatok betöltésekor:", e)
    }
  }

  // Friss adatok lekérése a backendről újra, mivel a streak számítása a backend middleware-ben történik
  const token = localStorage.getItem('token')
  if (token) {
    try {
        const response = await fetch('http://maturasmart.hu/api/user', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        
        if (response.ok) {
            const freshUser = await response.json()
            user.value = freshUser
            localStorage.setItem('user', JSON.stringify(freshUser))
        }
    } catch (e) {
        console.error("Nem sikerült frissíteni a user adatokat:", e)
    }
  }

  // Kattintás figyelése a kereső bezárásához
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-container')) {
      showResults.value = false
    }
  })
})

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

// --- KERESŐ ---

const handleSearchInput = () => {
  if (searchTimeout) clearTimeout(searchTimeout)

  if (searchQuery.value.length < 2) {
    searchResults.value = []
    showResults.value = false
    return
  }

  isSearching.value = true
  
  searchTimeout = setTimeout(async () => {
    try {
      const token = localStorage.getItem('token')
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

const selectResult = (url) => {
  router.push(url)
  searchQuery.value = ""
  showResults.value = false
  mobileOpen.value = false
}

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
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">

      <div class="flex items-center gap-6 lg:gap-8 shrink-0">
          
          <RouterLink
            to="/main"
            class="text-2xl font-bold tracking-wide flex items-center gap-2"
            :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
          >
            Matura<span class="accent-text">Smart</span>
          </RouterLink>

          <div v-if="props.mode !== 'landing'" class="hidden lg:block h-6 w-px bg-white/10"></div>

          <nav v-if="props.mode !== 'landing'" class="hidden lg:flex items-center gap-6">
            <RouterLink to="/main" class="nav-link flex items-center gap-2 text-sm font-medium opacity-70 hover:opacity-100" :class="{ 'active opacity-100': route.path === '/main' }">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Főoldal
            </RouterLink>
            <RouterLink to="/leaderboard" class="nav-link flex items-center gap-2 text-sm font-medium opacity-70 hover:opacity-100" :class="{ 'active opacity-100': route.path === '/leaderboard' }">
                <span>🏆</span> Ranglista
            </RouterLink>
          </nav>
           
           <nav v-else class="hidden lg:flex items-center gap-6 text-sm font-medium">
             <a @click.prevent="scrollToId('home')" class="nav-item cursor-pointer">Kezdőlap</a>
             <a @click.prevent="scrollToId('features')" class="nav-item cursor-pointer">Funkciók</a>
           </nav>
      </div>

      <div v-if="props.mode !== 'landing'" class="hidden md:block flex-1 max-w-xl relative search-container px-4">
          <div class="relative group">
             <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 group-focus-within:text-blue-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
             </span>
             <input 
                v-model="searchQuery"
                @input="handleSearchInput"
                @focus="showResults = searchQuery.length >= 2"
                type="text" 
                class="w-full pl-11 pr-4 py-2.5 rounded-xl text-sm transition-all border outline-none"
                :class="theme === 'dark' 
                   ? 'bg-[#1a1f40] border-white/10 text-white placeholder-gray-500 focus:bg-[#1e293b] focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10' 
                   : 'bg-gray-100 border-gray-200 text-gray-800 placeholder-gray-500 focus:bg-white focus:border-blue-500'"
                placeholder="Keress tantárgyat, témakört..."
             >
             <div v-if="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
             </div>
          </div>

          <div v-if="showResults && searchResults.length > 0" 
               class="absolute mt-2 w-[calc(100%-2rem)] left-4 rounded-xl shadow-2xl overflow-hidden z-50 border"
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
      </div>
      <div v-else class="flex-1"></div>


      <div class="flex items-center gap-3 lg:gap-4 shrink-0">

        <button
          @click="toggleTheme"
          class="w-9 h-9 rounded-full flex items-center justify-center transition hover:bg-white/10"
          :style="{ color: theme === 'dark' ? '#94a3b8' : '#64748b' }"
          title="Téma váltása"
        >
          <span v-if="theme==='dark'">🌙</span>
          <span v-else>☀️</span>
        </button>

        <template v-if="props.mode === 'landing'">
          <RouterLink
            to="/login"
            class="px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition hover:scale-105 text-sm"
            style="background: var(--accent); color: black;"
          >
            Belépés
          </RouterLink>
        </template>

        <template v-else>
          <div
            class="hidden sm:flex px-3 py-1.5 rounded-lg text-xs font-bold items-center gap-1.5 border"
            :class="theme === 'dark' ? 'bg-orange-500/10 border-orange-500/20 text-orange-400' : 'bg-orange-50 border-orange-200 text-orange-600'"
          >
            🔥 <span>{{ user.current_streak || 0 }}</span>
          </div>

          <button 
          v-if="user.role === 'admin'" @click="$router.push('/admin')" 
          class="hidden lg:flex items-center justify-center px-3 py-1.5 rounded-lg transition text-xs font-bold border" 
          :class="theme === 'dark'
              ? 'bg-blue-600/10 border-blue-600/20 text-blue-400 hover:bg-blue-600/20'
              : 'bg-blue-50 border-blue-200 text-blue-600 hover:bg-blue-100'"
          title="Admin felület"
          >
            Admin
          </button>

          <div class="flex items-center gap-2 pl-2 border-l border-white/10">
              
              <RouterLink
                to="/profile"
                class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md hover:ring-2 hover:ring-blue-500 transition-all overflow-hidden"
                :style="user.avatar_url 
                  ? 'border: 2px solid #1e293b;' 
                  : 'background: linear-gradient(to bottom right, #3b82f6, #6366f1); border: 2px solid #1e293b;'"
              >
                <img 
                  v-if="user.avatar_url" 
                  :src="user.avatar_url" 
                  alt="Profil" 
                  class="w-full h-full object-cover"
                />
                <span v-else>{{ user.full_name ? user.full_name.charAt(0).toUpperCase() : 'U' }}</span>
              </RouterLink>

              <button
                @click="handleLogout"
                class="hidden lg:flex w-9 h-9 items-center justify-center rounded-full transition text-slate-400 hover:text-red-400 hover:bg-red-500/10"
                title="Kijelentkezés"
              >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
              </button>
          </div>

        </template>

        <button
          class="lg:hidden text-2xl p-2"
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
          <RouterLink to="/main" class="block nav-item flex items-center gap-3" @click="mobileOpen = false">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Főoldal
          </RouterLink>

          <RouterLink to="/leaderboard" class="block nav-item flex items-center gap-3" @click="mobileOpen = false">
            <span>🏆</span> Ranglista
          </RouterLink>

          <RouterLink to="/profile" class="block nav-item flex items-center gap-3" @click="mobileOpen = false">
             <span>👤</span> Profil
          </RouterLink>
          
          <div class="pt-4 border-t border-gray-500/20 flex justify-center">
            <button
              @click="handleLogout"
              class="p-3 rounded-full transition-colors group"
              :class="theme === 'dark' ? 'bg-red-500/10 hover:bg-red-500/20' : 'bg-red-100 hover:bg-red-200'"
              title="Kijelentkezés"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                   class="w-6 h-6 text-red-500 group-hover:scale-110 transition-transform">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
              </svg>
            </button>
          </div>

        </template>
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
  position: relative;
}
.nav-link:hover {
  color: #60a5fa;
}
.nav-link.active {
  color: #60a5fa;
  font-weight: 600;
}

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