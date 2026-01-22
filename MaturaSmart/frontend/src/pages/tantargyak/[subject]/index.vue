<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const route = useRoute()
const router = useRouter()
const subject = ref(null)
const isLoading = ref(true)
const slug = route.params.subject

onMounted(async () => {
  const token = localStorage.getItem('token')
  if (!token) { router.push('/login'); return }

  try {
    const response = await fetch(`http://backend.vm1.test/api/tantargyak/${slug}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (response.status === 401) {
        localStorage.removeItem('token')
        router.push('/login')
        return
    }

    if (!response.ok) throw new Error('Hiba a betöltéskor')
    
    subject.value = await response.json()
    
    if (subject.value) {
        document.title = `${subject.value.name} | MaturaSmart`
    }
  } catch (error) {
    console.error("Hiba történt:", error)
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div v-if="isLoading" class="flex justify-center items-center h-[50vh]">
      <div class="relative w-20 h-20">
         <div class="absolute inset-0 border-4 border-blue-500/30 rounded-full animate-ping"></div>
         <div class="absolute inset-0 border-4 border-t-blue-500 rounded-full animate-spin"></div>
      </div>
    </div>

    <div v-else-if="subject" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-32">
      
      <div class="bg-[#10194E] border border-white/10 rounded-3xl p-8 mb-12 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/20 rounded-full blur-[100px] pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center text-5xl border border-white/20 shadow-lg backdrop-blur-sm">
                    {{ subject.icon || '📚' }}
                </div>
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">{{ subject.name }}</h1>
                    <p class="text-gray-400 max-w-lg">{{ subject.description }}</p>
                    
                    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-400">
                        <span class="bg-blue-900/50 px-3 py-1 rounded-lg border border-blue-500/30 text-blue-300 flex items-center gap-2">
                            <span>📚</span> {{ subject.stats?.total || 0 }} Lecke
                        </span>
                        <span class="flex items-center gap-2">
                            <span>⏱️</span> {{ subject.stats?.estimated_hours || 0 }} óra tanulás
                        </span>
                    </div>
                </div>
            </div>

            <div class="relative w-32 h-32 flex items-center justify-center shrink-0">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/20" />
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" 
                            :stroke-dasharray="180" 
                            :stroke-dashoffset="365 - (365 * (subject.stats?.progress || 0)) / 100"
                            class="text-blue-500 transition-all duration-1000 ease-out shadow-[0_0_15px_rgba(59,130,246,0.5)]" />
                </svg>
                <div class="absolute text-center">
                    <span class="text-2xl font-bold text-white">{{ subject.stats?.progress || 0 }}%</span>
                </div>
            </div>
        </div>
      </div>

      <div class="space-y-16">
        
        <div v-for="unit in (subject.units || [])" :key="unit.id" class="relative">
            
            <div class="sticky top-20 z-30 mb-8 pl-12 md:pl-0">
                <div class="inline-flex items-center gap-3 bg-[#0f172a]/95 backdrop-blur-xl border border-white/10 px-6 py-2 rounded-xl shadow-lg">
                    <span class="text-yellow-400 text-lg">📂</span>
                    <h2 class="text-lg font-bold text-white">{{ unit.title }}</h2>
                    <span class="text-xs text-gray-500 border-l border-gray-600 pl-2 ml-2">
                        {{ unit.topics?.length || 0 }} lecke
                    </span>
                </div>
            </div>

            <div class="relative pl-4 md:pl-4">
                <div class="absolute left-[19px] top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500/10 via-blue-500/40 to-blue-500/10"></div>

                <div class="space-y-8">
                    <div 
                        v-for="(topic, index) in (unit.topics || [])" 
                        :key="topic.id"
                        class="relative pl-12"
                    >
                        
                        <div class="absolute left-[11px] top-8 w-4 h-4 rounded-full border-[3px] border-[#020617] z-20 transition-all duration-300 shadow-lg"
                             :class="topic.is_completed ? 'bg-green-500 shadow-[0_0_10px_#22c55e]' : 'bg-blue-600'">
                        </div>
                        
                        <div v-if="topic.year_label" class="absolute left-[-50px] top-8 w-[50px] text-right pr-4 text-[10px] font-mono font-bold text-blue-400/70 hidden lg:block">
                            {{ topic.year_label }}
                        </div>

                        <RouterLink :to="`/tantargyak/${slug}/${topic.slug}`" class="block group/card">
                            <div class="bg-[#1e293b]/40 hover:bg-[#1e293b] border border-white/10 rounded-2xl p-6 transition-all duration-300 hover:translate-x-2 hover:shadow-xl hover:border-blue-500/30 relative overflow-hidden flex flex-col md:flex-row gap-6 items-start md:items-center"
                                 :class="{'border-green-500/20 bg-green-900/5': topic.is_completed}">
                                
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span v-if="topic.year_label" class="lg:hidden inline-block text-[10px] font-mono text-blue-400 bg-blue-900/30 px-1.5 py-0.5 rounded border border-blue-500/20">
                                            {{ topic.year_label }}
                                        </span>
                                        <h3 class="text-xl font-bold text-white group-hover/card:text-blue-300 transition-colors">
                                            {{ topic.title }}
                                        </h3>
                                        <span v-if="topic.is_completed" class="bg-green-500/10 text-green-500 text-[10px] font-bold px-2 py-0.5 rounded border border-green-500/20">
                                            KÉSZ
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-400 text-sm leading-relaxed max-w-3xl">
                                        {{ topic.description }}
                                    </p>
                                    
                                    <div class="mt-3 flex items-center gap-4 text-xs font-medium text-gray-500">
                                        <span class="flex items-center gap-1">⏱️ 45 perc</span>
                                        <span class="flex items-center gap-1">📝 12 kérdés</span>
                                        <span class="text-yellow-500/80 flex items-center gap-1">⭐ {{ topic.xp || 150 }} XP</span>
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/5 group-hover/card:bg-blue-600 group-hover/card:text-white transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </span>
                                </div>

                                <div class="absolute left-0 top-0 bottom-0 w-1 transition-colors duration-300"
                                     :class="topic.is_completed ? 'bg-green-500' : 'bg-blue-500/30 group-hover/card:bg-blue-500'">
                                </div>

                            </div>
                        </RouterLink>

                    </div>
                </div>
            </div>

        </div>

      </div>

    </div>

    <div v-else class="text-center py-20">
      <div class="text-6xl mb-4">😕</div>
      <h1 class="text-3xl font-bold text-white mb-4">Hoppá!</h1>
      <p class="text-gray-400 mb-6">Nem sikerült betölteni az adatokat.</p>
      <RouterLink to="/main" class="inline-block bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-xl transition-colors">
        Vissza a vezérlőpultra
      </RouterLink>
    </div>

  </BaseLayout>
</template>