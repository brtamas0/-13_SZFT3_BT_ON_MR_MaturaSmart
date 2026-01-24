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

</script>