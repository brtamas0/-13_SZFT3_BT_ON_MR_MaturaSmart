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
                </div>
            </div>
        </div>
    </div>
</div>
</template>