<template>
  <div
    class="bg-gray-50 dark:bg-[#020617] text-slate-800 dark:text-white min-h-screen flex items-center justify-center overflow-hidden relative transition-colors duration-300">

    <div class="grid-bg"></div>

    <button
      @click="toggleTheme"
      class="absolute top-5 right-5 w-10 h-10 rounded-full border border-gray-300 dark:border-white/20 bg-white/50 dark:bg-white/5 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-white/10 transition z-50 shadow-sm">
      🌓
    </button>

    <div class="relative z-10 text-center p-8 max-w-2xl w-full">
      <div class="text-gradient-404 mb-4">404</div>

      <div class="flex justify-center mb-6">
        <img
          src="/axel404.png"
          class="axel-dead-animation"
          alt="K.O. Axel"
          @error="imageError = true"
          v-show="!imageError"
        />
        <div v-if="imageError" class="text-6xl animate-pulse">🦎💀</div>
      </div>

      <h1 class="text-3xl md:text-4xl font-bold mb-4">
        Itt még a puskák se segítenek...
      </h1>

      <p
        class="text-slate-600 dark:text-gray-400 mb-10 text-lg leading-relaxed max-w-md mx-auto transition-colors">
        Ez az oldal úgy eltűnt, mint a motiváció hétfő reggel. <br />
        Vagy lehet, hogy Axel megette a házival együtt. 🦎😢
      </p>

      <RouterLink
        to="/"
        class="btn-shadow-404 inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-lg">
        🏃 Menekülés a főoldalra
      </RouterLink>
    </div>

    <footer
      class="absolute bottom-4 text-center text-sm text-slate-500 dark:text-gray-600 z-10 w-full">
      MaturaSmart • Hiba történt a mátrixban
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const imageError = ref(false);

const toggleTheme = () => {
  const html = document.documentElement;
  const current = html.getAttribute("data-theme") || "dark";
  const next = current === "dark" ? "light" : "dark";

  html.setAttribute("data-theme", next);
  localStorage.setItem("theme", next);

  if (next === "dark") html.classList.add("dark");
  else html.classList.remove("dark");
};

onMounted(() => {
  const saved = localStorage.getItem("theme") || "dark";
  document.documentElement.setAttribute("data-theme", saved);

  if (saved === "dark") document.documentElement.classList.add("dark");
  else document.documentElement.classList.remove("dark");
});
</script>

<style scoped>
.text-gradient-404 {
  font-size: 90px;
  font-weight: 900;
  background: linear-gradient(135deg, #4f46e5, #22d3ee);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.axel-dead-animation {
  width: 180px;
  filter: drop-shadow(0 0 20px rgba(0, 0, 0, 0.5));
  animation: float 2s infinite ease-in-out;
}

@keyframes float {
  0% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
  100% { transform: translateY(0); }
}

.grid-bg {
  position: absolute;
  inset: 0;
  background-image: linear-gradient(
      to right,
      rgba(255, 255, 255, 0.03) 1px,
      transparent 1px),
    linear-gradient(
      to bottom,
      rgba(255, 255, 255, 0.03) 1px,
      transparent 1px);
  background-size: 50px 50px;
}

.btn-shadow-404 {
  box-shadow: 0 15px 40px rgba(79, 70, 229, 0.4);
}
.btn-shadow-404:hover {
  box-shadow: 0 20px 50px rgba(79, 70, 229, 0.6);
}
</style>
