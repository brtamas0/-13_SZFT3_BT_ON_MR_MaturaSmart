<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const route = useRoute()
const topic = ref(null)
const isLoading = ref(true)
const selectedAnswers = ref({}) 

// URL paraméterek kinyerése
// Mivel a mappa neve [subject], a fájl neve pedig [topic].vue:
const subjectSlug = route.params.subject 
const topicSlug = route.params.topic 

onMounted(async () => {
  try {
    // Adatok lekérése a Backendről a topic slug alapján
    const response = await fetch(`http://backend.vm1.test/api/topics/${topicSlug}`)
    
    if (!response.ok) throw new Error('Hiba a betöltéskor')
    
    topic.value = await response.json()
  } catch (error) {
    console.error("Hiba történt:", error)
  } finally {
    isLoading.value = false
  }
})

// --- KVÍZ LOGIKA ---

// Válasz kiválasztása
const selectAnswer = (questionId, answer) => {
  // Ha már válaszolt erre a kérdésre, nem engedjük újra
  if (selectedAnswers.value[questionId]) return;

  // Eltároljuk a választást
  selectedAnswers.value[questionId] = {
    id: answer.id,
    isCorrect: answer.is_correct
  }
}

// Gombok színezése az eredmény alapján
const getAnswerClass = (questionId, answer) => {
  const selection = selectedAnswers.value[questionId]
  
  // 1. Alapállapot (még nem válaszolt)
  if (!selection) return 'bg-white/5 hover:bg-white/10 border-white/10 text-gray-300'

  // 2. Ezt a gombot nyomta meg a felhasználó
  if (selection.id === answer.id) {
    return answer.is_correct 
      ? 'bg-green-500/20 border-green-500 text-green-400 font-bold' // Helyes volt
      : 'bg-red-500/20 border-red-500 text-red-400' // Helytelen volt
  }

  // 3. Ez a gomb a helyes válasz (de a felhasználó mást nyomott) -> Megmutatjuk a megoldást
  if (answer.is_correct && selection.id !== answer.id) {
    return 'bg-green-500/10 border-green-500/50 text-green-500/70' 
  }

  // 4. Egyéb gombok inaktívvá tétele
  return 'opacity-50 cursor-not-allowed border-transparent'
}
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div v-if="isLoading" class="flex justify-center items-center h-[60vh]">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
    </div>

    <div v-else-if="topic" class="max-w-5xl mx-auto px-4 py-8">
      
      <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <RouterLink to="/" class="hover:text-white transition-colors">Vezérlőpult</RouterLink>
        <span>/</span>
        <RouterLink 
          :to="`/tantargyak/${subjectSlug}`" 
          class="hover:text-white transition-colors capitalize"
        >
          {{ subjectSlug }}
        </RouterLink>
        <span>/</span>
        <span class="text-white font-medium">{{ topic.title }}</span>
      </nav>

      <div class="bg-[#10194E] border border-white/10 rounded-3xl p-8 mb-10 shadow-2xl relative overflow-hidden">
        <div class="relative z-10">
          <div class="flex items-center gap-3 mb-2">
            <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">Lecke</span>
            <span class="text-gray-400 text-xs">Becsült idő: 15 perc</span>
          </div>
          <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4">{{ topic.title }}</h1>
          <p class="text-xl text-blue-200 font-light max-w-2xl">
            {{ topic.description }}
          </p>
        </div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
      </div>

      <div class="prose prose-invert prose-lg max-w-none mb-16 text-gray-300">
        
        <div class="bg-[#1e293b]/50 border-l-4 border-blue-500 p-6 rounded-r-xl mb-8">
          <h3 class="text-white font-bold text-xl mb-2">👋 Tanuljunk!</h3>
          <p>Olvasd el az anyagot, majd válaszolj a lenti kérdésekre.</p>
        </div>

        <div v-if="topic.content" v-html="topic.content"></div>
        
        <div v-else class="text-gray-500 italic p-4 border border-dashed border-gray-600 rounded-lg text-center">
          Ehhez a leckéhez még nincs feltöltve részletes tananyag.
        </div>

      </div>

      <div class="w-full h-px bg-white/10 my-12"></div>

      <div>
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-3xl font-bold text-white">Gyakorlás 📝</h2>
          <span class="bg-white/10 text-white px-3 py-1 rounded-full text-sm">
            {{ topic.questions.length }} kérdés
          </span>
        </div>

        <div class="space-y-8">
          <div 
            v-for="(question, index) in topic.questions" 
            :key="question.id" 
            class="bg-[#1e293b] border border-white/5 rounded-2xl p-6 md:p-8 hover:border-white/10 transition-colors"
          >
            <div class="flex gap-4 mb-6">
              <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 font-bold text-sm">
                {{ index + 1 }}
              </span>
              <h3 class="text-xl font-semibold text-white">{{ question.content }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <button 
                v-for="answer in question.answers" 
                :key="answer.id"
                @click="selectAnswer(question.id, answer)"
                :class="getAnswerClass(question.id, answer)"
                class="text-left p-4 rounded-xl border transition-all duration-200 relative overflow-hidden group"
              >
                <span class="relative z-10">{{ answer.text }}</span>
                <span v-if="selectedAnswers[question.id]?.id === answer.id" class="absolute right-4 top-1/2 -translate-y-1/2">
                  <span v-if="answer.is_correct">✅</span>
                  <span v-else>❌</span>
                </span>
              </button>
            </div>
            
            <div 
              v-if="selectedAnswers[question.id] && question.explanation" 
              class="mt-4 p-4 bg-blue-900/20 border border-blue-500/30 rounded-lg text-sm text-blue-200"
            >
              <strong>💡 Magyarázat:</strong> {{ question.explanation }}
            </div>
          </div>
        </div>

        <div class="mt-16 flex justify-center pb-20">
          <button class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 text-white text-lg font-bold py-4 px-10 rounded-2xl shadow-lg transition-transform hover:-translate-y-1">
            Lecke befejezése 🎉
          </button>
        </div>
      </div>

    </div>

    <div v-else class="text-center py-20">
      <h1 class="text-3xl font-bold text-white mb-4">Hoppá! 😕</h1>
      <p class="text-gray-400">Nem sikerült betölteni a leckét.</p>
      <RouterLink to="/" class="text-blue-400 hover:underline mt-4 inline-block">Vissza a főoldalra</RouterLink>
    </div>

  </BaseLayout>
</template>