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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-32">
      
      <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold text-white mb-3 tracking-tight">🏆 Ranglista</h1>
        <p class="text-lg text-gray-400">Kik a MaturaSmart legszorgalmasabb diákjai?</p>
      </div>

      <div v-if="isLoading" class="text-center py-20">
         <div class="animate-spin rounded-full h-14 w-14 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
      </div>

      <div v-else>
        <div v-if="leaderboard.length >= 3" class="flex justify-center items-end gap-6 md:gap-12 mb-16">
            
            <div class="flex flex-col items-center transform hover:scale-105 transition-transform duration-300">
                <div class="w-24 h-24 md:w-28 md:h-28 rounded-full border-4 border-gray-400 bg-gray-900 flex items-center justify-center text-3xl font-bold text-white mb-3 relative shadow-2xl">
                    {{ leaderboard[1].full_name.charAt(0) }}
                    <div class="absolute -bottom-4 bg-gray-400 text-black text-sm font-bold px-3 py-1 rounded-full shadow-lg">2.</div>
                </div>
                <div class="text-white font-bold text-lg text-center w-32 truncate mt-2">{{ leaderboard[1].full_name }}</div>
                <div class="text-blue-400 font-bold">{{ leaderboard[1].xp }} XP</div>
                <div class="h-32 w-24 md:w-32 bg-gradient-to-t from-gray-800 to-gray-700/50 rounded-t-xl mt-3 border-t border-gray-600"></div>
            </div>

            <div class="flex flex-col items-center z-10 transform hover:scale-105 transition-transform duration-300">
                <div class="text-5xl mb-4 animate-bounce drop-shadow-lg">👑</div>
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-yellow-400 bg-yellow-900/20 flex items-center justify-center text-5xl font-bold text-white mb-3 shadow-[0_0_40px_rgba(250,204,21,0.4)] relative">
                    {{ leaderboard[0].full_name.charAt(0) }}
                    <div class="absolute -bottom-4 bg-yellow-400 text-black text-base font-bold px-4 py-1 rounded-full shadow-lg">1.</div>
                </div>
                <div class="text-white font-bold text-xl text-center w-40 truncate mt-2">{{ leaderboard[0].full_name }}</div>
                <div class="text-yellow-400 font-bold text-lg">{{ leaderboard[0].xp }} XP</div>
                <div class="h-40 w-32 md:w-40 bg-gradient-to-t from-yellow-600/20 to-yellow-500/10 border-t border-yellow-500/50 rounded-t-xl mt-3 shadow-lg"></div>
            </div>

            <div class="flex flex-col items-center transform hover:scale-105 transition-transform duration-300">
                <div class="w-24 h-24 md:w-28 md:h-28 rounded-full border-4 border-orange-500 bg-orange-900/20 flex items-center justify-center text-3xl font-bold text-white mb-3 relative shadow-2xl">
                    {{ leaderboard[2].full_name.charAt(0) }}
                    <div class="absolute -bottom-4 bg-orange-500 text-black text-sm font-bold px-3 py-1 rounded-full shadow-lg">3.</div>
                </div>
                <div class="text-white font-bold text-lg text-center w-32 truncate mt-2">{{ leaderboard[2].full_name }}</div>
                <div class="text-blue-400 font-bold">{{ leaderboard[2].xp }} XP</div>
                <div class="h-24 w-24 md:w-32 bg-gradient-to-t from-orange-900/30 to-orange-800/20 rounded-t-xl mt-3 border-t border-orange-700"></div>
            </div>
        </div>

        <div class="bg-[#1e293b]/40 backdrop-blur-sm border border-white/5 rounded-3xl overflow-hidden shadow-2xl">
          
          <div class="grid grid-cols-12 gap-4 p-5 border-b border-white/10 text-gray-400 text-sm font-bold uppercase tracking-wider">
             <div class="col-span-2 md:col-span-1 text-center">#</div>
             <div class="col-span-7 md:col-span-9">Tanuló</div>
             <div class="col-span-3 md:col-span-2 text-right">Pontszám</div>
          </div>

          <div 
            v-for="(u, index) in leaderboard" 
            :key="u.id"
            class="grid grid-cols-12 gap-4 items-center p-5 border-b border-white/5 last:border-0 hover:bg-white/5 transition-all duration-200 group"
            :class="{ 'bg-blue-600/10 border-l-4 border-l-blue-500': u.id === currentUser?.id }"
          >
            <div class="col-span-2 md:col-span-1 text-center font-bold text-2xl" :class="getRankColor(index)">
               {{ getRankEmoji(index) }}
            </div>

            <div class="col-span-7 md:col-span-9 flex items-center gap-4 md:gap-6">
                <div class="w-12 h-12 rounded-full bg-[#0b1029] border border-white/10 flex items-center justify-center font-bold text-lg text-gray-400 group-hover:border-blue-500/50 group-hover:text-white transition-colors">
                    {{ u.full_name.charAt(0) }}
                </div>
                <div>
                    <div class="text-white font-bold text-lg" :class="{ 'text-blue-300': u.id === currentUser?.id }">
                        {{ u.full_name }} 
                        <span v-if="u.id === currentUser?.id" class="ml-2 text-xs bg-blue-500 text-white px-2 py-0.5 rounded-full">Te</span>
                    </div>
                    <div class="text-sm text-gray-500 group-hover:text-gray-400 transition-colors">{{ u.level }}. Szint</div>
                </div>
            </div>

            <div class="col-span-3 md:col-span-2 text-right font-mono font-bold text-xl text-blue-400 group-hover:text-blue-300">
                {{ u.xp }} XP
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="fixed bottom-0 left-0 w-full bg-[#0b1029]/90 backdrop-blur-md border-t border-white/10 p-4 z-40 shadow-[0_-10px_40px_rgba(0,0,0,0.5)]">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4">
                <span class="text-gray-400 text-sm uppercase font-bold hidden sm:inline">A te helyezésed:</span>
                <span class="text-3xl font-bold text-white">#{{ userRank }}</span>
            </div>
            <div class="flex items-center gap-3 bg-blue-600/20 px-4 py-2 rounded-xl border border-blue-500/30">
                <span class="text-blue-400 font-bold text-xl">{{ userXp }} XP</span>
            </div>
        </div>
    </div>

  </BaseLayout>
</template>