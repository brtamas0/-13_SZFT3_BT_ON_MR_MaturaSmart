<script setup>
import { ref, onMounted } from "vue"
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue";

const subjects = ref([])
const isLoading = ref(true)

onMounted(async () => {
  try {
    const response = await fetch('http://backend.vm1.test/api/subjects')
    subjects.value = await response.json()
  } catch (error) {
    console.error("Hiba a tantárgyak betöltésekor:", error)
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />
    <section class="mt-4">

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

    <section class="mt-12">
      <h2 class="text-xl font-bold mb-6 tracking-wide">
        Tantárgyaid
      </h2>

      <div v-if="isLoading" class="text-gray-400">Betöltés...</div>

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <RouterLink 
          v-for="s in subjects" 
          :key="s.id" 
          :to="`/subject/${s.slug}`"
          class="subject-card block group"
        >
          <div class="text-4xl mb-3">
             {{ s.icon ? s.icon : '📚' }}
          </div>
          
          <h4 class="text-lg font-semibold group-hover:text-blue-300 transition-colors">
            {{ s.name }}
          </h4>
          <p class="text-gray-400 text-xs mt-1">Kattints a tanuláshoz →</p>
        </RouterLink>

        <div class="add-card cursor-pointer">
          + Új tantárgy
        </div>

      </div>
    </section>

  </BaseLayout>
</template>


<style scoped>
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
  text-decoration: none; 
  color: inherit;
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
</style>