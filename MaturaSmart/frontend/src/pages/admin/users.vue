<script setup>
import { ref, onMounted } from 'vue'

const users = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    await fetchUsers()
})

const fetchUsers = async () => {
    loading.value = true
    error.value = null
    const token = localStorage.getItem('token')

    try {
        const res = await fetch('http://backend.vm1.test/api/admin/users', {
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })

        if (!res.ok) {
            throw new Error(`Hiba: ${res.status}`)
        }

        users.value = await res.json()
    } catch (e) {
        console.error(e)
        error.value = "Nem sikerült betölteni a felhasználókat."
    } finally {
        loading.value = false
    }
}
</script>
