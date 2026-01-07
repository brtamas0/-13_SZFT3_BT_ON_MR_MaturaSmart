<template>
  <div class="min-h-screen bg-gradient-to-b from-[#0b1029] to-[#031625] text-white">

<header class="w-full flex items-center justify-between px-6 md:px-10 py-5 border-b border-white/10">

  <div class="logo">
    Matura<span class="accent-text">Smart</span>
  </div>

  <nav class="hidden md:flex gap-6 text-gray-300 text-sm items-center">

    <span class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow-md">
      Vezérlőpult
    </span>

    <span class="nav-item">Tantárgyak</span>
    <span class="nav-item flex items-center gap-1">Naptár 📅</span>
    <span class="nav-item">Eredmények</span>
    <span class="nav-item flex items-center gap-1">Ranglista 🏆</span>
    <span class="nav-item">Profil</span>
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
        <span class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow-md w-fit">
          Vezérlőpult
        </span>

        <span class="nav-item">Tantárgyak</span>
        <span class="nav-item flex items-center gap-1">Naptár 📅</span>
        <span class="nav-item">Eredmények</span>
        <span class="nav-item flex items-center gap-1">Ranglista 🏆</span>
        <span class="nav-item">Profil</span>
      </div>
    </transition>

    <section class="max-w-6xl mx-auto mt-10 p-6 md:p-0">

      <div class="bg-[#10194E]/70 border border-white/10 rounded-3xl p-8 shadow-2xl">

        <div class="flex flex-col md:flex-row justify-between gap-6">

          <div>
            <h2 class="text-3xl font-bold">Szia Tamás! 👋</h2>
            <p class="text-gray-300 mt-2">Hátravan az írásbeliig:</p>

            <div class="grid grid-cols-3 gap-5 mt-5">
              <div class="time-box">
                <div class="time-number">153</div>
                <div class="time-text">NAP</div>
              </div>

              <div class="time-box">
                <div class="time-number">8</div>
                <div class="time-text">ÓRA</div>
              </div>

              <div class="time-box">
                <div class="time-number">25</div>
                <div class="time-text">PERC</div>
              </div>
            </div>
          </div>


          <div class="bg-[#0f173d] border border-white/10 rounded-2xl p-6 w-full shadow-xl">
            <h3 class="text-sm text-gray-400">LEGUTÓBBI LECKE FOLYTATÁSA:</h3>
            <p class="text-lg font-semibold">Matematika: Koordinátageometria</p>

            <div class="flex justify-between text-sm text-gray-300 mt-3">
              <span>Napi célkitűzés</span>
              <span>72%</span>
            </div>

            <div class="w-full h-3 bg-white/20 rounded-full overflow-hidden mb-4 mt-1">
              <div class="h-full bg-blue-500" style="width: 72%"></div>
            </div>

            <p class="text-gray-300 text-sm mb-4">
              Még <span class="text-white font-bold">2 feladat</span> a mai célhoz! ❤️
            </p>

            <div class="flex gap-4">
              <button class="continue-btn">
                ▶️ Folytatás
              </button>

              <button class="quiz-btn">
                📘 Napi Kvíz
              </button>
            </div>

          </div>

        </div>
      </div>
    </section>

    <section class="max-w-6xl mx-auto mt-12">
      <h2 class="text-xl font-bold mb-6 tracking-wide px-4 md:px-0">
        Tantárgyaid
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 md:px-0">

        <div v-for="s in subjects" :key="s.title" class="subject-card">
          <div class="text-4xl mb-3">{{ s.icon }}</div>
          <h4 class="text-lg font-semibold">{{ s.title }}</h4>
          <p class="text-gray-300 text-sm">{{ s.subtitle }}</p>
        </div>

        <div class="add-card">
          + Új tantárgy
        </div>

      </div>
    </section>

  </div>
</template>

<script setup>
import { ref } from "vue"

const mobileOpen = ref(false)

const subjects = [
  { title: "Matematika", subtitle: "Koordinátageometria", icon: "📐" },
  { title: "Szoftverfejlesztés", subtitle: "Backend API", icon: "💻" },
  { title: "Angol nyelv", subtitle: "Grammar", icon: "🗣️" },
  { title: "Történelem", subtitle: "Hidegháború", icon: "📜" },
  { title: "Magyar nyelv", subtitle: "Retorika", icon: "📖" }
]
</script>

<style scoped>
.nav-item {
  cursor: pointer;
  transition: .2s;
}
.nav-item:hover {
  color: white;
}

.time-box {
  background: rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  text-align: center;
  padding: 20px;
  border: 1px solid rgba(255, 255, 255, 0.15);
}
.time-number {
  font-size: 30px;
  font-weight: bold;
}
.time-text {
  font-size: 13px;
  letter-spacing: 1px;
  color: #c7c7c7;
}

.continue-btn {
  background:#5b6fff;
  padding:10px 22px;
  border-radius: 12px;
  transition:.25s;
}
.continue-btn:hover{
  background:#7d8dff;
}

.quiz-btn{
  background:rgba(255,255,255,.15);
  padding:10px 22px;
  border-radius:12px;
  transition:.25s;
}
.quiz-btn:hover{
  background:rgba(255,255,255,.35);
}

.subject-card{
  height:140px;
  background:linear-gradient(145deg,#1b274d,#111b33);
  border-radius:20px;
  border:1px solid rgba(255,255,255,0.15);
  padding:25px;
  transition:.25s;
  cursor:pointer;
}
.subject-card:hover{
  transform:translateY(-3px);
  background:rgba(255,255,255,.2);
}

.add-card{
  height:140px;
  border:2px dashed rgba(255,255,255,.4);
  border-radius:20px;
  display:flex;
  justify-content:center;
  align-items:center;
  font-size:18px;
  color:#cfcfcf;
  transition:.25s;
}
.add-card:hover{
  background:rgba(255,255,255,.2);
  color:white;
}

/* MOBILE NAV ANIMATION */
.fade-enter-active,
.fade-leave-active {
  transition: opacity .25s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
