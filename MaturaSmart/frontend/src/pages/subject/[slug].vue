<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const route = useRoute()
const subject = ref(null)
const isLoading = ref(true)

const slug = route.params.slug

onMounted(async () => {
  try {
    const response = await fetch(`http://backend.vm1.test/api/subjects/${slug}`)
    
    if (!response.ok) throw new Error('Nem található a tantárgy')
    
    subject.value = await response.json()
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div class="max-w-4xl mx-auto mt-8">
      
      <RouterLink to="/" class="text-gray-400 hover:text-white mb-6 inline-block transition-colors">
        ← Vissza a főoldalra
      </RouterLink>

      <div v-if="isLoading" class="text-white text-center py-10">
        Témakörök betöltése...
      </div>

      <div v-else-if="subject">
        
        <div class="flex items-center gap-4 mb-8">
          <div class="text-5xl bg-white/10 p-4 rounded-2xl border border-white/20">
            {{ subject.icon }}
          </div>
          <div>
            <h1 class="text-3xl font-bold text-white">{{ subject.name }}</h1>
            <p class="text-gray-400">Válassz egy témakört a tanuláshoz</p>
          </div>
        </div>

        <div class="space-y-4">
          
          <div 
            v-for="(topic, index) in subject.topics" 
            :key="topic.id"
            class="topic-card group"
          >
            <div class="flex items-center justify-between p-6">
              
              <div class="flex items-center gap-5">
                <span class="text-2xl font-bold text-gray-500 group-hover:text-blue-400 transition-colors">
                  {{ String(index + 1).padStart(2, '0') }}
                </span>
                
                <div>
                  <h3 class="text-xl font-bold text-white group-hover:text-blue-300 transition-colors">
                    {{ topic.title }}
                  </h3>
                  <p class="text-sm text-gray-400 mt-1">
                    {{ topic.description || 'Nincs leírás megadva.' }}
                  </p>
                </div>
              </div>

              <RouterLink 
                :to="`/topic/${topic.id}`" 
                class="start-btn opacity-0 group-hover:opacity-100 transition-all translate-x-4 group-hover:translate-x-0"
              >
                Indítás ▶️
              </RouterLink>

            </div>
          </div>

        </div>
      </div>

      <div v-else class="text-center py-20 text-gray-400">
        <h2 class="text-2xl font-bold">404 - A tantárgy nem található 😕</h2>
        <RouterLink to="/" class="text-blue-400 underline mt-4 block">Vissza a főoldalra</RouterLink>
      </div>

    </div>
  </BaseLayout>
</template>

<style scoped>
.topic-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  transition: all 0.3s ease;
  cursor: pointer;
  overflow: hidden;
}

.topic-card:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(91, 111, 255, 0.5);
  transform: translateX(5px);
}

.start-btn {
  background: #5b6fff;
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
}

.start-btn:hover {
  background: #7d8dff;
}
</style>