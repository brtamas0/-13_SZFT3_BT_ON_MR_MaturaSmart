<script setup>
import { ref, onMounted, nextTick } from 'vue'

const toggleTheme = () => {
  const html = document.documentElement
  const currentTheme = html.getAttribute('data-theme')
  const newTheme = (currentTheme === 'dark' || !currentTheme) ? 'light' : 'dark'
  html.setAttribute('data-theme', newTheme)
  localStorage.setItem('theme', newTheme)
}

onMounted(() => {
  const saved = localStorage.getItem('theme') || 'dark'
  document.documentElement.setAttribute('data-theme', saved)
})

const messages = ref([
  { id: 1, sender: 'ai', text: 'Szia! 👋 Én vagyok Axel. Miben segíthetek? 😉' }
])
const userInput = ref('')
const isLoading = ref(false)
const chatContainer = ref(null)

const testContext = ref({
  subject: 'Történelem',
  topic: 'Honfoglalás',
  chapter: 'A törzsek vándorlása',
  notes: 'A magyar törzsek vándorlása során érintették Levédiát és Etelközt...'
})

const renderMarkdown = (text) => {
  if (window.markdownit) {
    return window.markdownit({ html: true, linkify: true, typographer: true }).render(text)
  }
  return text
}

const scrollToBottom = async () => {
  await nextTick()
  if (chatContainer.value) chatContainer.value.scrollTop = chatContainer.value.scrollHeight
}

const sendMessage = async () => {
  if (!userInput.value.trim() || isLoading.value) return
  
  const userMsg = userInput.value
  
  messages.value.push({ id: Date.now(), sender: 'user', text: userMsg })
  userInput.value = ''
  scrollToBottom()
  isLoading.value = true

  const history = messages.value.slice(0, -1).map(msg => ({
    role: msg.sender === 'user' ? 'user' : 'assistant',
    content: msg.text
  }))

  try {
    const res = await fetch('http://backend.maturasmart.hu/api/ask-axel', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        message: userMsg, 
        history: history,
        ...testContext.value 
      })
    })

    const data = await res.json()
    messages.value.push({ id: Date.now()+1, sender: 'ai', text: data.answer })
  
  } catch (e) {
    console.error(e)
    messages.value.push({ id: Date.now()+1, sender: 'ai', text: '⚠️ Hiba: Nem értem el a szervert.' })
  } finally {
    isLoading.value = false
    scrollToBottom()
  }
}
</script>

<template>
  <div class="page-container">
    <div class="ambient-background"></div>

    <nav class="glass-nav">
        <div class="nav-top">
            <div class="logo">Matura<span class="accent-text">Smart</span> <small style="opacity: 0.7; font-size: 0.6em">| AI Teszt</small></div>
            <button @click="toggleTheme" class="theme-btn" title="Témaváltás">🌓</button>
        </div>
    </nav>

    <main class="container chat-layout">
        
        <aside class="card-3d sidebar">
            <h3 class="sidebar-title">🛠️ Kontextus</h3>
            
            <div class="input-group">
                <label>Tantárgy</label>
                <input v-model="testContext.subject" class="glass-input" placeholder="Pl. Történelem" />
            </div>

            <div class="input-group">
                <label>Témakör</label>
                <input v-model="testContext.topic" class="glass-input" placeholder="Pl. Honfoglalás" />
            </div>

            <div class="input-group">
                <label>Fejezet</label>
                <input v-model="testContext.chapter" class="glass-input" placeholder="Pl. Vándorlás" />
            </div>

            <div class="input-group full-height">
                <label>Jegyzet / Forrás</label>
                <textarea 
                    v-model="testContext.notes" 
                    class="glass-input textarea-resize" 
                    rows="10" 
                    placeholder="Másold ide a tananyagot..."
                ></textarea>
            </div>
        </aside>

        <section class="card-3d chat-window">
            <div class="chat-messages" ref="chatContainer">
                <div v-for="msg in messages" :key="msg.id" :class="['message-row', msg.sender]">
                    <div class="message-bubble">
                        <div v-if="msg.sender === 'ai'" v-html="renderMarkdown(msg.text)" class="markdown-body"></div>
                        <div v-else>{{ msg.text }}</div>
                    </div>
                </div>

                <div v-if="isLoading" class="typing-indicator">
                    Axel gépel<span>.</span><span>.</span><span>.</span>
                </div>
            </div>

            <form @submit.prevent="sendMessage" class="chat-input-area">
                <input 
                    v-model="userInput" 
                    type="text" 
                    placeholder="Írj valamit..." 
                    class="glass-input chat-input-field" 
                />
                <button type="submit" :disabled="isLoading || !userInput" class="btn-primary">
                    Küldés ➤
                </button>
            </form>
        </section>

    </main>
  </div>
</template>

<style>

.page-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.chat-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    height: calc(100vh - 80px);
    padding-top: 1rem;
    padding-bottom: 1rem;
    box-sizing: border-box;
}

@media (min-width: 900px) {
    .chat-layout {
        grid-template-columns: 300px 1fr;
    }
}

.sidebar {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    overflow-y: auto;
}

.sidebar-title {
    margin: 0;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    font-size: 1.1rem;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.input-group label {
    font-size: 0.85rem;
    opacity: 0.8;
    font-weight: 600;
}

.glass-input {
    background: rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: inherit;
    padding: 0.8rem;
    border-radius: 12px;
    outline: none;
    transition: 0.2s;
    font-family: inherit;
    width: 100%;
    box-sizing: border-box;
}
[data-theme="light"] .glass-input {
    background: rgba(255, 255, 255, 0.5);
    border-color: rgba(0,0,0,0.1);
}
.glass-input:focus {
    border-color: var(--accent);
    background: rgba(0, 0, 0, 0.2);
}

.textarea-resize {
    resize: none;
    height: 100%;
}
.full-height {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.chat-window {
    display: flex;
    flex-direction: column;
    padding: 0 !important;
    overflow: hidden;
}

.chat-messages {
    flex-grow: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message-row {
    display: flex;
    width: 100%;
}
.message-row.user { justify-content: flex-end; }
.message-row.ai { justify-content: flex-start; }

.message-bubble {
    max-width: 80%;
    padding: 1rem 1.2rem;
    border-radius: 16px;
    line-height: 1.5;
    word-wrap: break-word;
}

.message-row.user .message-bubble {
    background: var(--accent);
    color: white;
    border-bottom-right-radius: 2px;
}
.message-row.ai .message-bubble {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-bottom-left-radius: 2px;
}
[data-theme="light"] .message-row.ai .message-bubble {
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(0,0,0,0.05);
}

.chat-input-area {
    padding: 1rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    display: flex;
    gap: 0.5rem;
    background: rgba(0,0,0,0.02);
}

.typing-indicator {
    font-size: 0.8rem;
    opacity: 0.7;
    margin-left: 1rem;
}
.typing-indicator span {
    animation: blink 1.4s infinite both;
}
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes blink { 0% { opacity: 0.2; } 20% { opacity: 1; } 100% { opacity: 0.2; } }

.markdown-body code {
    background: rgba(0,0,0,0.2);
    padding: 2px 5px;
    border-radius: 4px;
}
</style>