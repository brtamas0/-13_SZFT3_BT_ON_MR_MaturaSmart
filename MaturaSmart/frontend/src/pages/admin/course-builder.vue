<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import PasswordConfirmModal from '@/components/PasswordConfirmModal.vue'

const props = defineProps(['id'])

const units = ref([])
const subjectName = ref('')
const loading = ref(true)

// Struktúra változók
const newUnitTitle = ref('')
const showModal = ref(false)
const actionToDelete = ref(null)

// SZERKESZTŐ ÁLLAPOTOK
const isEditing = ref(false)
const activeTab = ref('content') // 'content', 'quiz', 'flashcards', 'settings'
const editingTopic = ref(null) 
const editorContent = ref('') 

// KVÍZ VÁLTOZÓK
const questions = ref([])
const newQuestion = ref({
    content: '',
    xp: 10,
    answers: [
        { text: '', is_correct: true },
        { text: '', is_correct: false },
        { text: '', is_correct: false },
        { text: '', is_correct: false }
    ]
})

// FLASHCARD VÁLTOZÓK
const flashcards = ref([])
const newFlashcard = ref({ front: '', back: '' })

onMounted(async () => { await fetchStructure() })

// STRUKTÚRA BETÖLTÉSE
const fetchStructure = async () => {
    loading.value = true
    const token = localStorage.getItem('token')
    try {
        const subRes = await fetch(`http://backend.vm1.test/api/admin/subjects/${props.id}`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }})
        if(subRes.ok) subjectName.value = (await subRes.json()).name

        const res = await fetch(`http://backend.vm1.test/api/admin/subjects/${props.id}/units`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        })
        
        const data = await res.json()
        for (let unit of data) {
            const tRes = await fetch(`http://backend.vm1.test/api/admin/units/${unit.id}/topics`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            })
            unit.topics = await tRes.json()
            unit.newTopicTitle = ''
        }
        units.value = data
    } catch (e) { console.error(e) } finally { loading.value = false }
}

// EDITOR MEGNYITÁSA & ADATOK BETÖLTÉSE
const openEditor = async (topic) => {
    editingTopic.value = { ...topic }
    editorContent.value = topic.content || '<h3>👋 Üdv a szerkesztőben!</h3>\n<p>Kezdd el írni a tananyagot...</p>'
    activeTab.value = 'content'
    isEditing.value = true
    
    await loadQuestions(topic.id)
    await loadFlashcards(topic.id)
}

// KVÍZ LOGIKA
const loadQuestions = async (topicId) => {
    const token = localStorage.getItem('token')
    const res = await fetch(`http://backend.vm1.test/api/admin/topics/${topicId}/questions`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }})
    if(res.ok) questions.value = await res.json()
}

const addQuestion = async () => {
    if(!newQuestion.value.content) return alert("Írd be a kérdést!")
    const token = localStorage.getItem('token')
    
    await fetch(`http://backend.vm1.test/api/admin/topics/${editingTopic.value.id}/questions`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        body: JSON.stringify(newQuestion.value)
    })
    
    // Reset form
    newQuestion.value = { content: '', xp: 10, answers: [{ text: '', is_correct: true }, { text: '', is_correct: false }, { text: '', is_correct: false }, { text: '', is_correct: false }] }
    loadQuestions(editingTopic.value.id)
}

const deleteQuestion = async (id) => {
    const token = localStorage.getItem('token')
    await fetch(`http://backend.vm1.test/api/admin/questions/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } })
    loadQuestions(editingTopic.value.id)
}

const setCorrectAnswer = (index) => {
    newQuestion.value.answers.forEach((a, i) => a.is_correct = (i === index))
}

// FLASHCARD LOGIKA
const loadFlashcards = async (topicId) => {
    const token = localStorage.getItem('token')
    const res = await fetch(`http://backend.vm1.test/api/admin/topics/${topicId}/flashcards`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }})
    if(res.ok) flashcards.value = await res.json()
}

const addFlashcard = async () => {
    if(!newFlashcard.value.front || !newFlashcard.value.back) return
    const token = localStorage.getItem('token')
    await fetch(`http://backend.vm1.test/api/admin/topics/${editingTopic.value.id}/flashcards`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        body: JSON.stringify(newFlashcard.value)
    })
    newFlashcard.value = { front: '', back: '' }
    loadFlashcards(editingTopic.value.id)
}

const deleteFlashcard = async (id) => {
    const token = localStorage.getItem('token')
    await fetch(`http://backend.vm1.test/api/admin/flashcards/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } })
    loadFlashcards(editingTopic.value.id)
}

// FŐ MENTÉS (HTML + BEÁLLÍTÁSOK)
const saveTopicSettings = async () => {
    const token = localStorage.getItem('token')
    await fetch(`http://backend.vm1.test/api/admin/topics/${editingTopic.value.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        body: JSON.stringify({ 
            title: editingTopic.value.title,
            content: editorContent.value,
            xp: editingTopic.value.xp // XP mentése
        })
    })
    alert("Sikeres mentés!")
    fetchStructure() // Lista frissítése
}

// --- STRUKTÚRA MŰVELETEK ---
const addUnit = async () => {
    const token = localStorage.getItem('token'); await fetch(`http://backend.vm1.test/api/admin/subjects/${props.id}/units`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, body: JSON.stringify({ title: newUnitTitle.value, order: units.value.length + 1 }) }); newUnitTitle.value = ''; fetchStructure(); 
}
const addTopic = async (unit) => { 
    const token = localStorage.getItem('token'); await fetch(`http://backend.vm1.test/api/admin/units/${unit.id}/topics`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, body: JSON.stringify({ title: unit.newTopicTitle, xp: 100, order: unit.topics.length + 1 }) }); fetchStructure(); 
}
const reqDelete = (type, id) => { actionToDelete.value = { type, id }; showModal.value = true }
const executeDelete = async () => { 
    showModal.value = false; const token = localStorage.getItem('token'); const endpoint = actionToDelete.value.type === 'unit' ? `units/${actionToDelete.value.id}` : `topics/${actionToDelete.value.id}`; await fetch(`http://backend.vm1.test/api/admin/${endpoint}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } }); fetchStructure(); 
}

</script>

<template>
  <div class="h-full flex flex-col">
    
    <div v-if="!isEditing" class="flex-1 overflow-y-auto">
        <div class="flex items-center gap-4 mb-8 border-b border-gray-800 pb-4">
            <RouterLink to="/admin/subjects" class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-bold text-gray-300 transition">← Vissza</RouterLink>
            <div>
                <h1 class="text-2xl font-bold text-white">Kurzusépítő</h1>
                <p class="text-gray-400 text-sm">{{ subjectName }} tananyagának szerkesztése</p>
            </div>
        </div>

        <div v-if="loading" class="text-center py-10 text-blue-400 animate-pulse">Betöltés...</div>

        <div v-else class="space-y-8 max-w-5xl mx-auto pb-20">
            <div v-for="unit in units" :key="unit.id" class="bg-[#131b3d] border border-gray-700 rounded-xl overflow-hidden shadow-md animate-fade-in">
                <div class="bg-[#0b102e]/50 p-4 flex justify-between items-center border-b border-gray-700/50">
                    <h3 class="font-bold text-lg text-blue-200">📂 {{ unit.title }}</h3>
                    <button @click="reqDelete('unit', unit.id)" class="text-red-400 text-xs font-bold px-2 py-1 border border-red-900/30 rounded">TÖRLÉS</button>
                </div>
                <div class="p-4 space-y-2">
                    <div v-for="topic in unit.topics" :key="topic.id" class="flex items-center justify-between bg-[#0b102e] p-3 rounded-lg border border-gray-700/50 hover:border-blue-500/30 group transition">
                        <div class="flex items-center gap-3">
                            <span class="text-gray-500 text-lg">📄</span>
                            <span class="font-medium text-gray-200">{{ topic.title }}</span>
                            <span class="text-xs text-gray-500 bg-gray-800 px-2 rounded">{{ topic.xp }} XP</span>
                        </div>
                        <div class="flex gap-2">
                            <button @click="openEditor(topic)" class="bg-blue-600 text-white hover:bg-blue-500 px-3 py-1.5 rounded text-xs font-bold shadow-lg shadow-blue-900/20">✏️ SZERKESZTÉS</button>
                            <button @click="reqDelete('topic', topic.id)" class="text-gray-500 hover:text-red-400 px-2">×</button>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2 pl-4 border-l-2 border-gray-700 ml-2">
                        <input v-model="unit.newTopicTitle" placeholder="+ Új lecke címe..." class="bg-[#0b102e] border border-gray-700 rounded-lg px-3 py-2 text-sm text-white flex-1 focus:border-blue-500 outline-none" @keyup.enter="addTopic(unit)" />
                        <button @click="addTopic(unit)" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Hozzáadás</button>
                    </div>
                </div>
            </div>

            <div class="border-2 border-dashed border-gray-700 rounded-xl p-6 text-center hover:border-gray-500 transition bg-[#131b3d]/30">
                <div class="flex max-w-md mx-auto gap-2">
                    <input v-model="newUnitTitle" placeholder="Új mappa neve..." class="bg-[#0b102e] border border-gray-700 rounded-lg px-4 py-2 text-white flex-1 outline-none focus:border-blue-500" />
                    <button @click="addUnit" class="bg-green-600 hover:bg-green-500 text-white px-6 py-2 rounded-lg font-bold">Létrehozás</button>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="fixed inset-0 z-50 bg-[#0b102e] flex flex-col animate-fade-in">
        <div class="h-16 border-b border-gray-700 flex items-center justify-between px-6 bg-[#06091a]">
            <div class="flex items-center gap-4">
                <button @click="isEditing = false" class="text-gray-400 hover:text-white font-bold text-sm">← Vissza</button>
                <div class="h-6 w-px bg-gray-700"></div>
                <h2 class="font-bold text-white text-lg">{{ editingTopic.title }}</h2>
            </div>
            
            <div class="flex bg-gray-800 rounded-lg p-1 gap-1">
                <button @click="activeTab = 'content'" :class="activeTab === 'content' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-1.5 rounded-md text-sm font-bold transition">📄 Tartalom</button>
                <button @click="activeTab = 'quiz'" :class="activeTab === 'quiz' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-1.5 rounded-md text-sm font-bold transition">❓ Kvíz</button>
                <button @click="activeTab = 'flashcards'" :class="activeTab === 'flashcards' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-1.5 rounded-md text-sm font-bold transition">🃏 Kártyák</button>
                <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-1.5 rounded-md text-sm font-bold transition">⚙️ Beállítások</button>
            </div>

            <button @click="saveTopicSettings" class="bg-green-600 hover:bg-green-500 px-6 py-2 rounded-lg text-white font-bold shadow-lg shadow-green-900/40">
                💾 MENTÉS
            </button>
        </div>
        
        <div v-if="activeTab === 'content'" class="flex-1 flex overflow-hidden">
            <div class="w-1/2 flex flex-col border-r border-gray-700 bg-[#0d1117]">
                <textarea v-model="editorContent" class="flex-1 bg-[#0d1117] text-blue-300 font-mono p-4 outline-none resize-none text-sm leading-relaxed scrollbar-thin"></textarea>
            </div>
            <div class="w-1/2 flex flex-col bg-[#0b102e] border-l border-gray-700">
                <div class="flex-1 p-8 prose prose-invert max-w-none overflow-y-auto text-gray-200" v-html="editorContent"></div>
            </div>
        </div>

        <div v-if="activeTab === 'quiz'" class="flex-1 overflow-y-auto p-8 bg-[#0b102e]">
            <div class="max-w-4xl mx-auto">
                <h3 class="text-2xl font-bold text-white mb-6">Kérdések kezelése</h3>
                
                <div class="bg-[#131b3d] p-6 rounded-xl border border-gray-700 mb-8">
                    <h4 class="font-bold text-blue-400 mb-4">Új kérdés hozzáadása</h4>
                    <input v-model="newQuestion.content" placeholder="Mi a kérdés?" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white mb-4 focus:border-blue-500 outline-none" />
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div v-for="(ans, i) in newQuestion.answers" :key="i" class="flex items-center gap-2 bg-[#0b102e] p-2 rounded border border-gray-700">
                            <input type="radio" name="correct" :checked="ans.is_correct" @change="setCorrectAnswer(i)" class="cursor-pointer accent-green-500 w-4 h-4" />
                            <input v-model="ans.text" :placeholder="(i+1) + '. válaszlehetőség'" class="bg-transparent text-white w-full outline-none text-sm" />
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <span>XP Jutalom:</span>
                            <input v-model="newQuestion.xp" type="number" class="bg-[#0b102e] border border-gray-700 w-16 p-1 rounded text-center text-white" />
                        </div>
                        <button @click="addQuestion" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-lg font-bold">Kérdés Hozzáadása</button>
                    </div>
                </div>

                <div class="space-y-4">
                    <div v-for="q in questions" :key="q.id" class="bg-gray-800 p-4 rounded-xl border border-gray-700 flex justify-between items-start">
                        <div>
                            <p class="font-bold text-white text-lg mb-2">{{ q.content }} <span class="text-xs bg-gray-700 px-2 py-0.5 rounded text-gray-400">{{ q.xp }} XP</span></p>
                            <ul class="space-y-1">
                                <li v-for="a in q.answers" :key="a.id" :class="a.is_correct ? 'text-green-400 font-bold' : 'text-gray-400'" class="text-sm flex items-center gap-2">
                                    <span v-if="a.is_correct">✅</span>
                                    <span v-else>⚪</span>
                                    {{ a.text }}
                                </li>
                            </ul>
                        </div>
                        <button @click="deleteQuestion(q.id)" class="text-red-400 hover:bg-red-900/20 p-2 rounded">🗑️</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'flashcards'" class="flex-1 overflow-y-auto p-8 bg-[#0b102e]">
            <div class="max-w-4xl mx-auto">
                <h3 class="text-2xl font-bold text-white mb-6">Tanulókártyák</h3>
                
                <div class="bg-[#131b3d] p-6 rounded-xl border border-gray-700 mb-8 flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="text-gray-400 text-xs uppercase font-bold mb-1 block">Kártya eleje (Kérdés)</label>
                        <input v-model="newFlashcard.front" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white focus:border-blue-500 outline-none" />
                    </div>
                    <div class="flex-1">
                        <label class="text-gray-400 text-xs uppercase font-bold mb-1 block">Kártya hátulja (Válasz)</label>
                        <input v-model="newFlashcard.back" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white focus:border-blue-500 outline-none" />
                    </div>
                    <button @click="addFlashcard" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-lg font-bold">Hozzáadás</button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div v-for="card in flashcards" :key="card.id" class="bg-gray-800 p-4 rounded-xl border border-gray-700 relative group">
                        <button @click="deleteFlashcard(card.id)" class="absolute top-2 right-2 text-gray-500 hover:text-red-500">×</button>
                        <div class="text-white font-bold mb-2 pb-2 border-b border-gray-700">{{ card.front }}</div>
                        <div class="text-blue-400">{{ card.back }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'settings'" class="flex-1 p-8 bg-[#0b102e]">
            <div class="max-w-xl mx-auto bg-[#131b3d] p-8 rounded-xl border border-gray-700">
                <h3 class="text-xl font-bold text-white mb-6">Lecke beállításai</h3>
                
                <div class="mb-4">
                    <label class="text-gray-400 block mb-2">Lecke címe</label>
                    <input v-model="editingTopic.title" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white" />
                </div>

                <div class="mb-4">
                    <label class="text-gray-400 block mb-2">XP Jutalom (Elolvasásért)</label>
                    <input v-model="editingTopic.xp" type="number" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white" />
                    <p class="text-xs text-gray-500 mt-1">Ezt kapja meg a diák, ha végigolvasta a leckét.</p>
                </div>
            </div>
        </div>

    </div>

    <PasswordConfirmModal :visible="showModal" @confirm="executeDelete" @cancel="showModal = false" />
  </div>
</template>