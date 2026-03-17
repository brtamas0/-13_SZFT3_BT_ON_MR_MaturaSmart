<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '@/layouts/BaseLayout.vue'
import BaseHeader from '@/layouts/BaseHeader.vue'

const router = useRouter()
const activeFaq = ref(null)
const threeCanvas = ref(null)

let renderer = null
let scene = null
let camera = null
let frameId = null
let ring = null
let stars = null
let onResizeHandler = null

const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index
}

const scrollToSection = (id) => {
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const goToLogin = () => {
  router.push('/login')
}

const initThree = async () => {
  if (!threeCanvas.value) return


  const THREE = await import(/* @vite-ignore */ 'https://unpkg.com/three@0.179.1/build/three.module.js')

  scene = new THREE.Scene()
  camera = new THREE.PerspectiveCamera(55, threeCanvas.value.clientWidth / threeCanvas.value.clientHeight, 0.1, 100)
  camera.position.z = 5

  renderer = new THREE.WebGLRenderer({ canvas: threeCanvas.value, alpha: true, antialias: true })
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  renderer.setSize(threeCanvas.value.clientWidth, threeCanvas.value.clientHeight)

  const ambient = new THREE.AmbientLight(0xffffff, 0.9)
  scene.add(ambient)

  const pointLight = new THREE.PointLight(0x6d8dff, 2.2)
  pointLight.position.set(3, 4, 5)
  scene.add(pointLight)

  const ringGeometry = new THREE.TorusKnotGeometry(1.2, 0.35, 220, 32)
  const ringMaterial = new THREE.MeshStandardMaterial({
    color: 0x7f9bff,
    emissive: 0x2d3b95,
    roughness: 0.3,
    metalness: 0.7,
  })

  ring = new THREE.Mesh(ringGeometry, ringMaterial)
  scene.add(ring)

  const starCount = 260
  const positions = new Float32Array(starCount * 3)
  for (let i = 0; i < starCount; i++) {
    positions[i * 3] = (Math.random() - 0.5) * 16
    positions[i * 3 + 1] = (Math.random() - 0.5) * 10
    positions[i * 3 + 2] = (Math.random() - 0.5) * 14
  }

  const starGeometry = new THREE.BufferGeometry()
  starGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3))
  const starMaterial = new THREE.PointsMaterial({ color: 0xffffff, size: 0.04 })
  stars = new THREE.Points(starGeometry, starMaterial)
  scene.add(stars)

  const animate = () => {
    frameId = requestAnimationFrame(animate)
    ring.rotation.x += 0.003
    ring.rotation.y += 0.005
    stars.rotation.y += 0.0008
    renderer.render(scene, camera)
  }
  animate()

  onResizeHandler = () => {
    if (!threeCanvas.value || !camera || !renderer) return
    const width = threeCanvas.value.clientWidth
    const height = threeCanvas.value.clientHeight
    camera.aspect = width / height
    camera.updateProjectionMatrix()
    renderer.setSize(width, height)
  }

  window.addEventListener('resize', onResizeHandler)
}


onMounted(() => {
  const saved = localStorage.getItem('darkMode')
  if (saved === '1') {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }

  initThree()
})

onUnmounted(() => {
  if (frameId) cancelAnimationFrame(frameId)
  if (onResizeHandler) window.removeEventListener('resize', onResizeHandler)
  if (scene) {
    scene.traverse((obj) => {
      if (obj.geometry) obj.geometry.dispose?.()
      if (obj.material) {
        if (Array.isArray(obj.material)) obj.material.forEach((m) => m.dispose?.())
        else obj.material.dispose?.()
      }
    })
  }

  renderer?.dispose?.()
  renderer = null
  scene = null
  camera = null
  ring = null
  stars = null
  onResizeHandler = null
})
</script>

<template>
  <BaseLayout mode="landing">
    <BaseHeader mode="landing" />

    <main class="landing-page container" id="home">
      <section class="hero-section">
        <div class="hero-copy">
          <p class="hero-badge">MaturaSmart • interaktív érettségi platform</p>
          <h1>
            A sikeres érettségi <br>
            <span>itt kezdődik.</span>
          </h1>
          <p class="hero-description">
            Felejtsd el a magolást. Tanulj Axel társaságában, játékos leckékkel és személyre szabott tervekkel.
          </p>
          <div class="hero-actions">
            <button @click="goToLogin" class="btn-primary">🚀 Kezdjük el ingyen!</button>
            <button @click="scrollToSection('features')" class="btn-outline">Funkciók</button>
          </div>
        </div>

        <div class="hero-visual">
          <canvas ref="threeCanvas" class="three-canvas" aria-label="3D animáció"></canvas>
          <img src="/axel.png" alt="Axel" class="landing-hero-img">
        </div>
      </section>

      <section class="stats-grid">
        <div class="card-3d"><span class="stat-number">1.200+</span><p>Sikeres érettségi</p></div>
        <div class="card-3d"><span class="stat-number">8.500+</span><p>Megoldott feladat</p></div>
        <div class="card-3d"><span class="stat-number">4.8</span><p>Átlagos osztályzat</p></div>
      </section>

      <section id="features" class="section-head">
        <h2>Miért a MaturaSmart?</h2>
        <p>Minden eszköz, amire szükséged van a jeles érettségihez.</p>
      </section>

      <section class="grid-3 features-grid">
        <div class="card-3d"><div class="feature-icon-large">👾</div><h3>Virtuális Tanulótárs</h3><p>Axel végigkísér a felkészülésen: hasznos tippekkel, motiváló üzenetekkel és jegyzetekkel segít.</p></div>
        <div class="card-3d"><div class="feature-icon-large">📅</div><h3>Okos Tervező</h3><p>A rendszer automatikusan beosztja a tanulnivalót a vizsga napjáig.</p></div>
        <div class="card-3d"><div class="feature-icon-large">🎮</div><h3>Gamifikáció</h3><p>Gyűjts XP-t, versenyezz az osztálytársaiddal a ranglistán, és tartsd fenn a napi szériádat.</p></div>
        <div class="card-3d"><div class="feature-icon-large">🃏</div><h3>Szókártyák</h3><p>Tanulj fogalmakat vagy évszámokat a beépített, forgatható kártyákkal.</p></div>
        <div class="card-3d"><div class="feature-icon-large">📱</div><h3>Zsebre vágható tudás</h3><p>Unalmas a buszút? Kapd elő a telód és pörgess le egy tesztet!</p></div>
        <div class="card-3d"><div class="feature-icon-large">♿</div><h3>Korlátok nélkül</h3><p>Diszlexia-barát nézet és kontrasztos témák, hogy mindenkinek élmény legyen a tanulás!</p></div>
      </section>

      <section id="mission" class="mission-section card-3d">
        <h2>A Projekt Célja 🎯</h2>
        <p>
          A MaturaSmart projektet azzal a küldetéssel hoztuk létre, hogy modernizáljuk a magyar középiskolai érettségi felkészülést.
          Célunk egy olyan <strong>innovatív, webes alapú oktatási platform</strong> biztosítása, amely az élményszerű tanulást helyezi előtérbe.
        </p>
        <p>
          Hiszünk abban, hogy a <strong>technológia és a pedagógia</strong> ötvözésével minden diák képes kihozni magából a maximumot.
        </p>
      </section>

      <section id="faq" class="faq-wrapper">
        <h2>Gyakori Kérdések</h2>
        <div class="card-3d">
          <div class="faq-item" :class="{ active: activeFaq === 0 }" @click="toggleFaq(0)"><div class="faq-question">Ingyenes a használata? <span class="arrow">▼</span></div><div class="faq-answer">Igen! A MaturaSmart alapverziója minden diák számára ingyenesen elérhető.</div></div>
          <div class="faq-item" :class="{ active: activeFaq === 1 }" @click="toggleFaq(1)"><div class="faq-question">Milyen tantárgyak érhetők el? <span class="arrow">▼</span></div><div class="faq-answer">Jelenleg a fő érettségi tárgyak: Matematika, Történelem, Magyar nyelv és irodalom, Angol és Informatika.</div></div>
          <div class="faq-item" :class="{ active: activeFaq === 2 }" @click="toggleFaq(2)"><div class="faq-question">Használhatom mobilon is? <span class="arrow">▼</span></div><div class="faq-answer">Abszolút! Az oldal reszponzív kialakítású, így telefonon és tableten is tökéletes élményt nyújt.</div></div>
          <div class="faq-item" :class="{ active: activeFaq === 3 }" @click="toggleFaq(3)"><div class="faq-question">Ki az az Axel? <span class="arrow">▼</span></div><div class="faq-answer">Axel, a virtuális segéded, aki végigkísér a felkészülésen és javaslatokat tesz a tanulási útvonaladra.</div></div>
        </div>
      </section>

      <section class="final-cta card-3d">
        <h2>Készen állsz az ötösre?</h2>
        <button @click="goToLogin" class="btn-primary">Csatlakozom</button>
      </section>
    </main>

    <footer class="footer">
      <div class="container footer-inner">
        <div class="logo">Matura<span class="accent-text">Smart</span></div>
        <div class="footer-links">
          <a href="#">Felhasználási Feltételek</a>
          <a href="#">Adatvédelem</a>
          <a href="#">Kapcsolat</a>
        </div>
        <p>© 2025 MaturaSmart. Minden jog fenntartva.</p>
      </div>
    </footer>
  </BaseLayout>
</template>

<style scoped>
.landing-page { padding-top: 2rem; }
.hero-section { display: grid; gap: 2rem; align-items: center; margin-bottom: 2.5rem; }
.hero-copy h1 { font-size: clamp(2.2rem, 6vw, 4rem); line-height: 1.1; margin: 0 0 1rem; }
.hero-copy h1 span { color: var(--accent); }
.hero-description { font-size: 1.1rem; color: var(--text-secondary); max-width: 48ch; }
.hero-badge { display: inline-block; padding: 0.35rem 0.75rem; border-radius: 999px; background: var(--glass-bg); border: 1px solid var(--glass-border); margin-bottom: 1rem; }
.hero-actions { display: flex; gap: 0.8rem; flex-wrap: wrap; }
.hero-visual { position: relative; min-height: 360px; border-radius: 24px; overflow: hidden; border: 1px solid var(--glass-border); background: radial-gradient(circle at 35% 25%, rgba(87, 108, 255, 0.45), rgba(9, 13, 41, 0.75)); }
.three-canvas { position: absolute; inset: 0; width: 100%; height: 100%; }
.landing-hero-img { position: absolute; bottom: 0; right: 5%; width: min(50%, 260px); filter: drop-shadow(0 0 30px rgba(124, 147, 255, 0.55)); }
.stats-grid { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem; margin-bottom: 4rem; }
.stats-grid .card-3d { text-align: center; }
.section-head { text-align: center; margin: 4rem 0 2rem; }
.section-head p { color: var(--text-secondary); max-width: 600px; margin: 0 auto; }
.features-grid .card-3d { text-align: center; }
.mission-section { margin-top: 5rem; text-align: center; }
.mission-section h2 { font-size: clamp(2rem, 4vw, 2.7rem); margin-top: 0; }
.mission-section p { max-width: 800px; margin: 0.8rem auto; line-height: 1.8; }
.faq-wrapper { margin-top: 5rem; max-width: 800px; margin-inline: auto; }
.faq-wrapper h2 { text-align: center; font-size: 2rem; margin-bottom: 1.5rem; }
.final-cta { margin-top: 4rem; text-align: center; padding: 3rem 1rem; }
.final-cta h2 { margin-top: 0; font-size: clamp(1.8rem, 4vw, 2.6rem); }
.footer-inner { text-align: center; padding-bottom: 2rem; }
.footer-inner p { font-size: 0.8rem; color: var(--text-secondary); opacity: 0.6; }

@media (min-width: 960px) {
  .hero-section { grid-template-columns: 1.1fr 1fr; margin-top: 1rem; }
  .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem; }
}
</style>
