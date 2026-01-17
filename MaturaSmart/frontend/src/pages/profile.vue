<script setup>
import { ref, onMounted, computed } from 'vue'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue" // Figyeld az import útvonalat!

const user = ref(null)
const isLoading = ref(true)
const isEditing = ref(false)
const formName = ref('')
const message = ref('')

// Alap statisztikák
const stats = ref([
  { label: 'Összes XP', value: '0', icon: '🏆', color: 'text-yellow-400' },
  { label: 'Szint', value: '1', icon: '⚡', color: 'text-blue-400' },
  { label: 'Streak', value: '0 nap', icon: '🔥', color: 'text-orange-500' },
  { label: 'Leckék', value: '0', icon: '📚', color: 'text-green-400' }
])

// Szint számítás
const levelInfo = computed(() => {
  if (!user.value) return { level: 1, nextLevelXp: 100, remainingXp: 100, progress: 0 }
  
  const xp = user.value.xp || 0
  const level = Math.floor(Math.sqrt(xp / 100)) + 1
  const xpForCurrentLevel = 100 * Math.pow(level - 1, 2)
  const xpForNextLevel = 100 * Math.pow(level, 2)
  const range = xpForNextLevel - xpForCurrentLevel
  const currentProgressXP = xp - xpForCurrentLevel
  const progressPercent = Math.min(Math.round((currentProgressXP / range) * 100), 100)

  return {
    level: level,
    nextLevelXp: xpForNextLevel,
    remainingXp: xpForNextLevel - xp,
    progress: progressPercent
  }
})

onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
      formName.value = user.value.full_name || ''
      
      // Adatok betöltése
      stats.value[0].value = user.value.xp || 0
      stats.value[1].value = levelInfo.value.level
      stats.value[2].value = (user.value.current_streak || 0) + ' nap'
      stats.value[3].value = user.value.xp ? Math.floor(user.value.xp / 50) : 0 
    } catch (e) {
      console.error(e)
    }
  }
  isLoading.value = false
})

const updateProfile = async () => {
  const token = localStorage.getItem('token')
  try {
    const response = await fetch('http://backend.vm1.test/api/user/profile', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ full_name: formName.value })
    })

    if (!response.ok) throw new Error('Hiba')
    const data = await response.json()
    
    user.value = data.user
    localStorage.setItem('user', JSON.stringify(data.user))
    
    message.value = '✅ Sikeres mentés!'
    isEditing.value = false
    setTimeout(() => message.value = '', 3000)
  } catch (error) {
    message.value = '❌ Hiba történt.'
  }
}
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div class="max-w-5xl mx-auto px-4 py-24 text-white">
      
      <div v-if="isLoading" class="text-center">Betöltés...</div>

      <div v-else-if="user" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-[#10194E] border border-white/10 rounded-3xl p-8 text-center shadow-2xl relative overflow-hidden h-fit">
           <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-blue-600/20 to-transparent pointer-events-none"></div>

           <div class="relative inline-block mb-4">
              <div class="w-32 h-32 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 p-1 shadow-lg shadow-blue-500/30">
                 <div class="w-full h-full rounded-full bg-[#10194E] flex items-center justify-center text-5xl font-bold text-white">
                    {{ user.full_name?.charAt(0) || 'U' }}
                 </div>
              </div>
              <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-blue-600 border-4 border-[#10194E] rounded-full flex items-center justify-center font-bold text-white text-sm">
                 {{ levelInfo.level }}
              </div>
           </div>

           <h2 class="text-2xl font-bold text-white mb-1">{{ user.full_name }}</h2>
           <p class="text-gray-400 text-sm mb-6">{{ user.email }}</p>

           <div class="text-left mb-6 bg-black/20 p-4 rounded-xl border border-white/5">
              <div class="flex justify-between text-xs mb-2">
                 <span class="text-blue-300 font-bold">{{ levelInfo.level }}. Szint</span>
                 <span class="text-gray-400">Következő: {{ levelInfo.nextLevelXp }} XP</span>
              </div>
              
              <div class="w-full h-3 bg-gray-700/50 rounded-full overflow-hidden relative">
                 <div 
                    class="h-full bg-gradient-to-r from-blue-500 to-indigo-400 transition-all duration-1000"
                    :style="{ width: levelInfo.progress + '%' }"
                 ></div>
              </div>
              
              <p class="text-xs text-center mt-2 text-gray-500">
                 Még <span class="text-white font-bold">{{ levelInfo.remainingXp }} XP</span> a {{ levelInfo.level + 1 }}. szinthez! 🚀
              </p>
           </div>

           <div class="inline-block bg-yellow-500/10 text-yellow-400 border border-yellow-500/50 px-4 py-1 rounded-full font-bold text-sm mb-6">
              {{ levelInfo.level < 5 ? 'Kezdő Diák 🎓' : 'Haladó Tanuló 🧠' }}
           </div>
        </div>

        <div class="md:col-span-2 space-y-8">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div v-for="stat in stats" :key="stat.label" class="bg-[#1e293b] border border-white/5 p-4 rounded-2xl text-center">
                    <div class="text-2xl mb-1">{{ stat.icon }}</div>
                    <div class="text-2xl font-bold text-white mb-1">{{ stat.value }}</div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide" :class="stat.color">{{ stat.label }}</div>
                </div>
            </div>

            <div class="bg-[#1e293b]/50 border border-white/10 rounded-3xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Profil beállítások</h3>
                    <button @click="isEditing = !isEditing" class="text-sm text-blue-400 font-bold">
                        {{ isEditing ? 'Mégse' : '✏️ Szerkesztés' }}
                    </button>
                </div>

                <div v-if="message" class="mb-4 p-3 rounded-xl text-center font-bold" :class="message.includes('Hiba') ? 'text-red-400 bg-red-500/10' : 'text-green-400 bg-green-500/10'">
                    {{ message }}
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Teljes név</label>
                        <input v-if="isEditing" v-model="formName" type="text" class="w-full bg-[#0b1029] border border-white/20 rounded-xl px-4 py-3 text-white focus:border-blue-500 outline-none">
                        <div v-else class="w-full bg-white/5 border border-transparent rounded-xl px-4 py-3 text-gray-300">{{ user.full_name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Email</label>
                        <div class="w-full bg-white/5 border border-transparent rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed">{{ user.email }}</div>
                    </div>
                    <button v-if="isEditing" @click="updateProfile" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-xl">Mentés</button>
                </div>
            </div>
        </div>

      </div>
    </div>
  </BaseLayout>
</template>