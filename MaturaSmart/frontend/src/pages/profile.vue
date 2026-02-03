<script setup>
import { ref, onMounted, computed } from 'vue'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const user = ref(null)
const isLoading = ref(true)
const isEditing = ref(false)
const formName = ref('')

const profileMessage = ref({ text: '', type: '' })
const passwordMessage = ref({ text: '', type: '' })

// Jelszó form
const passwordForm = ref({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
})
const isChangingPassword = ref(false)

// Mock Jelvények
const badges = ref([
    { id: 1, name: 'Korai Madár', icon: '🌅', desc: 'Tanulás reggel 8 előtt', earned: true },
    { id: 2, name: 'Streak Mester', icon: '🔥', desc: '7 napos sorozat', earned: false },
    { id: 3, name: 'Matek Zseni', icon: '📐', desc: 'Hibátlan Pitagorasz teszt', earned: true },
    { id: 4, name: 'Szorgos', icon: '📚', desc: '5 lecke befejezése', earned: true },
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

onMounted(async () => {
    await fetchProfile()
})

const fetchProfile = async () => {
  const token = localStorage.getItem('token')
  if(!token) return 

  try {
      const storedUser = localStorage.getItem('user')
      if (storedUser) {
          user.value = JSON.parse(storedUser)
          formName.value = user.value.full_name
      }

      const res = await fetch('http://backend.maturasmart.hu/api/profile', {
         headers: { 'Authorization': `Bearer ${token}` }
      })
      if (res.ok) {
          const data = await res.json()
          user.value = data.user
      }
      
  } catch (e) {
      console.error(e)
  } finally {
      isLoading.value = false
  }
}

// Profil adat frissítés
const updateProfile = async () => {
  const token = localStorage.getItem('token')
  profileMessage.value = { text: '', type: '' }

  try {
    // ...
const response = await fetch('http://backend.maturasmart.hu/api/profile/update', {
  method: 'PUT',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({ 
      full_name: formName.value,
      email: user.value.email 
  })
})
    const data = await response.json()

    if (!response.ok) throw new Error(data.message || 'Hiba történt.')
    
    user.value.full_name = formName.value 
    if(data.user) user.value = data.user
    localStorage.setItem('user', JSON.stringify(user.value))
    
    profileMessage.value = { text: '✅ Adatok mentve!', type: 'success' }
    isEditing.value = false
    setTimeout(() => profileMessage.value = { text: '', type: '' }, 3000)

  } catch (error) {
    profileMessage.value = { text: '⚠️ ' + error.message, type: 'error' }
  }
}

// Jelszó frissítés
const updatePassword = async () => {
    const token = localStorage.getItem('token')
    passwordMessage.value = { text: '', type: '' }
    isChangingPassword.value = true

    if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
        passwordMessage.value = { text: '⚠️ A jelszavak nem egyeznek!', type: 'error' }
        isChangingPassword.value = false
        return
    }

    try {
        const response = await fetch('http://backend.maturasmart.hu/api/profile/password', {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                current_password: passwordForm.value.current_password,
                new_password: passwordForm.value.new_password,
                new_password_confirmation: passwordForm.value.new_password_confirmation
            })
        })

        const data = await response.json()

        if (!response.ok) throw new Error(data.message || 'Hiba a jelszó cseréjénél.')

        passwordMessage.value = { text: '✅ Jelszó sikeresen megváltoztatva!', type: 'success' }
        
        // Form törlése
        passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' }
        
        setTimeout(() => passwordMessage.value = { text: '', type: '' }, 3000)

    } catch (error) {
        passwordMessage.value = { text: '⚠️ ' + error.message, type: 'error' }
    } finally {
        isChangingPassword.value = false
    }
}
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div v-if="isLoading" class="flex justify-center items-center h-[80vh] bg-[#020617]">
        <div class="flex flex-col items-center gap-4">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            <div class="text-blue-500 font-bold uppercase tracking-widest text-xs">Profil betöltése...</div>
        </div>
    </div>

    <div v-else-if="user" class="min-h-screen pb-20 overflow-hidden relative bg-gradient-to-b from-[#0b1029] to-[#031625]">
      
      <div class="absolute inset-0 bg-[#020617]" 
           style="mask-image: radial-gradient(ellipse at center, black 40%, transparent 100%); -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 100%); pointer-events: none;">
      </div>

      <div class="fixed inset-0 z-0 pointer-events-none opacity-20" 
           style="background-image: linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px); background-size: 50px 50px; mask-image: radial-gradient(circle, black 30%, transparent 80%); -webkit-mask-image: radial-gradient(circle, black 30%, transparent 80%);">
      </div>

      <div class="relative h-64 w-full bg-gradient-to-r from-blue-900/80 to-[#0b1029]/80 overflow-hidden border-b border-white/5 z-10">
          <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
          <div class="absolute top-1/2 left-1/4 w-96 h-96 bg-blue-500/30 rounded-full blur-[100px]"></div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 -mt-24">
          
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-4 space-y-6">
                
                <div class="bg-[#1e293b]/90 backdrop-blur-xl border border-white/10 rounded-[40px] p-8 text-center shadow-2xl relative overflow-hidden group animate-fade-in-up">
                    
                    <div class="relative inline-block mb-6">
                        <div class="w-40 h-40 rounded-full p-1 bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-600 shadow-[0_0_40px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_60px_rgba(59,130,246,0.5)] transition-all duration-500">
                            <div class="w-full h-full rounded-full bg-[#0f172a] flex items-center justify-center text-6xl font-black text-white overflow-hidden relative">
                                <img v-if="user.avatar_url" :src="user.avatar_url" class="w-full h-full object-cover" alt="Avatar">
                                <span v-else>{{ user.full_name?.charAt(0).toUpperCase() }}</span>
                                
                                <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </div>
                        <div class="absolute -bottom-3 -right-3 w-12 h-12 bg-[#0f172a] rounded-full flex items-center justify-center border-4 border-[#1e293b]">
                            <span class="text-2xl">👑</span>
                        </div>
                    </div>

                    <h1 class="text-3xl font-black text-white mb-2 tracking-tight">{{ user.full_name }}</h1>
                    <p class="text-slate-400 text-sm font-medium mb-6 flex items-center justify-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        {{ user.email }}
                    </p>

                    <div class="bg-[#0f172a] rounded-2xl p-4 border border-white/5 mb-6 text-left relative overflow-hidden">
                        <div class="flex justify-between items-end mb-2 relative z-10">
                            <div>
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest block mb-1">Jelenlegi Szint</span>
                                <span class="text-2xl font-black text-white">{{ levelInfo.level }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-blue-400 font-bold text-sm">{{ user.xp }} XP</span>
                                <span class="text-slate-600 text-xs"> / {{ levelInfo.nextLevelXp }}</span>
                            </div>
                        </div>
                        
                        <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden relative z-10">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-all duration-1000" :style="{ width: levelInfo.progress + '%' }"></div>
                        </div>
                        <div class="text-[10px] text-slate-500 mt-2 text-center relative z-10">
                            Még <span class="text-white font-bold">{{ levelInfo.remainingXp }} XP</span> a szintlépéshez
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-center gap-2">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border" :class="user.role === 'admin' ? 'bg-red-500/10 border-red-500/20 text-red-400' : 'bg-blue-500/10 border-blue-500/20 text-blue-400'">
                            {{ user.role === 'student' ? 'diák' : user.role }}
                        </span>
                        <span v-if="user.graduation_year" class="px-3 py-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold uppercase tracking-wider">
                            🎓 {{ user.graduation_year }}
                        </span>
                    </div>

                </div>
            </div>

            <div class="lg:col-span-8 space-y-8">
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-[#1e293b]/80 backdrop-blur border border-white/5 p-5 rounded-[32px] relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-yellow-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                        <div class="text-3xl mb-2">🏆</div>
                        <div class="text-2xl font-black text-white">{{ user.xp }}</div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Összes XP</div>
                    </div>
                    <div class="bg-[#1e293b]/80 backdrop-blur border border-white/5 p-5 rounded-[32px] relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-blue-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                        <div class="text-3xl mb-2">⚡</div>
                        <div class="text-2xl font-black text-white">{{ levelInfo.level }}</div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Szint</div>
                    </div>
                    <div class="bg-[#1e293b]/80 backdrop-blur border border-white/5 p-5 rounded-[32px] relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-orange-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                        <div class="text-3xl mb-2">🔥</div>
                        <div class="text-2xl font-black text-white">{{ user.current_streak || 0 }}</div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Nap Streak</div>
                    </div>
                    <div class="bg-[#1e293b]/80 backdrop-blur border border-white/5 p-5 rounded-[32px] relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-green-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                        <div class="text-3xl mb-2">💎</div>
                        <div class="text-2xl font-black text-white">{{ user.gems || 0 }}</div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Drágakő</div>
                    </div>
                </div>

                <div class="bg-[#1e293b]/60 backdrop-blur border border-white/5 rounded-[40px] p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <h3 class="text-lg font-bold text-white uppercase tracking-wide">Jelvények</h3>
                        <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ badges.filter(b => b.earned).length }} / {{ badges.length }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4">
                        <div v-for="badge in badges" :key="badge.id" 
                             class="flex items-center gap-4 p-4 rounded-3xl border transition-all duration-300"
                             :class="badge.earned ? 'bg-[#0f172a] border-white/10 hover:border-blue-500/30' : 'bg-transparent border-transparent opacity-40 grayscale'">
                            
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl bg-[#1e293b] border border-white/5 shadow-inner">
                                {{ badge.icon }}
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-sm">{{ badge.name }}</h4>
                                <p class="text-xs text-slate-400">{{ badge.desc }}</p>
                            </div>
                            <div v-if="badge.earned" class="ml-auto text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1e293b]/60 backdrop-blur border border-white/5 rounded-[40px] p-8">
                     <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">👤</span>
                            <h3 class="text-lg font-bold text-white uppercase tracking-wide">Adataim</h3>
                        </div>
                        <button @click="isEditing = !isEditing" class="text-xs font-bold bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-lg transition-colors border border-white/10">
                            {{ isEditing ? 'Mégse' : '✏️ Szerkesztés' }}
                        </button>
                    </div>

                    <div v-if="profileMessage.text" class="mb-6 p-4 rounded-2xl text-sm font-bold flex items-center gap-2" :class="profileMessage.type === 'error' ? 'bg-red-500/10 text-red-400' : 'bg-green-500/10 text-green-400'">
                        <span>{{ profileMessage.type === 'error' ? '⚠️' : '✅' }}</span>
                        {{ profileMessage.text }}
                    </div>

                    <div class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Teljes Név</label>
                                <input v-if="isEditing" v-model="formName" type="text" class="w-full bg-[#0b1029] border border-blue-500/50 rounded-2xl px-4 py-3 text-white focus:outline-none shadow-[0_0_0_4px_rgba(59,130,246,0.1)] transition-all">
                                <div v-else class="w-full bg-[#0f172a] border border-white/5 rounded-2xl px-4 py-3 text-slate-300">{{ user.full_name }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Email Cím</label>
                                <div class="w-full bg-[#0f172a] border border-white/5 rounded-2xl px-4 py-3 text-slate-500 cursor-not-allowed flex items-center justify-between">
                                    {{ user.email }}
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd" /></svg>
                                </div>
                            </div>
                        </div>

                        <div v-if="isEditing" class="pt-4 flex justify-end">
                            <button @click="updateProfile" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-2xl shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0">
                                Adatok Mentése
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1e293b]/60 backdrop-blur border border-white/5 rounded-[40px] p-8">
                     <div class="flex items-center gap-3 mb-6">
                        <span class="text-2xl">🔒</span>
                        <h3 class="text-lg font-bold text-white uppercase tracking-wide">Biztonság</h3>
                    </div>

                    <div v-if="passwordMessage.text" class="mb-6 p-4 rounded-2xl text-sm font-bold flex items-center gap-2" :class="passwordMessage.type === 'error' ? 'bg-red-500/10 text-red-400' : 'bg-green-500/10 text-green-400'">
                        <span>{{ passwordMessage.type === 'error' ? '⚠️' : '✅' }}</span>
                        {{ passwordMessage.text }}
                    </div>

                    <form @submit.prevent="updatePassword" class="space-y-6">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Jelenlegi Jelszó</label>
                                <input v-model="passwordForm.current_password" type="password" required class="w-full bg-[#0b1029] border border-white/10 rounded-2xl px-4 py-3 text-white focus:border-blue-500 focus:outline-none transition-colors" placeholder="••••••••">
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Új Jelszó</label>
                                <input 
                                    v-model="passwordForm.new_password" 
                                    type="password" 
                                    required 
                                    minlength="8" 
                                    class="w-full bg-[#0b1029] border border-white/10 rounded-2xl px-4 py-3 text-white focus:border-blue-500 focus:outline-none transition-colors" 
                                    placeholder="••••••••"
                                >
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Új Jelszó Megerősítése</label>
                                <input 
                                    v-model="passwordForm.new_password_confirmation" 
                                    type="password" 
                                    required 
                                    minlength="8" 
                                    class="w-full bg-[#0b1029] border border-white/10 rounded-2xl px-4 py-3 text-white focus:border-blue-500 focus:outline-none transition-colors" 
                                    placeholder="••••••••"
                                >
                            </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" :disabled="isChangingPassword" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-8 rounded-2xl shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-wait flex items-center gap-2">
                                <span v-if="isChangingPassword" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                                <span>{{ isChangingPassword ? 'Mentés...' : 'Jelszó Módosítása' }}</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
          </div>

      </div>
    </div>
  </BaseLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>