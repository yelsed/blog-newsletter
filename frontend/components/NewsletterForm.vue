<script setup lang="ts">
const { api } = useApi()

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
        errorMessage.value = fetchError.data?.message ?? 'Something went wrong. Please try again.'
      }
    } else {
      errorMessage.value = 'Something went wrong. Please try again.'
    }
  }
}
</script>

<template>
  <form @submit.prevent="subscribe" class="space-y-4">
    <div v-if="status === 'success'" class="rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 text-sm">
      Thanks for subscribing! Check your email to verify your subscription.
    </div>

    <div v-if="status === 'error'" class="rounded-lg bg-red-50 border border-red-200 p-4 text-red-800 text-sm">
      {{ errorMessage }}
    </div>

    <template v-if="status !== 'success'">
      <div>
        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Name (optional)</label>
        <input
          id="name"
          v-model="name"
          type="text"
          placeholder="Your name"
          class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 focus:outline-none"
        />
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input
          id="email"
          v-model="email"
          type="email"
          required
          placeholder="you@example.com"
          class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 focus:outline-none"
        />
      </div>

      <button
        type="submit"
        :disabled="status === 'loading'"
        class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ status === 'loading' ? 'Subscribing...' : 'Subscribe' }}
      </button>
    </template>
  </form>
</template>
