<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const route = useRoute()
const router = useRouter()
const subject = ref(null)
const isLoading = ref(true)

const slug = route.params.subject

// Ideiglenes mock adatok (később backendről jöhetnek)
const mockStats = {
  progress: 35, // Százalékos haladás
  completed: 4,
  total: 12,
  difficulty: 'Közepes',
  estimatedHours: 18
}

onMounted(async () => {
  const token = localStorage.getItem('token')
  
  if (!token) {
    router.push('/login')
    return
  }

  try {
    const response = await fetch(`http://backend.vm1.test/api/tantargyak/${slug}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.status === 401) {
       localStorage.removeItem('token')
       router.push('/login')
       return
    }

    if (!response.ok) throw new Error('Nem található a tantárgy')
    
    subject.value = await response.json()
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
})

const progressColor = computed(() => {
  if (mockStats.progress < 30) return 'bg-red-500'
  if (mockStats.progress < 70) return 'bg-yellow-500'
  return 'bg-green-500'
})
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div v-if="isLoading" class="flex justify-center items-center h-[50vh]">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
    </div>

    <div v-else-if="subject" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <div class="bg-[#10194E] border border-white/10 rounded-3xl p-8 mb-8 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
          
          <div class="flex items-center gap-6">
            <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center text-5xl border border-white/20 shadow-lg backdrop-blur-sm">
              {{ subject.icon || '📘' }}
            </div>
            <div>
              <div class="flex items-center gap-3 mb-1">
                <RouterLink to="/main" class="text-xs font-bold text-gray-400 hover:text-white uppercase tracking-wider transition-colors">
                  ← Vissza
                </RouterLink>
                <span class="text-gray-600 text-xs">|</span>
                <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">Tantárgy</span>
              </div>
              <h1 class="text-4xl font-extrabold text-white tracking-tight">{{ subject.name }}</h1>
              <p class="text-gray-400 mt-1">Készülj fel az érettségire strukturáltan!</p>
            </div>
          </div>

          <div class="w-full md:w-1/3 bg-black/20 p-5 rounded-xl border border-white/5">
            <div class="flex justify-between items-end mb-2">
              <span class="text-gray-300 font-medium">Összesített haladás</span>
              <span class="text-2xl font-bold text-white">{{ mockStats.progress }}%</span>
            </div>
            <div class="w-full h-4 bg-gray-700/50 rounded-full overflow-hidden">
              <div 
                class="h-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(59,130,246,0.5)]" 
                :class="progressColor"
                :style="{ width: `${mockStats.progress}%` }"
              ></div>
            </div>
            <p class="text-xs text-gray-400 mt-2 text-right">
              Még {{ mockStats.total - mockStats.completed }} téma van hátra
            </p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-8 space-y-4">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-white">Témakörök</h2>
            <span class="text-sm text-gray-400">{{ subject.topics.length }} db lecke</span>
          </div>

          <div 
            v-for="(topic, index) in subject.topics" 
            :key="topic.id"
            class="group bg-[#1e293b]/50 hover:bg-[#1e293b] border border-white/10 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg hover:border-blue-500/50 cursor-pointer relative overflow-hidden"
          >
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300"></div>

            <div class="flex items-start justify-between gap-4">
              <div class="flex gap-5">
                <div class="flex flex-col items-center justify-center w-12 h-12 bg-white/5 rounded-xl border border-white/10 font-mono text-lg font-bold text-gray-500 group-hover:text-blue-400 group-hover:border-blue-500/30 transition-colors">
                  {{ index + 1 }}
                </div>

                <div>
                  <h3 class="text-xl font-bold text-white group-hover:text-blue-300 transition-colors">
                    {{ topic.title }}
                  </h3>
                  <p class="text-gray-400 text-sm mt-1 leading-relaxed max-w-xl">
                    {{ topic.description || 'Ismerd meg az alapokat és oldj meg gyakorló feladatokat.' }}
                  </p>
                  
                  <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1">⏱️ 45 perc</span>
                    <span class="flex items-center gap-1">📝 12 kérdés</span>
                  </div>
                </div>
              </div>

              <div class="flex flex-col items-end gap-2">
                <RouterLink 
                  :to="`/tantargyak/${slug}/${topic.slug}`"
                  class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-blue-900/20 whitespace-nowrap"
                >
                  Indítás
                </RouterLink>
                <span v-if="index === 0" class="text-xs text-green-400 font-medium">Folyamatban</span>
                 <span v-else class="text-xs text-gray-500">Még nem kezdted el</span>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
          
          <div class="bg-[#10194E] border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4 border-b border-white/10 pb-2">Statisztika</h3>
            
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-gray-400">Nehézség</span>
                <div class="flex text-yellow-500">
                  <span>★</span><span>★</span><span>★</span><span class="text-gray-600">★</span><span class="text-gray-600">★</span>
                </div>
              </div>
              
              <div class="flex justify-between items-center">
                <span class="text-gray-400">Becsült idő</span>
                <span class="text-white font-mono">{{ mockStats.estimatedHours }} óra</span>
              </div>

              <div class="flex justify-between items-center">
                <span class="text-gray-400">Megszerzett XP</span>
                <span class="text-purple-400 font-bold">+1250 XP</span>
              </div>
            </div>

            <button class="w-full mt-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-sm font-bold text-gray-300 transition-colors">
              📊 Részletes elemzés
            </button>
          </div>

          <div class="bg-gradient-to-br from-indigo-900 to-purple-900 rounded-2xl p-6 border border-white/10 relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-white font-bold text-lg mb-2">Tartsd a lendületet! 🔥</h3>
              <p class="text-indigo-200 text-sm mb-4">
                Ha minden nap teljesítesz egy leckét, 2 hét alatt végzel ezzel a tárggyal!
              </p>
              <div class="text-xs font-mono bg-black/20 inline-block px-3 py-1 rounded text-indigo-100">
                Következő cél: 3 nap streak
              </div>
            </div>
            <div class="absolute -bottom-4 -right-4 text-9xl opacity-10">🏆</div>
          </div>

        </div>
      </div>
    </div>

    <div v-else class="text-center py-20">
      <h1 class="text-3xl font-bold text-white mb-4">Hoppá! 😕</h1>
      <p class="text-gray-400 mb-6">Axel nem talált ilyen tantárgyat az oldalon.</p>
      <RouterLink to="/main" class="text-blue-400 hover:underline">Vissza a vezérlőpultra</RouterLink>
    </div>

  </BaseLayout>
</template>