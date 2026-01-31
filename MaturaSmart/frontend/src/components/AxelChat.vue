<script setup>
import { ref } from 'vue'

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