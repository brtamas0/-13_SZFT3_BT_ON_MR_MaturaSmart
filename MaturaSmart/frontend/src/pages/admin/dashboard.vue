<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({ users: 0, subjects: 0, topics: 0 })
const loading = ref(true)

onMounted(async () => {
    const token = localStorage.getItem('token')
    try {
        const res = await fetch('http://backend.vm1.test/api/admin/stats', {
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        if (res.ok) stats.value = await res.json()
    } catch (e) {
        console.error("Hiba:", e)
    } finally {
        loading.value = false
    }
})
</script>