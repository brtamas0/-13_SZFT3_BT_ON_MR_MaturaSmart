<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const fullName = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

const handleRegister = async () => {
  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = "A jelszavak nem egyeznek!"
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('http://backend.maturasmart.hu/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        full_name: fullName.value,
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value
      })
    })

    const data = await response.json()

    if (!response.ok) {
        
        const errorText = data.message || 'Hiba a regisztráció során.'
        if (data.errors?.email) throw new Error('Ez az email cím már foglalt!')
        throw new Error(errorText)
    }
    
    localStorage.setItem('token', data.token)
    localStorage.setItem('user', JSON.stringify(data.user))

    router.push('/main')

  } catch (error) {
    errorMessage.value = error.message
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-[#020617] flex items-center justify-center text-white p-4 font-sans relative overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative w-full max-w-5xl z-10 grid md:grid-cols-2 rounded-3xl overflow-hidden border border-white/10 shadow-2xl bg-[#0b1029]/80 backdrop-blur-sm">
        
        <div class="hidden md:flex bg-gradient-to-br from-[#0f172a] via-[#1e1b4b] to-[#312e81] p-12 flex-col justify-between relative overflow-hidden">
            <div class="relative z-20">
                <div class="bg-white/10 backdrop-blur-md border border-white/10 p-4 rounded-2xl inline-block mb-6">
                    🚀 Csatlakozz a jövő tanulóihoz!
                </div>
                <h1 class="text-4xl font-extrabold leading-tight">
                    Hozd ki magadból<br/>a maximumot.
                </h1>
                <p class="text-indigo-200 mt-4 text-lg">
                    A MaturaSmart segít, hogy kevesebb stresszel, hatékonyabban készülj az érettségire.
                </p>
            </div>

            <div class="relative z-10 mt-10 flex justify-center">
                 <div class="text-9xl animate-bounce">🎓</div>
            </div>

            <div class="relative z-20 mt-auto">
                <div class="flex items-center gap-2 text-sm text-indigo-300">
                    <span>✅ Ingyenes regisztráció</span>
                    <span>•</span>
                    <span>✅ Azonnali hozzáférés</span>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12 flex flex-col justify-center w-full">
            
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-white">Fiók létrehozása</h2>
                <p class="text-slate-400 text-sm mt-2">Add meg adataidat a regisztrációhoz</p>
            </div>

            <div v-if="errorMessage" class="mb-6 p-4 bg-red-500/10 border border-red-500/50 rounded-xl text-red-200 text-sm flex items-center gap-3">
                ⚠️ <span>{{ errorMessage }}</span>
            </div>

            <form @submit.prevent="handleRegister" class="space-y-4">
                
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase text-slate-500 ml-1">Teljes név</label>
                    <input 
                        v-model="fullName"
                        type="text" 
                        placeholder="Kovács Anna"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                        required
                    >
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase text-slate-500 ml-1">Email cím</label>
                    <input 
                        v-model="email"
                        type="email" 
                        placeholder="pelda@email.com"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                        required
                    >
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase text-slate-500 ml-1">Jelszó</label>
                    <input 
                        v-model="password"
                        type="password" 
                        placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                        required
                        minlength="6"
                    >
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase text-slate-500 ml-1">Jelszó megerősítése</label>
                    <input 
                        v-model="passwordConfirmation"
                        type="password" 
                        placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                        required
                    >
                </div>

                <button 
                    type="submit" 
                    :disabled="isLoading"
                    class="w-full py-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-lg shadow-lg shadow-blue-900/30 transition-all transform hover:-translate-y-0.5 active:scale-95 flex justify-center items-center gap-2 mt-4">
                    <span v-if="isLoading" class="animate-spin">⏳</span>
                    <span v-else>Regisztrálok</span>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-slate-400">
                    Már van fiókod? 
                    <RouterLink to="/login" class="text-blue-400 font-bold hover:text-blue-300 hover:underline">
                        Jelentkezz be itt
                    </RouterLink>
                </p>
                <RouterLink to="/" class="block mt-4 text-xs text-slate-600 hover:text-slate-400 transition-colors">
                    ← Vissza a főoldalra
                </RouterLink>
            </div>
        </div>
    </div>
  </div>
</template>