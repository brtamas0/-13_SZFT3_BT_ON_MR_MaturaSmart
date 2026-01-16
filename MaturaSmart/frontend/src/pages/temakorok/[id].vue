<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const route = useRoute()
const topic = ref(null)
const isLoading = ref(true)
const selectedAnswers = ref({}) // Itt tároljuk, mit jelölt be a diák { question_id: answer_id }

// Az URL-ből vesszük ki az ID-t
const topicId = route.params.id

onMounted(async () => {
  try {
    // Fontos: A Backendben a 'show' metódusnál legyen ->with(['questions.answers', 'subject'])
    const response = await fetch(`http://backend.vm1.test/api/temakorok/${topicId}`)
    
    if (!response.ok) throw new Error('Hiba a betöltéskor')
    
    topic.value = await response.json()
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
})

// Válasz kezelése (Azonnali visszajelzés)
const selectAnswer = (questionId, answer) => {
  // Ha már válaszolt erre, ne engedjük módosítani (opcionális)
  if (selectedAnswers.value[questionId]) return;

  selectedAnswers.value[questionId] = {
    id: answer.id,
    isCorrect: answer.is_correct
  }
}

// Segédfüggvény a gombok színezéséhez
const getAnswerClass = (questionId, answer) => {
  const selection = selectedAnswers.value[questionId]
  
  // Ha még nincs kiválasztva semmi
  if (!selection) {
    return 'bg-white/5 hover:bg-white/10 border-white/10 text-gray-300'
  }

  // Ha ezt a választ választottuk
  if (selection.id === answer.id) {
    return answer.is_correct 
      ? 'bg-green-500/20 border-green-500 text-green-400 font-bold' // Helyes
      : 'bg-red-500/20 border-red-500 text-red-400' // Helytelen
  }

  // Ha nem ezt választottuk, de ez lett volna a helyes (megmutatjuk a megoldást)
  if (answer.is_correct && selection.id !== answer.id) {
    return 'bg-green-500/10 border-green-500/50 text-green-500/70' 
  }

  // Minden más inaktív
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
          v-if="topic.subject" 
          :to="`/tantargyak/${topic.subject.slug}`" 
          class="hover:text-white transition-colors"
        >
          {{ topic.subject.name }}
        </RouterLink>
        <span v-else>Tantárgy</span>
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

      <div class="prose prose-invert prose-lg max-w-none mb-16">
        <div class="bg-[#1e293b]/50 border-l-4 border-blue-500 p-6 rounded-r-xl">
          <h3 class="text-white font-bold text-xl mb-2">👋 Üdvözlünk a leckében!</h3>
          <p class="text-gray-300">
            Ez itt a tananyag helye. Jelenleg ez egy statikus szöveg, de később ide fogjuk renderelni 
            az adatbázisban tárolt <strong>Markdown</strong> tartalmat.
          </p>
          <p class="text-gray-300 mt-2">
            Olvasd el figyelmesen az alábbiakat, majd oldd meg a feladatokat a lap alján!
          </p>
        </div>
        
        <div class="mt-8 text-gray-300 space-y-4 leading-relaxed">
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
          <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
      </div>

      <div class="w-full h-px bg-white/10 my-12"></div>

      <div>
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-3xl font-bold text-white">Gyakorló feladatok 📝</h2>
          <span class="bg-white/10 text-white px-3 py-1 rounded-full text-sm">
            {{ topic.questions.length }} kérdés
          </span>
        </div>

        <div class="space-y-8">
          <div 
            v-for="(question, index) in topic.questions" 
            :key="question.id" 
            class="bg-[#1e293b] border border-white/5 rounded-2xl p-6 md:p-8 transition-all hover:border-white/10"
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
              class="mt-4 p-4 bg-blue-900/20 border border-blue-500/30 rounded-lg text-sm text-blue-200 animate-fade-in"
            >
              <strong>💡 Magyarázat:</strong> {{ question.explanation }}
            </div>

          </div>
        </div>

        <div class="mt-16 flex justify-center pb-20">
          <button class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-lg font-bold py-4 px-10 rounded-2xl shadow-lg hover:shadow-blue-500/25 transition-all transform hover:-translate-y-1">
            Lecke befejezése 🎉
          </button>
        </div>

      </div>

    </div>

    <div v-else class="text-center py-20">
      <h1 class="text-3xl font-bold text-white mb-4">Hoppá! 😕</h1>
      <p class="text-gray-400 mb-6">Ez a lecke nem található.</p>
      <RouterLink to="/" class="text-blue-400 hover:underline">Vissza a vezérlőpultra</RouterLink>
    </div>

  </BaseLayout>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>