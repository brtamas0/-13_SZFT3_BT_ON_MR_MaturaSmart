<script setup>
import { ref, nextTick, watch, computed } from 'vue'

const props = defineProps({
  subject: String,
  topicTitle: String,
  topicContent: String
})

const isOpen = ref(false)
const isThinking = ref(false)
const messageInput = ref('')
const messages = ref([
  { 
    role: 'assistant', 
    content: `Szia! Axel vagyok. 👋\nLátom a(z) **${props.topicTitle}** témakört tanulod. Miben segíthetek ezzel kapcsolatban?` 
  }
])

const chatContainer = ref(null)

// Kép váltogatása gondolkodás közben
const axelAvatar = computed(() => {
    return isThinking.value ? '/axel.png' : '/axel.png' //első axel_thinking.png lesz később
})

// Görgetés az aljára
const scrollToBottom = async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
}

// HTML tagek eltávolítása a kontextusból (token spórolás)
const stripHtml = (html) => {
   let tmp = document.createElement("DIV")
   tmp.innerHTML = html
   return tmp.textContent || tmp.innerText || ""
}

const sendMessage = async () => {
  if (!messageInput.value.trim() || isSubmitting.value) return

  const userMsg = messageInput.value
  messages.value.push({ role: 'user', content: userMsg })
  messageInput.value = ''
  isThinking.value = true
  await scrollToBottom()

  try {
    const token = localStorage.getItem('token')
    const cleanContent = stripHtml(props.topicContent || "") // Szöveg tisztítása HTML tagektől

    const response = await fetch('http://backend.vm1.test/api/ask-axel', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        message: userMsg,
        subject: props.subject,
        topic: props.topicTitle,
        notes: cleanContent,
        history: messages.value.slice(-6) // Csak az utolsó 6 üzenetet küldjük
      })
    })

    const data = await response.json()
    
    if (response.ok) {
      messages.value.push({ role: 'assistant', content: data.answer })
    } else {
      messages.value.push({ role: 'assistant', content: 'Bocsi, most kicsit elvesztettem a fonalat. Próbáld újra később! 😵‍💫' })
    }

  } catch (error) {
    console.error(error)
    messages.value.push({ role: 'assistant', content: 'Hálózati hiba történt. 🔌' })
  } finally {
    isThinking.value = false
    await scrollToBottom()
  }
}

const isSubmitting = computed(() => isThinking.value)
</script>

<template>
  <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-4 font-sans">
    
    <transition name="slide-up">
      <div v-if="isOpen" class="bg-[#1e293b] border border-blue-500/30 w-[350px] md:w-[400px] h-[500px] rounded-2xl shadow-2xl flex flex-col overflow-hidden">
        
        <div class="bg-blue-600/20 p-4 border-b border-white/5 flex items-center justify-between backdrop-blur-md">
          <div class="flex items-center gap-3">
            <div class="relative">
                <img :src="axelAvatar" class="w-10 h-10 rounded-full border-2 border-blue-400 bg-[#0b1029] object-cover transition-all duration-300" alt="Axel">
                <span v-if="isThinking" class="absolute bottom-0 right-0 w-3 h-3 bg-yellow-400 rounded-full animate-bounce"></span>
                <span v-else class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full"></span>
            </div>
            <div>
              <h3 class="text-white font-bold text-sm">Axel</h3>
              <p class="text-blue-300 text-[10px] uppercase font-bold tracking-widest">AI Mentor</p>
            </div>
          </div>
          <button @click="isOpen = false" class="text-gray-400 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
          </button>
        </div>

        <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4 scrollbar-thin scrollbar-thumb-blue-600/50 scrollbar-track-transparent">
          <div v-for="(msg, i) in messages" :key="i" class="flex gap-3" :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
            
            <div v-if="msg.role === 'assistant'" class="w-8 h-8 flex-shrink-0 rounded-full bg-blue-600/20 flex items-center justify-center text-xs border border-blue-500/30 overflow-hidden">
                <img :src="axelAvatar" class="w-full h-full object-cover">
            </div>
            
            <div class="max-w-[80%] rounded-2xl p-3 text-sm leading-relaxed shadow-sm"
                 :class="msg.role === 'user' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-[#0f172a] text-gray-200 border border-white/10 rounded-bl-none prose prose-invert prose-sm'">
                 <span v-html="msg.content.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')"></span>
            </div>
          </div>
          
          <div v-if="isThinking" class="flex gap-3">
             <div class="w-8 h-8 rounded-full bg-blue-600/20 flex items-center justify-center border border-blue-500/30">
                <!-- <img src="/axel_thinking.png" class="w-full h-full object-cover rounded-full"> -->
                <img src="/axel.png" class="w-full h-full object-cover rounded-full">

             </div>
             <div class="bg-[#0f172a] p-3 rounded-2xl rounded-bl-none border border-white/10 flex gap-1 items-center">
                <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></span>
                <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce delay-100"></span>
                <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce delay-200"></span>
             </div>
          </div>
        </div>

        <div class="p-4 bg-[#0f172a] border-t border-white/5">
          <form @submit.prevent="sendMessage" class="relative">
            <input 
              v-model="messageInput" 
              type="text" 
              placeholder="Kérdezz a leckéről..." 
              class="w-full bg-[#1e293b] text-white rounded-xl pl-4 pr-12 py-3 border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-sm placeholder-gray-500 transition-all"
              :disabled="isThinking"
            >
            <button 
                type="submit" 
                :disabled="!messageInput.trim() || isThinking"
                class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
              </svg>
            </button>
          </form>
          <div class="text-[10px] text-gray-600 text-center mt-2">
            Axel is tévedhet. Ellenőrizd a fontos információkat.
          </div>
        </div>

      </div>
    </transition>

    <button 
      @click="isOpen = !isOpen" 
      class="group relative w-16 h-16 rounded-full shadow-2xl transition-transform hover:scale-105 active:scale-95 flex items-center justify-center border-4 border-[#1e293b]"
      :class="isOpen ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-600 hover:bg-blue-500'"
    >
      <div v-if="!isOpen" class="absolute inset-0 rounded-full border-2 border-white/20 animate-ping"></div>
      
      <img v-if="!isOpen" src="/axel.png" class="w-full h-full rounded-full object-cover">
      
      <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 text-white">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
      
      <div v-if="!isOpen" class="absolute right-full mr-4 bg-white text-blue-900 px-3 py-1 rounded-lg text-xs font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
        Segíthetek a tanulásban?
        <div class="absolute top-1/2 -right-1 w-2 h-2 bg-white rotate-45 -translate-y-1/2"></div>
      </div>
    </button>

  </div>
</template>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px) scale(0.95); }
.scrollbar-thin::-webkit-scrollbar { width: 6px; }
.scrollbar-thin::-webkit-scrollbar-thumb { background-color: #3b82f6; border-radius: 20px; }
.scrollbar-thin::-webkit-scrollbar-track { background-color: transparent; }
</style>