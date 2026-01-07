<script setup>
import { ref, nextTick, onMounted } from 'vue'

const messages = ref([
  { id: 1, sender: 'ai', text: 'Szia! 👋 Én vagyok Axel tesztüzemmódban. Állítsd be a kontextust fent, és teszteljük a tudásomat!' }
])
const userInput = ref('')
const isLoading = ref(false)
const chatContainer = ref(null)

const testContext = ref({
  subject: 'Történelem',
  topic: 'Honfoglalás',
  chapter: 'A törzsek vándorlása',
  notes: 'A magyar törzsek vándorlása során érintették Levédiát és Etelközt. A hét vezér szövetséget kötött (vérszerződés).'
})
const renderMarkdown = (text) => {
  if (window.markdownit) {
    const md = window.markdownit({
      html: true,
      linkify: true,
      typographer: true
    });
    return md.render(text);
  }
  return text;
}
const scrollToBottom = async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
}

const sendMessage = async () => {
  if (!userInput.value.trim() || isLoading.value) return

  const userMsg = userInput.value
  messages.value.push({ id: Date.now(), sender: 'user', text: userMsg })
  userInput.value = ''
  scrollToBottom()
  isLoading.value = true

  try {
    const res = await fetch('http://backend.vm1.test/api/ask-axel', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        message: userMsg,
        subject: testContext.value.subject,
        topic: testContext.value.topic,
        chapter: testContext.value.chapter,
        notes: testContext.value.notes
      })
    })

    const data = await res.json()

    messages.value.push({ 
      id: Date.now() + 1, 
      sender: 'ai', 
      text: data.answer || 'Hiba: Nem érkezett válasz.' 
    })

  } catch (error) {
    console.error(error)
    messages.value.push({ 
      id: Date.now() + 1, 
      sender: 'ai', 
      text: '⚠️ Hiba történt a kommunikációban. Fut a backend?' 
    })
  } finally {
    isLoading.value = false
    scrollToBottom()
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-[#020617] text-slate-900 dark:text-white p-4 flex flex-col items-center">
    
    <div class="w-full max-w-4xl flex flex-col gap-4 h-[90vh]">
      
      <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <h1 class="text-xl font-bold mb-4">🛠️ Axel AI Playground</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
          <div>
            <label class="block text-slate-500 mb-1">Tantárgy</label>
            <input v-model="testContext.subject" class="w-full p-2 rounded bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600" />
          </div>
          <div>
            <label class="block text-slate-500 mb-1">Témakör</label>
            <input v-model="testContext.topic" class="w-full p-2 rounded bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600" />
          </div>
          <div>
            <label class="block text-slate-500 mb-1">Fejezet</label>
            <input v-model="testContext.chapter" class="w-full p-2 rounded bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600" />
          </div>
        </div>
        <div class="mt-3">
            <label class="block text-slate-500 mb-1">Jegyzet / Tananyag (RAG forrás)</label>
            <textarea v-model="testContext.notes" rows="2" class="w-full p-2 rounded bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-xs"></textarea>
        </div>
      </div>

      <div 
        ref="chatContainer"
        class="flex-grow bg-white dark:bg-slate-900/50 rounded-xl shadow-inner border border-slate-200 dark:border-slate-700 overflow-y-auto p-4 space-y-4"
      >
        <div 
          v-for="msg in messages" 
          :key="msg.id" 
          :class="['flex', msg.sender === 'user' ? 'justify-end' : 'justify-start']"
        >
          <div 
            :class="[
              'max-w-[80%] p-4 rounded-2xl whitespace-pre-wrap leading-relaxed',
              msg.sender === 'user' 
                ? 'bg-indigo-600 text-white rounded-br-none' 
                : 'bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-slate-200 rounded-bl-none'
            ]"
          >
            <div 
  v-html="renderMarkdown(msg.text)" 
  class="markdown-body text-sm"
></div>
          </div>
        </div>

        <div v-if="isLoading" class="flex justify-start">
            <div class="bg-slate-200 dark:bg-slate-800 p-3 rounded-2xl rounded-bl-none text-slate-500 text-sm animate-pulse">
                Axel gépel... ✍️
            </div>
        </div>
      </div>

      <form @submit.prevent="sendMessage" class="flex gap-2">
        <input 
          v-model="userInput" 
          type="text" 
          placeholder="Kérdezz valamit Axeltől..."
          class="flex-grow p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />
        <button 
          type="submit" 
          :disabled="isLoading || !userInput"
          class="bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 rounded-xl font-bold transition-colors"
        >
          Küldés
        </button>
      </form>

    </div>
  </div>
</template>