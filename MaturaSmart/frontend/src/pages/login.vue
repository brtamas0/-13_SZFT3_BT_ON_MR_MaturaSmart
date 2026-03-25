<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const password = ref('')
const isLoading = ref(false)
const isDark = ref(true)
const errorMessage = ref('')

const handleGoogleLogin = () => {
  window.location.href = 'http://backend.maturasmart.hu/auth/google/redirect'
}

const handleEmailLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('http://backend.maturasmart.hu/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Hiba a bejelentkezéskor')
    }

    localStorage.setItem('token', data.token)

    localStorage.setItem('user', JSON.stringify(data.user))

    router.push('/main')

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
  <div
    class="auth-login-page min-h-screen bg-[#020617] flex items-center justify-center text-white relative overflow-hidden p-4 font-sans">
  <!--
    <button @click="toggleTheme"
      class="absolute top-5 right-5 z-50 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md transition-all cursor-pointer border border-white/5 shadow-lg"
      title="Téma váltása">
      <span v-if="isDark">🌓</span> <span v-else>☀️</span>
    </button>
  -->
    <div class="relative w-full max-w-5xl z-10">
      <div class="grid md:grid-cols-2 rounded-3xl overflow-hidden border border-white/10 shadow-2xl bg-[#020617]">

        <div
          class="hidden md:flex bg-gradient-to-br from-[#0f172a] via-[#1e1b4b] to-[#312e81] p-8 md:p-12 relative flex-col min-h-[600px] overflow-hidden">
          <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/20 blur-[100px] rounded-full pointer-events-none">
          </div>

          <div class="relative z-20 self-start animate-fade-in-down">
            <div
              class="bg-white text-slate-900 text-sm font-medium px-5 py-3 rounded-2xl rounded-br-none shadow-xl transform -rotate-2 max-w-[220px]">
              Szia! 👋 Én vagyok Axel.<br />
              Lépj be, és pörgessük fel az agyad! 🚀
            </div>
          </div>

          <div class="flex-grow flex items-center justify-center relative z-10 my-4">
            <img src="/axel.png" alt="Axel"
              class="w-64 md:w-80 h-auto object-contain drop-shadow-[0_20px_50px_rgba(0,0,0,0.5)]"
              style="animation: floatHero 6s ease-in-out infinite;" />
          </div>

          <div class="relative z-20">
            <p class="text-4xl font-extrabold leading-tight tracking-tight">
              Matura<span class="text-indigo-400">Smart</span>
            </p>
            <h2 class="text-2xl font-bold mt-2 text-indigo-100">
              Tanulj okosabban,<br />ne keményebben.
            </h2>
            <p class="text-sm text-indigo-200/60 mt-4 max-w-xs leading-relaxed">
              A mesterséges intelligencia által támogatott személyes felkészítőd a 2025-ös érettségire.
            </p>
          </div>
        </div>

        <div class="bg-[#0b1121] p-8 md:p-12 flex flex-col justify-center w-full">

          <div class="text-center mb-8">
            <div class="text-5xl mb-4 inline-block transform hover:scale-110 transition duration-300">🚀</div>
            <h2 class="text-3xl font-bold text-white">Kezdjük el!</h2>
            <p class="text-slate-400 text-sm mt-2">Add meg adataidat a belépéshez</p>
          </div>

          <div v-if="errorMessage"
            class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-xl text-red-200 text-sm text-center w-full max-w-sm mx-auto">
            ⚠️ {{ errorMessage }}
          </div>

          <form @submit.prevent="handleEmailLogin" class="space-y-4 w-full max-w-sm mx-auto">

            <div class="space-y-1">
              <label class="text-xs font-bold uppercase text-slate-500 ml-1">Email cím</label>
              <input v-model="email" type="email" placeholder="test@example.com"
                class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                required>
            </div>

            <div class="space-y-1">
              <input v-model="password" type="password" placeholder="••••••••"
                class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                required>
              <div class="flex justify-between ml-1">
                <label class="text-xs font-bold uppercase text-slate-500">Jelszó</label>
                <RouterLink to="/forgot-password" class="text-xs text-indigo-400 hover:text-indigo-300">
                  Elfelejtetted?
                </RouterLink>
              </div>
            </div>

            <button type="submit" :disabled="isLoading"
              class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-lg shadow-lg shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5 active:scale-95 flex justify-center items-center gap-2 mt-2">
              <span v-if="isLoading" class="animate-spin">⏳</span>
              <span v-else>Belépés</span>
            </button>
          </form>

          <div class="relative flex py-6 items-center w-full max-w-sm mx-auto">
            <div class="flex-grow border-t border-slate-700"></div>
            <span class="flex-shrink-0 mx-4 text-slate-500 text-xs uppercase font-bold">Vagy</span>
            <div class="flex-grow border-t border-slate-700"></div>
          </div>

          <div class="w-full max-w-sm mx-auto">
            <button @click="handleGoogleLogin"
              class="w-full flex items-center justify-center gap-3 px-6 py-3.5 bg-white text-slate-900 rounded-xl font-bold hover:bg-slate-100 transition-all shadow-md transform hover:-translate-y-0.5">
              <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path
                  d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                  fill="#4285F4" />
                <path
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  fill="#34A853" />
                <path
                  d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.84z"
                  fill="#FBBC05" />
                <path
                  d="M12 4.36c1.6 0 3.06.56 4.23 1.69l3.18-3.18C17.45 1.14 14.97 0 12 0 7.7 0 3.99 2.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  fill="#EA4335" />
              </svg>
              <span>Google fiókkal</span>
            </button>
          </div>

          <p class="text-center mt-8 text-sm text-slate-500">
            Nincs még fiókod?
            <RouterLink to="/register" class="text-indigo-400 font-bold hover:text-indigo-300 hover:underline">
              Regisztrálj ingyen
            </RouterLink>
          </p>

          <router-link to="/" class="mt-4 text-xs text-center text-slate-600 hover:text-slate-400 transition-colors">
            ← Vissza a főoldalra
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes fade-in-down {
  0% {
    opacity: 0;
    transform: translateY(-10px) rotate(-1deg);
  }

  100% {
    opacity: 1;
    transform: translateY(0) rotate(-1deg);
  }
}

.animate-fade-in-down {
  animation: fade-in-down 0.8s ease-out forwards;
}

[data-theme="light"] .auth-login-page {
  background: linear-gradient(180deg, var(--surface-1), var(--surface-2));
  color: var(--text-primary);
}

[data-theme="light"] .auth-login-page [class*="bg-[#020617]"],
[data-theme="light"] .auth-login-page [class*="bg-[#0b1121]"] {
  background: rgba(255, 255, 255, 0.96) !important;
  border-color: rgba(45, 114, 182, 0.2) !important;
}

[data-theme="light"] .auth-login-page [class*="from-[#0f172a]"],
[data-theme="light"] .auth-login-page [class*="via-[#1e1b4b]"],
[data-theme="light"] .auth-login-page [class*="to-[#312e81]"] {
  background: linear-gradient(145deg, #e4f1fa, #aedae1) !important;
}

[data-theme="light"] .auth-login-page input {
  background: #ffffff !important;
  color: var(--text-primary) !important;
  border-color: rgba(45, 114, 182, 0.25) !important;
}
</style>
