<script setup>
import { ref } from "vue";

const props = defineProps({
  mode: {
    type: String,
    default: "app", 
  },
});

const router = useRouter();
const mobileOpen = ref(false);
const closeMobile = () => (mobileOpen.value = false);
</script>

<template>
  <header
    class="fixed top-0 left-0 w-full z-50
           backdrop-blur-xl bg-[#0b1029]/70
           border-b border-white/10"
  >
    <div class="max-w-7xl mx-auto px-6 md:px-10 h-20 flex items-center justify-between">

      <RouterLink to="/" class="text-2xl font-bold tracking-wide">
        Matura<span class="text-[#6CA6FF]">Smart</span>
      </RouterLink>

      <nav class="hidden md:flex items-center gap-6 text-sm text-gray-300">
        <template v-if="props.mode === 'landing'">
          <a @click.prevent="scrollToId('home')" href="#home" class="nav-item">Kezdőlap</a>
          <a @click.prevent="scrollToId('features')" href="#features" class="nav-item">Funkciók</a>
          <a @click.prevent="scrollToId('mission')" href="#mission" class="nav-item">Célunk</a>
        </template>

        <template v-else>
          <RouterLink
            to="/main"
            class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow-md hover:bg-blue-500 transition"
          >
            Vezérlőpult
          </RouterLink>
        </template>
      </nav>

      <div class="flex items-center gap-4">
        <template v-if="props.mode === 'landing'">
          <button class="text-xl hover:opacity-80 transition">🌓</button>
          <RouterLink
            to="/login"
            class="bg-[#6CA6FF] text-black font-semibold px-4 py-2 rounded-xl shadow-md hover:bg-[#8bb8ff] transition text-sm"
          >
            Belépés
          </RouterLink>
        </template>

        <template v-else>
          <div class="px-4 py-2 bg-black/40 rounded-2xl shadow-lg text-sm flex items-center gap-2">
            🔥 <span>23</span>
          </div>

          <RouterLink to="/profile" class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white border-2 border-[#0b1029] shadow-md hover:scale-105 transition-transform" title="Profil">
            {{ user.full_name?.charAt(0) || 'U' }}
          </RouterLink>

          <button @click="handleLogout" class="hidden md:block text-gray-400 hover:text-red-400 transition" title="Kilépés">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
          </button>
        </template>

        <button class="md:hidden text-3xl" @click="mobileOpen = !mobileOpen">☰</button>
      </div>
    </div>

    <transition name="fade">
  <div
    v-if="mobileOpen"
    class="md:hidden bg-[#0b1029]/95 border-t border-white/10 px-6 py-6 space-y-4"
  >
    <!-- LANDING -->
    <template v-if="props.mode === 'landing'">
      <RouterLink to="/#home" class="block nav-item" @click="mobileOpen = false">Kezdőlap</RouterLink>
      <RouterLink to="/#features" class="block nav-item" @click="mobileOpen = false">Funkciók</RouterLink>
      <RouterLink to="/#mission" class="block nav-item" @click="mobileOpen = false">Célunk</RouterLink>
      <RouterLink to="/#faq" class="block nav-item" @click="mobileOpen = false">GYIK</RouterLink>
    </template>

    <!-- APP -->
    <template v-else>
      <RouterLink to="/main" class="block nav-item" @click="mobileOpen = false">Vezérlőpult</RouterLink>
      <RouterLink to="/subjects" class="block nav-item" @click="mobileOpen = false">Tantárgyak</RouterLink>
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
.base-header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 50;
  backdrop-filter: blur(16px);
  background: var(--nav-bg);
  border-bottom: 1px solid var(--glass-border);
}

.nav-text {
  color: var(--text-secondary);
}

.nav-item {
  position: relative;
  transition: all 0.2s;
  cursor: pointer;
}
.nav-item:hover {
  color: var(--text-primary);
}

.accent-text {
  color: var(--accent);
}

.btn-primary-sm {
  background: var(--accent);
  color: white;
}

.mobile-link {
  display: block;
  font-weight: 600;
  color: #94a3b8;
  transition: color 0.2s;
}
.mobile-link:hover {
  color: white;
}

.slide-enter-active, .slide-leave-active { transition: all 0.3s ease; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-10px); }
</style>