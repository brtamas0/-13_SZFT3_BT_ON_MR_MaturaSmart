<script setup>
import { ref } from "vue";

const props = defineProps({
  mode: {
    type: String,
    default: "app", // landing | app
  },
});

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
          <RouterLink to="/#home" class="nav-item">Kezdőlap</RouterLink>
          <RouterLink to="/#features" class="nav-item">Funkciók</RouterLink>
          <RouterLink to="/#mission" class="nav-item">Célunk</RouterLink>
          <RouterLink to="/#faq" class="nav-item">GYIK</RouterLink>
        </template>

        <template v-else>
          <RouterLink
            to="/main"
            class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow-md hover:bg-blue-500 transition"
          >
            Vezérlőpult
          </RouterLink>
          <RouterLink to="/subjects" class="nav-item">Tantárgyak</RouterLink>
          <RouterLink to="/calendar" class="nav-item">Naptár 📅</RouterLink>
          <RouterLink to="/results" class="nav-item">Eredmények</RouterLink>
          <RouterLink to="/leaderboard" class="nav-item">Ranglista 🏆</RouterLink>
          <RouterLink to="/profile" class="nav-item">Profil</RouterLink>
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
.nav-item {
  cursor: pointer;
  transition: 0.2s;
}
.nav-item:hover {
  color: white;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
