<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue' 
import { useRoute, useRouter } from 'vue-router' 
import BaseLayout from "@/layouts/BaseLayout.vue"
import BaseHeader from "@layouts/BaseHeader.vue"

const route = useRoute()
const router = useRouter()
const topic = ref(null)
const isLoading = ref(true)

// --- SCROLL & PROGRESS LOGIKA ---
const maxScrollPercentage = ref(0) 

const updateScroll = () => {
  const scrollTop = window.scrollY
  const docHeight = document.documentElement.scrollHeight
  const winHeight = window.innerHeight
  const scrollTotal = docHeight - winHeight
  
  if (scrollTotal <= 0) return

  const currentPct = Math.min(scrollTop / scrollTotal, 1) * 100
  
  if (currentPct > maxScrollPercentage.value) {
      maxScrollPercentage.value = currentPct
  }
}

const selectedAnswers = ref({}) 

// KOMBINÁLT PROGRESS (scroll + quiz)
const totalProgress = computed(() => {
    if (!topic.value) return 0
    const scrollPart = maxScrollPercentage.value * 0.5 
    const totalQuestions = topic.value.questions.length
    const answeredCount = Object.keys(selectedAnswers.value).length
    const quizPercent = totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0
    const quizPart = quizPercent * 0.5
    return Math.round(scrollPart + quizPart)
})

// --- KÁRTYA LOGIKA ---
const currentCardIndex = ref(0)
const isFlipped = ref(false)

const nextCard = () => {
    isFlipped.value = false
    setTimeout(() => {
        if (topic.value && currentCardIndex.value < topic.value.flashcards.length - 1) {
            currentCardIndex.value++
        } else {
            currentCardIndex.value = 0 
        }
    }, 300) 
}

const prevCard = () => {
    isFlipped.value = false
    setTimeout(() => {
        if (currentCardIndex.value > 0) {
            currentCardIndex.value--
        }
    }, 300)
}

// --- KVÍZ LOGIKA ---
const isSubmitting = ref(false)
const showSuccessModal = ref(false)
const resultData = ref({ xp: 0, message: '', isFirstTime: true })

const subjectSlug = route.params.subject 
const topicSlug = route.params.topic 

onMounted(async () => {
  window.addEventListener('scroll', updateScroll) 
  
  const token = localStorage.getItem('token')
  if (!token) return router.push('/login')

  try {
    const response = await fetch(`http://backend.vm1.test/api/topics/${subjectSlug}/${topicSlug}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    
    if (response.status === 401) {
       localStorage.removeItem('token'); router.push('/login'); return
    }
    if (!response.ok) throw new Error('Hiba')
    
    topic.value = await response.json()
    
    setTimeout(updateScroll, 500)

    if (topic.value) {
        
        let subjectName = topic.value.subject?.title;

        if (!subjectName && subjectSlug) {
            subjectName = subjectSlug.charAt(0).toUpperCase() + subjectSlug.slice(1);
        }

        const finalSubjectName = subjectName || 'Lecke';

        document.title = `${finalSubjectName} | ${topic.value.title} | MaturaSmart`
    }

  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
})

onUnmounted(() => {
    window.removeEventListener('scroll', updateScroll) 
})

const selectAnswer = (questionId, answer) => {
  if (selectedAnswers.value[questionId]) return;
  selectedAnswers.value[questionId] = { id: answer.id, isCorrect: answer.is_correct }
}

const getAnswerClass = (questionId, answer) => {
  const selection = selectedAnswers.value[questionId]
  if (!selection) return 'bg-[#0f172a] border-white/5 text-gray-400 hover:border-blue-500/50 hover:bg-blue-900/10'
  if (selection.id === answer.id) return answer.is_correct 
    ? 'bg-green-500/20 border-green-500 text-green-400 font-bold shadow-[0_0_15px_rgba(34,197,94,0.3)]' 
    : 'bg-red-500/20 border-red-500 text-red-400 shadow-[0_0_15px_rgba(239,68,68,0.3)]'
  if (answer.is_correct && selection.id !== answer.id) return 'bg-green-500/5 border-green-500/30 text-green-500/50 border-dashed' 
  return 'opacity-30 cursor-not-allowed border-transparent grayscale'
}

const finishLesson = async () => {
  const token = localStorage.getItem('token') 
  if (!token) return router.push('/login')

  isSubmitting.value = true
  const answersPayload = {}
  Object.keys(selectedAnswers.value).forEach(qId => answersPayload[qId] = selectedAnswers.value[qId].id)

  try {
    const response = await fetch('http://backend.vm1.test/api/gamification/complete-topic', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
      body: JSON.stringify({ topic_id: topic.value.id, answers: answersPayload })
    })

    const data = await response.json()
    if (!response.ok) throw new Error(data.message)

    resultData.value = { xp: data.xp_gained, totalXp: data.total_xp, message: data.message }
    showSuccessModal.value = true
  } catch (error) {
    alert(error.message)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <BaseLayout>
    <BaseHeader mode="app" />

    <div v-if="isLoading" class="flex justify-center items-center h-[80vh]">
      <div class="relative w-20 h-20">
         <div class="absolute inset-0 border-4 border-blue-500/30 rounded-full animate-ping"></div>
         <div class="absolute inset-0 border-4 border-t-blue-500 rounded-full animate-spin"></div>
      </div>
    </div>

    <div v-else-if="topic" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-32">
      
      <div class="relative rounded-3xl overflow-hidden p-8 md:p-12 mb-10 border border-white/10 shadow-2xl group">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/40 to-[#0b1029] z-0"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-[100px] group-hover:bg-blue-400/30 transition-colors duration-700"></div>
        
        <div class="relative z-10">
            <nav class="flex items-center gap-2 text-xs text-blue-300 font-bold uppercase tracking-widest mb-4">
                <RouterLink to="/main" class="hover:text-white transition-colors">Vezérlőpult</RouterLink> / 
                <span class="text-white">{{ subjectSlug }}</span>
            </nav>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-4 tracking-tight drop-shadow-lg">
                {{ topic.title }}
            </h1>
            <p class="text-lg text-blue-100/80 max-w-2xl leading-relaxed">
                {{ topic.description }}
            </p>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-8 relative">
        
        <div class="w-full lg:w-2/3 space-y-12">
            
            <div class="bg-[#1e293b]/40 backdrop-blur-md border border-white/5 rounded-3xl p-8 shadow-xl">
                <div class="flex items-center gap-3 mb-6 border-b border-white/5 pb-4">
                    <span class="text-2xl">📚</span>
                    <h2 class="text-2xl font-bold text-white">Tananyag</h2>
                </div>
                <div class="prose prose-invert prose-lg max-w-none text-gray-300 leading-loose">
                    <div v-if="topic.content" v-html="topic.content"></div>
                    <div v-else class="text-gray-500 italic">Nincs feltöltött tartalom.</div>
                </div>
            </div>

            <div class="bg-[#1e293b] border border-white/10 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
                
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📝</span>
                        <h2 class="text-2xl font-bold text-white">Tudáspróba</h2>
                    </div>
                    <span class="bg-blue-600/20 text-blue-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border border-blue-500/30">
                        {{ topic.questions.length }} Kérdés
                    </span>
                </div>

                <div class="space-y-6">
                    <div 
                        v-for="(question, index) in topic.questions" 
                        :key="question.id" 
                        class="group bg-[#0b1029]/50 border border-white/5 rounded-2xl p-6 hover:border-blue-500/30 transition-all duration-300"
                    >
                        <div class="flex gap-4 mb-4">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-white/5 text-gray-400 font-bold flex items-center justify-center text-sm border border-white/5">
                                {{ index + 1 }}
                            </span>
                            <div class="flex-1">
                                <div class="flex justify-between items-start gap-4">
                                    <h3 class="text-lg font-bold text-white group-hover:text-blue-200 transition-colors">
                                        {{ question.content }}
                                    </h3>
                                    <span class="shrink-0 text-xs font-bold bg-yellow-500/10 text-yellow-500 px-2 py-1 rounded border border-yellow-500/20">
                                        {{ question.xp || 10 }} XP
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pl-0 md:pl-12">
                            <button 
                                v-for="answer in question.answers" 
                                :key="answer.id"
                                @click="selectAnswer(question.id, answer)"
                                :class="getAnswerClass(question.id, answer)"
                                class="text-left px-4 py-3 rounded-xl border transition-all duration-200 text-sm font-medium relative overflow-hidden"
                            >
                                {{ answer.text }}
                            </button>
                        </div>
                        
                        <transition name="slide-fade">
                            <div v-if="selectedAnswers[question.id] && question.explanation" class="mt-4 ml-0 md:ml-12 p-4 bg-blue-900/20 border-l-4 border-blue-500 rounded-r-lg text-sm text-blue-200">
                                <strong>💡 Tudtad?</strong> {{ question.explanation }}
                            </div>
                        </transition>
                    </div>
                </div>

                <div class="mt-10 flex justify-center">
                    <button 
                        @click="finishLesson"
                        :disabled="isSubmitting"
                        class="w-full md:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 px-12 rounded-2xl shadow-[0_10px_40px_-10px_rgba(37,99,235,0.5)] transition-all hover:-translate-y-1 hover:shadow-blue-500/40 disabled:opacity-50 disabled:cursor-wait"
                    >
                        <span v-if="isSubmitting" class="animate-spin mr-2">⏳</span>
                        {{ isSubmitting ? 'Eredmények mentése...' : 'Lecke Befejezése & XP Begyűjtése 🚀' }}
                    </button>
                </div>
            </div>

        </div>

        <div class="w-full lg:w-1/3 relative">
            
            <div class="sticky top-8 space-y-6 z-30">

                <div class="bg-[#1e293b] border border-white/10 rounded-3xl p-6 shadow-xl">
                    <div class="flex items-end justify-between mb-2">
                        <h3 class="text-white font-bold text-sm uppercase tracking-widest">Lecke haladás</h3>
                        <span class="text-2xl font-black text-white">{{ totalProgress }}%</span>
                    </div>
                    
                    <div class="w-full h-3 bg-white/5 rounded-full overflow-hidden relative">
                        <div class="absolute top-0 left-0 h-full bg-blue-600 transition-all duration-500 ease-out" :style="{ width: (maxScrollPercentage * 0.5) + '%' }"></div>
                        <div class="absolute top-0 h-full bg-green-500 transition-all duration-500 ease-out" :style="{ left: (maxScrollPercentage * 0.5) + '%', width: ((Object.keys(selectedAnswers).length / topic.questions.length) * 50) + '%' }"></div>
                    </div>
                    
                    <div class="flex justify-between text-[10px] text-gray-500 mt-2 font-bold uppercase tracking-wider">
                        <span class="flex items-center gap-1"><span class="text-lg">👀</span></span>
                        <span class="flex items-center gap-1"><span class="text-lg">✅</span></span>
                    </div>
                </div>
                
                <div v-if="topic.flashcards && topic.flashcards.length > 0" class="bg-[#1e293b]/80 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl relative overflow-visible">
                    
                    <div class="absolute -top-3 -right-3 bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg rotate-3">
                        ⚡ Villámkérdések
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-white font-bold text-lg">Gyakorló Kártyák</h3>
                        <p class="text-gray-400 text-xs mt-1">Forgasd meg és ismételj!</p>
                    </div>

                    <div class="scene w-full h-64 cursor-pointer mb-6" @click="isFlipped = !isFlipped">
                        <div class="card relative w-full h-full transition-transform duration-500 transform-style-3d" :class="{ 'is-flipped': isFlipped }">
                            <div class="card-face card-front absolute inset-0 bg-gradient-to-br from-[#0f172a] to-[#1e293b] border border-indigo-500/30 rounded-2xl flex flex-col items-center justify-center p-6 text-center backface-hidden shadow-inner group">
                                <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">❓</div>
                                <h4 class="text-white font-bold leading-snug">{{ topic.flashcards[currentCardIndex].front }}</h4>
                                <span class="absolute bottom-4 text-[10px] text-gray-500 uppercase tracking-widest">Kattints</span>
                            </div>
                            <div class="card-face card-back absolute inset-0 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex flex-col items-center justify-center p-6 text-center backface-hidden rotate-y-180 shadow-lg border border-white/20">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-2xl mb-4">💡</div>
                                <h4 class="text-white font-bold leading-snug">{{ topic.flashcards[currentCardIndex].back }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-2">
                        <button @click.stop="prevCard" :disabled="currentCardIndex === 0" class="p-3 rounded-full bg-white/5 border border-white/5 text-gray-400 hover:bg-blue-600 hover:border-blue-500 hover:text-white transition-all disabled:opacity-30 disabled:cursor-not-allowed group">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                        </button>
                        <span class="text-xs font-bold text-gray-500">{{ currentCardIndex + 1 }} / {{ topic.flashcards.length }}</span>
                        <button @click.stop="nextCard" :disabled="currentCardIndex === topic.flashcards.length - 1" class="p-3 rounded-full bg-white/5 border border-white/5 text-gray-400 hover:bg-blue-600 hover:border-blue-500 hover:text-white transition-all disabled:opacity-30 disabled:cursor-not-allowed group">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 group-hover:translate-x-0.5 transition-transform"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                        </button>
                    </div>
                </div>

                <div class="bg-blue-600/10 border border-blue-500/20 rounded-2xl p-4 text-center">
                    <p class="text-blue-300 text-sm font-medium">
                        "A tanulás nem verseny, de azért nyerni jó érzés!" 😉
                    </p>
                </div>

            </div>
        </div>

      </div>

    </div>

    <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" @click="showSuccessModal = false"></div>
      <div class="relative bg-[#1e293b] border border-white/10 rounded-3xl p-8 max-w-md w-full text-center shadow-2xl animate-pop-in">
        <div class="text-7xl mb-4 drop-shadow-[0_0_15px_rgba(255,255,255,0.5)]">{{ resultData.xp > 0 ? '🏆' : '👍' }}</div>
        <h2 class="text-3xl font-black text-white mb-2">{{ resultData.xp > 0 ? 'Lecke Teljesítve!' : 'Gyakorlás Kész!' }}</h2>
        <p class="text-gray-400 mb-6">{{ resultData.message }}</p>

        <div v-if="resultData.xp > 0" class="bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/50 rounded-2xl p-6 mb-6 relative overflow-hidden">
          <div class="absolute inset-0 bg-yellow-500/10 animate-pulse"></div>
          <p class="text-yellow-400 font-bold text-sm uppercase tracking-widest relative z-10">Jutalom</p>
          <p class="text-5xl font-black text-white mt-2 relative z-10 drop-shadow-md">+{{ resultData.xp }} XP</p>
        </div>
        
        <div class="grid gap-3">
          <RouterLink :to="`/tantargyak/${subjectSlug}`" class="block w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-xl transition-all hover:scale-105">Vissza a témakörökhöz</RouterLink>
          <button @click="showSuccessModal = false" class="block w-full text-gray-500 hover:text-white py-2 transition-colors">Maradok még</button>
        </div>
      </div>
    </div>

  </BaseLayout>
</template>

<style scoped>
/* 3D Kártya */
.scene { perspective: 1000px; }
.transform-style-3d { transform-style: preserve-3d; }
.backface-hidden { backface-visibility: hidden; }
.rotate-y-180 { transform: rotateY(180deg); }
.is-flipped { transform: rotateY(180deg); }

/* Animációk */
.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
@keyframes popIn {
  0% { transform: scale(0.9); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-pop-in { animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
</style>