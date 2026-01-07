<script setup>
import { ref, nextTick } from 'vue'

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
  <div class="min-h-screen bg-gray-100 text-slate-900 p-4 flex flex-col items-center">
    
    <div class="w-full max-w-4xl flex flex-col gap-4 h-[90vh]">
      
      <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <h1 class="text-xl font-bold mb-4 text-indigo-700">🛠️ Axel AI Playground</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
          <div>
            <label class="block text-slate-500 mb-1">Tantárgy</label>
            <input v-model="testContext.subject" class="w-full p-2 rounded bg-slate-50 border border-slate-300 focus:border-indigo-500 outline-none" />
          </div>
          <div>
            <label class="block text-slate-500 mb-1">Témakör</label>
            <input v-model="testContext.topic" class="w-full p-2 rounded bg-slate-50 border border-slate-300 focus:border-indigo-500 outline-none" />
          </div>
          <div>
            <label class="block text-slate-500 mb-1">Fejezet</label>
            <input v-model="testContext.chapter" class="w-full p-2 rounded bg-slate-50 border border-slate-300 focus:border-indigo-500 outline-none" />
          </div>
        </div>
        <div class="mt-3">
            <label class="block text-slate-500 mb-1">Jegyzet / Tananyag (RAG forrás)</label>
            <textarea v-model="testContext.notes" rows="2" class="w-full p-2 rounded bg-slate-50 border border-slate-300 text-xs focus:border-indigo-500 outline-none"></textarea>
        </div>
      </div>

      <div 
        ref="chatContainer"
        class="flex-grow bg-white rounded-xl shadow-inner border border-slate-200 overflow-y-auto p-4 space-y-4"
      >
        <div 
          v-for="msg in messages" 
          :key="msg.id" 
          :class="['flex', msg.sender === 'user' ? 'justify-end' : 'justify-start']"
        >
          <div 
            :class="[
              'max-w-[85%] p-4 rounded-2xl leading-relaxed shadow-sm',
              msg.sender === 'user' 
                ? 'bg-indigo-600 text-white rounded-br-none'
                : 'bg-slate-100 text-slate-800 rounded-bl-none border border-slate-200'
            ]"
          >
            <div v-if="msg.sender === 'ai'" 
                 v-html="renderMarkdown(msg.text)" 
                 class="markdown-body text-sm">
            </div>
            <div v-else class="text-sm whitespace-pre-wrap">
                {{ msg.text }}
            </div>

          </div>
        </div>

        <div v-if="isLoading" class="flex justify-start">
            <div class="bg-slate-100 p-3 rounded-2xl rounded-bl-none text-slate-500 text-sm animate-pulse border border-slate-200">
                Axel gondolkodik... 🧠
            </div>
        </div>
      </div>

      <form @submit.prevent="sendMessage" class="flex gap-2">
        <input 
          v-model="userInput" 
          type="text" 
          placeholder="Írj valamit..."
          class="flex-grow p-4 rounded-xl bg-white border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm"
        />
        <button 
          type="submit" 
          :disabled="isLoading || !userInput"
          class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 rounded-xl font-bold transition-colors shadow-sm"
        >
          Küldés
        </button>
      </form>

    </div>
  </div>
</template>

<style>

.markdown-body {
  line-height: 1.6;
  color: inherit;
}

/* Címsorok */
.markdown-body h1, .markdown-body h2, .markdown-body h3 {
  font-weight: 700;
  margin-top: 1em;
  margin-bottom: 0.5em;
  color: inherit;
}

.markdown-body h1 { font-size: 1.4em; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.3em; }
.markdown-body h2 { font-size: 1.2em; }
.markdown-body h3 { font-size: 1.1em; text-decoration: underline; }

.markdown-body ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
.markdown-body ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
.markdown-body li { margin-bottom: 0.25em; }

.markdown-body strong { font-weight: 800; color: inherit; }
.markdown-body a { color: #2563eb; text-decoration: underline; }

.markdown-body code {
  background-color: rgba(0,0,0,0.05);
  padding: 0.2em 0.4em;
  border-radius: 4px;
  font-family: monospace;
  font-weight: 600;
  color: #db2777;
}

.markdown-body pre {
  background-color: #1e293b;
  color: #f1f5f9;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin-bottom: 1rem;
}
.markdown-body pre code {
  background-color: transparent;
  color: inherit;
  padding: 0;
}
</style>