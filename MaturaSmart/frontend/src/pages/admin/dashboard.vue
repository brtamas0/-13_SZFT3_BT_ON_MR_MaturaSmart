<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({ users: 0, subjects: 0, topics: 0 })
const loading = ref(true)

// --- MODAL VÁLTOZÓK ---
const showMessageModal = ref(false)
const messageForm = ref({ title: '', message: '' })
const isSending = ref(false)
const notificationStatus = ref(null) // 'success' vagy 'error'

onMounted(async () => {
    fetchStats()
})

const fetchStats = async () => {
    const token = localStorage.getItem('token')
    try {
        const res = await fetch('http://backend.maturasmart.hu/api/admin/stats', {
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        if (res.ok) stats.value = await res.json()
    } catch (e) {
        console.error("Hiba:", e)
    } finally {
        loading.value = false
    }
}

const sendSystemMessage = async () => {
    if(!messageForm.value.title || !messageForm.value.message) return;

    isSending.value = true
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
            notificationStatus.value = 'success'
            // Form törlése és modal bezárása 2mp múlva
            setTimeout(() => {
                showMessageModal.value = false
                messageForm.value = { title: '', message: '' }
                notificationStatus.value = null
            }, 1500)
        } else {
            notificationStatus.value = 'error'
        }
    } catch (e) {
        console.error(e)
        notificationStatus.value = 'error'
    } finally {
        isSending.value = false
    }
}
</script>

<template>
  <div class="relative">
    <h1 class="text-3xl font-bold mb-8 text-white">Vezérlőpult</h1>

    <div v-if="loading" class="text-blue-400 animate-pulse">Adatok betöltése...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl group-hover:scale-110 transition duration-500">👥</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Felhasználók</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.users }}</p>
        </div>

        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl group-hover:scale-110 transition duration-500">📚</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Tantárgyak</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.subjects }}</p>
        </div>

        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl group-hover:scale-110 transition duration-500">📄</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Témakörök</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.topics }}</p>
        </div>
    </div>
    
    <div class="mt-10 bg-[#131b3d] p-6 rounded-2xl border border-gray-800">
        <h3 class="text-white font-bold mb-4">Gyorsműveletek</h3>
        <div class="flex gap-4">
            <a href="/admin/subjects/">
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold transition text-sm">
                + Új Tantárgy
            </button>
            </a>
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
                <h3 class="text-xl font-bold text-white">📢 Üzenet minden felhasználónak</h3>
                <button @click="showMessageModal = false" class="text-gray-400 hover:text-white transition">✖</button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Cím</label>
                    <input 
                        v-model="messageForm.title"
                        type="text" 
                        placeholder="Pl. Karbantartás értesítés"
                        class="w-full bg-[#131b3d] border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-blue-500 transition"
                    >
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Üzenet szövege</label>
                    <textarea 
                        v-model="messageForm.message"
                        rows="4"
                        placeholder="Írd ide a rendszerüzenetet..."
                        class="w-full bg-[#131b3d] border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-blue-500 transition resize-none"
                    ></textarea>
                </div>
            </div>

            <div v-if="notificationStatus === 'success'" class="mt-4 p-3 bg-green-500/20 text-green-300 rounded-lg text-sm text-center">
                ✅ Üzenet sikeresen elküldve!
            </div>
            <div v-if="notificationStatus === 'error'" class="mt-4 p-3 bg-red-500/20 text-red-300 rounded-lg text-sm text-center">
                ❌ Hiba történt a küldéskor.
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button 
                    @click="showMessageModal = false"
                    class="px-4 py-2 text-gray-400 hover:text-white transition font-medium text-sm"
                >
                    Mégse
                </button>
                <button 
                    @click="sendSystemMessage"
                    :disabled="isSending || !messageForm.title || !messageForm.message"
                    class="bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg font-bold transition text-sm flex items-center gap-2"
                >
                    <span v-if="isSending" class="animate-spin">⏳</span>
                    <span v-else>Küldés 🚀</span>
                </button>
            </div>

        </div>
    </div>

  </div>
</template>