<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'

const props = defineProps({
  orders:       Object,  // paginated {data, links, meta}
  counts:       Object,  // {all, pending, preparing, out_for_delivery, completed, canceled}
  statusLabels: Object,
  activeStatus: String,
  sortField:    { type: String, default: 'created_at' },
  sortDir:      { type: String, default: 'desc' },
})

// ── Helpers ──────────────────────────────────────────────────────────────────
const fmt = (v) =>
  Number(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

const fmtDate = (d) =>
  new Date(d).toLocaleString('pt-BR', {
    day: '2-digit', month: '2-digit', year: '2-digit',
    hour: '2-digit', minute: '2-digit',
  })

const deliveryLabels = { delivery: 'Entrega', pickup: 'Retirada' }

// ── Filter tabs ───────────────────────────────────────────────────────────────
const tabs = [
  { key: 'all',               label: 'Todos' },
  { key: 'pending',           label: 'Pendente' },
  { key: 'preparing',         label: 'Preparando' },
  { key: 'out_for_delivery',  label: 'Saiu p/ entrega' },
  { key: 'completed',         label: 'Concluído' },
  { key: 'canceled',          label: 'Cancelado' },
]

function setFilter(key) {
  const params = key === 'all' ? {} : { status: key }
  params.sort = props.sortField
  params.direction = props.sortDir
  router.get(route('admin.orders.index'), params, { preserveState: false })
}

function sort(field) {
  let dir = 'desc'
  if (props.sortField === field) {
    dir = props.sortDir === 'asc' ? 'desc' : 'asc'
  }
  const params = { sort: field, direction: dir }
  if (props.activeStatus !== 'all') params.status = props.activeStatus
  router.get(route('admin.orders.index'), params, { preserveState: false })
}

// ── Order detail modal ────────────────────────────────────────────────────────
const selectedOrder  = ref(null)
const statusUpdating = ref(false)

function openOrder(order) {
  selectedOrder.value = order
}
function closeOrder() {
  selectedOrder.value = null
}

const logoutForm = useForm({})
function logout() {
  logoutForm.post(route('admin.logout'))
}

// Status buttons to show in the modal (all except the current one if desired)
const changeableStatuses = [
  { key: 'preparing',        label: 'Preparando' },
  { key: 'out_for_delivery', label: 'Saiu p/ entrega' },
  { key: 'completed',        label: 'Concluído' },
  { key: 'canceled',         label: 'Cancelado' },
]

function updateStatus(status) {
  if (!selectedOrder.value || statusUpdating.value) return
  statusUpdating.value = true

  router.patch(
    route('admin.orders.updateStatus', selectedOrder.value.id),
    { status },
    {
      preserveScroll: true,
      onSuccess: () => {
        closeOrder()
        statusUpdating.value = false
      },
      onError: () => { statusUpdating.value = false },
    }
  )
}
</script>

<template>
  <div class="admin-layout">

    <!-- ── SIDEBAR ─────────────────────────────────────────────────────────── -->
    <aside class="sidebar">
      <div class="sb-logo">nunuca<span class="sb-logo-nu">.</span>nu</div>

      <nav class="sb-nav">
        <Link :href="route('admin.dashboard')"
              :class="['sb-link', { active: route().current('admin.dashboard') }]">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
          </svg>
          Dashboard
        </Link>

        <Link :href="route('admin.orders.index')"
              :class="['sb-link', { active: route().current('admin.orders.*') }]">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
            <rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
          </svg>
          Pedidos
          <span v-if="counts.pending > 0" class="sb-badge">{{ counts.pending }}</span>
        </Link>

        <Link :href="route('admin.products.index')"
              :class="['sb-link', { active: route().current('admin.products.*') }]">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
          </svg>
          Produtos
        </Link>
      </nav>

      <div class="sb-footer">
        <a :href="route('home')" target="_blank" class="sb-footer-link">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
          Ver loja
        </a>
        <button class="sb-footer-link sb-logout" @click="logout">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Sair
        </button>
      </div>
    </aside>

    <!-- ── MAIN ─────────────────────────────────────────────────────────────── -->
    <main class="admin-main">

      <!-- Header -->
      <header class="page-header">
        <div>
          <h1 class="page-title">Pedidos</h1>
          <p class="page-sub">
            {{ orders.meta?.total ?? orders.data?.length ?? 0 }}
            {{ (orders.meta?.total ?? 0) === 1 ? 'pedido' : 'pedidos' }}
          </p>
        </div>
      </header>

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          :class="['filter-tab', { active: activeStatus === tab.key }]"
          @click="setFilter(tab.key)"
        >
          {{ tab.label }}
          <span class="tab-count">{{ counts[tab.key] ?? 0 }}</span>
        </button>
      </div>

      <!-- Orders Table Panel -->
      <div class="panel">
        <div class="table-wrap">
          <table v-if="orders.data && orders.data.length > 0">
            <thead>
              <tr>
                <th class="sortable-th" @click="sort('id')">
                  Código
                  <span v-if="sortField === 'id'" class="sort-arrow">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th>Cliente</th>
                <th class="sortable-th" @click="sort('total_items')">
                  Itens
                  <span v-if="sortField === 'total_items'" class="sort-arrow">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th class="sortable-th" @click="sort('total')">
                  Total
                  <span v-if="sortField === 'total'" class="sort-arrow">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th>Tipo</th>
                <th>Status</th>
                <th class="sortable-th" @click="sort('created_at')">
                  Data
                  <span v-if="sortField === 'created_at'" class="sort-arrow">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="order in orders.data"
                :key="order.id"
                class="order-row"
                @click="openOrder(order)"
              >
                <td><span class="mono">{{ order.code }}</span></td>
                <td>{{ order.customer_name }}</td>
                <td>{{ order.total_items }} {{ order.total_items === 1 ? 'item' : 'itens' }}</td>
                <td><strong>{{ fmt(order.total) }}</strong></td>
                <td><span class="dt-chip">{{ deliveryLabels[order.delivery_type] ?? order.delivery_type }}</span></td>
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
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.3">
              <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
              <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
            <p>{{ activeStatus === 'all' ? 'Nenhum pedido ainda.' : 'Nenhum pedido com este status.' }}</p>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="orders.links && orders.links.length > 3" class="pagination">
        <template v-for="link in orders.links" :key="link.label">
          <button
            v-if="link.url"
            :class="['page-btn', { active: link.active }]"
            @click="router.get(link.url)"
            v-html="link.label"
          />
          <span v-else class="page-btn page-btn--disabled" v-html="link.label" />
        </template>
      </div>

    </main>

    <!-- ── ORDER DETAIL MODAL ─────────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="selectedOrder" class="modal-backdrop" @click.self="closeOrder">
          <div class="modal-box">

            <!-- Modal header -->
            <div class="modal-head">
              <div>
                <div class="modal-code">
                  {{ selectedOrder.code }}
                  <span :class="['status-badge', `status-${selectedOrder.status}`]" style="font-size:12px;margin-left:8px">
                    {{ statusLabels[selectedOrder.status] ?? selectedOrder.status }}
                  </span>
                </div>
                <div class="modal-date">{{ fmtDate(selectedOrder.created_at) }}</div>
              </div>
              <button class="modal-close" @click="closeOrder">✕</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

              <!-- Customer info -->
              <div class="detail-section">
                <div class="detail-label">Cliente</div>
                <div class="detail-box">
                  <div class="detail-row"><span>Nome</span><strong>{{ selectedOrder.customer_name }}</strong></div>
                  <div class="detail-row"><span>Telefone</span><strong>{{ selectedOrder.customer_phone }}</strong></div>
                  <div v-if="selectedOrder.customer_email" class="detail-row">
                    <span>E-mail</span><strong>{{ selectedOrder.customer_email }}</strong>
                  </div>
                  <div class="detail-row">
                    <span>Tipo de entrega</span>
                    <strong>{{ deliveryLabels[selectedOrder.delivery_type] ?? selectedOrder.delivery_type }}</strong>
                  </div>
                  <div v-if="selectedOrder.address" class="detail-row">
                    <span>Endereço</span><strong>{{ selectedOrder.address }}</strong>
                  </div>
                </div>
              </div>

              <!-- Items -->
              <div class="detail-section">
                <div class="detail-label">Itens do pedido</div>
                <div class="detail-box">
                  <div
                    v-for="(item, idx) in selectedOrder.items"
                    :key="idx"
                    class="detail-row"
                  >
                    <span>{{ item.product_name }} {{ item.product_flavor }} × {{ item.quantity }}</span>
                    <strong>{{ fmt(item.subtotal) }}</strong>
                  </div>
                  <hr class="detail-divider">
                  <div class="detail-total-row">
                    <span>Total</span>
                    <strong>{{ fmt(selectedOrder.total) }}</strong>
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div v-if="selectedOrder.notes" class="detail-section">
                <div class="detail-label">Observações</div>
                <div class="detail-box">
                  <div class="detail-row"><span>{{ selectedOrder.notes }}</span></div>
                </div>
              </div>

            </div>

            <!-- Status change -->
            <div class="status-section">
              <div class="status-section-label">Alterar status</div>
              <div class="status-grid">
                <button
                  v-for="s in changeableStatuses"
                  :key="s.key"
                  :class="['status-btn', `status-btn--${s.key}`, { current: selectedOrder.status === s.key }]"
                  :disabled="statusUpdating"
                  @click="updateStatus(s.key)"
                >
                  {{ s.label }}
                </button>
              </div>
            </div>

          </div>
        </div>
      </Transition>
    </Teleport>

  </div>
</template>

<style scoped>
/* ── Layout ─────────────────────────────────────────────────────────────────── */
.admin-layout { display: flex; min-height: 100vh; background: #F6EEE0; font-family: 'DM Sans', sans-serif; }

/* ── Sidebar ────────────────────────────────────────────────────────────────── */
.sidebar { width: 240px; position: fixed; top: 0; left: 0; bottom: 0; background: #3B1A0C; display: flex; flex-direction: column; z-index: 50; }
.sb-logo { padding: 28px 24px 20px; font-family: 'Fredoka', sans-serif; font-size: 20px; font-weight: 700; color: #F6EEE0; line-height: .9; border-bottom: 1px solid rgba(246,238,224,.08); }
.sb-logo-nu { color: #B07535; }
.sb-nav { flex: 1; display: flex; flex-direction: column; gap: 2px; padding: 16px 12px; }
.sb-link { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 10px; color: rgba(246,238,224,.55); font-size: 14px; font-weight: 400; text-decoration: none; transition: background .18s, color .18s; position: relative; }
.sb-link:hover { background: rgba(246,238,224,.07); color: rgba(246,238,224,.85); }
.sb-link.active { background: rgba(246,238,224,.12); color: #F6EEE0; font-weight: 500; }
.sb-badge { margin-left: auto; background: #B07535; color: #fff; font-size: 11px; font-weight: 700; border-radius: 999px; padding: 1px 7px; min-width: 20px; text-align: center; }
.sb-footer { padding: 16px 12px; border-top: 1px solid rgba(246,238,224,.08); display: flex; flex-direction: column; gap: 2px; }
.sb-footer-link { display: flex; align-items: center; gap: 8px; padding: 9px 14px; border-radius: 10px; color: rgba(246,238,224,.5); font-size: 13px; text-decoration: none; background: none; border: none; cursor: pointer; transition: background .15s, color .15s; width: 100%; text-align: left; }
.sb-footer-link:hover { background: rgba(246,238,224,.07); color: #F6EEE0; }
.sb-logout:hover { background: rgba(239,68,68,.12); color: #f87171; }

/* ── Main ───────────────────────────────────────────────────────────────────── */
.admin-main { margin-left: 240px; flex: 1; padding: 40px 48px; }
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
.page-title { font-family: 'Fredoka', sans-serif; font-size: 28px; font-weight: 600; color: #3B1A0C; }
.page-sub { font-size: 14px; color: #7A5040; font-weight: 300; margin-top: 2px; }

/* ── Filter tabs ────────────────────────────────────────────────────────────── */
.filter-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 24px; }
.filter-tab { padding: 8px 18px; border-radius: 999px; border: 1.5px solid #EAD9C2; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 400; color: #7A5040; cursor: pointer; transition: all .18s; display: flex; align-items: center; gap: 6px; }
.filter-tab:hover { border-color: #3B1A0C; color: #3B1A0C; }
.filter-tab.active { background: #3B1A0C; color: #F6EEE0; border-color: #3B1A0C; font-weight: 500; }
.tab-count { border-radius: 999px; padding: 1px 7px; font-size: 11px; min-width: 20px; text-align: center; background: #EAD9C2; color: #7A5040; }
.filter-tab.active .tab-count { background: rgba(255,255,255,.22); color: #F6EEE0; }

/* ── Panel ──────────────────────────────────────────────────────────────────── */
.panel { background: #FDFAF4; border-radius: 16px; border: 1px solid #EAD9C2; overflow: hidden; }
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { font-size: 11px; text-transform: uppercase; letter-spacing: .12em; color: #7A5040; font-weight: 400; text-align: left; padding: 12px 16px; background: #F6EEE0; border-bottom: 1px solid #EAD9C2; white-space: nowrap; }
td { padding: 13px 16px; font-size: 13.5px; font-weight: 300; color: #3B1A0C; border-bottom: 1px solid #EAD9C2; vertical-align: middle; }
tr:last-child td { border-bottom: none; }
.order-row { cursor: pointer; transition: background .15s; }
.order-row:hover td { background: rgba(246,238,224,.5); }
.mono { font-family: 'Fredoka', sans-serif; font-size: 14px; font-weight: 600; letter-spacing: .03em; }
.dt-chip { display: inline-block; padding: 3px 9px; border-radius: 6px; font-size: 12px; background: #EAD9C2; color: #7A5040; white-space: nowrap; }
.date-cell { font-size: 13px; color: #7A5040; white-space: nowrap; }
.empty-state { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 64px 24px; color: #7A5040; font-size: 14px; font-weight: 300; text-align: center; }
.empty-state p { margin: 0; }

/* ── Status badges ──────────────────────────────────────────────────────────── */
.status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 500; white-space: nowrap; }
.status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
.status-pending          { background: #FEF3C7; color: #92600A; }
.status-preparing        { background: #DBEAFE; color: #1D4ED8; }
.status-out_for_delivery { background: #EDE9FE; color: #7C3AED; }
.status-completed        { background: #DCFCE7; color: #166534; }
.status-canceled         { background: #FEE2E2; color: #991B1B; }

/* ── Pagination ─────────────────────────────────────────────────────────────── */
.pagination { display: flex; gap: 6px; justify-content: center; padding: 24px 0 8px; flex-wrap: wrap; }
.page-btn { padding: 7px 14px; border-radius: 8px; border: 1.5px solid #EAD9C2; background: #FDFAF4; color: #7A5040; font-size: 13px; cursor: pointer; transition: all .18s; }
.page-btn:hover:not(:disabled) { border-color: #3B1A0C; color: #3B1A0C; }
.page-btn.active { background: #3B1A0C; color: #F6EEE0; border-color: #3B1A0C; }
.page-btn--disabled { opacity: .4; cursor: default; }

/* ── Sortable headers ──────────────────────────────────────────────── */
.sortable-th { cursor: pointer; user-select: none; transition: color .15s; }
.sortable-th:hover { color: #3B1A0C; }
.sort-arrow { margin-left: 4px; font-size: 10px; }

/* ── Modal ──────────────────────────────────────────────────────────────────── */
.modal-backdrop { position: fixed; inset: 0; background: rgba(59,26,12,.48); z-index: 400; display: flex; align-items: center; justify-content: center; padding: 24px; backdrop-filter: blur(4px); }
.modal-box { background: #FDFAF4; border-radius: 20px; width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(59,26,12,.22); }
.modal-head { padding: 24px 28px 0; display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.modal-code { font-family: 'Fredoka', sans-serif; font-size: 22px; font-weight: 600; color: #3B1A0C; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.modal-date { font-size: 13px; color: #7A5040; font-weight: 300; margin-top: 4px; }
.modal-close { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #EAD9C2; background: transparent; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; color: #3B1A0C; transition: background .2s; flex-shrink: 0; font-family: 'DM Sans', sans-serif; }
.modal-close:hover { background: #EAD9C2; }
.modal-body { padding: 20px 28px 0; }
.detail-section { margin-bottom: 20px; }
.detail-label { font-size: 10.5px; text-transform: uppercase; letter-spacing: .14em; color: #7A5040; font-weight: 400; margin-bottom: 8px; }
.detail-box { background: #F6EEE0; border-radius: 10px; padding: 12px 16px; }
.detail-row { display: flex; justify-content: space-between; align-items: baseline; font-size: 13.5px; color: #3B1A0C; font-weight: 300; margin-bottom: 5px; gap: 12px; }
.detail-row:last-child { margin-bottom: 0; }
.detail-row span:first-child { color: #7A5040; white-space: nowrap; }
.detail-row strong { font-weight: 500; text-align: right; }
.detail-divider { border: none; border-top: 1px solid #EAD9C2; margin: 8px 0; }
.detail-total-row { display: flex; justify-content: space-between; align-items: baseline; padding-top: 4px; }
.detail-total-row span { font-size: 13px; color: #7A5040; }
.detail-total-row strong { font-family: 'Fredoka', sans-serif; font-size: 22px; font-weight: 600; color: #3B1A0C; }

/* ── Status change ──────────────────────────────────────────────────────────── */
.status-section { padding: 0 28px 28px; }
.status-section-label { font-size: 10.5px; text-transform: uppercase; letter-spacing: .14em; color: #7A5040; font-weight: 400; margin-bottom: 10px; margin-top: 20px; }
.status-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
.status-btn { padding: 9px 8px; border-radius: 10px; border: 1.5px solid #EAD9C2; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 12.5px; font-weight: 400; cursor: pointer; color: #7A5040; transition: all .18s; text-align: center; }
.status-btn:hover:not(:disabled) { border-color: #3B1A0C; color: #3B1A0C; background: #F6EEE0; }
.status-btn:disabled { opacity: .5; cursor: not-allowed; }
.status-btn--preparing.current         { background: #DBEAFE; color: #1D4ED8; border-color: #BFDBFE; }
.status-btn--out_for_delivery.current  { background: #EDE9FE; color: #7C3AED; border-color: #DDD6FE; }
.status-btn--completed.current         { background: #DCFCE7; color: #166534; border-color: #BBF7D0; }
.status-btn--canceled.current          { background: #FEE2E2; color: #991B1B; border-color: #FECACA; }

/* ── Modal transition ───────────────────────────────────────────────────────── */
.modal-enter-active, .modal-leave-active { transition: opacity .25s ease; }
.modal-enter-active .modal-box, .modal-leave-active .modal-box { transition: transform .28s cubic-bezier(.4,0,.2,1); }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from .modal-box { transform: translateY(20px) scale(.97); }

@media (max-width: 900px) {
  .admin-main { margin-left: 0; padding: 24px 16px; }
  .sidebar { display: none; }
  .status-grid { grid-template-columns: 1fr 1fr; }
}
</style>
