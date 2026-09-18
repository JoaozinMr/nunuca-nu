<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  recentOrders: Array,
  statusLabels: Object,
})

const fmt = (v) =>
  Number(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

const fmtDate = (d) =>
  new Date(d).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  })

const todayLabel = new Date().toLocaleDateString('pt-BR', {
  weekday: 'long',
  day: '2-digit',
  month: 'long',
  year: 'numeric',
})

const logoutForm = useForm({})
function logout() {
  logoutForm.post(route('admin.logout'))
}

function goToOrder(order) {
  router.visit(route('admin.orders.index'), { data: { highlight: order.id } })
}

const deliveryLabels = {
  delivery: 'Entrega',
  pickup: 'Retirada',
}
</script>

<template>
  <div class="admin-layout">
    <!-- ───────────── SIDEBAR ───────────── -->
    <aside class="sidebar">
      <div class="sb-logo">
        nunuca<span class="sb-logo-nu">.</span>nu
      </div>

      <nav class="sb-nav">
        <!-- Dashboard -->
        <Link
          :href="route('admin.dashboard')"
          :class="['sb-link', { active: route().current('admin.dashboard') }]"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="14" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/>
          </svg>
          Dashboard
        </Link>

        <!-- Pedidos -->
        <Link
          :href="route('admin.orders.index')"
          :class="['sb-link', { active: route().current('admin.orders.*') }]"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
            <rect x="9" y="3" width="6" height="4" rx="1"/>
            <line x1="9" y1="12" x2="15" y2="12"/>
            <line x1="9" y1="16" x2="13" y2="16"/>
          </svg>
          Pedidos
          <span v-if="stats.pending_count > 0" class="sb-badge">{{ stats.pending_count }}</span>
        </Link>

        <!-- Produtos -->
        <Link
          :href="route('admin.products.index')"
          :class="['sb-link', { active: route().current('admin.products.*') }]"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
            <line x1="12" y1="22.08" x2="12" y2="12"/>
          </svg>
          Produtos
          <span v-if="stats.low_stock_count > 0" class="sb-badge sb-badge--warn">{{ stats.low_stock_count }}</span>
        </Link>
      </nav>

      <div class="sb-footer">
        <a :href="route('home')" target="_blank" class="sb-footer-link">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            <polyline points="15 3 21 3 21 9"/>
            <line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
          Ver loja
        </a>
        <button class="sb-footer-link sb-logout" @click="logout">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Sair
        </button>
      </div>
    </aside>

    <!-- ───────────── MAIN CONTENT ───────────── -->
    <main class="admin-main">

      <!-- Page Header -->
      <header class="page-header">
        <div>
          <h1 class="page-title">Dashboard</h1>
          <p class="page-sub">{{ todayLabel }}</p>
        </div>
      </header>

      <!-- ── Stats Grid (4 columns) ── -->
      <div class="stats-grid">
        <!-- Revenue Today -->
        <div class="stat-card accent">
          <span class="stat-label">Receita hoje</span>
          <span class="stat-val">{{ fmt(stats.revenue_today) }}</span>
          <span class="stat-sub">hoje</span>
        </div>

        <!-- Revenue Monthly -->
        <div class="stat-card">
          <span class="stat-label">Receita mensal</span>
          <span class="stat-val">{{ fmt(stats.revenue_monthly) }}</span>
          <span class="stat-sub">este mês</span>
        </div>

        <!-- Orders Today -->
        <div class="stat-card">
          <span class="stat-label">Pedidos hoje</span>
          <span class="stat-val">{{ stats.orders_today }}</span>
          <span class="stat-sub">{{ stats.orders_total }} total</span>
        </div>

        <!-- Pending -->
        <div class="stat-card">
          <span class="stat-label">Pendentes</span>
          <span class="stat-val">{{ stats.pending_count }}</span>
          <span class="stat-sub">aguardando ação</span>
        </div>
      </div>

      <!-- ── Status Mini-Cards Row ── -->
      <div class="mini-cards-row">
        <!-- Preparando -->
        <div class="mini-card">
          <div class="mini-icon mini-icon--blue">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
            </svg>
          </div>
          <div class="mini-info">
            <span class="mini-val">{{ stats.preparing_count }}</span>
            <span class="mini-label">Preparando</span>
          </div>
        </div>

        <!-- Saiu para entrega -->
        <div class="mini-card">
          <div class="mini-icon mini-icon--purple">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="1" y="3" width="15" height="13" rx="1"/>
              <path d="M16 8h4l3 5v3h-7V8z"/>
              <circle cx="5.5" cy="18.5" r="2.5"/>
              <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
          </div>
          <div class="mini-info">
            <span class="mini-val">{{ stats.delivery_count }}</span>
            <span class="mini-label">Saiu para entrega</span>
          </div>
        </div>

        <!-- Estoque baixo -->
        <div class="mini-card" :class="{ 'mini-card--warn': stats.low_stock_count > 0 }">
          <div class="mini-icon" :class="stats.low_stock_count > 0 ? 'mini-icon--warn' : 'mini-icon--gray'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
          </div>
          <div class="mini-info">
            <span class="mini-val" :class="{ 'mini-val--warn': stats.low_stock_count > 0 }">{{ stats.low_stock_count }}</span>
            <span class="mini-label">Estoque baixo</span>
          </div>
        </div>
      </div>

      <!-- ── Recent Orders Panel ── -->
      <div class="panel">
        <div class="panel-head">
          <h2 class="panel-title">Pedidos recentes</h2>
          <Link :href="route('admin.orders.index')" class="panel-action">
            Ver todos →
          </Link>
        </div>

        <div class="table-wrap">
          <table v-if="recentOrders && recentOrders.length > 0">
            <thead>
              <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Itens</th>
                <th>Total</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="order in recentOrders"
                :key="order.id"
                class="order-row"
                @click="goToOrder(order)"
              >
                <td>
                  <span class="mono">#{{ order.code }}</span>
                </td>
                <td>{{ order.customer_name }}</td>
                <td>
                  <span class="dt-chip">{{ order.total_items }} {{ order.total_items === 1 ? 'item' : 'itens' }}</span>
                </td>
                <td>
                  <strong>{{ fmt(order.total) }}</strong>
                </td>
                <td>
                  <span class="dt-chip">{{ deliveryLabels[order.delivery_type] ?? order.delivery_type }}</span>
                </td>
                <td>
                  <span :class="['status-badge', `status-${order.status}`]">
                    {{ statusLabels[order.status] ?? order.status }}
                  </span>
                </td>
                <td class="date-cell">{{ fmtDate(order.created_at) }}</td>
              </tr>
            </tbody>
          </table>

          <div v-else class="empty-state">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.35">
              <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
              <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
            <p>Nenhum pedido encontrado ainda.</p>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>

<style scoped>
/* ─── Layout ─── */
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #F6EEE0;
  font-family: 'DM Sans', sans-serif;
}

/* ─── Sidebar ─── */
.sidebar {
  width: 240px;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  background: #3B1A0C;
  display: flex;
  flex-direction: column;
  z-index: 50;
  overflow-y: auto;
}

.sb-logo {
  padding: 28px 24px 20px;
  font-family: 'Fredoka', 'Nunito', sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #F6EEE0;
  letter-spacing: -0.3px;
  border-bottom: 1px solid rgba(246,238,224,.08);
  margin-bottom: 12px;
}

.sb-logo-nu {
  color: #B07535;
}

.sb-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 0 12px;
}

.sb-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 10px;
  color: rgba(246, 238, 224, 0.55);
  font-size: 14px;
  font-weight: 400;
  text-decoration: none;
  transition: background 0.15s, color 0.15s;
  position: relative;
}

.sb-link:hover {
  background: rgba(246, 238, 224, 0.07);
  color: rgba(246, 238, 224, 0.85);
}

.sb-link.active {
  background: rgba(246, 238, 224, 0.12);
  color: #F6EEE0;
  font-weight: 500;
}

.sb-badge {
  margin-left: auto;
  background: #B07535;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  border-radius: 999px;
  padding: 1px 7px;
  line-height: 18px;
  min-width: 20px;
  text-align: center;
}

.sb-badge--warn {
  background: #d97706;
}

.sb-footer {
  padding: 16px 12px;
  border-top: 1px solid rgba(246,238,224,.08);
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.sb-footer-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 14px;
  border-radius: 10px;
  color: rgba(246, 238, 224, 0.5);
  font-size: 13px;
  font-weight: 500;
  text-decoration: none;
  background: none;
  border: none;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
  width: 100%;
  text-align: left;
}

.sb-footer-link:hover {
  background: rgba(246,238,224,.07);
  color: #F6EEE0;
}

.sb-logout:hover {
  background: rgba(239, 68, 68, 0.12);
  color: #f87171;
}

/* ─── Main ─── */
.admin-main {
  margin-left: 240px;
  flex: 1;
  padding: 40px 48px;
  min-height: 100vh;
}

/* ─── Page Header ─── */
.page-header {
  margin-bottom: 32px;
}

.page-title {
  font-family: 'Fredoka', sans-serif;
  font-size: 28px;
  font-weight: 600;
  color: #3B1A0C;
  margin: 0 0 4px;
  letter-spacing: -0.4px;
}

.page-sub {
  font-size: 14px;
  color: #7A5040;
  margin: 0;
  text-transform: capitalize;
}

/* ─── Stats Grid ─── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
  margin-bottom: 18px;
}

.stat-card {
  background: #FDFAF4;
  border-radius: 16px;
  padding: 22px 24px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  border: 1px solid #EAD9C2;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  transition: box-shadow 0.15s;
}

.stat-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.09);
}

.stat-card.accent {
  background: linear-gradient(135deg, #3d1c06 0%, #5c2d10 100%);
  color: #fff;
}

.stat-label {
  font-size: 12px;
  font-weight: 600;
  color: #7A5040;
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

.stat-card.accent .stat-label {
  color: rgba(245, 215, 170, 0.7);
}

.stat-val {
  font-size: 26px;
  font-weight: 700;
  color: #3B1A0C;
  line-height: 1.1;
  letter-spacing: -0.5px;
}

.stat-card.accent .stat-val {
  color: #f5d7aa;
}

.stat-sub {
  font-size: 12px;
  color: #7A5040;
}

.stat-card.accent .stat-sub {
  color: rgba(245, 215, 170, 0.55);
}

/* ─── Mini Cards Row ─── */
.mini-cards-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-bottom: 28px;
}

.mini-card {
  background: #fff;
  border-radius: 14px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.05);
  transition: box-shadow 0.15s;
}

.mini-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.06);
}

.mini-card--warn {
  border: 1px solid #fde68a;
  background: #fffbeb;
}

.mini-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.mini-icon--blue {
  background: #eff6ff;
  color: #3b82f6;
}

.mini-icon--purple {
  background: #f5f3ff;
  color: #7c3aed;
}

.mini-icon--warn {
  background: #fef3c7;
  color: #d97706;
}

.mini-icon--gray {
  background: #f3f4f6;
  color: #9ca3af;
}

.mini-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.mini-val {
  font-size: 22px;
  font-weight: 700;
  color: #3B1A0C;
  line-height: 1;
}

.mini-val--warn {
  color: #d97706;
}

.mini-label {
  font-size: 12px;
  color: #7A5040;
  font-weight: 500;
}

/* ─── Panel ─── */
.panel {
  background: #FDFAF4;
  border-radius: 18px;
  border: 1px solid #EAD9C2;
  overflow: hidden;
}

.panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #EAD9C2;
}

.panel-title {
  font-size: 16px;
  font-weight: 700;
  color: #3B1A0C;
  margin: 0;
}

.panel-action {
  font-size: 13px;
  font-weight: 600;
  color: #B07535;
  text-decoration: none;
  transition: color 0.15s;
}

.panel-action:hover {
  color: #8a5a20;
}

/* ─── Table ─── */
.table-wrap {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13.5px;
}

th {
  text-align: left;
  padding: 10px 16px;
  font-size: 11px;
  font-weight: 700;
  color: #7A5040;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background: #F6EEE0;
  border-bottom: 1px solid #EAD9C2;
  white-space: nowrap;
}

td {
  padding: 13px 16px;
  color: #3B1A0C;
  border-bottom: 1px solid #EAD9C2;
  vertical-align: middle;
}

tr:last-child td {
  border-bottom: none;
}

.order-row {
  cursor: pointer;
  transition: background 0.12s;
}

.order-row:hover td {
  background: rgba(246,238,224,.5);
}

.mono {
  font-family: 'Fredoka', sans-serif;
  font-size: 14px;
  color: #3B1A0C;
  font-weight: 600;
}

.dt-chip {
  display: inline-block;
  background: #EAD9C2;
  color: #7A5040;
  border-radius: 6px;
  padding: 2px 9px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.date-cell {
  font-size: 12.5px;
  color: #7A5040;
  white-space: nowrap;
}

/* ─── Status Badges ─── */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.status-pending {
  background: #fef9c3;
  color: #854d0e;
}

.status-preparing {
  background: #dbeafe;
  color: #1d4ed8;
}

.status-out_for_delivery {
  background: #ede9fe;
  color: #5b21b6;
}

.status-completed {
  background: #dcfce7;
  color: #15803d;
}

.status-canceled {
  background: #fee2e2;
  color: #b91c1c;
}

/* ─── Empty State ─── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 56px 24px;
  color: #7A5040;
  font-size: 14px;
}

.empty-state p {
  margin: 0;
}

/* ─── Responsive ─── */
@media (max-width: 1100px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .mini-cards-row {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .admin-main {
    margin-left: 0;
    padding: 24px 16px;
  }
  .sidebar {
    display: none;
  }
  .stats-grid {
    grid-template-columns: 1fr 1fr;
  }
  .mini-cards-row {
    grid-template-columns: 1fr;
  }
}
</style>
