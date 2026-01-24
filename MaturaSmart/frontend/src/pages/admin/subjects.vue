<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PasswordConfirmModal from '@/components/PasswordConfirmModal.vue'

const subjects = ref([])
const router = useRouter()
const isCreating = ref(false)
const newSubject = ref({ name: '', description: '', icon: '📘' })

// Állapotok
const loading = ref(true)
const error = ref(null)

// Modal állapot
const showModal = ref(false)
const itemToDelete = ref(null)

onMounted(async () => { 
    await fetchSubjects() 
})

// Adatok lekérése
const fetchSubjects = async () => {
    loading.value = true
    error.value = null
    const token = localStorage.getItem('token')
    
    try {
        const res = await fetch('http://backend.vm1.test/api/admin/subjects', {
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })

        if (!res.ok) throw new Error(`Hiba: ${res.status}`)

        subjects.value = await res.json()
    } catch (e) {
        console.error(e)
        error.value = "Nem sikerült betölteni a tantárgyakat."
    } finally {
        loading.value = false
    }
}

// Új létrehozása
const createSubject = async () => {
    if (!newSubject.value.name) return alert("A név kötelező!")

    const token = localStorage.getItem('token')
    try {
        const res = await fetch('http://backend.vm1.test/api/admin/subjects', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify(newSubject.value)
        })
        
        if (!res.ok) throw new Error('Hiba a mentéskor')

        // Sikeres mentés után
        isCreating.value = false
        newSubject.value = { name: '', description: '', icon: '📘' }
        fetchSubjects()
    } catch (e) {
        alert("Hiba történt a tantárgy létrehozásakor!")
    }
}

// Törlés előkészítése
const confirmDelete = (id) => {
    itemToDelete.value = id
    showModal.value = true
}

// Törlés végrehajtása
const executeDelete = async () => {
    showModal.value = false
    const token = localStorage.getItem('token')
    
    try {
        const res = await fetch(`http://backend.vm1.test/api/admin/subjects/${itemToDelete.value}`, {
            method: 'DELETE',
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })

        if(res.ok) {
            fetchSubjects()
        } else {
            alert("Nem sikerült a törlés.")
        }
    } catch (e) {
        alert("Hálózati hiba a törléskor!")
    }
}
</script>

<template>
  <div>
    <div class="flex justify-between items-end mb-10 border-b border-gray-800 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Tantárgyak</h1>
            <p class="text-gray-400">A kurzusok és tananyagok legfelső szintje.</p>
        </div>
        <button 
            @click="isCreating = !isCreating" 
            class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-lg shadow-blue-900/20 flex items-center gap-2"
        >
            <span>{{ isCreating ? '✕ Mégse' : '+ Új Tantárgy' }}</span>
        </button>
    </div>

    <div v-if="isCreating" class="bg-[#131b3d] p-6 rounded-2xl mb-8 border border-blue-500/30 shadow-lg animate-fade-in">
        <h3 class="text-white font-bold mb-4">Új tantárgy adatai</h3>
        <div class="grid gap-4">
            <div class="flex gap-4">
                <input v-model="newSubject.icon" placeholder="Emoji" class="bg-[#0b102e] border border-gray-700 p-3 rounded-xl text-white w-20 text-center text-xl focus:border-blue-500 outline-none placeholder-gray-600" />
                <input v-model="newSubject.name" placeholder="Tantárgy neve (pl. Történelem)" class="bg-[#0b102e] border border-gray-700 p-3 rounded-xl text-white flex-1 focus:border-blue-500 outline-none placeholder-gray-600" />
            </div>
            <textarea v-model="newSubject.description" placeholder="Rövid leírás a tantárgyról..." class="bg-[#0b102e] border border-gray-700 p-3 rounded-xl text-white w-full h-24 focus:border-blue-500 outline-none resize-none placeholder-gray-600"></textarea>
            <div class="flex justify-end">
                <button @click="createSubject" class="bg-green-600 hover:bg-green-500 py-2 px-6 rounded-xl text-white font-bold transition shadow-lg shadow-green-900/20">Mentés</button>
            </div>
        </div>
    </div>

    <div v-if="error" class="bg-red-900/50 border border-red-500 text-red-200 p-4 rounded-xl mb-6">
        ⚠️ {{ error }}
    </div>

    <div v-if="loading" class="text-center py-10">
        <div class="text-blue-400 text-xl font-bold animate-pulse">Adatok betöltése...</div>
    </div>

    <div v-else-if="subjects.length === 0 && !error && !isCreating" class="text-center py-16 text-gray-500 border-2 border-dashed border-gray-800 rounded-2xl">
        <div class="text-4xl mb-4">📚</div>
        <p>Nincs még felvéve tantárgy.</p>
        <button @click="isCreating = true" class="text-blue-400 font-bold mt-2 hover:underline">Kattints ide a létrehozáshoz!</button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div v-for="sub in subjects" :key="sub.id" class="group bg-[#131b3d] border border-gray-800 hover:border-blue-500/50 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl -mr-10 -mt-10 transition group-hover:bg-blue-500/10"></div>

            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-[#0b102e] flex items-center justify-center text-3xl border border-gray-700 shadow-inner">
                    {{ sub.icon }}
                </div>
                <button @click="confirmDelete(sub.id)" class="text-gray-600 hover:text-red-500 transition p-2 bg-gray-900/50 rounded-lg hover:bg-red-900/20">
                    🗑️
                </button>
            </div>
            
            <h3 class="text-xl font-bold text-white mb-2 relative z-10">{{ sub.name }}</h3>
            <p class="text-gray-400 text-sm mb-6 line-clamp-2 h-10 relative z-10">{{ sub.description || 'Nincs leírás megadva.' }}</p>
            
            <button 
                @click="router.push({ name: 'CourseBuilder', params: { id: sub.id } })" 
                class="relative z-10 w-full bg-[#0b102e] hover:bg-blue-600 hover:text-white text-blue-400 border border-blue-900/30 py-3 rounded-xl font-bold transition text-sm flex items-center justify-center gap-2 group-hover:border-blue-500/50"
            >
                <span>Tartalom Szerkesztése</span>
                <span>→</span>
            </button>
        </div>
    </div>

    <PasswordConfirmModal 
        :visible="showModal" 
        @confirm="executeDelete" 
        @cancel="showModal = false" 
    />
  </div>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>