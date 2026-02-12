<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({ users: 0, subjects: 0, topics: 0 })
const loading = ref(true)


const showMessageModal = ref(false)
const messageForm = ref({ title: '', message: '', type: 'info', expires_at: '' })
const isSending = ref(false)
const statusMsg = ref(null) 

onMounted(async () => {
    fetchStats()
})

const fetchStats = async () => {
    const token = localStorage.getItem('token')
    try {
        const res = await fetch('http://backend.maturasmart.hu/api/admin/stats', {
            headers: { 'Authorization': `Bearer ${token}` }
        })
        if (res.ok) stats.value = await res.json()
    } catch (e) { console.error(e) } 
    finally { loading.value = false }
}

const sendSystemMessage = async () => {
    if(!messageForm.value.title || !messageForm.value.message) return;

    isSending.value = true
    statusMsg.value = null
    const token = localStorage.getItem('token')

    try {
        const res = await fetch('http://backend.maturasmart.hu/api/admin/system-message', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(messageForm.value)
        })

        if (res.ok) {
            statusMsg.value = { type: 'success', text: 'Üzenet sikeresen aktiválva! 🚀' }
            setTimeout(() => {
                showMessageModal.value = false
                messageForm.value = { title: '', message: '', type: 'info', expires_at: '' }
                statusMsg.value = null
            }, 1500)
        } else {
            statusMsg.value = { type: 'error', text: 'Hiba történt a mentéskor.' }
        }
    } catch (e) {
        statusMsg.value = { type: 'error', text: 'Hálózati hiba.' }
    } finally {
        isSending.value = false
    }
}
</script>

<template>
  <div>
    <h1 class="text-3xl font-bold mb-8 text-white">Vezérlőpult</h1>

    <div v-if="loading" class="text-blue-400 animate-pulse">Adatok betöltése...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-50 text-6xl">👥</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Felhasználók</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.users }}</p>
        </div>
        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-50 text-6xl">📚</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Tantárgyak</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.subjects }}</p>
        </div>
        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-50 text-6xl">📄</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Leckék száma</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.topics }}</p>
        </div>
    </div>
    
    <div class="mt-10 bg-[#131b3d] p-6 rounded-2xl border border-gray-800">
        <h3 class="text-white font-bold mb-4">Gyorsműveletek</h3>
        <div class="flex gap-4">
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold transition text-sm">
                + Új Tantárgy
            </button>
            <button 
                @click="showMessageModal = true"
                class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-bold transition text-sm flex items-center gap-2">
                📢 Rendszerüzenet küldése
            </button>
        </div>
    </div>

    <div v-if="showMessageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="bg-[#0b102e] w-full max-w-lg rounded-2xl border border-gray-700 shadow-2xl p-6 transform transition-all scale-100">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">📢 Globális Üzenet</h3>
                <button @click="showMessageModal = false" class="text-gray-400 hover:text-white transition">✖</button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Cím</label>
                    <input v-model="messageForm.title" type="text" placeholder="Pl. Karbantartás"
                        class="w-full bg-[#131b3d] border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Üzenet</label>
                    <textarea v-model="messageForm.message" rows="3" placeholder="Az üzenet szövege..."
                        class="w-full bg-[#131b3d] border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-blue-500 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Típus</label>
                        <select v-model="messageForm.type" class="w-full bg-[#131b3d] border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-blue-500">
                            <option value="info">🔵 Info (Kék)</option>
                            <option value="warning">🟡 Figyelem (Sárga)</option>
                            <option value="danger">🔴 Hiba (Piros)</option>
                            <option value="success">🟢 Siker (Zöld)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Lejárat (Opcionális)</label>
                        <input v-model="messageForm.expires_at" type="datetime-local" 
                            class="w-full bg-[#131b3d] border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-blue-500 [&::-webkit-calendar-picker-indicator]:invert">
                    </div>
                </div>
            </div>

            <div v-if="statusMsg" class="mt-4 p-3 rounded-lg text-sm text-center"
                 :class="statusMsg.type === 'success' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'">
                {{ statusMsg.text }}
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button @click="showMessageModal = false" class="px-4 py-2 text-gray-400 hover:text-white text-sm">Mégse</button>
                <button @click="sendSystemMessage" :disabled="isSending"
                    class="bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white px-6 py-2 rounded-lg font-bold text-sm flex items-center gap-2">
                    <span v-if="isSending" class="animate-spin">⏳</span>
                    <span v-else>Aktiválás 🚀</span>
                </button>
            </div>

        </div>
    </div>
  </div>
</template>