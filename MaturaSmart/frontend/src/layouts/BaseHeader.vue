<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'

const mobileOpen = ref(false)
</script>

<template>
  <header
    class="w-full backdrop-blur-md bg-white/5 border-b border-white/10 shadow-lg px-6 md:px-10 py-5 flex items-center justify-between relative z-20"
  >
    <RouterLink to="/" class="text-2xl font-bold tracking-wide">
         Matura<span class="text-[#6CA6FF]">Smart</span>
    </RouterLink>



    <nav class="hidden md:flex gap-6 text-gray-300 text-sm items-center">

      <RouterLink
        to="/"
        class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow-md hover:bg-blue-500 transition"
      >
        Vezérlőpult
      </RouterLink>

      <RouterLink to="/subjects" class="nav-item">Tantárgyak</RouterLink>
      <RouterLink to="/calendar" class="nav-item flex items-center gap-1">Naptár 📅</RouterLink>
      <RouterLink to="/results" class="nav-item">Eredmények</RouterLink>
      <RouterLink to="/leaderboard" class="nav-item flex items-center gap-1">Ranglista 🏆</RouterLink>
      <RouterLink to="/profile" class="nav-item">Profil</RouterLink>
    </nav>

    <div class="flex items-center gap-4">

      <div class="px-4 py-2 bg-black/40 rounded-2xl shadow-lg text-lg flex items-center gap-2">
        <span>🔥</span> <span>23</span>
      </div>

      <button
        class="md:hidden text-3xl"
        @click="mobileOpen = !mobileOpen"
      >
        ☰
      </button>
    </div>
  </header>

  <transition name="fade">
    <div
      v-if="mobileOpen"
      class="md:hidden bg-[#0d1538] border-b border-white/10 px-6 py-4 flex flex-col gap-4 text-gray-300 text-sm"
    >
      <RouterLink
        to="/"
        class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow-md w-fit"
      >
        Vezérlőpult
      </RouterLink>

      <RouterLink to="/subjects" class="nav-item">Tantárgyak</RouterLink>
      <RouterLink to="/calendar" class="nav-item flex items-center gap-1">Naptár 📅</RouterLink>
      <RouterLink to="/results" class="nav-item">Eredmények</RouterLink>
      <RouterLink to="/leaderboard" class="nav-item flex items-center gap-1">Ranglista 🏆</RouterLink>
      <RouterLink to="/profile" class="nav-item">Profil</RouterLink>
    </div>
  </transition>
</template>

<style scoped>
.nav-item {
  cursor: pointer;
  transition: .2s;
}
.nav-item:hover {
  color: white;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity .25s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
