<script setup>
import { ref, computed, watch } from 'vue'
import { router, Link, useForm, usePage } from '@inertiajs/vue3'

// ---------------------------------------------------------------------------
// Props
// ---------------------------------------------------------------------------
const props = defineProps({
  products: Array,     // [{id, name, flavor, category, price, description, image_path, is_available, is_new, stock, min_stock}]
  lowStockCount: Number,
})

// ---------------------------------------------------------------------------
// Add / Edit Modal state
// ---------------------------------------------------------------------------
const showProductModal = ref(false)
const editingProduct   = ref(null)   // null → create mode, object → edit mode
const imagePreviewUrl  = ref(null)

const form = useForm({
  name:          '',
  flavor:        '',
  category:      '',
  price:         '',
  description:   '',
  stock:         '',
  min_stock:     '',
  image:         null,
  is_available:  true,
  is_new:        false,
})

function openAddModal() {
  editingProduct.value  = null
  imagePreviewUrl.value = null
  form.reset()
  form.is_available = true
  form.is_new       = false
  showProductModal.value = true
}

function openEditModal(product) {
  editingProduct.value = product
  form.name          = product.name          ?? ''
  form.flavor        = product.flavor        ?? ''
  form.category      = product.category      ?? ''
  form.price         = product.price         ?? ''
  form.description   = product.description   ?? ''
  form.stock         = product.stock         ?? ''
  form.min_stock     = product.min_stock     ?? ''
  form.image         = null
  form.is_available  = !!product.is_available
  form.is_new        = !!product.is_new
  imagePreviewUrl.value = product.image_path ? `/storage/${product.image_path}` : null
  showProductModal.value = true
}

function closeProductModal() {
  showProductModal.value = false
  editingProduct.value   = null
  imagePreviewUrl.value  = null
  form.reset()
  form.clearErrors()
}

function onImageChange(event) {
  const file = event.target.files[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => {
    form.image = e.target.result
    imagePreviewUrl.value = e.target.result
  }
  reader.readAsDataURL(file)
}

function submitProduct() {
  if (editingProduct.value) {
    // PATCH via POST with method spoofing
    form.post(route('admin.products.update', editingProduct.value.id), {
      _method: 'PATCH',
      forceFormData: true,
      onSuccess: () => closeProductModal(),
    })
  } else {
    form.post(route('admin.products.store'), {
      forceFormData: true,
      onSuccess: () => closeProductModal(),
    })
  }
}

// ---------------------------------------------------------------------------
// Delete Modal state
// ---------------------------------------------------------------------------
const showDeleteModal  = ref(false)
const deletingProduct  = ref(null)
const deleteProcessing = ref(false)

function openDeleteModal(product) {
  deletingProduct.value = product
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  deletingProduct.value = null
}

function confirmDelete() {
  if (!deletingProduct.value) return
  deleteProcessing.value = true
  router.delete(route('admin.products.destroy', deletingProduct.value.id), {
    onFinish: () => {
      deleteProcessing.value = false
      closeDeleteModal()
    },
  })
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------
function formatPrice(value) {
  const num = parseFloat(value)
  if (isNaN(num)) return 'R$ 0,00'
  return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

const logoutForm = useForm({})
function logout() {
  logoutForm.post(route('admin.logout'))
}
</script>

<template>
  <!-- ===== LAYOUT WRAPPER ===================================================== -->
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

    <!-- ── MAIN CONTENT ────────────────────────────────────────────────────── -->
    <main class="admin-main">

      <!-- Page header -->
      <div class="page-header">
        <div class="page-header-left">
          <h1 class="page-title">Produtos</h1>
          <p class="page-subtitle">
            {{ products.length }} produto{{ products.length !== 1 ? 's' : '' }} cadastrado{{ products.length !== 1 ? 's' : '' }}
          </p>
        </div>
        <button class="btn-primary" @click="openAddModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Novo produto
        </button>
      </div>

      <!-- Low stock banner -->
      <div v-if="lowStockCount > 0" class="low-stock-banner">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>
          <strong>{{ lowStockCount }}</strong> produto{{ lowStockCount !== 1 ? 's' : '' }} com estoque baixo
        </span>
      </div>

      <!-- Product grid -->
      <div v-if="products.length > 0" class="product-grid">
        <div v-for="product in products" :key="product.id" class="product-card">

          <!-- Image -->
          <div class="product-image-wrapper">
            <img
              v-if="product.image_path"
              :src="`/storage/${product.image_path}`"
              :alt="product.name"
              class="product-image"
            />
            <div v-else class="product-image-placeholder">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              <span>Sem imagem</span>
            </div>

            <!-- Unavailable overlay -->
            <div v-if="!product.is_available" class="badge-overlay badge-unavailable">
              Indisponível
            </div>

            <!-- New overlay -->
            <div v-if="product.is_new" class="badge-overlay badge-new">
              Novidade
            </div>
          </div>

          <!-- Card body -->
          <div class="product-body">
            <span class="product-category">{{ product.category }}</span>
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-flavor">{{ product.flavor }}</p>
            <p class="product-description">{{ product.description }}</p>

            <!-- Stock info -->
            <div class="product-stock" :class="{ 'stock-low': product.stock <= product.min_stock }">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
              </svg>
              Estoque: {{ product.stock }}
            </div>

            <!-- Footer row -->
            <div class="product-footer">
              <span class="product-price">{{ formatPrice(product.price) }}</span>
              <div class="product-actions">
                <button class="icon-btn icon-btn-edit" @click="openEditModal(product)" title="Editar produto">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button class="icon-btn icon-btn-delete" @click="openDeleteModal(product)" title="Excluir produto">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6"/><path d="M14 11v6"/>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
          <line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <p>Nenhum produto cadastrado ainda.</p>
        <button class="btn-primary" @click="openAddModal">Adicionar primeiro produto</button>
      </div>

    </main>
  </div>

  <!-- ===== ADD / EDIT MODAL =================================================== -->
  <Teleport to="body">
    <div v-if="showProductModal" class="modal-backdrop" @click.self="closeProductModal">
      <div class="modal modal-product">
        <div class="modal-header">
          <h2 class="modal-title">{{ editingProduct ? 'Editar produto' : 'Novo produto' }}</h2>
          <button class="modal-close" @click="closeProductModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitProduct" class="modal-body">

          <!-- Nome + Sabor -->
          <div class="form-grid-2">
            <div class="form-field">
              <label class="form-label">Nome <span class="required">*</span></label>
              <input v-model="form.name" type="text" class="form-input" :class="{ 'input-error': form.errors.name }" placeholder="Ex: Bolo de Cenoura" />
              <span v-if="form.errors.name" class="error-msg">{{ form.errors.name }}</span>
            </div>
            <div class="form-field">
              <label class="form-label">Sabor</label>
              <input v-model="form.flavor" type="text" class="form-input" :class="{ 'input-error': form.errors.flavor }" placeholder="Ex: Chocolate" />
              <span v-if="form.errors.flavor" class="error-msg">{{ form.errors.flavor }}</span>
            </div>
          </div>

          <!-- Categoria + Preço -->
          <div class="form-grid-2">
            <div class="form-field">
              <label class="form-label">Categoria <span class="required">*</span></label>
              <input v-model="form.category" type="text" class="form-input" :class="{ 'input-error': form.errors.category }" placeholder="Ex: Bolos" />
              <span v-if="form.errors.category" class="error-msg">{{ form.errors.category }}</span>
            </div>
            <div class="form-field">
              <label class="form-label">Preço (R$) <span class="required">*</span></label>
              <input v-model="form.price" type="number" step="0.01" min="0" class="form-input" :class="{ 'input-error': form.errors.price }" placeholder="0,00" />
              <span v-if="form.errors.price" class="error-msg">{{ form.errors.price }}</span>
            </div>
          </div>

          <!-- Descrição -->
          <div class="form-field">
            <label class="form-label">Descrição</label>
            <textarea v-model="form.description" class="form-input form-textarea" :class="{ 'input-error': form.errors.description }" placeholder="Descreva o produto..." rows="3"></textarea>
            <span v-if="form.errors.description" class="error-msg">{{ form.errors.description }}</span>
          </div>

          <!-- Estoque + Estoque mínimo -->
          <div class="form-grid-2">
            <div class="form-field">
              <label class="form-label">Estoque</label>
              <input v-model="form.stock" type="number" min="0" class="form-input" :class="{ 'input-error': form.errors.stock }" placeholder="0" />
              <span v-if="form.errors.stock" class="error-msg">{{ form.errors.stock }}</span>
            </div>
            <div class="form-field">
              <label class="form-label">Estoque mínimo</label>
              <input v-model="form.min_stock" type="number" min="0" class="form-input" :class="{ 'input-error': form.errors.min_stock }" placeholder="0" />
              <span v-if="form.errors.min_stock" class="error-msg">{{ form.errors.min_stock }}</span>
            </div>
          </div>

          <!-- Imagem -->
          <div class="form-field">
            <label class="form-label">Imagem</label>
            <div class="image-upload-area">
              <div v-if="imagePreviewUrl" class="image-preview">
                <img :src="imagePreviewUrl" alt="Preview" class="preview-img" />
                <button type="button" class="remove-image-btn" @click="imagePreviewUrl = null; form.image = null" title="Remover imagem">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                  </svg>
                </button>
              </div>
              <label class="file-input-label" :class="{ 'has-preview': imagePreviewUrl }">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <span>{{ imagePreviewUrl ? 'Trocar imagem' : 'Escolher imagem' }}</span>
                <input type="file" accept="image/*" class="file-input-hidden" @change="onImageChange" />
              </label>
            </div>
            <span v-if="form.errors.image" class="error-msg">{{ form.errors.image }}</span>
          </div>

          <!-- Toggles -->
          <div class="form-toggles">
            <div class="toggle-row">
              <div class="toggle-info">
                <span class="toggle-label">Disponível para venda</span>
                <span class="toggle-hint">Produto visível e disponível na loja</span>
              </div>
              <label class="toggle">
                <input type="checkbox" v-model="form.is_available" />
                <span class="toggle-slider"></span>
              </label>
            </div>

            <div class="toggle-row">
              <div class="toggle-info">
                <span class="toggle-label">Marcar como novidade</span>
                <span class="toggle-hint">Exibe o selo "Novidade" no produto</span>
              </div>
              <label class="toggle">
                <input type="checkbox" v-model="form.is_new" />
                <span class="toggle-slider"></span>
              </label>
            </div>
          </div>

          <!-- Modal actions -->
          <div class="modal-actions">
            <button type="button" class="btn-secondary" @click="closeProductModal" :disabled="form.processing">
              Cancelar
            </button>
            <button type="submit" class="btn-primary" :disabled="form.processing">
              <span v-if="form.processing">Salvando...</span>
              <span v-else>Salvar</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>

  <!-- ===== CONFIRM DELETE MODAL =============================================== -->
  <Teleport to="body">
    <div v-if="showDeleteModal" class="modal-backdrop" @click.self="closeDeleteModal">
      <div class="modal modal-delete">
        <div class="modal-header">
          <div class="delete-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
              <path d="M10 11v6"/><path d="M14 11v6"/>
              <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
          </div>
          <h2 class="modal-title">Excluir produto?</h2>
        </div>

        <div class="modal-body">
          <p class="delete-warning">
            Tem certeza que deseja excluir
            <strong>{{ deletingProduct?.name }}</strong>?
            Essa ação não pode ser desfeita.
          </p>

          <div class="modal-actions">
            <button type="button" class="btn-secondary" @click="closeDeleteModal" :disabled="deleteProcessing">
              Cancelar
            </button>
            <button type="button" class="btn-danger" @click="confirmDelete" :disabled="deleteProcessing">
              <span v-if="deleteProcessing">Excluindo...</span>
              <span v-else>Excluir</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
/* ============================================================
   CSS VARIABLES
   ============================================================ */
:root {
  --choco:    #3B1A0C;
  --caramel:  #B07535;
  --cream:    #F6EEE0;
  --cream-2:  #EAD9C2;
  --white:    #FDFAF4;
  --text-dark:#3B1A0C;
  --text-mid: #7A5040;
  --text-soft:#7A5040;
  --red:      #DC2626;
  --red-light:#FEE2E2;
}

/* ============================================================
   LAYOUT
   ============================================================ */
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: var(--cream);
  font-family: 'DM Sans', sans-serif;
}

/* ── SIDEBAR ──────────────────────────────────────────────── */
.sidebar { width: 240px; position: fixed; top: 0; left: 0; bottom: 0; background: #3B1A0C; display: flex; flex-direction: column; z-index: 50; }
.sb-logo { padding: 28px 24px 20px; font-family: 'Fredoka', sans-serif; font-size: 20px; font-weight: 700; color: #F6EEE0; line-height: .9; border-bottom: 1px solid rgba(246,238,224,.08); }
.sb-logo-nu { color: #B07535; }
.sb-nav { flex: 1; display: flex; flex-direction: column; gap: 2px; padding: 16px 12px; }
.sb-link { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 10px; color: rgba(246,238,224,.55); font-size: 14px; font-weight: 400; text-decoration: none; transition: background .18s, color .18s; position: relative; }
.sb-link:hover { background: rgba(246,238,224,.07); color: rgba(246,238,224,.85); }
.sb-link.active { background: rgba(246,238,224,.12); color: #F6EEE0; font-weight: 500; }
.sb-footer { padding: 16px 12px; border-top: 1px solid rgba(246,238,224,.08); display: flex; flex-direction: column; gap: 2px; }
.sb-footer-link { display: flex; align-items: center; gap: 8px; padding: 9px 14px; border-radius: 10px; color: rgba(246,238,224,.5); font-size: 13px; text-decoration: none; background: none; border: none; cursor: pointer; transition: background .15s, color .15s; width: 100%; text-align: left; }
.sb-footer-link:hover { background: rgba(246,238,224,.07); color: #F6EEE0; }
.sb-logout:hover { background: rgba(239,68,68,.12); color: #f87171; }

/* ── MAIN CONTENT ─────────────────────────────────────────── */
.admin-main {
  margin-left: 240px;
  flex: 1;
  padding: 40px 48px;
  min-height: 100vh;
}

/* ── PAGE HEADER ──────────────────────────────────────────── */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
}

.page-title {
  font-family: 'Fredoka', sans-serif;
  font-size: 28px;
  color: var(--choco);
  margin: 0 0 4px;
}

.page-subtitle {
  font-size: 14px;
  color: var(--text-soft);
  margin: 0;
}

/* ── LOW STOCK BANNER ─────────────────────────────────────── */
.low-stock-banner {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #FFFBEB;
  border: 1px solid #F59E0B;
  border-radius: 10px;
  padding: 12px 16px;
  color: #92400E;
  font-size: 14px;
  margin-bottom: 24px;
}

.low-stock-banner svg {
  flex-shrink: 0;
  color: #D97706;
}

/* ── PRODUCT GRID ─────────────────────────────────────────── */
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 20px;
}

/* ── PRODUCT CARD ─────────────────────────────────────────── */
.product-card {
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  transition: box-shadow 0.2s, transform 0.2s;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  transform: translateY(-2px);
}

/* Image */
.product-image-wrapper {
  position: relative;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  background: var(--cream-2);
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.product-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: var(--text-soft);
  font-size: 12px;
}

/* Badges */
.badge-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.badge-unavailable {
  background: rgba(0, 0, 0, 0.55);
  color: #fff;
}

.badge-new {
  background: rgba(196, 122, 43, 0.8);
  color: #fff;
  align-items: flex-start;
  justify-content: flex-end;
  padding: 10px;
  inset: auto 0 auto 0;
  top: 0;
  height: auto;
}

/* Card body */
.product-body {
  padding: 14px;
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: 4px;
}

.product-category {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: var(--caramel);
}

.product-name {
  font-family: 'Fredoka', sans-serif;
  font-size: 18px;
  color: var(--choco);
  margin: 0;
  line-height: 1.2;
}

.product-flavor {
  font-family: 'Caveat', cursive;
  font-size: 15px;
  color: var(--text-mid);
  margin: 0;
}

.product-description {
  font-size: 13px;
  color: var(--text-soft);
  margin: 4px 0 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
}

.product-stock {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: var(--text-soft);
  margin-top: 6px;
}

.product-stock.stock-low {
  color: #D97706;
  font-weight: 600;
}

/* Footer */
.product-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
  padding-top: 10px;
}

.product-price {
  font-family: 'Fredoka', sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: var(--choco);
}

.product-actions {
  display: flex;
  gap: 6px;
}

.icon-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s, transform 0.1s;
}

.icon-btn:active { transform: scale(0.92); }

.icon-btn-edit {
  background: var(--cream-2);
  color: var(--choco);
}

.icon-btn-edit:hover {
  background: var(--choco);
  color: #fff;
}

.icon-btn-delete {
  background: var(--red-light);
  color: var(--red);
}

.icon-btn-delete:hover {
  background: var(--red);
  color: #fff;
}

/* ── EMPTY STATE ──────────────────────────────────────────── */
.empty-state {
  text-align: center;
  padding: 80px 24px;
  color: var(--text-soft);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.empty-state p {
  font-size: 16px;
  margin: 0;
}

/* ============================================================
   BUTTONS
   ============================================================ */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--choco);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s, transform 0.1s;
  white-space: nowrap;
}

.btn-primary:hover { background: #7A4E2D; }
.btn-primary:active { transform: scale(0.97); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  color: var(--text-mid);
  border: 1.5px solid var(--cream-2);
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: border-color 0.15s, background 0.15s;
}

.btn-secondary:hover { background: var(--cream-2); }
.btn-secondary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--red);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}

.btn-danger:hover { background: #B91C1C; }
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

/* ============================================================
   MODAL
   ============================================================ */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 16px;
  backdrop-filter: blur(2px);
}

.modal {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.18);
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-product { max-width: 520px; }
.modal-delete  { max-width: 380px; }

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid var(--cream-2);
  gap: 12px;
}

.modal-title {
  font-family: 'Fredoka', sans-serif;
  font-size: 20px;
  color: var(--choco);
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text-soft);
  padding: 4px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  transition: color 0.15s, background 0.15s;
  flex-shrink: 0;
}

.modal-close:hover {
  color: var(--text-dark);
  background: var(--cream-2);
}

.modal-body {
  padding: 20px 24px 24px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding-top: 6px;
}

/* ── DELETE MODAL SPECIFICS ────────────────────────────────── */
.delete-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--red-light);
  color: var(--red);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.delete-warning {
  font-size: 14px;
  color: var(--text-mid);
  line-height: 1.6;
  margin: 0;
}

/* ============================================================
   FORM ELEMENTS
   ============================================================ */
.form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-dark);
}

.required {
  color: var(--red);
  margin-left: 2px;
}

.form-input {
  border: 1.5px solid var(--cream-2);
  border-radius: 8px;
  padding: 9px 12px;
  font-size: 14px;
  color: var(--text-dark);
  background: #fff;
  transition: border-color 0.15s, box-shadow 0.15s;
  outline: none;
  font-family: inherit;
}

.form-input:focus {
  border-color: var(--choco);
  box-shadow: 0 0 0 3px rgba(92, 58, 30, 0.12);
}

.form-input.input-error {
  border-color: var(--red);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.error-msg {
  font-size: 12px;
  color: var(--red);
}

/* ── IMAGE UPLOAD ─────────────────────────────────────────── */
.image-upload-area {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.image-preview {
  position: relative;
  width: 72px;
  height: 72px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
  border: 1.5px solid var(--cream-2);
}

.preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.remove-image-btn {
  position: absolute;
  top: 3px;
  right: 3px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: rgba(0,0,0,0.55);
  color: #fff;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}

.file-input-label {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 14px;
  border: 1.5px dashed var(--cream-2);
  border-radius: 8px;
  color: var(--text-mid);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: border-color 0.15s, background 0.15s;
}

.file-input-label:hover {
  border-color: var(--choco);
  background: var(--cream);
  color: var(--choco);
}

.file-input-hidden {
  display: none;
}

/* ── TOGGLES ──────────────────────────────────────────────── */
.form-toggles {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.toggle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 10px 12px;
  background: var(--cream);
  border-radius: 10px;
}

.toggle-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.toggle-label {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-dark);
}

.toggle-hint {
  font-size: 12px;
  color: var(--text-soft);
}

/* Custom toggle */
.toggle {
  position: relative;
  width: 44px;
  height: 24px;
  display: inline-block;
  flex-shrink: 0;
}

.toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  inset: 0;
  background: var(--cream-2);
  border-radius: 999px;
  cursor: pointer;
  transition: background 0.2s;
}

.toggle-slider::before {
  content: '';
  position: absolute;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: white;
  top: 3px;
  left: 3px;
  transition: transform 0.2s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.toggle input:checked + .toggle-slider {
  background: var(--choco);
}

.toggle input:checked + .toggle-slider::before {
  transform: translateX(20px);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 900px) {
  .admin-main {
    margin-left: 0;
    padding: 24px 16px;
  }

  .sidebar {
    display: none;
  }

  .form-grid-2 {
    grid-template-columns: 1fr;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
