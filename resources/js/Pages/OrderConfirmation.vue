<template>
  <!-- Flash Banner -->
  <Transition name="flash">
    <div v-if="flash" class="flash-banner">
      {{ flash }}
    </div>
  </Transition>

  <!-- Sticky Nav -->
  <header class="nav">
    <div class="nav-inner">
      <Link :href="route('home')" class="nav-logo">
        <span class="logo-text">nunuca<span class="logo-dot">.nu</span></span>
      </Link>
      <Link :href="route('home')" class="nav-home-link">← Voltar ao início</Link>
    </div>
  </header>

  <main class="page">

    <!-- Confirmation Hero -->
    <section class="hero">
      <div class="hero-emoji">🎉</div>
      <h1 class="hero-title">Pedido recebido!</h1>
      <p class="hero-subtitle">Obrigada pela compra! Aguarde a confirmação.</p>
      <div class="order-code-badge">
        <span class="order-code-label">Código do pedido</span>
        <span class="order-code-value">{{ order.code }}</span>
      </div>
    </section>

    <!-- Two-column layout -->
    <div class="columns">

      <!-- Left: Order Summary -->
      <div class="card">
        <h2 class="card-title">Resumo do pedido</h2>
        <ul class="item-list">
          <li v-for="(item, index) in order.items" :key="index" class="item-row">
            <div class="item-info">
              <span class="item-name">{{ item.product_name }}</span>
              <span v-if="item.product_flavor" class="item-flavor">{{ item.product_flavor }}</span>
            </div>
            <div class="item-right">
              <span class="item-qty">× {{ item.quantity }}</span>
              <span class="item-subtotal">{{ formatCurrency(item.subtotal) }}</span>
            </div>
          </li>
        </ul>
        <div class="divider"></div>
        <div class="total-row">
          <span class="total-label">Total</span>
          <span class="total-value">{{ formatCurrency(order.total) }}</span>
        </div>
      </div>

      <!-- Right: PIX Payment -->
      <div class="card">
        <h2 class="card-title">Como pagar</h2>
        <p class="pix-section-subtitle">Pagamento via PIX</p>

        <div class="pix-key-box">
          <span class="pix-key-label">Chave PIX</span>
          <span class="pix-key-value">{{ pix.key || 'Chave PIX a configurar' }}</span>
        </div>

        <p class="pix-beneficiary">
          <strong>Beneficiário:</strong> {{ pix.beneficiary }}
        </p>

        <p class="pix-instructions">
          Copie a chave PIX, abra seu banco e confirme o pagamento de
          <strong>{{ formatCurrency(order.total) }}</strong>.
          Envie o comprovante pelo WhatsApp ou Instagram.
        </p>

        <button
          class="btn-copy"
          :class="{ copied: copied }"
          :disabled="!pix.key"
          @click="copyPix"
        >
          <span v-if="!copied">📋 Copiar chave PIX</span>
          <span v-else>✅ Copiado!</span>
        </button>
      </div>

    </div>

    <!-- Delivery Info Card -->
    <div class="card delivery-card">
      <h2 class="card-title">Informações de entrega</h2>
      <div class="delivery-grid">
        <div class="delivery-field">
          <span class="delivery-field-label">Nome</span>
          <span class="delivery-field-value">{{ order.customer_name }}</span>
        </div>
        <div class="delivery-field">
          <span class="delivery-field-label">Telefone</span>
          <span class="delivery-field-value">{{ order.customer_phone }}</span>
        </div>
        <div class="delivery-field">
          <span class="delivery-field-label">Tipo</span>
          <span class="delivery-field-value">
            <span class="delivery-badge" :class="order.delivery_type === 'delivery' ? 'badge-delivery' : 'badge-pickup'">
              {{ order.delivery_type === 'delivery' ? 'Entrega' : 'Retirada' }}
            </span>
          </span>
        </div>
        <div v-if="order.delivery_type === 'delivery' && order.address" class="delivery-field delivery-field-full">
          <span class="delivery-field-label">Endereço</span>
          <span class="delivery-field-value">{{ order.address }}</span>
        </div>
        <div v-if="order.notes" class="delivery-field delivery-field-full">
          <span class="delivery-field-label">Observações</span>
          <span class="delivery-field-value">{{ order.notes }}</span>
        </div>
      </div>
    </div>

    <!-- Return Button -->
    <div class="return-section">
      <Link :href="route('home')" class="btn-return">
        🍫 Fazer outro pedido
      </Link>
    </div>

  </main>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  order: Object, // {id, code, customer_name, customer_phone, customer_email, delivery_type, address, notes, status, total, items: [{product_name, product_flavor, quantity, unit_price, subtotal}], created_at}
  pix: Object    // {key: string, beneficiary: string}
})

const page = usePage()
const flash = computed(() => page.props.flash?.success ?? null)

const copied = ref(false)

function formatCurrency(value) {
  return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

async function copyPix() {
  if (!props.pix?.key) return
  try {
    await navigator.clipboard.writeText(props.pix.key)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2500)
  } catch {
    // fallback for browsers without clipboard API
    const el = document.createElement('textarea')
    el.value = props.pix.key
    document.body.appendChild(el)
    el.select()
    document.execCommand('copy')
    document.body.removeChild(el)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2500)
  }
}
</script>

<style scoped>
/* ─── CSS Variables ─────────────────────────────────── */
:root {
  --choco: #3B1A0C;
  --caramel: #B07535;
  --cream: #F6EEE0;
  --cream-dark: #EBE0CC;
  --text: #2C1A0E;
  --text-muted: #7A5C3A;
  --radius: 16px;
  --shadow: 0 4px 24px rgba(59, 26, 12, 0.10);
}

/* ─── Flash Banner ──────────────────────────────────── */
.flash-banner {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: #2d7a2d;
  color: #fff;
  text-align: center;
  padding: 14px 24px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  font-weight: 500;
  letter-spacing: 0.01em;
}

.flash-enter-active,
.flash-leave-active {
  transition: transform 0.35s ease, opacity 0.35s ease;
}
.flash-enter-from,
.flash-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

/* ─── Nav ───────────────────────────────────────────── */
.nav {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--choco);
  box-shadow: 0 2px 12px rgba(59, 26, 12, 0.3);
}

.nav-inner {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 24px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.nav-logo {
  text-decoration: none;
}

.logo-text {
  font-family: 'Fredoka', sans-serif;
  font-size: 1.75rem;
  font-weight: 600;
  color: var(--cream);
  letter-spacing: 0.02em;
}

.logo-dot {
  color: var(--caramel);
}

.nav-home-link {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem;
  color: var(--cream);
  text-decoration: none;
  opacity: 0.85;
  transition: opacity 0.2s;
}

.nav-home-link:hover {
  opacity: 1;
  text-decoration: underline;
}

/* ─── Page ──────────────────────────────────────────── */
.page {
  min-height: 100vh;
  background: var(--cream);
  padding-bottom: 80px;
}

/* ─── Hero ──────────────────────────────────────────── */
.hero {
  text-align: center;
  padding: 80px 24px 60px;
}

.hero-emoji {
  font-size: 4rem;
  margin-bottom: 16px;
  display: block;
  line-height: 1;
}

.hero-title {
  font-family: 'Fredoka', sans-serif;
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 600;
  color: var(--choco);
  margin: 0 0 12px;
}

.hero-subtitle {
  font-family: 'DM Sans', sans-serif;
  font-size: 1.1rem;
  color: var(--text-muted);
  margin: 0 0 32px;
}

.order-code-badge {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  background: #fff;
  border: 2px solid var(--caramel);
  border-radius: 20px;
  padding: 16px 40px;
  gap: 4px;
  box-shadow: var(--shadow);
}

.order-code-label {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.78rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.order-code-value {
  font-family: 'Fredoka', sans-serif;
  font-size: 2rem;
  font-weight: 600;
  color: var(--choco);
  letter-spacing: 0.04em;
}

/* ─── Columns ───────────────────────────────────────── */
.columns {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  max-width: 1000px;
  margin: 0 auto 24px;
  padding: 0 24px;
}

@media (max-width: 700px) {
  .columns {
    grid-template-columns: 1fr;
  }
}

/* ─── Cards ─────────────────────────────────────────── */
.card {
  background: #fff;
  border-radius: var(--radius);
  padding: 28px;
  box-shadow: var(--shadow);
}

.card-title {
  font-family: 'Fredoka', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--choco);
  margin: 0 0 20px;
}

/* ─── Item List ─────────────────────────────────────── */
.item-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
}

.item-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.item-name {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text);
}

.item-flavor {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.82rem;
  color: var(--text-muted);
}

.item-right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.item-qty {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.85rem;
  color: var(--text-muted);
}

.item-subtotal {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text);
  min-width: 70px;
  text-align: right;
}

.divider {
  height: 1px;
  background: var(--cream-dark);
  margin: 20px 0;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.total-label {
  font-family: 'Fredoka', sans-serif;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--choco);
}

.total-value {
  font-family: 'Fredoka', sans-serif;
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--caramel);
}

/* ─── PIX Section ───────────────────────────────────── */
.pix-section-subtitle {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin: 0 0 16px;
}

.pix-key-box {
  background: var(--cream);
  border: 2px solid var(--caramel);
  border-radius: 12px;
  padding: 14px 18px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 14px;
}

.pix-key-label {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.pix-key-value {
  font-family: 'Caveat', cursive;
  font-size: 1.35rem;
  font-weight: 700;
  color: var(--caramel);
  word-break: break-all;
}

.pix-beneficiary {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem;
  color: var(--text-muted);
  margin: 0 0 12px;
}

.pix-beneficiary strong {
  color: var(--text);
}

.pix-instructions {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem;
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0 0 20px;
}

.pix-instructions strong {
  color: var(--choco);
}

.btn-copy {
  width: 100%;
  padding: 13px 20px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-family: 'Fredoka', sans-serif;
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.02em;
  background: var(--caramel);
  color: #fff;
  transition: background 0.2s, transform 0.1s;
}

.btn-copy:hover:not(:disabled) {
  background: #96612c;
  transform: translateY(-1px);
}

.btn-copy:disabled {
  background: #c8b49a;
  cursor: not-allowed;
}

.btn-copy.copied {
  background: #2d7a2d;
}

/* ─── Delivery Card ─────────────────────────────────── */
.delivery-card {
  max-width: 1000px;
  margin: 0 auto 24px;
  margin-left: auto;
  margin-right: auto;
  width: calc(100% - 48px);
}

.delivery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}

.delivery-field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.delivery-field-full {
  grid-column: 1 / -1;
}

.delivery-field-label {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.delivery-field-value {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  color: var(--text);
  font-weight: 500;
}

.delivery-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
  font-family: 'DM Sans', sans-serif;
}

.badge-delivery {
  background: #E8F4FD;
  color: #1a6fa8;
}

.badge-pickup {
  background: #FDF3E8;
  color: var(--caramel);
}

/* ─── Return Section ─────────────────────────────────── */
.return-section {
  text-align: center;
  margin-top: 40px;
}

.btn-return {
  display: inline-block;
  padding: 16px 40px;
  background: var(--choco);
  color: var(--cream);
  border-radius: 14px;
  font-family: 'Fredoka', sans-serif;
  font-size: 1.1rem;
  font-weight: 500;
  text-decoration: none;
  letter-spacing: 0.02em;
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
  box-shadow: 0 4px 16px rgba(59, 26, 12, 0.25);
}

.btn-return:hover {
  background: #5a2a12;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(59, 26, 12, 0.3);
}
</style>
