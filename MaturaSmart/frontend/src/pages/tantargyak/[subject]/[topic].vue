<script setup>
import { ref, onMounted } from 'vue' 
import { useRoute, useRouter } from 'vue-router' 
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const route = useRoute()
const router = useRouter()
const topic = ref(null)
const isLoading = ref(true)
const selectedAnswers = ref({}) 

const isSubmitting = ref(false)
const showSuccessModal = ref(false)
const resultData = ref({ xp: 0, message: '', isFirstTime: true })

const subjectSlug = route.params.subject 
const topicSlug = route.params.topic 

onMounted(async () => {
  const token = localStorage.getItem('token')
  
  if (!token) {
    router.push('/login')
    return
  }

  try {
    const response = await fetch(`http://backend.vm1.test/api/topics/${topicSlug}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    // Ha lejárt a token (401), kidobjuk a loginra
    if (response.status === 401) {
       localStorage.removeItem('token')
       router.push('/login')
       return
    }

    if (!response.ok) throw new Error('Hiba a betöltéskor')
    
    topic.value = await response.json()
  } catch (error) {
    console.error("Hiba történt:", error)
  } finally {
    isLoading.value = false
  }
})

// Válasz kiválasztása
const selectAnswer = (questionId, answer) => {
  if (selectedAnswers.value[questionId]) return;
  selectedAnswers.value[questionId] = { id: answer.id, isCorrect: answer.is_correct }
}

// Stílusok a gombokhoz (Helyes/Helytelen)
const getAnswerClass = (questionId, answer) => {
  const selection = selectedAnswers.value[questionId]
  if (!selection) return 'bg-white/5 hover:bg-white/10 border-white/10 text-gray-300'
  if (selection.id === answer.id) return answer.is_correct ? 'bg-green-500/20 border-green-500 text-green-400 font-bold' : 'bg-red-500/20 border-red-500 text-red-400'
  if (answer.is_correct && selection.id !== answer.id) return 'bg-green-500/10 border-green-500/50 text-green-500/70' 
  return 'opacity-50 cursor-not-allowed border-transparent'
}

// Lecke befejezése
const finishLesson = async () => {
  const token = localStorage.getItem('token') 

  if (!token) {
    alert("Kérlek, jelentkezz be a pontszerzéshez!")
    router.push('/login')
    return
  }

  isSubmitting.value = true

  // Pontszámítás
  let correctCount = 0
  const totalQuestions = topic.value.questions.length
  
  if (totalQuestions > 0) {
    topic.value.questions.forEach(q => {
      if (selectedAnswers.value[q.id]?.isCorrect) correctCount++
    })
  }
  
  const percentage = totalQuestions > 0 
    ? Math.round((correctCount / totalQuestions) * 100) 
    : 100

  try {
    // Mentés az adatbázisba
    const response = await fetch('http://backend.vm1.test/api/gamification/complete-topic', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        topic_id: topic.value.id,
        percentage: percentage
      })
    })

    const data = await response.json()

    if (!response.ok) throw new Error(data.message || 'Hiba a mentéskor')

    // Eredmény mentése a Modalhoz
    resultData.value = {
      xp: data.xp_gained,
      totalXp: data.total_xp,
      message: data.message,
      isFirstTime: data.first_time
    }
    showSuccessModal.value = true

  } catch (error) {
    console.error(error)
    alert("Hiba történt: " + error.message)
  } finally {
    isSubmitting.value = false
  }
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
          <button 
            @click="finishLesson"
            :disabled="isSubmitting"
            class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 text-white text-lg font-bold py-4 px-10 rounded-2xl shadow-lg transition-transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-wait flex items-center gap-3"
          >
            <span v-if="isSubmitting" class="animate-spin text-xl">⏳</span>
            <span>{{ isSubmitting ? 'Mentés...' : 'Lecke befejezése 🎉' }}</span>
          </button>
        </div>
      </div>

    </div>

    <div v-else class="text-center py-20">
      <h1 class="text-3xl font-bold text-white mb-4">Hoppá! 😕</h1>
      <p class="text-gray-400">Nem sikerült betölteni a leckét.</p>
      <RouterLink to="/" class="text-blue-400 hover:underline mt-4 inline-block">Vissza a főoldalra</RouterLink>
    </div>

    <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showSuccessModal = false"></div>
      
      <div class="relative bg-[#1e293b] border border-white/10 rounded-3xl p-8 max-w-md w-full text-center shadow-2xl transform transition-all scale-100">
        
        <div class="text-6xl mb-4 animate-bounce">
          {{ resultData.isFirstTime ? '🏆' : '👍' }}
        </div>
        
        <h2 class="text-3xl font-extrabold text-white mb-2">
          {{ resultData.isFirstTime ? 'Lecke Teljesítve!' : 'Újra teljesítve!' }}
        </h2>
        
        <p class="text-gray-400 mb-6">{{ resultData.message }}</p>

        <div v-if="resultData.xp > 0" class="bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/50 rounded-xl p-4 mb-6">
          <p class="text-yellow-400 font-bold text-xl uppercase tracking-widest">Megszerzett Jutalom</p>
          <p class="text-4xl font-extrabold text-white mt-1">+{{ resultData.xp }} XP</p>
        </div>
        
        <div v-else class="bg-white/5 rounded-xl p-4 mb-6">
          <p class="text-gray-400 text-sm">Már megszerezted a pontokat ezért a leckéért.</p>
        </div>

        <div class="space-y-3">
          <RouterLink 
            :to="`/tantargyak/${subjectSlug}`" 
            class="block w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-xl transition-colors"
          >
            Vissza a témakörökhöz
          </RouterLink>
          
          <button 
            @click="showSuccessModal = false"
            class="block w-full text-gray-400 hover:text-white py-2 transition-colors"
          >
            Maradok még itt
          </button>
        </div>
      </div>
    </div>

  </BaseLayout>
</template>