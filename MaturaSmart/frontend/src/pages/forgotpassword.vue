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
        
        <div class="hidden md:flex bg-gradient-to-br from-[#0f172a] via-[#1e1b4b] to-[#312e81] p-12 relative flex-col min-h-[600px]">
           <div class="relative z-20 mt-auto">
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