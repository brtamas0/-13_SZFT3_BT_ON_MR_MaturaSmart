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