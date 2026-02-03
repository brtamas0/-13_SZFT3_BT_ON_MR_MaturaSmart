<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const isLoading = ref(false)
const isDark = ref(true)
const errorMessage = ref('')
const successMessage = ref('')

const handleForgotPassword = async () => {
  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    // Backend API
    const response = await fetch('http://backend.maturasmart.hu/api/forgot-password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: email.value
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Nem található felhasználó ezzel az email címmel.')
    }

    // Sikeres küldés
    successMessage.value = 'Az emlékeztető emailt elküldtük! Nézd meg a spam mappát is. 📧'
    email.value = ''

  } catch (error) {
    console.error(error)
    errorMessage.value = error.message
  } finally {
    isLoading.value = false
  }
}

const toggleTheme = () => {
  const html = document.documentElement
  const currentTheme = html.getAttribute('data-theme')
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark'
  
  html.setAttribute('data-theme', newTheme)
  localStorage.setItem('theme', newTheme)
  isDark.value = newTheme === 'dark'
}

onMounted(() => {
  const saved = localStorage.getItem('theme') || 'dark'
  document.documentElement.setAttribute('data-theme', saved)
  isDark.value = saved === 'dark'
})
</script>

<template>
  <div class="min-h-screen bg-[#020617] flex items-center justify-center text-white relative overflow-hidden p-4 font-sans">
    
    <button 
      @click="toggleTheme" 
      class="absolute top-5 right-5 z-50 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md transition-all cursor-pointer border border-white/5 shadow-lg"
      title="Téma váltása"
    >
      <span v-if="isDark">🌓</span> <span v-else>☀️</span>
    </button>

    <div class="relative w-full max-w-5xl z-10">
      <div class="grid md:grid-cols-2 rounded-3xl overflow-hidden border border-white/10 shadow-2xl bg-[#020617]">
        
        <div class="hidden md:flex bg-gradient-to-br from-[#0f172a] via-[#1e1b4b] to-[#312e81] p-8 md:p-12 relative flex-col min-h-[600px] overflow-hidden">
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/20 blur-[100px] rounded-full pointer-events-none"></div>

            <div class="relative z-20 self-start animate-fade-in-down">
                <div class="bg-white text-slate-900 text-sm font-medium px-5 py-3 rounded-2xl rounded-br-none shadow-xl transform -rotate-2 max-w-[240px]">
                    Szia! 👋 Ne aggódj, mindenkivel megesik.<br />
                    Segítek új jelszót kérni! 🔑
                </div>
            </div>

            <div class="flex-grow flex items-center justify-center relative z-10 my-4">
                 <img 
                    src="/axel.png" 
                    alt="Axel" 
                    class="w-64 md:w-80 h-auto object-contain drop-shadow-[0_20px_50px_rgba(0,0,0,0.5)]" 
                    style="animation: floatHero 6s ease-in-out infinite;"
                 />
            </div>

            <div class="relative z-20">
                <p class="text-4xl font-extrabold leading-tight tracking-tight">
                    Matura<span class="text-indigo-400">Smart</span>
                </p>
                <h2 class="text-2xl font-bold mt-2 text-indigo-100">
                    Elfelejtett jelszó? <br />
                    Pillanatok alatt megoldjuk.
                </h2>
            </div>
        </div>

        <div class="bg-[#0b1121] p-8 md:p-12 flex flex-col justify-center w-full">
            
            <div class="text-center mb-8">
                <div class="text-5xl mb-4 inline-block transform hover:scale-110 transition duration-300">🔐</div>
                <h2 class="text-3xl font-bold text-white">Jelszó visszaállítása</h2>
                <p class="text-slate-400 text-sm mt-2">Add meg az email címed, és küldünk egy linket.</p>
            </div>

            <div v-if="errorMessage" class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-xl text-red-200 text-sm text-center w-full max-w-sm mx-auto animate-pulse">
                ⚠️ {{ errorMessage }}
            </div>

            <div v-if="successMessage" class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-xl text-green-200 text-sm text-center w-full max-w-sm mx-auto">
                {{ successMessage }}
            </div>

            <form v-if="!successMessage" @submit.prevent="handleForgotPassword" class="space-y-4 w-full max-w-sm mx-auto">
                
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase text-slate-500 ml-1">Email cím</label>
                    <input 
                        v-model="email"
                        type="email" 
                        placeholder="pelda@email.com"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                        required
                    >
                </div>

                <button 
                    type="submit" 
                    :disabled="isLoading"
                    class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-lg shadow-lg shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5 active:scale-95 flex justify-center items-center gap-2 mt-4">
                    <span v-if="isLoading" class="animate-spin">⏳</span>
                    <span v-else>Emlékeztető küldése</span>
                </button>
            </form>
            
            <div class="text-center mt-8 space-y-4">
                 <p class="text-sm text-slate-500">
                  Mégis eszedbe jutott?
                </p>
                <RouterLink to="/login" class="inline-block px-6 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors text-sm font-bold">
                    ← Vissza a belépéshez
                </RouterLink>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes fade-in-down {
  0% { opacity: 0; transform: translateY(-10px) rotate(-1deg); }
  100% { opacity: 1; transform: translateY(0) rotate(-1deg); }
}

@keyframes floatHero {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

.animate-fade-in-down {
  animation: fade-in-down 0.8s ease-out forwards;
}
</style>