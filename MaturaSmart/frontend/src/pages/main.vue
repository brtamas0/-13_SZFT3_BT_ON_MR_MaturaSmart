<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue"
import { useRouter } from "vue-router"
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const router = useRouter()
const subjects = ref([])
const lastTopic = ref(null)
const isLoading = ref(true)
const user = ref(null)

const examDate = new Date('2026-05-04T08:00:00')
const remaining = ref({ days: 0, hours: 0, minutes: 0 })
let timerInterval = null

const updateCountdown = () => {
  const now = new Date()
  const diff = examDate - now
  if (diff <= 0) {
    remaining.value = { days: 0, hours: 0, minutes: 0 }
    return
  }
  remaining.value = {
    days: Math.floor(diff / (1000 * 60 * 60 * 24)),
    hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
    minutes: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  }
}


const firstName = computed(() => {
  if (!user.value || !user.value.full_name) return 'Tanuló'
  const parts = user.value.full_name.split(' ')
  return parts.length > 1 ? parts[1] : parts[0]
})

onMounted(async () => {
  const token = localStorage.getItem('token')
  const storedUser = localStorage.getItem('user')

  if (!token || !storedUser) { router.push('/login'); return }
  user.value = JSON.parse(storedUser)

  updateCountdown()
  timerInterval = setInterval(updateCountdown, 60000)

  try {
    const response = await fetch('http://backend.maturasmart.hu/api/dashboard', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })

    if (response.ok) {
      const data = await response.json()
      subjects.value = data.subjects
      lastTopic.value = data.last_topic

      if (data.user) {
        user.value = data.user
        localStorage.setItem('user', JSON.stringify(data.user))
      }
    }
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div v-if="isLoading" class="flex justify-center items-center h-[80vh]">
      <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <div
        class="bg-[#10194E] border border-white/10 rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden mb-16">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/20 rounded-full blur-[100px] pointer-events-none">
        </div>

        <div class="relative z-10 grid grid-cols-1 xl:grid-cols-2 gap-12 items-center">

          <div>
            <div
              class="inline-block px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-blue-300 uppercase tracking-widest mb-4">
              Vezérlőpult
            </div>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-4 tracking-tight leading-tight">
              Szia, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">{{
                firstName }}</span>! 👋
            </h1>
            <p class="text-blue-200/80 text-lg mb-8">
              Már csak ennyi időd van felkészülni az írásbelire:
            </p>

            <div class="flex gap-4 md:justify-start justify-center">
              <div
                class="bg-[#0b102e]/60 backdrop-blur border border-white/10 rounded-2xl p-4 text-center min-w-[80px]">
                <div class="text-2xl md:text-3xl font-black text-white">{{ remaining.days }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Nap</div>
              </div>
              <div class="text-2xl font-light text-slate-600 self-center">:</div>
              <div
                class="bg-[#0b102e]/60 backdrop-blur border border-white/10 rounded-2xl p-4 text-center min-w-[80px]">
                <div class="text-2xl md:text-3xl font-black text-white">{{ remaining.hours }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Óra</div>
              </div>
              <div class="text-2xl font-light text-slate-600 self-center">:</div>
              <div
                class="bg-[#0b102e]/60 backdrop-blur border border-white/10 rounded-2xl p-4 text-center min-w-[80px]">
                <div class="text-2xl md:text-3xl font-black text-white">{{ remaining.minutes }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Perc</div>
              </div>
            </div>
          </div>

          <div>
            <div v-if="lastTopic"
              class="bg-[#1e293b]/50 backdrop-blur-xl border border-white/10 rounded-3xl p-8 relative overflow-hidden group hover:border-blue-500/30 transition-all duration-300">
              <div class="flex justify-between items-start mb-6">
                <div>
                  <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full animate-pulse"
                      :class="lastTopic.progress === 100 ? 'bg-green-500' : 'bg-yellow-500'"></span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                      {{ lastTopic.progress === 100 ? 'BEFEJEZVE' : 'FOLYTATÁS' }}
                    </span>
                  </div>
                  <h3 class="text-2xl font-black text-white leading-tight group-hover:text-blue-200 transition-colors">
                    {{ lastTopic.title }}
                  </h3>
                  <p class="text-sm text-blue-300 font-medium mt-1">{{ lastTopic.subject.title }}</p>
                </div>
                <span class="text-4xl shadow-lg rounded-full bg-white/5 p-2">
                  {{ lastTopic.progress === 100 ? '🏆' : '🚀' }}
                </span>
              </div>

              <div class="flex justify-between text-xs text-slate-400 mb-2 font-bold uppercase tracking-wide">
                <span>Lecke állapota</span>
                <span class="text-white">{{ lastTopic.progress }}%</span>
              </div>

              <div class="w-full h-2 bg-slate-900/50 rounded-full overflow-hidden mb-8">
                <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-500 relative transition-all duration-1000"
                  :style="{ width: lastTopic.progress + '%' }">
                </div>
              </div>

              <div class="grid grid-cols-1 gap-4">
                <RouterLink :to="`/tantargyak/${lastTopic.subject.slug}/${lastTopic.slug}`"
                  class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl text-center transition-all hover:scale-105 shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2">
                  <span>▶️</span> {{ lastTopic.progress === 100 ? 'Gyakorlás' : 'Folytatás' }}
                </RouterLink>
                <button
                  class="bg-white/5 hover:bg-white/10 text-white font-bold py-3 rounded-xl transition-colors border border-white/5 hidden">
                  Részletek
                </button>
              </div>
            </div>

            <div v-else
              class="bg-[#1e293b]/50 border border-white/10 rounded-3xl p-10 text-center flex flex-col items-center justify-center h-full min-h-[300px]">
              <div class="text-6xl mb-4 opacity-50">🎓</div>
              <h3 class="text-2xl font-bold text-white mb-2">Üdv a fedélzeten!</h3>
              <p class="text-slate-400 mb-6 max-w-xs">Válassz egy tantárgyat lentebb, és kezdd el gyűjteni az XP-ket!
              </p>
              <div class="animate-bounce text-blue-400 text-2xl">⬇️</div>
            </div>
          </div>

        </div>
      </div>

      <div class="flex items-center gap-4 mb-8">
        <div class="h-8 w-1.5 bg-blue-500 rounded-full"></div>
        <h2 class="text-2xl font-bold text-white tracking-wide">Tantárgyak</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">

        <RouterLink v-for="s in subjects" :key="s.id" :to="`/tantargyak/${s.slug}`"
          class="group relative bg-[#1e293b] border border-white/5 rounded-[32px] p-1 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
          <div
            class="absolute inset-0 rounded-[32px] bg-gradient-to-br from-transparent to-transparent group-hover:from-blue-500/20 group-hover:to-purple-500/20 transition-all duration-300">
          </div>

          <div class="bg-[#131b40] rounded-[28px] p-6 h-full flex flex-col relative z-10 overflow-hidden">

            <div
              class="absolute -right-6 -bottom-6 text-9xl opacity-5 grayscale group-hover:grayscale-0 group-hover:opacity-10 transition-all duration-500 rotate-12">
              {{ s.visuals.icon }}
            </div>

            <div class="flex justify-between items-start mb-6">
              <div
                class="w-16 h-16 rounded-2xl flex items-center justify-center text-4xl shadow-inner border border-white/5"
                :class="[s.visuals.bg, s.visuals.color]">
                {{ s.visuals.icon }}
              </div>
              <div
                class="px-3 py-1 rounded-full border border-white/5 bg-[#0f172a] text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                {{ s.total_topics }} Lecke
              </div>
            </div>

            <div class="mb-4">
              <h4 class="text-2xl font-black text-white mb-1 group-hover:text-blue-200 transition-colors">{{ s.title }}
              </h4>
              <p class="text-xs text-slate-500 font-medium">Kattints a tanuláshoz</p>
            </div>

            <div class="mt-auto">
              <div class="flex justify-between items-end mb-2">
                <span class="text-[10px] font-bold text-slate-500 uppercase">Haladás</span>
                <span class="text-xs font-bold" :class="s.visuals.color">{{ s.progress }}%</span>
              </div>

              <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-500 relative transition-all duration-1000"
                  :style="{ width: s.progress + '%' }">
                </div>
              </div>
            </div>

          </div>
        </RouterLink>

        <div
          class="border-2 border-dashed border-white/10 rounded-[32px] p-8 flex flex-col items-center justify-center text-center opacity-40 hover:opacity-100 hover:border-blue-500/30 transition-all cursor-pointer group min-h-[240px]">
          <div
            class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center text-2xl text-slate-400 mb-4 group-hover:bg-blue-500/20 group-hover:text-blue-400 transition-colors">
            +
          </div>
          <span class="font-bold text-slate-500 group-hover:text-white transition-colors">Hamarosan...</span>
        </div>

      </div>

    </div>
  </BaseLayout>
</template>