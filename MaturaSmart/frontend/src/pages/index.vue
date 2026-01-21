<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '@/layouts/BaseLayout.vue'
import BaseHeader from '@/layouts/BaseHeader.vue'

const router = useRouter()

const activeFaq = ref(null)
const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index
}

onMounted(() => {
  const saved = localStorage.getItem('darkMode')
  if (saved === '1') {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
})


const goToLogin = () => {
  router.push('/login')
}
</script>


<template>
  <BaseLayout>

    <BaseHeader mode="landing" />

    <div class="ambient-background"></div>

    <main class="container" id="home">

      <section class="hero-section">
        <div class="card-3d hero-card">
          <div class="landing-hero-content" style="flex: 1;">
            <h1 class="hero-title-large" style="margin-bottom: 1rem;">
              A sikeres érettségi <br>
              <span class="accent-text">itt kezdődik.</span>
            </h1>

            <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">
              Felejtsd el a magolást. Tanulj Axel társaságában, játékos leckékkel és személyre szabott tervekkel.
            </p>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
              <button @click="goToLogin" class="btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                🚀 Kezdjük el ingyen!
              </button>

              <button @click="scrollToSection('features')" class="btn-outline" style="font-size: 1.1rem; padding: 1rem 2rem;">
                Funkciók
              </button>
            </div>
          </div>

          <div class="hero-right" style="display: flex; justify-content: center; align-items: center; flex: 1;">
            <img src="/axel.png" alt="Axel" class="landing-hero-img">
          </div>
        </div>
      </section>
      <div class="grid-3" style="margin-bottom: 4rem;">
        <div class="card-3d" style="text-align: center;">
          <span class="stat-number">1.200+</span>
          <p style="margin: 0; color: var(--text-secondary);">Sikeres érettségi</p>
        </div>

        <div class="card-3d" style="text-align: center;">
          <span class="stat-number">8.500+</span>
          <p style="margin: 0; color: var(--text-secondary);">Megoldott feladat</p>
        </div>

        <div class="card-3d" style="text-align: center;">
          <span class="stat-number">4.8</span>
          <p style="margin: 0; color: var(--text-secondary);">Átlagos osztályzat</p>
        </div>
      </div>

      <div id="features" style="text-align: center; margin: 4rem 0 2rem;">
        <h2 style="font-size: 2rem; margin-bottom: 1rem;">Miért a MaturaSmart?</h2>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
          Minden eszköz, amire szükséged van a jeles érettségihez.
        </p>
      </div>

      <div class="grid-3">
        <div class="card-3d" style="text-align: center;">
          <div class="feature-icon-large">👾</div>
          <h3>Virtuális Tanulótárs</h3>
          <p style="color: var(--text-secondary);">
            Axel végigkísér a felkészülésen: hasznos tippekkel, motiváló üzenetekkel és jegyzetekkel segít.
          </p>
        </div>

        <div class="card-3d" style="text-align: center;">
          <div class="feature-icon-large">📅</div>
          <h3>Okos Tervező</h3>
          <p style="color: var(--text-secondary);">
            A rendszer automatikusan beosztja a tanulnivalót a vizsga napjáig.
          </p>
        </div>

        <div class="card-3d" style="text-align: center;">
          <div class="feature-icon-large">🎮</div>
          <h3>Gamifikáció</h3>
          <p style="color: var(--text-secondary);">
            Gyűjts XP-t, versenyezz az osztálytársaiddal a ranglistán, és tartsd fenn a napi szériádat.
          </p>
        </div>

        <div class="card-3d" style="text-align: center;">
          <div class="feature-icon-large">🃏</div>
          <h3>Szókártyák</h3>
          <p style="color: var(--text-secondary);">
            Tanulj fogalmakat vagy évszámokat a beépített, forgatható kártyákkal.
          </p>
        </div>

        <div class="card-3d" style="text-align: center;">
          <div class="feature-icon-large">📱</div>
          <h3>Zsebre vágható tudás</h3>
          <p style="color: var(--text-secondary);">
            Unalmas a buszút? Kapd elő a telód és pörgess le egy tesztet!
          </p>
        </div>

        <div class="card-3d" style="text-align: center;">
          <div class="feature-icon-large">♿</div>
          <h3>Korlátok nélkül</h3>
          <p style="color: var(--text-secondary);">
            Diszlexia-barát nézet és kontrasztos témák, hogy mindenkinek élmény legyen a tanulás!
          </p>
        </div>
      </div>

      <section id="mission" style="margin-top: 5rem;">
        <div class="card-3d mission-card">
          <div style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">A Projekt Célja 🎯</h2>

            <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 1.5rem;">
              A MaturaSmart projektet azzal a küldetéssel hoztuk létre, hogy modernizáljuk a magyar középiskolai érettségi felkészülést.
              Célunk egy olyan <strong>innovatív, webes alapú oktatási platform</strong> biztosítása, amely az élményszerű tanulást helyezi előtérbe.
            </p>

            <p style="font-size: 1.1rem; line-height: 1.8;">
              Hiszünk abban, hogy a <strong>technológia és a pedagógia</strong> ötvözésével minden diák képes kihozni magából a maximumot.
            </p>
          </div>
        </div>
      </section>

      <div id="faq" style="margin-top: 5rem; max-width: 800px; margin-left: auto; margin-right: auto;">
        <h2 style="font-size: 2rem; text-align: center; margin-bottom: 2rem;">Gyakori Kérdések</h2>

        <div class="card-3d">
          <div class="faq-item" :class="{ active: activeFaq === 0 }" @click="toggleFaq(0)">
            <div class="faq-question">Ingyenes a használata? <span class="arrow">▼</span></div>
            <div class="faq-answer">Igen! A MaturaSmart alapverziója minden diák számára ingyenesen elérhető.</div>
          </div>

          <div class="faq-item" :class="{ active: activeFaq === 1 }" @click="toggleFaq(1)">
            <div class="faq-question">Milyen tantárgyak érhetők el? <span class="arrow">▼</span></div>
            <div class="faq-answer">
              Jelenleg a fő érettségi tárgyak: Matematika, Történelem, Magyar nyelv és irodalom, Angol és Informatika.
            </div>
          </div>

          <div class="faq-item" :class="{ active: activeFaq === 2 }" @click="toggleFaq(2)">
            <div class="faq-question">Használhatom mobilon is? <span class="arrow">▼</span></div>
            <div class="faq-answer">
              Abszolút! Az oldal reszponzív kialakítású, így telefonon és tableten is tökéletes élményt nyújt.
            </div>
          </div>

          <div class="faq-item" :class="{ active: activeFaq === 3 }" @click="toggleFaq(3)">
            <div class="faq-question">Ki az az Axel? <span class="arrow">▼</span></div>
            <div class="faq-answer">
              Axel, a virtuális segéded, aki végigkísér a felkészülésen és javaslatokat tesz a tanulási útvonaladra.
            </div>
          </div>
        </div>
      </div>

      <div style="text-align: center; margin-top: 5rem; padding: 3rem; border-radius: 30px; border: 1px solid var(--glass-border);">
        <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Készen állsz az ötösre?</h2>
        <button @click="goToLogin" class="btn-primary" style="font-size: 1.2rem; padding: 1rem 3rem;">
          Csatlakozom
        </button>
      </div>

    </main>

    <footer class="footer">
      <div class="container" style="padding-bottom: 2rem;">
        <div class="logo" style="margin-bottom: 1rem;">
          Matura<span class="accent-text">Smart</span>
        </div>

        <div class="footer-links">
          <a href="#">Felhasználási Feltételek</a>
          <a href="#">Adatvédelem</a>
          <a href="#">Kapcsolat</a>
        </div>

        <p style="font-size: 0.8rem; color: var(--text-secondary); opacity: 0.5;">
          © 2025 MaturaSmart. Minden jog fenntartva.
        </p>
      </div>
    </footer>

  </BaseLayout>
</template>
