<script setup>
import { ref, computed, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps(['topic', 'questions'])
const emit = defineEmits(['complete'])
const router = useRouter() 

const status = ref('intro')
const currentQuestionIndex = ref(0)
const userAnswers = ref({}) 
const timeLeft = ref(0)
const timerInterval = ref(null)

const serverXp = ref(0) 
const scorePercentage = ref(0)
const correctCount = ref(0)
const passed = ref(false)
const isProcessing = ref(false)

const startTest = () => {
    status.value = 'running'
    if (props.topic.time_limit_minutes) {
        timeLeft.value = props.topic.time_limit_minutes * 60
        startTimer()
    }
}

const startTimer = () => {
    timerInterval.value = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value--
        } else {
            finishTest()
        }
    }, 1000)
}

const formattedTime = computed(() => {
    const m = Math.floor(timeLeft.value / 60)
    const s = timeLeft.value % 60
    return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`
})

const selectAnswer = (qId, aId) => {
    userAnswers.value[qId] = aId
}

const next = () => {
    if (currentQuestionIndex.value < props.questions.length - 1) {
        currentQuestionIndex.value++
    } else {
        finishTest()
    }
}

const finishTest = () => {
    clearInterval(timerInterval.value)
    isProcessing.value = true //backendre várás
    
    let totalXp = 0
    let earnedXpLocal = 0
    let correct = 0 
    
    props.questions.forEach(q => {
        totalXp += q.xp
        const userAnswerId = userAnswers.value[q.id]
        const correctAns = q.answers.find(a => a.is_correct)
        
        if (userAnswerId === correctAns.id) {
            earnedXpLocal += q.xp
            correct++
        }
    })

    correctCount.value = correct
    scorePercentage.value = totalXp > 0 ? Math.round((earnedXpLocal / totalXp) * 100) : 0
    
    emit('complete', { answers: userAnswers.value })
}
const showResult = (actualXp, isPassed) => {
    serverXp.value = actualXp
    passed.value = isPassed
    status.value = 'finished'
    isProcessing.value = false
}

const goToNext = () => {
    if (props.topic.next_slug) {
        router.push(`/tantargyak/${router.currentRoute.value.params.subject}/${props.topic.next_slug}`)
    } else {
        router.push('/main')
    }
}

defineExpose({ showResult })

onUnmounted(() => clearInterval(timerInterval.value))
</script>

<template>
    <div class="w-full max-w-6xl mx-auto">
        
        <div v-if="status === 'intro'" class="flex flex-col items-center justify-center min-h-[60vh] text-center">
             <div class="bg-[#161b22] border border-gray-700 rounded-3xl p-12 shadow-2xl w-full max-w-4xl relative overflow-hidden">
                 <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16"></div>
                 <div class="relative z-10">
                    <div class="text-7xl mb-6">🎓</div>
                    <h1 class="text-4xl md:text-5xl font-black text-white mb-6">{{ topic.title }}</h1>
                    <p class="text-xl text-gray-400 mb-10">Készen állsz a megmérettetésre?</p>
                    
                    <div class="flex flex-wrap justify-center gap-4 mb-10">
                        <div class="bg-gray-800 px-6 py-3 rounded-xl border border-gray-700">
                            <div class="text-xs text-gray-500 font-bold uppercase">Kérdések</div>
                            <div class="text-2xl font-bold text-white">{{ questions.length }}</div>
                        </div>
                        <div class="bg-gray-800 px-6 py-3 rounded-xl border border-gray-700">
                            <div class="text-xs text-gray-500 font-bold uppercase">Időkorlát</div>
                            <div class="text-2xl font-bold text-white">{{ topic.time_limit_minutes ? topic.time_limit_minutes + ' p' : '∞' }}</div>
                        </div>
                        <div class="bg-gray-800 px-6 py-3 rounded-xl border border-gray-700">
                            <div class="text-xs text-gray-500 font-bold uppercase">Szint</div>
                            <div class="text-2xl font-bold text-yellow-500">{{ topic.passing_percentage || 50 }}%</div>
                        </div>
                    </div>

                    <button @click="startTest" class="bg-blue-600 hover:bg-blue-500 text-white font-bold text-xl px-12 py-4 rounded-2xl shadow-lg shadow-blue-600/20 transition hover:scale-105">
                        Indítás
                    </button>
                 </div>
             </div>
        </div>

        <div v-if="status === 'running' && !isProcessing" class="w-full">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <span class="text-gray-500 text-xs font-bold uppercase">Kérdés</span>
                    <div class="text-3xl font-black text-white">{{ currentQuestionIndex + 1 }} <span class="text-gray-600 text-xl">/ {{ questions.length }}</span></div>
                </div>
                <div v-if="topic.time_limit_minutes" class="text-2xl font-mono font-bold" :class="timeLeft < 60 ? 'text-red-500 animate-pulse' : 'text-gray-300'">
                    {{ formattedTime }}
                </div>
            </div>

            <div class="w-full bg-gray-800 h-2 rounded-full mb-8 overflow-hidden">
                <div class="bg-blue-500 h-full transition-all duration-300" :style="{ width: ((currentQuestionIndex + 1) / questions.length) * 100 + '%' }"></div>
            </div>

            <div class="bg-[#1e293b] border border-gray-700 rounded-3xl p-8 md:p-12 shadow-2xl relative">
                <h3 class="text-2xl md:text-3xl font-bold text-white mb-10 leading-snug">
                    {{ questions[currentQuestionIndex].content }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <button 
                        v-for="ans in questions[currentQuestionIndex].answers" 
                        :key="ans.id"
                        @click="selectAnswer(questions[currentQuestionIndex].id, ans.id)"
                        class="text-left p-6 rounded-2xl border-2 transition-all group hover:bg-gray-800"
                        :class="userAnswers[questions[currentQuestionIndex].id] === ans.id 
                            ? 'border-blue-500 bg-blue-500/10' 
                            : 'border-gray-700 bg-gray-800/20'"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                :class="userAnswers[questions[currentQuestionIndex].id] === ans.id ? 'border-blue-500' : 'border-gray-600'">
                                <div v-if="userAnswers[questions[currentQuestionIndex].id] === ans.id" class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            </div>
                            <span class="text-lg text-gray-300 group-hover:text-white font-medium">{{ ans.text }}</span>
                        </div>
                    </button>
                </div>

                <div class="flex justify-end border-t border-gray-700 pt-6">
                    <button 
                        @click="next" 
                        class="bg-white text-gray-900 hover:bg-gray-200 font-bold text-lg px-10 py-3 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!userAnswers[questions[currentQuestionIndex].id]"
                    >
                        {{ currentQuestionIndex === questions.length - 1 ? 'Befejezés' : 'Következő' }} →
                    </button>
                </div>
            </div>
        </div>

        <div v-if="isProcessing" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-gray-400 font-bold animate-pulse">Eredmények kiértékelése...</p>
        </div>

        <div v-if="status === 'finished'" class="flex justify-center items-center min-h-[60vh]">
            <div class="bg-[#1e293b] border border-gray-700 rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col md:flex-row">
                
                <div class="w-full md:w-2/5 p-10 flex flex-col justify-center items-center text-center relative" :class="passed ? 'bg-green-900/20' : 'bg-red-900/20'">
                    <div class="text-8xl mb-4">{{ passed ? '🏆' : '⚠️' }}</div>
                    <h2 class="text-4xl font-black text-white mb-2">{{ passed ? 'SIKERES!' : 'SIKERTELEN' }}</h2>
                    <p class="text-gray-300">{{ passed ? 'Szép munka, így tovább!' : 'Gyakorolj még egy kicsit!' }}</p>
                </div>

                <div class="w-full md:w-3/5 p-10 bg-[#0f172a]">
                    <h3 class="text-white font-bold text-xl mb-6 uppercase tracking-wider text-gray-500">Összesítés</h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-gray-800 p-4 rounded-xl">
                            <div class="text-xs text-gray-500 font-bold uppercase">Jutalom</div>
                            <div class="text-3xl font-black text-yellow-400">+{{ serverXp }} XP</div>
                        </div>
                        <div class="bg-gray-800 p-4 rounded-xl">
                            <div class="text-xs text-gray-500 font-bold uppercase">Eredmény</div>
                            <div class="text-3xl font-black" :class="passed ? 'text-green-400' : 'text-red-400'">
                                {{ scorePercentage }}%
                            </div>
                        </div>
                        <div class="bg-gray-800 p-4 rounded-xl col-span-2 flex justify-between items-center">
                            <div>
                                <div class="text-xs text-gray-500 font-bold uppercase">Helyes válaszok</div>
                                <div class="text-2xl font-bold text-white">{{ correctCount }} / {{ questions.length }}</div>
                            </div>
                            <div class="text-3xl">✅</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button @click="router.push('/main')" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-4 rounded-xl border border-gray-700">
                            Kilépés
                        </button>
                        <button v-if="passed && topic.next_slug" @click="goToNext" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-900/20">
                            Következő ➔
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>