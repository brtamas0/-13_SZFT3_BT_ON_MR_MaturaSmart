<script setup>
import { ref } from 'vue'

const props = defineProps(['visible'])
const emit = defineEmits(['confirm', 'cancel'])

const password = ref('')
const error = ref('')
const isLoading = ref(false)

const handleConfirm = async () => {
  error.value = ''
  isLoading.value = true
  
  const token = localStorage.getItem('token')
  
  try {
    const res = await fetch('http://backend.vm1.test/api/admin/verify-password', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ password: password.value })
    })

    if (res.ok) {
        emit('confirm')
        password.value = ''
    } else {
        error.value = 'Hibás jelszó!'
    }
  } catch (e) {
    error.value = 'Hálózati hiba'
  } finally {
    isLoading.value = false
  }
}

</script>

<template>
  <div v-if="visible" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-gray-900 border border-red-500/50 rounded-xl p-6 w-full max-w-md shadow-2xl shadow-red-900/50">
        <h3 class="text-xl font-bold text-white mb-2">⚠️ Biztonsági ellenőrzés</h3>
        <p class="text-gray-400 mb-4 text-sm">A törlés végrehajtásához add meg az admin jelszavad.</p>

        <input 
            v-model="password" 
            type="password" 
            placeholder="Jelszó..." 
            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white mb-2 focus:border-red-500 outline-none transition"
            @keyup.enter="handleConfirm"
        />
        
        <p v-if="error" class="text-red-500 text-sm mb-4 font-bold">{{ error }}</p>

        <div class="flex gap-3 mt-4">
            <button @click="$emit('cancel')" class="flex-1 px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 font-bold transition">Mégse</button>
            <button @click="handleConfirm" :disabled="isLoading" class="flex-1 px-4 py-2 rounded-lg bg-red-600 text-white font-bold hover:bg-red-500 transition shadow-lg shadow-red-900/30">
                {{ isLoading ? 'Ellenőrzés...' : 'Törlés' }}
            </button>
        </div>
    </div>
  </div>
</template>
