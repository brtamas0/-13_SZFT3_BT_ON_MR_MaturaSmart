<script setup>
import { ref, onMounted } from "vue"
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
</script>

<template>
  <header
    class="fixed top-0 left-0 w-full z-50 backdrop-blur-xl border-b transition-all duration-300"
    :class="theme === 'dark'
      ? 'bg-[#0b102e]/80 border-white/10'
      : 'bg-white/80 border-black/10'"
  >
    <div class="max-w-7xl mx-auto px-6 md:px-10 h-20 flex items-center justify-between">

      <RouterLink
        to="/"
        class="text-2xl font-bold tracking-wide"
        :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
      >
        Matura<span class="accent-text">Smart</span>
      </RouterLink>

      <nav class="hidden md:flex items-center gap-8 text-sm font-medium">

        <template v-if="props.mode === 'landing'">
          <a @click.prevent="scrollToId('home')" class="nav-item" style="color: var(--accent); font-weight: 600;">Kezdőlap</a>
          <a @click.prevent="scrollToId('features')" class="nav-item">Funkciók</a>
          <a @click.prevent="scrollToId('mission')" class="nav-item">Célunk</a>
          <a @click.prevent="scrollToId('faq')" class="nav-item">GYIK</a>
        </template>

        <template v-else>
          <RouterLink
            to="/main"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/main' }"
            :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
          >
            Vezérlőpult
          </RouterLink>

          <RouterLink
            to="/tantargyak"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path.startsWith('/tantargyak') }"
            :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
          >
            Tantárgyak
          </RouterLink>

          <RouterLink
            to="/calendar"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/calendar' }"
            :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
          >
            Naptár 📅
          </RouterLink>

          <RouterLink
            to="/results"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/results' }"
            :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
          >
            Eredmények
          </RouterLink>

          <RouterLink
            to="/leaderboard"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/leaderboard' }"
            :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
          >
            Ranglista 🏆
          </RouterLink>

          <RouterLink
            to="/profile"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/profile' }"
            :style="{ color: theme === 'dark' ? 'white' : '#1a1a1a' }"
          >
            Profil
          </RouterLink>
        </template>
      </nav>

      <div class="flex items-center gap-4">

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
            class="px-4 py-2 rounded-2xl shadow-lg text-sm flex items-center gap-2"
            :style="theme === 'dark'
              ? 'background: rgba(0,0,0,0.4); color: white;'
              : 'background: rgba(0,0,0,0.05); color: #333;'"
          >
            🔥 <span>23</span>
          </div>

          <RouterLink
            to="/profile"
            class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md hover:scale-105 transition-transform"
            style="background: linear-gradient(to bottom right, #2563eb, #4f46e5); border: 2px solid #0b1029;"
          >
            {{ 
               user.full_name 
                 ? user.full_name
                     .split(' ')
                     .map(n => n[0])
                     .join('')
                     .toUpperCase()
                     .substring(0, 2)
                 : 'U' 
            }}
          </RouterLink>

        <button
          @click="handleLogout"
          class="hidden md:flex items-center justify-center px-4 py-2 rounded-xl transition shadow-md font-medium cursor-pointer"
          :class="theme === 'dark'
            ? 'bg-red-600/20 text-red-400 hover:bg-red-600/30 hover:text-red-300'
            : 'bg-red-100 text-red-600 hover:bg-red-200'"
          title="Kijelentkezés"
        >
          Kijelentkezés
        </button>

        </template>

        <button
          class="md:hidden text-3xl"
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
        class="md:hidden px-6 py-6 space-y-4"
        :class="theme === 'dark'
          ? 'bg-[#0b102e]/95 border-white/10'
          : 'bg-white/95 border-black/10'"
      >
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
      </div>
    </transition>
  </header>
</template>

<style scoped>
.nav-item {
  cursor: pointer;
  transition: 0.2s;
  color: var(--text-secondary);
}
.nav-item:hover {
  color: var(--text-primary);
}
</style>