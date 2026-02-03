<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const isLoading = ref(false)
</script>

<template>
  <div class="min-h-screen bg-[#020617] flex items-center justify-center text-white relative overflow-hidden p-4 font-sans">
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
                </div>
        </div>
        <div class="bg-[#0b1121] p-8 md:p-12 flex flex-col justify-center w-full">
            <div class="text-center mb-8">
                <div class="text-5xl mb-4">🔐</div>
                <h2 class="text-3xl font-bold text-white">Jelszó visszaállítása</h2>
                <p class="text-slate-400 text-sm mt-2">Add meg az email címed, és küldünk egy linket.</p>
            </div>

            <form class="space-y-4 w-full max-w-sm mx-auto">
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
                    class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-lg shadow-lg shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5 active:scale-95 flex justify-center items-center gap-2 mt-4">
                    <span>Emlékeztető küldése</span>
                </button>
            </form>
            
            <div class="text-center mt-8 space-y-4">
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