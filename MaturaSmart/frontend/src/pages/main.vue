<script setup>
import { ref, onMounted, onUnmounted } from "vue"
import { useRouter } from "vue-router"
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const router = useRouter()
const subjects = ref([])
const isLoading = ref(true)
const user = ref(null)

// --- ÉRETTSÉGI VISSZASZÁMLÁLÓ ---
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

onMounted(async () => {
  // 1. Felhasználó ellenőrzése
  const token = localStorage.getItem('token')
  const storedUser = localStorage.getItem('user')

  if (!token || !storedUser) {
    router.push('/login') 
    return
  }
  user.value = JSON.parse(storedUser)

  // 2. Visszaszámláló indítása
  updateCountdown()
  timerInterval = setInterval(updateCountdown, 10000) //10mp

  // 3. Tantárgyak lekérése
  try {
    const token = localStorage.getItem('token') // <--- Token kinyerése

const response = await fetch('http://backend.vm1.test/api/tantargyak', {
  headers: {
    'Authorization': `Bearer ${token}`, // <--- Token küldése
    'Accept': 'application/json'
  }
})
    if (response.ok) subjects.value = await response.json()
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
    
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <div class="bg-[#10194E]/80 border border-white/10 rounded-3xl p-6 md:p-10 shadow-2xl backdrop-blur-md relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-blue-900/20 to-transparent pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-10">
          
          <div class="flex-1 w-full text-center md:text-left">
            <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-2 tracking-tight">
              Szia {{ user?.full_name?.split(' ')[1] || 'Tanuló' }}! 👋
            </h2>
            <p class="text-blue-200 text-lg mb-8 font-light">
              Már csak ennyi időd van felkészülni az írásbelire:
            </p>
            <div class="flex flex-wrap justify-center md:justify-start gap-4">
              
              <div class="time-box group">
                <div class="time-number text-blue-400 group-hover:scale-110 transition-transform">
                  {{ remaining.days }}
                </div>
                <div class="time-text">NAP</div>
              </div>

              <div class="text-3xl font-light text-gray-600 self-center hidden sm:block">:</div>

              <div class="time-box group">
                <div class="time-number text-indigo-400 group-hover:scale-110 transition-transform">
                  {{ remaining.hours }}
                </div>
                <div class="time-text">ÓRA</div>
              </div>

              <div class="text-3xl font-light text-gray-600 self-center hidden sm:block">:</div>

              <div class="time-box group">
                <div class="time-number text-purple-400 group-hover:scale-110 transition-transform">
                  {{ remaining.minutes }}
                </div>
                <div class="time-text">PERC</div>
              </div>

            </div>
          </div>

          <div class="w-full md:w-[400px] lg:w-[450px] bg-[#0f173d] border border-white/10 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-blue-500/30 transition-colors">
            
            <div class="absolute top-0 right-0 w-40 h-40 bg-blue-600/10 rounded-full blur-3xl -mr-10 -mt-10"></div>

            <div class="relative z-10">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Legutóbbi aktivitás</h3>
                  <p class="text-xl font-bold text-white leading-tight">Matematika: <br/><span class="text-indigo-300">Pitagorasz-tétel</span></p>
                </div>
                <span class="text-2xl">🏆</span>
              </div>

              <div class="flex justify-between text-sm text-gray-300 mb-2">
                <span>Felkészültség</span>
                <span class="text-white font-bold">{{ user?.xp ? Math.min(Math.round(user.xp / 10), 100) : 5 }}%</span>
              </div>

              <div class="w-full h-3 bg-black/40 rounded-full overflow-hidden mb-6 border border-white/5">
                <div 
                  class="h-full bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500 transition-all duration-1000 relative"
                  :style="{ width: user?.xp ? Math.min(user.xp / 10, 100) + '%' : '5%' }"
                >
                  <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <RouterLink to="/tantargyak/matematika/pitagorasz-tetel" class="continue-btn flex justify-center items-center gap-2">
                  <span>▶️</span> Folytatás
                </RouterLink>

                <button class="quiz-btn flex justify-center items-center gap-2" onclick="alert('Hamarosan!')">
                  <span>📝</span> Kvíz
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>

      <section class="mt-16 mb-20">
        <div class="flex items-center gap-3 mb-8">
          <div class="h-8 w-1 bg-blue-500 rounded-full"></div>
          <h2 class="text-2xl font-bold text-white tracking-wide">Válassz tantárgyat</h2>
        </div>

        <div v-if="isLoading" class="flex justify-center py-20">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

          <RouterLink 
            v-for="s in subjects" 
            :key="s.id" 
            :to="`/tantargyak/${s.slug}`"
            class="subject-card block group relative overflow-hidden"
          >
             <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 group-hover:from-blue-600/20 group-hover:to-purple-600/20 transition-all duration-500"></div>
            
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <div class="text-5xl mb-4 transform group-hover:scale-110 group-hover:-translate-y-1 transition-transform duration-300 inline-block drop-shadow-lg">
                   {{ s.icon ? s.icon : '📚' }}
                </div>
                <h4 class="text-2xl font-bold text-white group-hover:text-blue-300 transition-colors">
                  {{ s.name }}
                </h4>
              </div>
              
              <div class="mt-4 flex items-center text-sm text-gray-400 group-hover:text-white transition-colors">
                <span>Tanulás indítása</span>
                <span class="ml-2 transform group-hover:translate-x-1 transition-transform">→</span>
              </div>
            </div>
          </RouterLink>

          <div class="add-card cursor-pointer group hover:border-blue-500/50 hover:bg-white/5">
            <div class="text-center">
              <div class="text-4xl text-gray-600 group-hover:text-blue-400 mb-2 transition-colors">+</div>
              <span class="font-medium text-gray-400 group-hover:text-white transition-colors">Új tantárgy</span>
            </div>
          </div>

        </div>
      </section>

    </div>
  </BaseLayout>
</template>

<style scoped>
.time-box {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 20px;
  text-align: center;
  padding: 15px 25px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  min-width: 100px;
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
}
.time-number {
  font-size: 32px;
  font-weight: 800;
  line-height: 1;
  font-family: monospace;
}
.time-text {
  font-size: 10px;
  letter-spacing: 2px;
  color: #94a3b8;
  margin-top: 8px;
  font-weight: bold;
  text-transform: uppercase;
}

/* Gombok */
.continue-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  padding: 12px;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.2s;
  text-align: center;
  border: 1px solid rgba(255,255,255,0.1);
}
.continue-btn:hover {
  filter: brightness(1.1);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
}

.quiz-btn {
  background: rgba(255, 255, 255, 0.05);
  color: #cbd5e1;
  padding: 12px;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.2s;
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.quiz-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

/* Kártyák */
.subject-card {
  height: 200px;
  background: #1e293b;
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.05);
  padding: 32px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.subject-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
  border-color: rgba(59, 130, 246, 0.4);
}

.add-card {
  height: 200px;
  border: 2px dashed rgba(255, 255, 255, 0.15);
  border-radius: 24px;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: .25s;
}
</style>