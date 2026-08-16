<template>
  <div class="page-wrapper">
    <div class="card">
      <!-- Logo -->
      <div class="logo">
        <span class="logo-brand">nunuca</span><span class="logo-tld">.nu</span>
      </div>

      <!-- Subtitle -->
      <p class="restricted-label">Área restrita</p>

      <!-- Title -->
      <h1 class="card-title">Entrar</h1>

      <!-- Form -->
      <form @submit.prevent="submit" novalidate>
        <!-- Email -->
        <div class="field-group">
          <label class="field-label" for="email">E-mail</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="field-input"
            :class="{ 'field-input--error': form.errors.email }"
            placeholder="seu@email.com"
            autocomplete="email"
            autofocus
          />
          <span v-if="form.errors.email" class="field-error">
            {{ form.errors.email }}
          </span>
        </div>

        <!-- Password -->
        <div class="field-group">
          <label class="field-label" for="password">Senha</label>
          <div class="input-wrapper">
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="field-input field-input--password"
              :class="{ 'field-input--error': form.errors.password }"
              placeholder="••••••••"
              autocomplete="current-password"
            />
            <button
              type="button"
              class="eye-toggle"
              @click="showPassword = !showPassword"
              :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
            >
              <!-- Eye Open -->
              <svg
                v-if="!showPassword"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="eye-icon"
              >
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
              <!-- Eye Off -->
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="eye-icon"
              >
                <path
                  d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"
                />
                <path
                  d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"
                />
                <line x1="1" y1="1" x2="23" y2="23" />
              </svg>
            </button>
          </div>
          <span v-if="form.errors.password" class="field-error">
            {{ form.errors.password }}
          </span>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          class="submit-btn"
          :disabled="form.processing"
        >
          <span v-if="form.processing" class="btn-spinner" aria-hidden="true"></span>
          {{ form.processing ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const showPassword = ref(false)

const form = useForm({
  email: '',
  password: '',
})

const submit = () => form.post(route('admin.login.attempt'))
</script>

<style scoped>
/* ── CSS Variables ───────────────────────────────────────── */
:root {
  --cream: #F6EEE0;
  --choco: #3B1A0C;
  --caramel: #B07535;
  --caramel-light: #C89050;
  --caramel-muted: #C4A06A;
  --error: #D94F4F;
  --input-border: #E2D5C3;
  --input-focus: #B07535;
  --text-muted: #9B7D5A;
  --shadow: 0 8px 40px rgba(59, 26, 12, 0.12), 0 2px 8px rgba(59, 26, 12, 0.06);
}

/* ── Page Layout ─────────────────────────────────────────── */
.page-wrapper {
  min-height: 100vh;
  background-color: #F6EEE0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 16px;
  font-family: 'DM Sans', sans-serif;
}

/* ── Card ────────────────────────────────────────────────── */
.card {
  background: #ffffff;
  border-radius: 20px;
  width: 100%;
  max-width: 420px;
  padding: 44px 40px 40px;
  box-shadow: var(--shadow);
}

/* ── Logo ────────────────────────────────────────────────── */
.logo {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 0;
  margin-bottom: 6px;
  font-family: 'Fredoka', sans-serif;
  font-size: 2rem;
  font-weight: 600;
  letter-spacing: -0.5px;
}

.logo-brand {
  color: #3B1A0C;
}

.logo-tld {
  color: #B07535;
}

/* ── Restricted Label ────────────────────────────────────── */
.restricted-label {
  text-align: center;
  font-size: 0.775rem;
  font-weight: 500;
  color: #C4A06A;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin: 0 0 24px;
}

/* ── Card Title ──────────────────────────────────────────── */
.card-title {
  font-family: 'Fredoka', sans-serif;
  font-size: 1.75rem;
  font-weight: 600;
  color: #3B1A0C;
  text-align: center;
  margin: 0 0 28px;
  letter-spacing: -0.3px;
}

/* ── Field Group ─────────────────────────────────────────── */
.field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 20px;
}

.field-label {
  font-size: 0.825rem;
  font-weight: 600;
  color: #3B1A0C;
  letter-spacing: 0.01em;
}

/* ── Input Wrapper (for password + eye toggle) ───────────── */
.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

/* ── Text Input ──────────────────────────────────────────── */
.field-input {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid #E2D5C3;
  border-radius: 10px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem;
  color: #3B1A0C;
  background: #FDFAF6;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
  box-sizing: border-box;
}

.field-input::placeholder {
  color: #C4B09A;
  font-weight: 400;
}

.field-input:focus {
  border-color: #B07535;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(176, 117, 53, 0.12);
}

.field-input--password {
  padding-right: 44px;
}

.field-input--error {
  border-color: #D94F4F;
  background: #FFF8F8;
}

.field-input--error:focus {
  border-color: #D94F4F;
  box-shadow: 0 0 0 3px rgba(217, 79, 79, 0.1);
}

/* ── Eye Toggle ──────────────────────────────────────────── */
.eye-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
  color: #9B7D5A;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: color 0.2s ease;
  line-height: 0;
}

.eye-toggle:hover {
  color: #3B1A0C;
}

.eye-icon {
  width: 18px;
  height: 18px;
}

/* ── Field Error ─────────────────────────────────────────── */
.field-error {
  font-size: 0.78rem;
  color: #D94F4F;
  font-weight: 500;
  padding-left: 2px;
  line-height: 1.4;
}

/* ── Submit Button ───────────────────────────────────────── */
.submit-btn {
  width: 100%;
  padding: 13px;
  margin-top: 8px;
  background: #3B1A0C;
  color: #F6EEE0;
  border: none;
  border-radius: 10px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
  box-shadow: 0 2px 12px rgba(59, 26, 12, 0.18);
}

.submit-btn:hover:not(:disabled) {
  background: #5a2a14;
  box-shadow: 0 4px 16px rgba(59, 26, 12, 0.28);
  transform: translateY(-1px);
}

.submit-btn:active:not(:disabled) {
  background: #3B1A0C;
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(59, 26, 12, 0.18);
}

.submit-btn:disabled {
  background: #7a5545;
  cursor: not-allowed;
  opacity: 0.75;
  box-shadow: none;
  transform: none;
}

/* ── Button Spinner ──────────────────────────────────────── */
.btn-spinner {
  width: 16px;
  height: 16px;
  border: 2.5px solid rgba(246, 238, 224, 0.35);
  border-top-color: #F6EEE0;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  flex-shrink: 0;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 480px) {
  .card {
    padding: 36px 24px 32px;
  }

  .logo {
    font-size: 1.75rem;
  }

  .card-title {
    font-size: 1.5rem;
  }
}
</style>
