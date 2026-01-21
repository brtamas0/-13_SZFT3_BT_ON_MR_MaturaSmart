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

// THEME HANDLING – only for landing
const theme = ref("light")

onMounted(() => {
  if (props.mode === "landing") {
    const saved = localStorage.getItem("theme") || "light"
    theme.value = saved
    document.documentElement.setAttribute("data-theme", saved)
  } else {
    // APP MODE ALWAYS DARK
    document.documentElement.setAttribute("data-theme", "dark")
  }
})

const toggleTheme = () => {
  if (props.mode !== "landing") return
  const next = theme.value === "dark" ? "light" : "dark"
  theme.value = next
  document.documentElement.setAttribute("data-theme", next)
  localStorage.setItem("theme", next)
}

// Scroll (landing)
const scrollToId = (id) => {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: "smooth" })
}

// Dummy user
const user = { full_name: "User" }
const handleLogout = () => router.push("/login")
</script>

<template>
  <header
    class="fixed top-0 left-0 w-full z-50 backdrop-blur-xl border-b transition-all duration-300"
    :class="props.mode === 'app' ? 'bg-[#0b102e]/80 border-white/10' : ''"
    :style="props.mode === 'landing'
      ? { background: 'var(--nav-bg)', borderColor: 'var(--glass-border)' }
      : {}"
  >
    <div class="max-w-7xl mx-auto px-6 md:px-10 h-20 flex items-center justify-between">

      <!-- LOGO -->
      <RouterLink
        to="/"
        class="text-2xl font-bold tracking-wide"
        :style="{ color: props.mode === 'landing' ? 'var(--text-primary)' : 'white' }"
      >
        Matura<span class="accent-text">Smart</span>
      </RouterLink>

      <!-- DESKTOP NAV -->
      <nav class="hidden md:flex items-center gap-8 text-sm font-medium">

        <!-- LANDING NAV -->
        <template v-if="props.mode === 'landing'">
          <a @click.prevent="scrollToId('home')" class="nav-item" style="color: var(--accent); font-weight: 600;">Kezdőlap</a>
          <a @click.prevent="scrollToId('features')" class="nav-item">Funkciók</a>
          <a @click.prevent="scrollToId('mission')" class="nav-item">Célunk</a>
          <a @click.prevent="scrollToId('faq')" class="nav-item">GYIK</a>
        </template>

        <!-- APP NAV -->
        <template v-else>
          <RouterLink
            to="/main"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/main' }"
            style="color: white"
          >
            Vezérlőpult
          </RouterLink>

          <RouterLink
            to="/tantargyak"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path.startsWith('/tantargyak') }"
            style="color: white"
          >
            Tantárgyak
          </RouterLink>

          <RouterLink
            to="/calendar"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/calendar' }"
            style="color: white"
          >
            Naptár 📅
          </RouterLink>

          <RouterLink
            to="/results"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/results' }"
            style="color: white"
          >
            Eredmények
          </RouterLink>

          <RouterLink
            to="/leaderboard"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/leaderboard' }"
            style="color: white"
          >
            Ranglista 🏆
          </RouterLink>

          <RouterLink
            to="/profile"
            class="hover:text-blue-400 transition"
            :class="{ 'text-blue-400 font-semibold': route.path === '/profile' }"
            style="color: white"
          >
            Profil
          </RouterLink>
        </template>
      </nav>

      <!-- RIGHT SIDE -->
      <div class="flex items-center gap-4">

        <!-- THEME TOGGLE (landing only) -->
        <button
          v-if="props.mode === 'landing'"
          @click="toggleTheme"
          class="text-xl hover:opacity-80 transition theme-btn"
        >
          🌓
        </button>

        <!-- LANDING LOGIN -->
        <template v-if="props.mode === 'landing'">
          <RouterLink
            to="/login"
            class="px-4 py-2 rounded-xl font-semibold shadow-md transition text-sm"
            style="background: var(--accent); color: black;"
          >
            Belépés
          </RouterLink>
        </template>

        <!-- APP RIGHT SIDE -->
        <template v-else>
          <!-- STREAK -->
          <div class="px-4 py-2 rounded-2xl shadow-lg text-sm flex items-center gap-2"
               style="background: rgba(0,0,0,0.4); color: white;">
            🔥 <span>23</span>
          </div>

          <!-- PROFILE -->
          <RouterLink
            to="/profile"
            class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md hover:scale-105 transition-transform"
            style="background: linear-gradient(to bottom right, #2563eb, #4f46e5); border: 2px solid #0b1029;"
          >
            {{ user.full_name?.charAt(0) || 'U' }}
          </RouterLink>

          <!-- LOGOUT -->
          <button @click="handleLogout" class="hidden md:block text-gray-400 hover:text-red-400 transition">
            ⎋
          </button>
        </template>

        <!-- MOBILE MENU BUTTON -->
        <button class="md:hidden text-3xl" @click="mobileOpen = !mobileOpen" :style="{ color: props.mode === 'landing' ? 'var(--text-primary)' : 'white' }">
          ☰
        </button>
      </div>
    </div>

    <!-- MOBILE MENU -->
    <transition name="fade">
      <div
        v-if="mobileOpen"
        class="md:hidden px-6 py-6 space-y-4"
        :class="props.mode === 'app' ? 'bg-[#0b102e]/95 border-white/10' : ''"
        :style="props.mode === 'landing'
          ? { background: 'var(--nav-bg)', borderTop: '1px solid var(--glass-border)' }
          : {}"
      >
        <!-- LANDING -->
        <template v-if="props.mode === 'landing'">
          <a @click="scrollToId('home'); mobileOpen=false" class="block nav-item">Kezdőlap</a>
          <a @click="scrollToId('features'); mobileOpen=false" class="block nav-item">Funkciók</a>
          <a @click="scrollToId('mission'); mobileOpen=false" class="block nav-item">Célunk</a>
          <a @click="scrollToId('faq'); mobileOpen=false" class="block nav-item">GYIK</a>
        </template>

        <!-- APP -->
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
