<script setup lang="ts">
const { api } = useApi()
const { t } = useI18n()

const email = ref('')
const name = ref('')
const status = ref<'idle' | 'loading' | 'success' | 'error'>('idle')
const errorMessage = ref('')

async function subscribe() {
  status.value = 'loading'
  errorMessage.value = ''

  try {
    await api<{ message: string }>('/newsletter/subscribe', {
      method: 'POST',
      body: JSON.stringify({ email: email.value, name: name.value || undefined }),
    })

    status.value = 'success'
    email.value = ''
    name.value = ''
  } catch (error: unknown) {
    status.value = 'error'

    if (error && typeof error === 'object' && 'data' in error) {
      const fetchError = error as { data?: { message?: string; errors?: Record<string, string[]> } }

      if (fetchError.data?.errors?.email) {
        errorMessage.value = fetchError.data.errors.email[0]
      } else {
        errorMessage.value = fetchError.data?.message ?? t('form.error_default')
      }
    } else {
      errorMessage.value = t('form.error_default')
    }
  }
}
</script>

<template>
  <form @submit.prevent="subscribe" class="form">
    <div v-if="status === 'success'" class="notice notice--success">
      {{ $t('form.success') }}
    </div>

    <div v-if="status === 'error'" class="notice notice--error">
      {{ errorMessage }}
    </div>

    <template v-if="status !== 'success'">
      <div class="field">
        <label for="name" class="label">{{ $t('form.name_label') }}</label>
        <input
          id="name"
          v-model="name"
          type="text"
          :placeholder="$t('form.name_placeholder')"
          class="input"
        />
      </div>

      <div class="field">
        <label for="email" class="label">{{ $t('form.email_label') }}</label>
        <input
          id="email"
          v-model="email"
          type="email"
          required
          :placeholder="$t('form.email_placeholder')"
          class="input"
        />
      </div>

      <button
        type="submit"
        :disabled="status === 'loading'"
        class="submit-btn"
      >
        {{ status === 'loading' ? $t('form.submitting') : $t('form.submit') }}
      </button>
    </template>
  </form>
</template>

<style scoped>
.form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.notice {
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 0.875rem;
  line-height: 1.5;
}

.notice--success {
  background: oklch(72% 0.22 135 / 0.12);
  color: oklch(32% 0.14 135);
  border: 1px solid oklch(72% 0.22 135 / 0.3);
}

@media (prefers-color-scheme: dark) {
  .notice--success {
    background: oklch(72% 0.22 135 / 0.15);
    color: oklch(82% 0.18 135);
    border-color: oklch(72% 0.22 135 / 0.35);
  }
}

.notice--error {
  background: oklch(62% 0.22 25 / 0.10);
  color: oklch(35% 0.16 25);
  border: 1px solid oklch(62% 0.22 25 / 0.28);
}

@media (prefers-color-scheme: dark) {
  .notice--error {
    background: oklch(62% 0.22 25 / 0.15);
    color: oklch(78% 0.18 25);
    border-color: oklch(62% 0.22 25 / 0.35);
  }
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.label {
  font-family: var(--font-body);
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-muted);
  letter-spacing: 0.01em;
}

.input {
  width: 100%;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 10px 14px;
  font-family: var(--font-body);
  font-size: 0.9rem;
  color: var(--text);
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.input::placeholder {
  color: var(--text-muted);
  opacity: 0.6;
}

.input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px oklch(72% 0.22 135 / 0.18);
}

.submit-btn {
  width: 100%;
  background: var(--accent);
  color: var(--accent-text);
  border: none;
  border-radius: 10px;
  padding: 11px 16px;
  font-family: var(--font-display);
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.01em;
  cursor: pointer;
  transition: opacity 0.15s ease, transform 0.1s ease;
}

.submit-btn:hover:not(:disabled) {
  opacity: 0.88;
  transform: translateY(-1px);
}

.submit-btn:active:not(:disabled) {
  transform: translateY(0);
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.submit-btn:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 3px;
}
</style>
