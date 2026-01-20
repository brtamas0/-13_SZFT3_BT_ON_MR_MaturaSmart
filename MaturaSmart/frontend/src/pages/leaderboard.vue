<script setup>
import { ref, onMounted } from 'vue'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const leaderboard = ref([])
const userRank = ref(0)
const userXp = ref(0)
const isLoading = ref(true)
const currentUser = ref(null)

onMounted(async () => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) currentUser.value = JSON.parse(storedUser)

  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch('http://backend.vm1.test/api/leaderboard', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    const data = await response.json()
    
    leaderboard.value = data.leaderboard
    userRank.value = data.user_rank
    userXp.value = data.user_xp
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
})

const getRankColor = (index) => {
  if (index === 0) return 'text-yellow-400 drop-shadow-[0_0_15px_rgba(250,204,21,0.6)]'
  if (index === 1) return 'text-gray-300 drop-shadow-[0_0_15px_rgba(209,213,219,0.6)]' 
  if (index === 2) return 'text-orange-400 drop-shadow-[0_0_15px_rgba(251,146,60,0.6)]'
  return 'text-gray-500'
}

const getRankEmoji = (index) => {
  if (index === 0) return '👑'
  if (index === 1) return '🥈'
  if (index === 2) return '🥉'
  return `#${index + 1}`
}
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div class="min-h-screen bg-[#020617] pb-32 relative overflow-hidden">
      
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
        
        <div class="text-center mb-16">
          <div class="inline-block px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-blue-300 uppercase tracking-widest mb-4">
             Toplista
          </div>
          <h1 class="text-5xl font-black text-white mb-3 tracking-tight">Ranglista</h1>
          <p class="text-lg text-blue-200/60">Kik a MaturaSmart legszorgalmasabb diákjai?</p>
        </div>

        <div v-if="isLoading" class="text-center py-20">
           <div class="animate-spin rounded-full h-14 w-14 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
        </div>

        <div v-else>
          
          <div v-if="leaderboard.length >= 3" class="flex flex-col md:flex-row justify-center items-end gap-6 md:gap-8 mb-20 px-4">
              
              <div class="order-2 md:order-1 flex flex-col items-center w-full md:w-1/3 transform hover:scale-105 transition-transform duration-300">
                  <div class="relative mb-4">
                      <div class="w-24 h-24 rounded-full border-4 border-slate-300 bg-slate-800 flex items-center justify-center text-3xl font-bold text-white shadow-2xl overflow-hidden">
                          {{ leaderboard[1].full_name.charAt(0) }}
                      </div>
                      <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-slate-300 text-slate-900 text-xs font-black px-3 py-1 rounded-full border-4 border-[#020617]">2</div>
                  </div>
                  <div class="text-white font-bold text-lg text-center truncate w-full">{{ leaderboard[1].full_name }}</div>
                  <div class="text-slate-400 font-mono font-bold text-sm">{{ leaderboard[1].xp }} XP</div>
                  <div class="h-24 w-full bg-gradient-to-t from-slate-800/40 to-slate-800/10 rounded-t-2xl mt-4 border-x border-t border-slate-500/20"></div>
              </div>

              <div class="order-1 md:order-2 flex flex-col items-center w-full md:w-1/3 z-10 -mt-10 transform hover:scale-105 transition-transform duration-300">
                  <div class="text-5xl mb-4 animate-bounce drop-shadow-lg">👑</div>
                  <div class="relative mb-4">
                      <div class="w-32 h-32 rounded-full border-4 border-yellow-400 bg-yellow-900/20 flex items-center justify-center text-5xl font-bold text-white shadow-[0_0_40px_rgba(250,204,21,0.4)] overflow-hidden">
                          {{ leaderboard[0].full_name.charAt(0) }}
                      </div>
                      <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 text-base font-black px-4 py-1 rounded-full border-4 border-[#020617]">1</div>
                  </div>
                  <div class="text-white font-bold text-xl text-center truncate w-full">{{ leaderboard[0].full_name }}</div>
                  <div class="text-yellow-400 font-mono font-bold text-lg">{{ leaderboard[0].xp }} XP</div>
                  <div class="h-32 w-full bg-gradient-to-t from-yellow-600/20 to-yellow-500/10 border-t border-yellow-500/50 rounded-t-2xl mt-4 shadow-lg relative overflow-hidden">
                      <div class="absolute inset-0 bg-yellow-500/10 blur-xl animate-pulse"></div>
                  </div>
              </div>

              <div class="order-3 md:order-3 flex flex-col items-center w-full md:w-1/3 transform hover:scale-105 transition-transform duration-300">
                  <div class="relative mb-4">
                      <div class="w-24 h-24 rounded-full border-4 border-orange-500 bg-orange-900/20 flex items-center justify-center text-3xl font-bold text-white shadow-2xl overflow-hidden">
                          {{ leaderboard[2].full_name.charAt(0) }}
                      </div>
                      <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-orange-500 text-orange-900 text-xs font-black px-3 py-1 rounded-full border-4 border-[#020617]">3</div>
                  </div>
                  <div class="text-white font-bold text-lg text-center truncate w-full">{{ leaderboard[2].full_name }}</div>
                  <div class="text-orange-400 font-mono font-bold text-sm">{{ leaderboard[2].xp }} XP</div>
                  <div class="h-16 w-full bg-gradient-to-t from-orange-900/30 to-orange-800/20 rounded-t-2xl mt-4 border-t border-orange-700/30"></div>
              </div>
          </div>

          <div class="bg-[#1e293b]/40 backdrop-blur-sm border border-white/5 rounded-3xl overflow-hidden shadow-2xl">
            
            <div class="grid grid-cols-12 gap-4 p-5 border-b border-white/10 text-gray-400 text-xs font-bold uppercase tracking-widest">
                <div class="col-span-2 md:col-span-1 text-center">#</div>
                <div class="col-span-7 md:col-span-9">Tanuló</div>
                <div class="col-span-3 md:col-span-2 text-right">XP</div>
            </div>

            <div 
              v-for="(u, index) in leaderboard.slice(3)" 
              :key="u.id"
              class="grid grid-cols-12 gap-4 items-center p-5 border-b border-white/5 last:border-0 hover:bg-white/5 transition-all duration-200 group"
              :class="{ 'bg-blue-600/10 border-l-4 border-l-blue-500': u.id === currentUser?.id }"
            >
              <div class="col-span-2 md:col-span-1 text-center font-black text-lg text-slate-500 group-hover:text-white transition-colors">
                 {{ index + 4 }}
              </div>

              <div class="col-span-7 md:col-span-9 flex items-center gap-4 md:gap-6">
                  <div class="w-10 h-10 rounded-full bg-[#0b1029] border border-white/10 flex items-center justify-center font-bold text-sm text-gray-400 group-hover:border-blue-500/50 group-hover:text-white transition-colors">
                      {{ u.full_name.charAt(0) }}
                  </div>
                  <div>
                      <div class="text-white font-bold text-base md:text-lg flex items-center gap-2">
                          {{ u.full_name }} 
                          <span v-if="u.id === currentUser?.id" class="text-[10px] bg-blue-500 text-white px-2 py-0.5 rounded-full uppercase font-bold tracking-wide">Te</span>
                      </div>
                      <div class="text-xs text-gray-500 group-hover:text-gray-400 transition-colors">{{ u.level || 1 }}. Szint</div>
                  </div>
              </div>

              <div class="col-span-3 md:col-span-2 text-right font-mono font-bold text-lg text-blue-400 group-hover:text-blue-300">
                  {{ u.xp }}
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="fixed bottom-0 left-0 w-full bg-[#0b1029]/80 backdrop-blur-xl border-t border-white/10 p-4 z-40 shadow-[0_-10px_40px_rgba(0,0,0,0.5)]">
          <div class="max-w-4xl mx-auto flex items-center justify-between px-4">
              <div class="flex items-center gap-4">
                  <span class="text-gray-400 text-xs uppercase font-bold tracking-widest hidden sm:inline">Helyezésed:</span>
                  <span class="text-3xl font-black text-white">#{{ userRank }}</span>
              </div>
              <div class="flex items-center gap-3 bg-blue-600/20 px-6 py-2 rounded-xl border border-blue-500/30">
                  <span class="text-blue-400 font-mono font-bold text-xl">{{ userXp }} XP</span>
              </div>
          </div>
      </div>

    </div>
  </BaseLayout>
</template>