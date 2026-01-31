<script setup>
import { ref } from 'vue'
import { nextTick, computed } from 'vue'

const isThinking = ref(false)
const chatContainer = ref(null)
const stripHtml = (html) => {
   let tmp = document.createElement("DIV")
   tmp.innerHTML = html
   return tmp.textContent || tmp.innerText || ""
}

const sendMessage = async () => {
  if (!messageInput.value.trim() || isThinking.value) return

  const userMsg = messageInput.value
  messages.value.push({ role: 'user', content: userMsg })
  messageInput.value = ''
  isThinking.value = true
  await scrollToBottom()

  try {
    const token = localStorage.getItem('token')
    const cleanContent = stripHtml(props.topicContent || "")

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
        history: messages.value.slice(-6)
      })
    })

    const data = await response.json()
    if (response.ok) {
      messages.value.push({ role: 'assistant', content: data.answer })
    }
  } catch (error) {
    messages.value.push({ role: 'assistant', content: 'Hálózati hiba történt. 🔌' })
  } finally {
    isThinking.value = false
    await scrollToBottom()
  }
}
const axelAvatar = computed(() => {
    return isThinking.value ? '/axel.png' : '/axel.png'
})

const scrollToBottom = async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
}
const props = defineProps({
  subject: String,
  topicTitle: String,
  topicContent: String
})

const isOpen = ref(false)
const messageInput = ref('')
const messages = ref([
  { 
    role: 'assistant', 
    content: `Szia! Axel vagyok. 👋\nLátom a(z) **${props.topicTitle}** témakört tanulod. Miben segíthetek ezzel kapcsolatban?` 
  }
])
</script>

<template>
  <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-4 font-sans">
    <transition name="slide-up">
      <div v-if="isOpen" class="bg-[#1e293b] border border-blue-500/30 w-[350px] md:w-[400px] h-[500px] rounded-2xl shadow-2xl flex flex-col overflow-hidden">
        <div class="bg-blue-600/20 p-4 border-b border-white/5 flex items-center justify-between backdrop-blur-md">
          <h3 class="text-white font-bold text-sm">Axel</h3>
          <button @click="isOpen = false" class="text-gray-400 hover:text-white transition">X</button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
           </div>
      </div>
    </transition>

    <button @click="isOpen = !isOpen" class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center shadow-2xl">
      <span class="text-white font-bold">{{ isOpen ? 'X' : 'Axel' }}</span>
    </button>
  </div>
</template>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }
</style>