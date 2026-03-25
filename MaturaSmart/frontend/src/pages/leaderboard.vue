<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const leaderboard = ref([])
const userRank = ref(0)
const userXp = ref(0)
const isLoading = ref(true)
const currentUser = ref(null)

const originalBodyStyle = document.body.style.backgroundColor

onMounted(async () => {
  document.body.style.backgroundColor = '#020617'

  const storedUser = localStorage.getItem('user')
  if (storedUser) currentUser.value = JSON.parse(storedUser)

  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch('http://backend.maturasmart.hu/api/leaderboard', {
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

onUnmounted(() => {
  document.body.style.backgroundColor = originalBodyStyle || ''
})
</script>

<template>
  <BaseLayout class="bg-[#020617] min-h-screen text-slate-200">
    
    <div class="sticky top-0 z-30 bg-[#020617] border-b border-white/5">
        <BaseHeader mode="app" />
    </div>

    <div class="relative flex flex-col pb-0">
      
      <div class="fixed top-0 left-0 w-[800px] h-[800px] bg-blue-600/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
      <div class="fixed bottom-0 right-0 w-[800px] h-[800px] bg-purple-600/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

      <div class="w-full px-4 sm:px-8 lg:px-12 py-8 relative z-10 flex-1 flex flex-col">
        
        <div class="text-center mb-16 mt-4">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-900/20 border border-blue-500/20 text-xs font-bold text-blue-300 uppercase tracking-widest mb-4 shadow-[0_0_15px_rgba(59,130,246,0.3)]">
              <span>🏆</span> Ranglista
          </div>
          <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight drop-shadow-xl">Legjobb Tanulók</h1>
        </div>

        <div v-if="isLoading" class="flex-1 flex justify-center items-center min-h-[400px]">
           <div class="relative w-16 h-16">
              <div class="absolute inset-0 border-4 border-blue-500/30 rounded-full animate-ping"></div>
              <div class="absolute inset-0 border-4 border-t-blue-500 rounded-full animate-spin"></div>
           </div>
        </div>

        <div v-else class="flex flex-col gap-12 pb-32">
          
          <div v-if="leaderboard.length >= 3" class="flex justify-center items-end gap-4 sm:gap-8 md:gap-16 w-full mb-8">
              
              <div class="order-1 flex flex-col items-center group relative top-8">
                  <div class="relative mb-3 transition-transform duration-300 group-hover:-translate-y-2">
                      <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-full border-4 border-slate-300 bg-[#0f172a] flex items-center justify-center text-3xl font-bold text-slate-300 shadow-2xl relative z-10 overflow-hidden">
                          <img v-if="leaderboard[1].avatar_url" :src="leaderboard[1].avatar_url" class="w-full h-full object-cover">
                          <span v-else>{{ leaderboard[1].full_name.charAt(0) }}</span>
                      </div>
                      <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-slate-300 text-slate-900 text-xs font-black px-3 py-0.5 rounded-full border-4 border-[#020617] z-20">#2</div>
                  </div>
                  <div class="text-center">
                      <div class="text-white font-bold text-lg mb-1">{{ leaderboard[1].full_name }}</div>
                      <div class="text-slate-400 font-mono text-sm font-bold bg-slate-800/50 px-3 py-1 rounded-full border border-slate-700">{{ leaderboard[1].xp }} XP</div>
                  </div>
              </div>

              <div class="order-2 flex flex-col items-center z-10 -mb-4 group">
                   <div class="text-6xl mb-2 animate-bounce drop-shadow-[0_0_15px_rgba(234,179,8,0.5)]">👑</div>
                   <div class="relative mb-4 transition-transform duration-300 group-hover:-translate-y-2">
                      <div class="w-28 h-28 sm:w-40 sm:h-40 rounded-full border-4 border-yellow-400 bg-yellow-900/10 flex items-center justify-center text-5xl font-bold text-yellow-100 shadow-[0_0_40px_rgba(250,204,21,0.2)] relative z-10 overflow-hidden">
                          <img v-if="leaderboard[0].avatar_url" :src="leaderboard[0].avatar_url" class="w-full h-full object-cover">
                          <span v-else>{{ leaderboard[0].full_name.charAt(0) }}</span>
                      </div>
                      <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 text-sm font-black px-5 py-1 rounded-full border-4 border-[#020617] z-20 shadow-lg">#1</div>
                  </div>
                  <div class="text-center">
                      <div class="text-white font-bold text-xl sm:text-2xl mb-2">{{ leaderboard[0].full_name }}</div>
                      <div class="text-yellow-400 font-mono text-lg font-bold bg-yellow-900/30 px-4 py-1.5 rounded-full border border-yellow-600/30 shadow-[0_0_15px_rgba(234,179,8,0.2)]">{{ leaderboard[0].xp }} XP</div>
                  </div>
              </div>

              <div class="order-3 flex flex-col items-center group relative top-12">
                  <div class="relative mb-3 transition-transform duration-300 group-hover:-translate-y-2">
                      <div class="w-16 h-16 sm:w-24 sm:h-24 rounded-full border-4 border-orange-500 bg-[#0f172a] flex items-center justify-center text-2xl font-bold text-orange-200 shadow-2xl relative z-10 overflow-hidden">
                          <img v-if="leaderboard[2].avatar_url" :src="leaderboard[2].avatar_url" class="w-full h-full object-cover">
                          <span v-else>{{ leaderboard[2].full_name.charAt(0) }}</span>
                      </div>
                      <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-orange-500 text-orange-900 text-xs font-black px-3 py-0.5 rounded-full border-4 border-[#020617] z-20">#3</div>
                  </div>
                  <div class="text-center">
                      <div class="text-white font-bold text-base mb-1">{{ leaderboard[2].full_name }}</div>
                      <div class="text-orange-400 font-mono text-xs font-bold bg-orange-900/30 px-3 py-1 rounded-full border border-orange-700/50">{{ leaderboard[2].xp }} XP</div>
                  </div>
              </div>

          </div>

          <div class="w-full max-w-[1600px] mx-auto mt-8">
              
              <div class="flex items-center justify-between mb-6 px-4">
                  <h3 class="text-slate-400 font-bold uppercase tracking-widest text-sm">Top 50</h3>
                  <div class="h-px flex-1 bg-white/10 ml-6"></div>
              </div>

              <div class="grid grid-cols-12 gap-4 px-6 py-3 text-slate-500 text-xs font-bold uppercase tracking-widest border-b border-white/5">
                  <div class="col-span-1 text-center">#</div>
                  <div class="col-span-8 md:col-span-9">Tanuló</div>
                  <div class="col-span-3 md:col-span-2 text-right">XP</div>
              </div>

              <div class="flex flex-col">
                  <div 
                    v-for="(u, index) in leaderboard.slice(3)" 
                    :key="u.id"
                    class="grid grid-cols-12 gap-4 items-center px-6 py-4 border-b border-white/5 hover:bg-white/[0.02] transition-colors duration-200 group rounded-xl my-1"
                    :class="{ 'bg-blue-600/10 border border-blue-500/30': u.id === currentUser?.id }"
                  >
                    <div class="col-span-1 text-center font-black text-lg text-slate-600 group-hover:text-white transition-colors">
                        {{ index + 4 }}
                    </div>

                    <div class="col-span-8 md:col-span-9 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-[#0f172a] border border-white/10 flex items-center justify-center font-bold text-sm text-slate-400 overflow-hidden">
                            <img v-if="u.avatar_url" :src="u.avatar_url" class="w-full h-full object-cover">
                            <span v-else>{{ u.full_name.charAt(0) }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="text-white font-bold text-sm md:text-base truncate flex items-center gap-2">
                                {{ u.full_name }} 
                                <span v-if="u.id === currentUser?.id" class="text-[10px] bg-blue-500 text-white px-2 py-0.5 rounded-full uppercase font-bold tracking-wide">Te</span>
                            </div>
                            <div class="text-xs text-slate-500 group-hover:text-slate-400 transition-colors">{{ u.level || 1 }}. Szint</div>
                        </div>
                    </div>

                    <div class="col-span-3 md:col-span-2 text-right font-mono font-bold text-base md:text-lg text-blue-400 group-hover:text-blue-300">
                        {{ u.xp }}
                    </div>
                  </div>
              </div>
          </div>

        </div>
      </div>

      <div class="fixed bottom-0 left-0 w-full bg-[#020617]/90 backdrop-blur-xl border-t border-white/10 p-4 z-50">
          <div class="w-full px-4 md:px-12 flex items-center justify-between">
              <div class="flex items-center gap-4">
                  <span class="text-slate-400 text-xs uppercase font-bold tracking-widest hidden sm:inline">Helyezésed:</span>
                  <div class="flex items-baseline gap-1">
                      <span class="text-3xl font-black text-white">#{{ userRank }}</span>
                  </div>
              </div>
              
              <div class="flex items-center gap-3 bg-blue-600/20 px-6 py-2 rounded-full border border-blue-500/30 shadow-[0_0_20px_rgba(37,99,235,0.2)]">
                  <span class="text-blue-400 font-mono font-bold text-xl">{{ userXp }} XP</span>
              </div>
          </div>
      </div>

    </div>
  </BaseLayout>
</template>