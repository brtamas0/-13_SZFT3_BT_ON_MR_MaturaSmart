<script setup>
import { ref, onMounted } from 'vue'


const globalMessage = ref(null)
const showPopup = ref(false)
const currentUserId = ref(null)

onMounted(async () => {
    
    
    const token = localStorage.getItem('token')
    if (!token) return;

    
    const storedUser = localStorage.getItem('user')
    if (storedUser) {
        try {
            const parsedUser = JSON.parse(storedUser)
            currentUserId.value = parsedUser.id
        } catch (e) { console.error(e) }
    }

    
    try {
        const res = await fetch('http://backend.maturasmart.hu/api/global-message', {
            headers: { 
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })
        
        if (res.ok) {
            const data = await res.json()
            
            
            if (data && data.id) {
                
                const userIdSuffix = currentUserId.value ? `_u${currentUserId.value}` : '_unknown'
                const storageKey = `hide_msg_${data.id}${userIdSuffix}`
                
                const isHidden = localStorage.getItem(storageKey)
                
                
                if (!isHidden) {
                    globalMessage.value = data
                    showPopup.value = true
                }
            }
        }
    } catch (e) {
        console.error("Global notification error:", e)
    }
})

const closePopup = () => {
    if (!globalMessage.value) return
    
    const userIdSuffix = currentUserId.value ? `_u${currentUserId.value}` : '_unknown'
    const storageKey = `hide_msg_${globalMessage.value.id}${userIdSuffix}`

    localStorage.setItem(storageKey, 'true')
    showPopup.value = false
}

const getIcon = (type) => {
    switch(type) {
        case 'warning': return '⚠️';
        case 'danger': return '🚨';
        case 'success': return '✅';
        default: return '📢';
    }
}
</script>

<template>
    <transition name="pop">
        <div v-if="showPopup" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-md">
            
            <div class="bg-[#1e293b] border border-gray-600 w-full max-w-md rounded-2xl shadow-2xl p-6 relative overflow-hidden transform transition-all">
                
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-2xl -mr-10 -mt-10 opacity-20"
                     :class="{
                        'bg-blue-500': globalMessage.type === 'info',
                        'bg-yellow-500': globalMessage.type === 'warning',
                        'bg-red-500': globalMessage.type === 'danger',
                        'bg-green-500': globalMessage.type === 'success'
                     }">
                </div>

                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">{{ getIcon(globalMessage.type) }}</span>
                            <h3 class="text-xl font-bold text-white">{{ globalMessage.title }}</h3>
                        </div>
                        <button @click="closePopup" class="text-gray-400 hover:text-white transition p-1 hover:bg-white/10 rounded-lg">✖</button>
                    </div>

                    <div class="text-gray-300 text-sm leading-relaxed mb-6 whitespace-pre-line">
                        {{ globalMessage.message }}
                    </div>

                    <button 
                        @click="closePopup"
                        class="w-full py-3 rounded-xl font-bold text-sm text-white transition shadow-lg transform active:scale-95"
                        :class="{
                            'bg-blue-600 hover:bg-blue-500 shadow-blue-600/20': globalMessage.type === 'info',
                            'bg-yellow-600 hover:bg-yellow-500 shadow-yellow-600/20': globalMessage.type === 'warning',
                            'bg-red-600 hover:bg-red-500 shadow-red-600/20': globalMessage.type === 'danger',
                            'bg-green-600 hover:bg-green-500 shadow-green-600/20': globalMessage.type === 'success'
                        }">
                        Rendben, értettem 👍
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.pop-enter-active, .pop-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.pop-enter-from, .pop-leave-to { opacity: 0; transform: scale(0.95) translateY(20px); }
</style>