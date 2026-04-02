<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { RouterLink } from 'vue-router'
import PasswordConfirmModal from '@/components/PasswordConfirmModal.vue'
import { VueDraggable } from 'vue-draggable-plus'

const props = defineProps(['id'])
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://backend.maturasmart.hu/api'

const units = ref([])
const subjectName = ref('')
const loading = ref(true)

const newUnitTitle = ref('')
const showModal = ref(false)
const actionToDelete = ref(null)


const isEditing = ref(false)
const activeTab = ref('content') 
const editingTopic = ref(null) 
const editorContent = ref('') 

const showAiModal = ref(false)
const aiSourceText = ref('')
const aiSourceFile = ref(null)
const aiSourceFileName = ref('')
const aiLoading = ref(false)


const questions = ref([])
const totalXp = computed(() => questions.value.reduce((sum, q) => sum + q.xp, 0))

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


const flashcards = ref([])
const newFlashcard = ref({ front: '', back: '' })

onMounted(async () => { await fetchStructure() })




const fetchStructure = async () => {
    loading.value = true
    const token = localStorage.getItem('token')
    try {

        const res = await fetch(`http://backend.vm1.test/api/admin/subjects/${props.id}/units`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        })
        
        const data = await res.json()
        
        for (let unit of data) {
            const tRes = await fetch(`http://backend.vm1.test/api/admin/units/${unit.id}/topics?t=${Date.now()}`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            })
            
            let loadedTopics = await tRes.json()
            loadedTopics.sort((a, b) => a.order - b.order)

            unit.topics = loadedTopics
            unit.newTopicTitle = ''
            unit.newTopicType = 'lesson'
        }
        units.value = data
        subjectName.value = data[0]?.subject?.name || ''
    } catch (e) { console.error(e) } finally { loading.value = false }
}


const openEditor = async (topic) => {
    editingTopic.value = { 
        ...topic, 
        reading_weight: topic.reading_weight !== undefined ? topic.reading_weight : 50 
    }
    editorContent.value = topic.content || '<h3>👋 Üdv a szerkesztőben!</h3>\n<p>Kezdd el írni a tananyagot...</p>'
    activeTab.value = topic.type === 'test' ? 'quiz' : 'content'
    isEditing.value = true
    await loadQuestions(topic.id)
    await loadFlashcards(topic.id)
}


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


const saveTopicSettings = async () => {
    const token = localStorage.getItem('token')
    let finalXp = editingTopic.value.xp
    if (editingTopic.value.type === 'test') {
        finalXp = totalXp.value
    }

    await fetch(`http://backend.vm1.test/api/admin/topics/${editingTopic.value.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        body: JSON.stringify({ 
            title: editingTopic.value.title,
            content: editorContent.value,
            xp: finalXp,
            time_limit_minutes: editingTopic.value.time_limit_minutes,
            passing_percentage: editingTopic.value.passing_percentage,
            reading_weight: editingTopic.value.reading_weight 
        })
    })
    alert("Sikeres mentés!")
    fetchStructure() 
}


const addUnit = async () => {
    const token = localStorage.getItem('token'); 
    await fetch(`http://backend.vm1.test/api/admin/subjects/${props.id}/units`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, body: JSON.stringify({ title: newUnitTitle.value, order: units.value.length + 1 }) }); 
    newUnitTitle.value = ''; fetchStructure(); 
}

const addTopic = async (unit) => { 
    if(!unit.newTopicTitle) return;
    const token = localStorage.getItem('token'); 
    await fetch(`http://backend.vm1.test/api/admin/units/${unit.id}/topics`, { 
        method: 'POST', 
        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, 
        body: JSON.stringify({ 
            title: unit.newTopicTitle, 
            type: unit.newTopicType || 'lesson',
            xp: unit.newTopicType === 'test' ? 0 : 50, 
            order: unit.topics.length + 1,
            reading_weight: 50
        }) 
    }); 
    unit.newTopicTitle = ''; 
    fetchStructure(); 
}

const handleAiFileChange = (event) => {
    const file = event.target.files?.[0]
    aiSourceFile.value = file || null
    aiSourceFileName.value = file?.name || ''
}

const resetAiModal = () => {
    aiSourceText.value = ''
    aiSourceFile.value = null
    aiSourceFileName.value = ''
    aiLoading.value = false
}

const openAiModal = () => {
    showAiModal.value = true
}

const closeAiModal = () => {
    showAiModal.value = false
    resetAiModal()
}

const generateMaterial = async () => {
    if (!aiSourceText.value.trim() && !aiSourceFile.value) {
        alert('Adj meg szöveget vagy tölts fel egy fájlt!')
        return
    }

    const token = localStorage.getItem('token')
    const formData = new FormData()
    formData.append('source_text', aiSourceText.value)
    if (aiSourceFile.value) {
        formData.append('source_file', aiSourceFile.value)
    }
    formData.append('topic_title', editingTopic.value?.title || '')
    formData.append('subject_name', subjectName.value || '')

    aiLoading.value = true
    try {
        const res = await fetch(`${API_BASE_URL}/admin/ai/generate-material`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: formData
        })

        const data = await res.json()
        if (!res.ok) throw new Error(data?.error || 'Sikertelen AI hívás')

        if (data?.html) {
            editorContent.value = data.html
            closeAiModal()
        }
    } catch (error) {
        console.error(error)
        alert(error.message || 'Nem sikerült tananyagot generálni.')
    } finally {
        aiLoading.value = false
    }
}

watch(showAiModal, (isOpen) => {
    if (!isOpen) resetAiModal()
})





const onDragEnd = async (unit) => {
    
    unit.topics.forEach((t, i) => t.order = i + 1)
    
    const token = localStorage.getItem('token')
    try {
        await fetch('http://backend.maturasmart.hu/api/admin/topics/reorder', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                unit_id: unit.id,
                topics: unit.topics.map(t => ({ id: t.id, order: t.order })) 
            })
        })
        console.log(`Unit ${unit.id} mentve!`)
    } catch(e) {
        console.error(e)
        alert("Hiba a mentéskor")
    }
}

const reqDelete = (type, id) => { actionToDelete.value = { type, id }; showModal.value = true }
const executeDelete = async () => { 
    showModal.value = false; const token = localStorage.getItem('token'); const endpoint = actionToDelete.value.type === 'unit' ? `units/${actionToDelete.value.id}` : `topics/${actionToDelete.value.id}`; await fetch(`http://backend.vm1.test/api/admin/${endpoint}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } }); fetchStructure(); 
}
</script>

<template>
  <div class="course-builder-page h-full flex flex-col">
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
                    
                    <VueDraggable 
                        v-model="unit.topics"
                        :animation="150"
                        group="topics"
                        handle=".drag-handle"
                        @add="onDragEnd(unit)"
                        @update="onDragEnd(unit)"
                        class="space-y-2"
                    >
                        <div v-for="topic in unit.topics" :key="topic.id" class="flex items-center justify-between bg-[#0b102e] p-3 rounded-lg border border-gray-700/50 hover:border-blue-500/30 group transition">
                            <div class="flex items-center gap-3">
                                <span class="drag-handle cursor-move text-gray-600 hover:text-white px-2 text-xl select-none">⋮⋮</span>
                                
                                <span class="text-gray-500 text-lg" v-if="topic.type === 'test'">📝</span>
                                <span class="text-gray-500 text-lg" v-else>📄</span>
                                
                                <span class="font-medium" :class="topic.type === 'test' ? 'text-yellow-400' : 'text-gray-200'">
                                    {{ topic.title }}
                                    <span v-if="topic.type === 'test'" class="text-[10px] uppercase bg-yellow-900/40 text-yellow-500 px-2 rounded ml-2 border border-yellow-700/50">Teszt</span>
                                </span>
                                <span class="text-xs text-gray-500 bg-gray-800 px-2 rounded">{{ topic.xp }} XP</span>
                            </div>
                            <div class="flex gap-2">
                                <button @click="openEditor(topic)" class="bg-blue-600 text-white hover:bg-blue-500 px-3 py-1.5 rounded text-xs font-bold shadow-lg shadow-blue-900/20">✏️ SZERKESZTÉS</button>
                                <button @click="reqDelete('topic', topic.id)" class="text-gray-500 hover:text-red-400 px-2">×</button>
                            </div>
                        </div>
                    </VueDraggable>
                    
                    <div class="mt-4 flex gap-2 pl-4 border-l-2 border-gray-700 ml-2">
                        <select v-model="unit.newTopicType" class="bg-[#0b102e] border border-gray-700 rounded-lg px-2 py-2 text-sm text-white focus:border-blue-500 outline-none">
                            <option value="lesson">📄 Lecke</option>
                            <option value="test">📝 Teszt</option>
                        </select>
                        <input v-model="unit.newTopicTitle" placeholder="Cím..." class="bg-[#0b102e] border border-gray-700 rounded-lg px-3 py-2 text-sm text-white flex-1 focus:border-blue-500 outline-none" @keyup.enter="addTopic(unit)" />
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
                <h2 class="font-bold text-lg flex items-center gap-2" :class="editingTopic.type === 'test' ? 'text-yellow-500' : 'text-white'">
                    <span v-if="editingTopic.type === 'test'">📝</span> 
                    {{ editingTopic.title }}
                </h2>
            </div>
            
            <div class="flex bg-gray-800 rounded-lg p-1 gap-1">
                <button v-if="editingTopic.type !== 'test'" @click="activeTab = 'content'" :class="activeTab === 'content' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-1.5 rounded-md text-sm font-bold transition">📄 Tartalom</button>
                <button @click="activeTab = 'quiz'" :class="activeTab === 'quiz' ? (editingTopic.type === 'test' ? 'bg-yellow-600 text-white' : 'bg-blue-600 text-white') : 'text-gray-400 hover:text-white'" class="px-4 py-1.5 rounded-md text-sm font-bold transition">
                    {{ editingTopic.type === 'test' ? '📝 Vizsgafeladatok' : '❓ Kvíz' }}
                </button>
                <button v-if="editingTopic.type !== 'test'" @click="activeTab = 'flashcards'" :class="activeTab === 'flashcards' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-1.5 rounded-md text-sm font-bold transition">🃏 Kártyák</button>
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

        <button
            v-if="activeTab === 'content'"
            @click="openAiModal"
            class="fixed bottom-6 right-6 z-[70] bg-blue-600 hover:bg-blue-500 text-white px-5 py-3 rounded-full font-bold shadow-2xl shadow-blue-900/40 flex items-center gap-2"
        >
            <span>✨</span>
            <span>Tananyag generálás</span>
        </button>

        <div v-if="showAiModal" class="fixed inset-0 z-[80] bg-black/60 flex items-center justify-center p-4" @click.self="closeAiModal">
            <div class="w-full max-w-2xl bg-[#131b3d] border border-gray-700 rounded-2xl shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-gray-700">
                    <h3 class="text-white text-lg font-bold">Tananyag generálás</h3>
                    <button class="text-gray-400 hover:text-white" @click="closeAiModal">✕</button>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="text-gray-300 text-sm font-semibold block mb-2">Forrás szöveg</label>
                        <textarea
                            v-model="aiSourceText"
                            class="w-full min-h-[180px] bg-[#0b102e] border border-gray-700 rounded-xl p-3 text-white outline-none focus:border-blue-500"
                            placeholder="Írd ide a generálás alapjául szolgáló vázlatot, jegyzetet vagy instrukciót..."
                        ></textarea>
                    </div>

                    <div>
                        <label class="text-gray-300 text-sm font-semibold block mb-2">Forrás feltöltés (kép/pdf/txt/docx)</label>
                        <input
                            type="file"
                            accept=".txt,.pdf,.docx,image/png,image/jpeg,image/webp"
                            @change="handleAiFileChange"
                            class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-500"
                        />
                        <p v-if="aiSourceFileName" class="text-xs text-blue-300 mt-2">Kiválasztva: {{ aiSourceFileName }}</p>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-700 flex justify-end gap-3">
                    <button @click="closeAiModal" class="px-4 py-2 rounded-lg border border-gray-600 text-gray-300 hover:text-white">Mégse</button>
                    <button
                        @click="generateMaterial"
                        :disabled="aiLoading"
                        class="px-5 py-2 rounded-lg bg-green-600 hover:bg-green-500 disabled:opacity-60 text-white font-bold"
                    >
                        {{ aiLoading ? 'Generálás...' : 'Generálás indítása' }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'quiz'" class="flex-1 flex flex-col bg-[#0b102e] overflow-hidden">
            <div v-if="editingTopic.type === 'test'" class="bg-[#161b22] border-b border-gray-700 p-6 shadow-lg z-10">
                <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-yellow-900/10 border border-yellow-700/30 p-4 rounded-xl flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center text-xl text-yellow-500">🏆</div>
                        <div><div class="text-xs font-bold text-yellow-500/80 uppercase">Összpontszám</div><div class="text-2xl font-bold text-white">{{ totalXp }} XP</div></div>
                    </div>
                    <div class="bg-blue-900/10 border border-blue-700/30 p-4 rounded-xl flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-xl text-blue-500">📊</div>
                        <div><div class="text-xs font-bold text-blue-400/80 uppercase">Feladatok</div><div class="text-2xl font-bold text-white">{{ questions.length }} db</div></div>
                    </div>
                    <div class="bg-gray-800/50 border border-gray-700 p-4 rounded-xl flex items-center justify-between">
                        <span class="text-sm text-gray-400 font-bold">⏱️ Időkorlát:</span>
                        <div class="flex items-center gap-2"><input v-model="editingTopic.time_limit_minutes" type="number" class="bg-gray-900 border border-gray-600 w-16 text-center rounded-lg text-white font-bold py-1 focus:border-yellow-500 outline-none" /><span class="text-gray-500 text-xs uppercase font-bold">perc</span></div>
                    </div>
                    <div class="bg-gray-800/50 border border-gray-700 p-4 rounded-xl flex items-center justify-between">
                        <span class="text-sm text-gray-400 font-bold">🎯 Küszöb:</span>
                        <div class="flex items-center gap-2"><input v-model="editingTopic.passing_percentage" type="number" class="bg-gray-900 border border-gray-600 w-16 text-center rounded-lg text-white font-bold py-1 focus:border-yellow-500 outline-none" /><span class="text-gray-500 text-xs uppercase font-bold">%</span></div>
                    </div>
                </div>
            </div>

            <div class="flex-1 flex overflow-hidden">
                <div class="flex-1 overflow-y-auto p-8 border-r border-gray-700 bg-[#0d1117] scrollbar-thin">
                    <div class="max-w-3xl mx-auto">
                        <div class="bg-[#161b22] p-8 rounded-2xl border border-gray-700 shadow-2xl relative">
                            <div class="absolute -top-3 left-6 bg-blue-600 text-white text-xs font-bold uppercase px-3 py-1 rounded-full shadow-lg">Új feladat szerkesztése</div>
                            <div class="mb-6"><label class="block text-xs uppercase font-bold text-gray-500 mb-2">Feladat szövege / Kérdés</label><textarea v-model="newQuestion.content" placeholder="Írd ide a kérdést..." class="w-full bg-[#0b102e] border border-gray-700 p-4 rounded-xl text-white text-lg focus:border-blue-500 outline-none h-32 resize-none leading-relaxed shadow-inner transition focus:ring-2 focus:ring-blue-900"></textarea></div>
                            <div class="space-y-3 mb-8"><label class="block text-xs uppercase font-bold text-gray-500 mb-2">Válaszlehetőségek</label><div v-for="(ans, i) in newQuestion.answers" :key="i" class="flex items-center gap-4 bg-[#0b102e] p-4 rounded-xl border border-gray-700 hover:border-gray-500 transition group focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500/50"><div class="relative flex items-center justify-center w-8 h-8 shrink-0"><input type="radio" name="correct" :checked="ans.is_correct" @change="setCorrectAnswer(i)" class="peer cursor-pointer appearance-none w-6 h-6 border-2 border-gray-600 rounded-full checked:border-green-500 checked:bg-green-500" /><div class="absolute text-white opacity-0 peer-checked:opacity-100 pointer-events-none transform scale-50 peer-checked:scale-100 transition">✓</div></div><div class="flex-1"><span class="text-xs text-gray-500 font-mono font-bold uppercase mb-1 block">{{ String.fromCharCode(65+i) }}. opció</span><input v-model="ans.text" :placeholder="`Válaszlehetőség szövege...`" class="bg-transparent text-white w-full outline-none text-base font-medium placeholder-gray-700" /></div></div></div>
                            <div class="flex justify-between items-center pt-6 border-t border-gray-700"><div class="flex items-center gap-3 bg-[#0b102e] px-4 py-2 rounded-lg border border-gray-700 shadow-inner"><span class="text-gray-400 text-xs font-bold uppercase">Pontérték:</span><input v-model="newQuestion.xp" type="number" class="bg-transparent w-12 text-center text-white font-bold outline-none text-lg" /><span class="text-yellow-500 font-bold">XP</span></div><button @click="addQuestion" class="px-8 py-3 rounded-xl font-bold shadow-lg transition flex items-center gap-2 transform active:scale-95" :class="editingTopic.type === 'test' ? 'bg-yellow-600 hover:bg-yellow-500 text-white shadow-yellow-900/20' : 'bg-blue-600 hover:bg-blue-500 text-white shadow-blue-900/20'"><span>+ Feladat rögzítése</span></button></div>
                        </div>
                    </div>
                </div>
                <div class="w-1/3 min-w-[350px] bg-[#0b102e] overflow-y-auto border-l border-gray-800 p-6 scrollbar-thin">
                    <h4 class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-6 flex items-center gap-2"><span>Rögzített feladatok</span><span class="bg-gray-800 text-gray-300 px-2 rounded-full text-[10px]">{{ questions.length }}</span></h4>
                    <div v-if="questions.length === 0" class="text-center py-20 text-gray-600 border-2 border-dashed border-gray-800 rounded-2xl"><div class="text-4xl mb-2 opacity-20">📝</div><p class="text-sm">Nincs még feladat rögzítve.</p></div>
                    <div class="space-y-4"><div v-for="(q, index) in questions" :key="q.id" class="bg-[#161b22] p-5 rounded-2xl border border-gray-800 hover:border-gray-600 transition group relative shadow-md"><div class="flex justify-between items-start mb-3"><div class="flex items-center gap-2"><span class="w-6 h-6 flex items-center justify-center bg-gray-800 rounded text-xs font-mono font-bold text-gray-400">#{{ index + 1 }}</span><span class="text-xs bg-gray-900/50 text-yellow-500 font-bold px-2 py-0.5 rounded border border-yellow-900/20">{{ q.xp }} XP</span></div><button @click="deleteQuestion(q.id)" class="text-gray-600 hover:text-red-400 hover:bg-red-900/20 p-1.5 rounded-lg transition">🗑️</button></div><p class="font-bold text-white text-sm mb-4 leading-relaxed">{{ q.content }}</p><div class="space-y-2"><div v-for="a in q.answers" :key="a.id" class="text-xs flex items-start gap-3 p-2 rounded-lg" :class="a.is_correct ? 'bg-green-900/10 border border-green-900/30' : 'bg-gray-900/30 border border-transparent'"><span v-if="a.is_correct" class="text-green-500 font-bold">✔</span><span v-else class="text-gray-600">⚪</span><span :class="a.is_correct ? 'text-green-100 font-medium' : 'text-gray-500'">{{ a.text }}</span></div></div></div></div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'flashcards'" class="flex-1 overflow-y-auto p-8 bg-[#0b102e]">
            <div class="max-w-4xl mx-auto"><h3 class="text-2xl font-bold text-white mb-6">Tanulókártyák</h3><div class="bg-[#131b3d] p-6 rounded-xl border border-gray-700 mb-8 flex gap-4 items-end"><div class="flex-1"><label class="text-gray-400 text-xs uppercase font-bold mb-1 block">Kártya eleje (Kérdés)</label><input v-model="newFlashcard.front" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white focus:border-blue-500 outline-none" /></div><div class="flex-1"><label class="text-gray-400 text-xs uppercase font-bold mb-1 block">Kártya hátulja (Válasz)</label><input v-model="newFlashcard.back" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white focus:border-blue-500 outline-none" /></div><button @click="addFlashcard" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-lg font-bold">Hozzáadás</button></div><div class="grid grid-cols-2 gap-4"><div v-for="card in flashcards" :key="card.id" class="bg-gray-800 p-4 rounded-xl border border-gray-700 relative group"><button @click="deleteFlashcard(card.id)" class="absolute top-2 right-2 text-gray-500 hover:text-red-500">×</button><div class="text-white font-bold mb-2 pb-2 border-b border-gray-700">{{ card.front }}</div><div class="text-blue-400">{{ card.back }}</div></div></div></div>
        </div>

        <div v-if="activeTab === 'settings'" class="flex-1 p-8 bg-[#0b102e]">
            <div class="max-w-xl mx-auto bg-[#131b3d] p-8 rounded-xl border border-gray-700">
                <h3 class="text-xl font-bold text-white mb-6">Általános beállítások</h3>
                
                <div class="mb-4">
                    <label class="text-gray-400 block mb-2">Lecke címe</label>
                    <input v-model="editingTopic.title" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white" />
                </div>

                <div v-if="editingTopic.type !== 'test'" class="mb-4">
                    <label class="text-gray-400 block mb-2">XP Jutalom</label>
                    <input v-model="editingTopic.xp" type="number" class="w-full bg-[#0b102e] border border-gray-700 p-3 rounded-lg text-white" />
                </div>

                <div v-if="editingTopic.type !== 'test'" class="mb-6 p-4 bg-[#0b102e] rounded-xl border border-gray-700">
                    <label class="text-white font-bold block mb-4">Haladás súlyozása</label>
                    
                    <div class="flex items-center justify-between text-xs text-gray-400 mb-2 font-bold uppercase">
                        <span>📖 Olvasás: {{ editingTopic.reading_weight }}%</span>
                        <span>❓ Kvíz: {{ 100 - editingTopic.reading_weight }}%</span>
                    </div>
                    
                    <input 
                        type="range" 
                        min="0" 
                        max="100" 
                        v-model="editingTopic.reading_weight" 
                        class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-blue-500"
                    />
                    
                    <p class="text-xs text-gray-500 mt-2">
                        Itt állíthatod be, hogy a haladás csík (progress bar) hány százalékát adja a görgetés, és mennyit a beépített kvíz.
                    </p>
                </div>
                
                <div v-else class="p-4 bg-yellow-900/10 border border-yellow-700/30 rounded-lg text-yellow-500 text-sm flex gap-3">
                    <span class="text-xl">ℹ️</span>
                    <p>Teszt esetén az XP-t a feladatok pontszámai határozzák meg automatikusan. Az időkorlátot és a küszöböt a <strong>📝 Vizsgafeladatok</strong> fülön, a felső sávban állíthatod be!</p>
                </div>
            </div>
        </div>

    </div>

    <PasswordConfirmModal :visible="showModal" @confirm="executeDelete" @cancel="showModal = false" />
  </div>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.scrollbar-thin::-webkit-scrollbar { width: 8px; }
.scrollbar-thin::-webkit-scrollbar-track { background: #0d1117; }
.scrollbar-thin::-webkit-scrollbar-thumb { background: #30363d; border-radius: 4px; }
.prose-invert h1, .prose-invert h2, .prose-invert h3 { color: white; font-weight: bold; }
.prose-invert p { color: #cbd5e1; }
.prose-invert strong { color: white; }

[data-theme="light"] .course-builder-page [class*="bg-[#131b3d]"],
[data-theme="light"] .course-builder-page [class*="bg-[#0b102e]"],
[data-theme="light"] .course-builder-page [class*="bg-[#161b22]"],
[data-theme="light"] .course-builder-page [class*="bg-[#0d1117]"],
[data-theme="light"] .course-builder-page [class*="bg-gray-800"] {
    background: #ffffff !important;
    border-color: rgba(45, 114, 182, 0.2) !important;
}

[data-theme="light"] .course-builder-page [class*="text-white"],
[data-theme="light"] .course-builder-page [class*="text-gray-400"],
[data-theme="light"] .course-builder-page [class*="text-gray-500"] {
    color: var(--text-primary) !important;
}

[data-theme="light"] .course-builder-page .bg-\[\#131b3d\] {
    background: #f8fbff !important;
}

[data-theme="light"] .course-builder-page .bg-\[\#0b102e\]\/50,
[data-theme="light"] .course-builder-page .bg-\[\#0b102e\] {
    background: #eef4ff !important;
}

[data-theme="light"] .course-builder-page .bg-\[\#0d1117\],
[data-theme="light"] .course-builder-page .bg-\[\#161b22\] {
    background: #f1f5f9 !important;
}

[data-theme="light"] .course-builder-page .text-blue-200 {
    color: #1e40af !important;
}

[data-theme="light"] .course-builder-page .text-gray-600 {
    color: #475569 !important;
}

[data-theme="light"] .course-builder-page .text-gray-300 {
    color: #334155 !important;
}
</style>
