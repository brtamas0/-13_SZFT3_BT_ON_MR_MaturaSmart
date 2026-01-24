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
</script>