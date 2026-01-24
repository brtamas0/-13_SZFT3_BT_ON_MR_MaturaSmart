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
</script>