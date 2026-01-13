<script setup>
import { ref } from 'vue'
const props = defineProps({
  mode: {
    type: String,
    default: "app" 
  }
})

const mobileOpen = ref(false)
</script>

<template>
  <header
    class="w-full backdrop-blur-md bg-white/5 border-b border-white/10 shadow-lg px-6 md:px-10 py-5 flex flex-col gap-4 relative z-20"
  >

    <div class="flex items-center justify-between">
      <RouterLink to="/" class="text-2xl font-bold tracking-wide">
        Matura<span class="text-[#6CA6FF]">Smart</span>
      </RouterLink>

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
          <div class="px-4 py-2 bg-black/40 rounded-2xl shadow-lg text-lg flex items-center gap-2">
            <span>🔥</span> <span>23</span>
          </div>
        </template>

        <button class="md:hidden text-3xl" @click="mobileOpen = !mobileOpen">☰</button>
      </div>
    </div>

    <nav class="hidden md:flex gap-6 text-gray-300 text-sm items-center">

      <template v-if="props.mode === 'landing'">
        <a href="#" class="hover:text-white transition">Kezdőlap</a>
        <a href="#" class="hover:text-white transition">Funkciók</a>
        <a href="#" class="hover:text-white transition">Célunk</a>
        <a href="#" class="hover:text-white transition">GYIK</a>
      </template>

      <template v-else>
         <RouterLink to="/main" class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow-md hover:bg-blue-500 transition">
            Vezérlőpult
        </RouterLink>
        <RouterLink to="/subjects" class="nav-item">Tantárgyak</RouterLink>
        <RouterLink to="/calendar" class="nav-item">Naptár 📅</RouterLink>
        <RouterLink to="/results" class="nav-item">Eredmények</RouterLink>
        <RouterLink to="/leaderboard" class="nav-item">Ranglista 🏆</RouterLink>
        <RouterLink to="/profile" class="nav-item">Profil</RouterLink>
      </template>

    </nav>

  </header>
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
