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
</script>