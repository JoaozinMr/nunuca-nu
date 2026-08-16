<template>
  <div class="page-wrapper">
    <!-- ═══════════════════════════════════════ NAV ═══════════════════════════════════════ -->
    <nav id="nav" :class="['site-nav', { 'nav-scrolled': scrolled }]">
      <div class="nav-inner">
        <a href="#" class="nav-logo">
          nunuca<span class="logo-nu">.</span>nu
        </a>
        <ul class="nav-links">
          <li><a href="#produtos">Cardápio</a></li>
          <li><a href="#sobre">Sobre</a></li>
          <li><a href="#contato">Contato</a></li>
        </ul>
        <button class="cart-btn" @click="cartOpen = true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 01-8 0"/>
          </svg>
          <span v-if="totalItems > 0" class="cart-badge">{{ totalItems }}</span>
        </button>
      </div>
    </nav>

    <!-- ═══════════════════════════════════════ HERO ═══════════════════════════════════════ -->
    <section class="hero">
      <div class="hero-left">
        <p class="hero-eyebrow">— Doceria artesanal</p>
        <h1 class="hero-h1">
          <span class="hero-h1-fredoka">Onde o amor</span>
          <span class="hero-h1-caveat">vira doce.</span>
        </h1>
        <p class="hero-subtitle">
          Doces artesanais feitos à mão com ingredientes selecionados.<br>
          Cada mordida é uma memória.
        </p>
        <div class="hero-ctas">
          <a href="#produtos" class="btn btn-primary">Ver cardápio</a>
          <a href="#sobre" class="btn btn-outline">Nossa história</a>
        </div>
      </div>
      <div class="hero-right">
        <div class="hero-img-wrap">
          <img
            src="/images/640388649_17853254505675357_2104312982336529400_n.jpg"
            alt="Doces nunuca.nu"
            class="hero-img"
          />
          <div class="hero-img-gradient"></div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ TICKER ═══════════════════════════════════════ -->
    <div class="ticker-wrap">
      <div class="ticker-track">
        <span class="ticker-set" aria-hidden="true">
          Alfajor&nbsp;·&nbsp;Palha Italiana&nbsp;·&nbsp;Copo Brigadeiro&nbsp;·&nbsp;Doce de Leite&nbsp;·&nbsp;Brigadeiro&nbsp;·&nbsp;Oreo&nbsp;·&nbsp;Artesanal&nbsp;·&nbsp;nunuca.nu&nbsp;&nbsp;&nbsp;
        </span>
        <span class="ticker-set" aria-hidden="true">
          Alfajor&nbsp;·&nbsp;Palha Italiana&nbsp;·&nbsp;Copo Brigadeiro&nbsp;·&nbsp;Doce de Leite&nbsp;·&nbsp;Brigadeiro&nbsp;·&nbsp;Oreo&nbsp;·&nbsp;Artesanal&nbsp;·&nbsp;nunuca.nu&nbsp;&nbsp;&nbsp;
        </span>
      </div>
    </div>

    <!-- ═══════════════════════════════════════ PRODUCTS ═══════════════════════════════════════ -->
    <section id="produtos" class="section products-section">
      <div class="section-header">
        <p class="section-eyebrow">— Nosso cardápio</p>
        <h2 class="section-title">Feitos pra você</h2>
        <p class="section-subtitle">Escolha seus favoritos e monte seu pedido com carinho.</p>
      </div>
      <div class="products-grid">
        <div
          v-for="product in products"
          :key="product.id"
          :class="['product-card', { 'product-unavailable': !product.is_available }]"
        >
          <div class="product-img-wrap">
            <img
              :src="product.image_path ? '/storage/' + product.image_path : '/images/default-product.jpg'"
              :alt="product.name"
              class="product-img"
            />
            <span v-if="product.is_new" class="badge-new">Novidade</span>
            <div v-if="!product.is_available" class="unavailable-overlay">Indisponível</div>
          </div>
          <div class="product-info">
            <span class="product-category">{{ product.category }}</span>
            <h3 class="product-name">{{ product.name }}</h3>
            <p v-if="product.flavor" class="product-flavor">{{ product.flavor }}</p>
            <p v-if="product.description" class="product-desc">{{ product.description }}</p>
            <div class="product-footer">
              <span class="product-price">
                {{ product.price.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
              </span>
              <div v-if="product.is_available">
                <button
                  v-if="!getCartItem(product.id)"
                  class="btn-add"
                  @click="addToCart(product)"
                >
                  Adicionar
                </button>
                <div v-else class="stepper">
                  <button class="stepper-btn" @click="decreaseQty(product.id)">−</button>
                  <span class="stepper-qty">{{ getCartItem(product.id).qty }}</span>
                  <button class="stepper-btn" @click="increaseQty(product.id)">+</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ ABOUT ═══════════════════════════════════════ -->
    <section id="sobre" class="about-section">
      <div class="about-left">
        <p class="section-eyebrow about-eyebrow">— Nossa história</p>
        <h2 class="about-title">Feito com<br><span class="about-script">amor de verdade.</span></h2>
        <p class="about-text">
          A nunuca.nu nasceu de uma cozinha cheia de afeto e de muita vontade de transformar ingredientes simples em momentos especiais. Cada doce é preparado artesanalmente, com atenção a cada detalhe — da escolha dos ingredientes à embalagem com carinho.
        </p>
        <p class="about-text">
          Mais do que doces, a gente entrega afeto. Nossa missão é que cada pessoa que provar um nunuca.nu lembre de algo bom, de alguém especial, de um momento único.
        </p>
        <div class="about-stats">
          <div class="stat">
            <span class="stat-num">100%</span>
            <span class="stat-label">Artesanal</span>
          </div>
          <div class="stat">
            <span class="stat-num">♥</span>
            <span class="stat-label">Feito com amor</span>
          </div>
          <div class="stat">
            <span class="stat-num">🍫</span>
            <span class="stat-label">Ingredientes selecionados</span>
          </div>
        </div>
      </div>
      <div class="about-right">
        <img
          src="/images/649235888_17854436478675357_3388398832682660146_n.jpg"
          alt="Sobre a nunuca.nu"
          class="about-img"
        />
      </div>
    </section>

    <!-- ═══════════════════════════════════════ FOOTER ═══════════════════════════════════════ -->
    <footer id="contato" class="site-footer">
      <div class="footer-inner">
        <div class="footer-brand">
          <a href="#" class="nav-logo footer-logo">nunuca<span class="logo-nu">.</span>nu</a>
          <p class="footer-desc">Doces artesanais feitos com amor, para adoçar todos os seus momentos.</p>
          <a href="https://instagram.com/nunuca.nu" target="_blank" rel="noopener" class="footer-insta">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
              <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
            </svg>
            @nunuca.nu
          </a>
        </div>
        <div class="footer-col">
          <h4 class="footer-col-title">Cardápio</h4>
          <ul>
            <li><a href="#produtos">Alfajores</a></li>
            <li><a href="#produtos">Brigadeiros</a></li>
            <li><a href="#produtos">Palha Italiana</a></li>
            <li><a href="#produtos">Copo Brigadeiro</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4 class="footer-col-title">Links</h4>
          <ul>
            <li><a href="#sobre">Nossa história</a></li>
            <li><a href="#produtos">Ver cardápio</a></li>
            <li><a href="https://instagram.com/nunuca.nu" target="_blank" rel="noopener">Instagram</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-copy">
        <span>© {{ new Date().getFullYear() }} nunuca.nu · Todos os direitos reservados</span>
      </div>
    </footer>

    <!-- ═══════════════════════════════════════ CART DRAWER ═══════════════════════════════════════ -->
    <Transition name="overlay-fade">
      <div v-if="cartOpen" class="cart-overlay" @click="cartOpen = false"></div>
    </Transition>
    <Transition name="drawer-slide">
      <div v-if="cartOpen" class="cart-drawer">
        <div class="drawer-header">
          <h3 class="drawer-title">Seu carrinho 🛍️</h3>
          <button class="drawer-close" @click="cartOpen = false">✕</button>
        </div>

        <!-- Empty state -->
        <div v-if="cart.length === 0" class="cart-empty">
          <span class="cart-empty-icon">🍬</span>
          <p>Seu carrinho está vazio.</p>
          <button class="btn btn-primary" style="margin-top:1rem;" @click="cartOpen = false">Ver cardápio</button>
        </div>

        <!-- Cart items -->
        <div v-else class="cart-body">
          <ul class="cart-list">
            <li v-for="item in cart" :key="item.id" class="cart-item">
              <img
                :src="item.image_path ? '/storage/' + item.image_path : '/images/default-product.jpg'"
                :alt="item.name"
                class="cart-item-img"
              />
              <div class="cart-item-info">
                <span class="cart-item-name">{{ item.name }}</span>
                <span class="cart-item-price">
                  {{ (item.price * item.qty).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
                </span>
              </div>
              <div class="stepper stepper-sm">
                <button class="stepper-btn" @click="decreaseQty(item.id)">−</button>
                <span class="stepper-qty">{{ item.qty }}</span>
                <button class="stepper-btn" @click="increaseQty(item.id)">+</button>
              </div>
            </li>
          </ul>

          <!-- Pickup/Delivery toggle -->
          <div class="dt-toggle">
            <button
              :class="['dt-btn', { active: deliveryType === 'pickup' }]"
              @click="deliveryType = 'pickup'"
            >🏪 Retirada</button>
            <button
              :class="['dt-btn', { active: deliveryType === 'delivery' }]"
              @click="deliveryType = 'delivery'"
            >🚚 Entrega</button>
          </div>

          <div class="cart-total">
            <span>Total</span>
            <span class="cart-total-value">
              {{ cartTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
            </span>
          </div>

          <button class="btn btn-primary btn-full" @click="openCheckout">
            Finalizar pedido
          </button>
        </div>
      </div>
    </Transition>

    <!-- ═══════════════════════════════════════ CHECKOUT MODAL ═══════════════════════════════════════ -->
    <Transition name="overlay-fade">
      <div v-if="checkoutOpen" class="modal-overlay" @click.self="checkoutOpen = false">
        <div class="modal">
          <div class="modal-header">
            <h3 class="modal-title">Finalizar pedido 🍫</h3>
            <button class="drawer-close" @click="checkoutOpen = false">✕</button>
          </div>
          <div class="modal-body">
            <!-- Order summary -->
            <div class="order-summary">
              <h4 class="summary-title">Resumo do pedido</h4>
              <ul class="summary-list">
                <li v-for="item in cart" :key="item.id" class="summary-item">
                  <span>{{ item.name }} × {{ item.qty }}</span>
                  <span>{{ (item.price * item.qty).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}</span>
                </li>
              </ul>
              <div class="summary-total">
                <span>Total</span>
                <strong>{{ cartTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}</strong>
              </div>
            </div>

            <!-- Checkout form -->
            <form @submit.prevent="submitOrder" class="checkout-form" novalidate>
              <div class="form-group">
                <label class="form-label">Nome completo *</label>
                <input
                  v-model="form.customer_name"
                  type="text"
                  class="form-input"
                  :class="{ 'input-error': form.errors.customer_name }"
                  placeholder="Seu nome"
                  required
                />
                <span v-if="form.errors.customer_name" class="error-msg">{{ form.errors.customer_name }}</span>
              </div>

              <div class="form-group">
                <label class="form-label">Telefone / WhatsApp *</label>
                <input
                  v-model="form.customer_phone"
                  type="tel"
                  class="form-input"
                  :class="{ 'input-error': form.errors.customer_phone }"
                  placeholder="(00) 00000-0000"
                  required
                />
                <span v-if="form.errors.customer_phone" class="error-msg">{{ form.errors.customer_phone }}</span>
              </div>

              <div class="form-group">
                <label class="form-label">E-mail <span class="optional">(opcional)</span></label>
                <input
                  v-model="form.customer_email"
                  type="email"
                  class="form-input"
                  :class="{ 'input-error': form.errors.customer_email }"
                  placeholder="seu@email.com"
                />
                <span v-if="form.errors.customer_email" class="error-msg">{{ form.errors.customer_email }}</span>
              </div>

              <div class="form-group">
                <label class="form-label">Tipo de entrega *</label>
                <select
                  v-model="form.delivery_type"
                  class="form-input"
                  :class="{ 'input-error': form.errors.delivery_type }"
                >
                  <option value="pickup">Retirada no local</option>
                  <option value="delivery">Entrega em domicílio</option>
                </select>
                <span v-if="form.errors.delivery_type" class="error-msg">{{ form.errors.delivery_type }}</span>
              </div>

              <Transition name="slide-down">
                <div v-if="form.delivery_type === 'delivery'" class="form-group">
                  <label class="form-label">Endereço de entrega *</label>
                  <input
                    v-model="form.address"
                    type="text"
                    class="form-input"
                    :class="{ 'input-error': form.errors.address }"
                    placeholder="Rua, número, bairro, cidade"
                  />
                  <span v-if="form.errors.address" class="error-msg">{{ form.errors.address }}</span>
                </div>
              </Transition>

              <div class="form-group">
                <label class="form-label">Observações <span class="optional">(opcional)</span></label>
                <textarea
                  v-model="form.notes"
                  class="form-input form-textarea"
                  :class="{ 'input-error': form.errors.notes }"
                  placeholder="Alguma preferência especial, alergia ou recado?"
                  rows="3"
                ></textarea>
                <span v-if="form.errors.notes" class="error-msg">{{ form.errors.notes }}</span>
              </div>

              <!-- PIX info -->
              <div v-if="pix" class="pix-info">
                <span class="pix-icon">💠</span>
                <div>
                  <p class="pix-label">Pagamento via PIX</p>
                  <p class="pix-key"><strong>Chave:</strong> {{ pix.key }}</p>
                  <p class="pix-key"><strong>Beneficiário:</strong> {{ pix.beneficiary }}</p>
                </div>
              </div>

              <button type="submit" class="btn btn-primary btn-full" :disabled="form.processing">
                <span v-if="form.processing" class="spinner"></span>
                {{ form.processing ? 'Enviando...' : 'Confirmar pedido' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

// ─── Props ───────────────────────────────────────────────────────────────────
const props = defineProps({
  products: Array,
  pix: Object,
})

// ─── Scroll / Nav ─────────────────────────────────────────────────────────────
const scrolled = ref(false)
function onScroll() {
  scrolled.value = window.scrollY > 10
}
onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// ─── Cart state ───────────────────────────────────────────────────────────────
const cart = ref([])
const cartOpen = ref(false)
const deliveryType = ref('pickup')

const totalItems = computed(() => cart.value.reduce((s, i) => s + i.qty, 0))
const cartTotal = computed(() => cart.value.reduce((s, i) => s + i.price * i.qty, 0))

function getCartItem(id) {
  return cart.value.find(i => i.id === id)
}

function addToCart(product) {
  const existing = getCartItem(product.id)
  if (existing) {
    existing.qty++
  } else {
    cart.value.push({ ...product, qty: 1 })
  }
}

function increaseQty(id) {
  const item = getCartItem(id)
  if (item) item.qty++
}

function decreaseQty(id) {
  const idx = cart.value.findIndex(i => i.id === id)
  if (idx === -1) return
  if (cart.value[idx].qty > 1) {
    cart.value[idx].qty--
  } else {
    cart.value.splice(idx, 1)
  }
}

// ─── Checkout ─────────────────────────────────────────────────────────────────
const checkoutOpen = ref(false)

const form = useForm({
  customer_name: '',
  customer_phone: '',
  customer_email: '',
  delivery_type: 'pickup',
  address: '',
  notes: '',
  items: [],
})

// sync delivery_type with drawer toggle
watch(deliveryType, val => {
  form.delivery_type = val
})

function openCheckout() {
  form.delivery_type = deliveryType.value
  form.items = cart.value.map(i => ({ id: i.id, qty: i.qty, price: i.price }))
  cartOpen.value = false
  checkoutOpen.value = true
}

function submitOrder() {
  form.items = cart.value.map(i => ({ id: i.id, qty: i.qty, price: i.price }))
  form.post(route('checkout.store'), {
    onSuccess: () => {
      checkoutOpen.value = false
      cart.value = []
      form.reset()
    },
  })
}
</script>

<style>
/* ─────────────────── GOOGLE FONTS ─────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Caveat:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap');

/* ─────────────────── TOKENS ─────────────────── */
:root {
  --choco: #3B1A0C;
  --choco-mid: #5A2515;
  --caramel: #B07535;
  --cream: #F6EEE0;
  --cream-2: #EAD9C2;
  --white: #FDFAF4;
  --muted: #7A5040;
  --nav-h: 68px;
  --radius: 18px;
  --shadow: 0 8px 32px rgba(59,26,12,0.13);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { scroll-behavior: smooth; }

body {
  font-family: 'DM Sans', sans-serif;
  background: var(--white);
  color: var(--choco);
  overflow-x: hidden;
}

/* ─────────────────── PAGE WRAPPER ─────────────────── */
.page-wrapper { min-height: 100vh; }

/* ─────────────────── NAV ─────────────────── */
.site-nav {
  position: sticky;
  top: 0;
  z-index: 100;
  height: var(--nav-h);
  background: var(--white);
  transition: border-bottom 0.25s, box-shadow 0.25s;
  border-bottom: 2px solid transparent;
}
.site-nav.nav-scrolled {
  border-bottom: 2px solid var(--cream-2);
  box-shadow: 0 2px 18px rgba(59,26,12,0.08);
}
.nav-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  height: 100%;
  display: flex;
  align-items: center;
  gap: 2rem;
}
.nav-logo {
  font-family: 'Fredoka', sans-serif;
  font-weight: 700;
  font-size: 1.6rem;
  color: var(--choco);
  text-decoration: none;
  letter-spacing: -0.02em;
  flex-shrink: 0;
}
.logo-nu {
  color: var(--caramel);
}
.nav-links {
  display: flex;
  gap: 2rem;
  list-style: none;
  margin-left: auto;
}
.nav-links a {
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--muted);
  text-decoration: none;
  transition: color 0.2s;
}
.nav-links a:hover { color: var(--caramel); }
.cart-btn {
  position: relative;
  background: var(--choco);
  color: var(--cream);
  border: none;
  border-radius: 50px;
  padding: 0.5rem 1.1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background 0.2s, transform 0.15s;
}
.cart-btn:hover { background: var(--choco-mid); transform: scale(1.04); }
.cart-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background: var(--caramel);
  color: #fff;
  border-radius: 999px;
  width: 20px;
  height: 20px;
  font-size: 0.7rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ─────────────────── HERO ─────────────────── */
.hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: calc(100vh - var(--nav-h));
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  align-items: center;
  gap: 3rem;
}
.hero-left {
  display: flex;
  flex-direction: column;
  gap: 1.4rem;
  padding: 4rem 0;
}
.hero-eyebrow {
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--caramel);
  letter-spacing: 0.04em;
}
.hero-h1 {
  line-height: 1.08;
}
.hero-h1-fredoka {
  font-family: 'Fredoka', sans-serif;
  font-weight: 700;
  font-size: clamp(2.6rem, 5vw, 4rem);
  color: var(--choco);
  display: block;
}
.hero-h1-caveat {
  font-family: 'Caveat', cursive;
  font-weight: 700;
  font-size: clamp(3rem, 6vw, 4.8rem);
  color: var(--caramel);
  display: block;
}
.hero-subtitle {
  font-size: 1.1rem;
  color: var(--muted);
  line-height: 1.65;
  max-width: 420px;
}
.hero-ctas {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

/* ─────────────────── BUTTONS ─────────────────── */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.75rem 1.75rem;
  border-radius: 50px;
  font-family: 'DM Sans', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  text-decoration: none;
  cursor: pointer;
  border: 2px solid transparent;
  transition: background 0.2s, color 0.2s, transform 0.15s, border-color 0.2s;
}
.btn:hover { transform: translateY(-2px); }
.btn-primary {
  background: var(--choco);
  color: var(--cream);
}
.btn-primary:hover { background: var(--choco-mid); }
.btn-primary:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  transform: none;
}
.btn-outline {
  background: transparent;
  color: var(--choco);
  border-color: var(--choco);
}
.btn-outline:hover {
  background: var(--choco);
  color: var(--cream);
}
.btn-full { width: 100%; }

/* ─────────────────── HERO IMAGE ─────────────────── */
.hero-right {
  height: 100%;
  display: flex;
  align-items: center;
}
.hero-img-wrap {
  position: relative;
  width: 100%;
  height: min(600px, calc(100vh - var(--nav-h) - 4rem));
  border-radius: var(--radius);
  overflow: hidden;
}
.hero-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.hero-img-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(to right, var(--white) 0%, transparent 35%);
  pointer-events: none;
}

/* ─────────────────── TICKER ─────────────────── */
.ticker-wrap {
  background: var(--choco);
  color: var(--cream);
  padding: 0.9rem 0;
  overflow: hidden;
  white-space: nowrap;
}
.ticker-track {
  display: inline-flex;
  animation: ticker 28s linear infinite;
}
.ticker-set {
  font-family: 'Fredoka', sans-serif;
  font-size: 1.05rem;
  font-weight: 500;
  letter-spacing: 0.04em;
  padding-right: 3rem;
  color: var(--cream);
}
@keyframes ticker {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}

/* ─────────────────── SECTIONS ─────────────────── */
.section {
  max-width: 1280px;
  margin: 0 auto;
  padding: 6rem 2rem;
}
.section-header {
  text-align: center;
  margin-bottom: 3.5rem;
}
.section-eyebrow {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--caramel);
  letter-spacing: 0.06em;
  text-transform: uppercase;
  margin-bottom: 0.6rem;
}
.section-title {
  font-family: 'Fredoka', sans-serif;
  font-weight: 700;
  font-size: clamp(2rem, 4vw, 2.8rem);
  color: var(--choco);
  margin-bottom: 0.7rem;
}
.section-subtitle {
  color: var(--muted);
  font-size: 1.05rem;
  max-width: 480px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ─────────────────── PRODUCTS GRID ─────────────────── */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.6rem;
}
.product-card {
  background: var(--white);
  border-radius: var(--radius);
  border: 1.5px solid var(--cream-2);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: transform 0.28s cubic-bezier(.22,.68,0,1.4), box-shadow 0.28s;
  cursor: default;
}
.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 40px rgba(59,26,12,0.14);
}
.product-unavailable {
  opacity: 0.55;
  pointer-events: none;
}
.product-img-wrap {
  position: relative;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  background: var(--cream-2);
}
.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.35s cubic-bezier(.22,.68,0,1.4);
}
.product-card:hover .product-img { transform: scale(1.06); }
.badge-new {
  position: absolute;
  top: 10px;
  left: 10px;
  background: var(--caramel);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  padding: 0.3rem 0.75rem;
  border-radius: 50px;
}
.unavailable-overlay {
  position: absolute;
  inset: 0;
  background: rgba(59,26,12,0.38);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 600;
  font-size: 0.9rem;
  letter-spacing: 0.05em;
}
.product-info {
  padding: 1rem 1.1rem 1.2rem;
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: 0.25rem;
}
.product-category {
  font-size: 0.72rem;
  font-weight: 700;
  color: var(--caramel);
  text-transform: uppercase;
  letter-spacing: 0.07em;
}
.product-name {
  font-family: 'Fredoka', sans-serif;
  font-weight: 600;
  font-size: 1.15rem;
  color: var(--choco);
  line-height: 1.2;
}
.product-flavor {
  font-family: 'Caveat', cursive;
  font-size: 1rem;
  color: var(--muted);
}
.product-desc {
  font-size: 0.82rem;
  color: var(--muted);
  line-height: 1.5;
  margin-top: 0.15rem;
}
.product-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
  padding-top: 0.8rem;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.product-price {
  font-weight: 700;
  font-size: 1.05rem;
  color: var(--choco);
}
.btn-add {
  background: var(--choco);
  color: var(--cream);
  border: none;
  border-radius: 50px;
  padding: 0.42rem 1rem;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.18s, transform 0.15s;
}
.btn-add:hover { background: var(--choco-mid); transform: scale(1.04); }

/* ─────────────────── STEPPER ─────────────────── */
.stepper {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  background: var(--cream);
  border-radius: 50px;
  padding: 0.2rem 0.3rem;
  border: 1.5px solid var(--cream-2);
}
.stepper-btn {
  background: var(--choco);
  color: var(--cream);
  border: none;
  border-radius: 50%;
  width: 26px;
  height: 26px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  transition: background 0.15s;
}
.stepper-btn:hover { background: var(--caramel); }
.stepper-qty {
  font-weight: 700;
  font-size: 0.92rem;
  min-width: 22px;
  text-align: center;
  color: var(--choco);
}
.stepper-sm .stepper-btn { width: 24px; height: 24px; font-size: 0.9rem; }
.stepper-sm .stepper-qty { font-size: 0.85rem; }

/* ─────────────────── ABOUT ─────────────────── */
.about-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 520px;
  background: var(--choco);
}
.about-left {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 1.4rem;
  padding: 5rem 4rem 5rem 6rem;
  max-width: 100%;
}
.about-eyebrow {
  color: var(--caramel);
}
.about-title {
  font-family: 'Fredoka', sans-serif;
  font-weight: 700;
  font-size: clamp(2rem, 3.5vw, 2.8rem);
  color: var(--cream);
  line-height: 1.15;
}
.about-script {
  font-family: 'Caveat', cursive;
  color: var(--caramel);
}
.about-text {
  color: var(--cream-2);
  font-size: 1rem;
  line-height: 1.7;
  max-width: 460px;
}
.about-stats {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
  margin-top: 0.5rem;
}
.stat {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.stat-num {
  font-family: 'Fredoka', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--caramel);
}
.stat-label {
  font-size: 0.8rem;
  color: var(--cream-2);
  font-weight: 500;
}
.about-right {
  position: relative;
  overflow: hidden;
}
.about-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* ─────────────────── FOOTER ─────────────────── */
.site-footer {
  background: var(--choco);
  color: var(--cream-2);
  border-top: 1px solid rgba(176,117,53,0.2);
}
.footer-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 4rem 2rem 2rem;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 3rem;
}
.footer-logo {
  color: var(--cream);
  margin-bottom: 1rem;
  display: inline-block;
}
.footer-brand {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}
.footer-desc {
  font-size: 0.9rem;
  color: var(--cream-2);
  line-height: 1.6;
  max-width: 280px;
}
.footer-insta {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--caramel);
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  transition: opacity 0.18s;
}
.footer-insta:hover { opacity: 0.8; }
.footer-col-title {
  font-family: 'Fredoka', sans-serif;
  font-size: 1rem;
  font-weight: 600;
  color: var(--cream);
  margin-bottom: 1rem;
}
.footer-col ul {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}
.footer-col a {
  color: var(--cream-2);
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.18s;
}
.footer-col a:hover { color: var(--caramel); }
.footer-copy {
  max-width: 1280px;
  margin: 0 auto;
  padding: 1.5rem 2rem;
  border-top: 1px solid rgba(255,255,255,0.08);
  font-size: 0.82rem;
  color: var(--muted);
  text-align: center;
}

/* ─────────────────── CART DRAWER ─────────────────── */
.cart-overlay {
  position: fixed;
  inset: 0;
  background: rgba(59,26,12,0.45);
  backdrop-filter: blur(4px);
  z-index: 200;
}
.cart-drawer {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  width: min(420px, 92vw);
  background: var(--white);
  z-index: 201;
  display: flex;
  flex-direction: column;
  box-shadow: -8px 0 48px rgba(59,26,12,0.18);
}
.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.4rem 1.6rem;
  border-bottom: 1.5px solid var(--cream-2);
  flex-shrink: 0;
}
.drawer-title {
  font-family: 'Fredoka', sans-serif;
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--choco);
}
.drawer-close {
  background: none;
  border: none;
  font-size: 1.1rem;
  color: var(--muted);
  cursor: pointer;
  padding: 0.3rem;
  border-radius: 50%;
  transition: background 0.15s, color 0.15s;
}
.drawer-close:hover { background: var(--cream-2); color: var(--choco); }
.cart-empty {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: var(--muted);
  font-size: 1rem;
  padding: 2rem;
  text-align: center;
}
.cart-empty-icon { font-size: 3rem; }
.cart-body {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 0;
  padding: 1rem 1.4rem;
}
.cart-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
  margin-bottom: 1.2rem;
}
.cart-item {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  background: var(--cream);
  border-radius: 12px;
  padding: 0.7rem;
}
.cart-item-img {
  width: 54px;
  height: 54px;
  border-radius: 10px;
  object-fit: cover;
  flex-shrink: 0;
}
.cart-item-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.cart-item-name {
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--choco);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cart-item-price {
  font-size: 0.85rem;
  color: var(--caramel);
  font-weight: 700;
}

/* ─────────────────── DT TOGGLE ─────────────────── */
.dt-toggle {
  display: flex;
  gap: 0.5rem;
  background: var(--cream);
  border-radius: 50px;
  padding: 0.3rem;
  margin-bottom: 1rem;
}
.dt-btn {
  flex: 1;
  background: none;
  border: none;
  border-radius: 50px;
  padding: 0.55rem 0.8rem;
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--muted);
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.dt-btn.active {
  background: var(--choco);
  color: var(--cream);
}
.cart-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.9rem 0;
  border-top: 1.5px solid var(--cream-2);
  margin-bottom: 1rem;
}
.cart-total-value {
  font-family: 'Fredoka', sans-serif;
  font-weight: 700;
  font-size: 1.3rem;
  color: var(--choco);
}

/* ─────────────────── MODAL ─────────────────── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(59,26,12,0.5);
  backdrop-filter: blur(5px);
  z-index: 300;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}
.modal {
  background: var(--white);
  border-radius: 24px;
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 24px 80px rgba(59,26,12,0.22);
  display: flex;
  flex-direction: column;
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.4rem 1.8rem;
  border-bottom: 1.5px solid var(--cream-2);
  position: sticky;
  top: 0;
  background: var(--white);
  z-index: 1;
}
.modal-title {
  font-family: 'Fredoka', sans-serif;
  font-weight: 700;
  font-size: 1.3rem;
  color: var(--choco);
}
.modal-body {
  padding: 1.6rem 1.8rem;
  display: flex;
  flex-direction: column;
  gap: 1.4rem;
}

/* ─────────────────── ORDER SUMMARY ─────────────────── */
.order-summary {
  background: var(--cream);
  border-radius: 14px;
  padding: 1.1rem 1.2rem;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}
.summary-title {
  font-family: 'Fredoka', sans-serif;
  font-weight: 600;
  font-size: 1rem;
  color: var(--choco);
  margin-bottom: 0.3rem;
}
.summary-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
}
.summary-item {
  display: flex;
  justify-content: space-between;
  font-size: 0.88rem;
  color: var(--muted);
}
.summary-total {
  display: flex;
  justify-content: space-between;
  font-size: 1rem;
  color: var(--choco);
  border-top: 1.5px solid var(--cream-2);
  padding-top: 0.6rem;
  margin-top: 0.3rem;
}
.summary-total strong {
  font-family: 'Fredoka', sans-serif;
  font-weight: 700;
  font-size: 1.15rem;
}

/* ─────────────────── CHECKOUT FORM ─────────────────── */
.checkout-form {
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}
.form-label {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--choco);
}
.optional {
  font-weight: 400;
  color: var(--muted);
}
.form-input {
  background: var(--cream);
  border: 1.5px solid var(--cream-2);
  border-radius: 10px;
  padding: 0.7rem 0.9rem;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  color: var(--choco);
  outline: none;
  transition: border-color 0.2s;
  width: 100%;
}
.form-input:focus { border-color: var(--caramel); }
.input-error { border-color: #c0392b !important; }
.error-msg {
  font-size: 0.8rem;
  color: #c0392b;
  font-weight: 500;
}
.form-textarea { resize: vertical; min-height: 80px; }

/* ─────────────────── PIX INFO ─────────────────── */
.pix-info {
  background: var(--cream);
  border-radius: 12px;
  padding: 1rem 1.1rem;
  display: flex;
  align-items: flex-start;
  gap: 0.8rem;
}
.pix-icon { font-size: 1.6rem; flex-shrink: 0; }
.pix-label {
  font-weight: 700;
  font-size: 0.9rem;
  color: var(--choco);
  margin-bottom: 0.3rem;
}
.pix-key {
  font-size: 0.85rem;
  color: var(--muted);
}

/* ─────────────────── SPINNER ─────────────────── */
.spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ─────────────────── TRANSITIONS ─────────────────── */
.overlay-fade-enter-active,
.overlay-fade-leave-active { transition: opacity 0.28s ease; }
.overlay-fade-enter-from,
.overlay-fade-leave-to { opacity: 0; }

.drawer-slide-enter-active,
.drawer-slide-leave-active { transition: transform 0.32s cubic-bezier(.4,0,.2,1); }
.drawer-slide-enter-from,
.drawer-slide-leave-to { transform: translateX(100%); }

.slide-down-enter-active,
.slide-down-leave-active { transition: all 0.25s ease; overflow: hidden; }
.slide-down-enter-from,
.slide-down-leave-to { opacity: 0; max-height: 0; }
.slide-down-enter-to,
.slide-down-leave-from { opacity: 1; max-height: 120px; }

/* ─────────────────── RESPONSIVE ─────────────────── */
@media (max-width: 1024px) {
  .products-grid { grid-template-columns: repeat(3, 1fr); }
  .about-left { padding: 4rem 2.5rem 4rem 3rem; }
}
@media (max-width: 768px) {
  .hero {
    grid-template-columns: 1fr;
    padding: 2rem 1.5rem 3rem;
    min-height: auto;
    gap: 2rem;
  }
  .hero-right { display: none; }
  .hero-left { padding: 2rem 0; }
  .products-grid { grid-template-columns: repeat(2, 1fr); }
  .about-section { grid-template-columns: 1fr; }
  .about-right { height: 280px; }
  .about-left { padding: 3.5rem 1.5rem; }
  .footer-inner { grid-template-columns: 1fr 1fr; }
  .nav-links { display: none; }
  .section { padding: 4rem 1.5rem; }
}
@media (max-width: 480px) {
  .products-grid { grid-template-columns: 1fr 1fr; gap: 1rem; }
  .footer-inner { grid-template-columns: 1fr; gap: 2rem; }
  .modal { border-radius: 18px; }
  .modal-body { padding: 1.2rem 1.2rem; }
}
</style>
