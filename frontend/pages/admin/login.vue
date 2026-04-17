<script setup lang="ts">
definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const { login } = useAuth()
const email = ref('')
const status = ref<'idle' | 'loading' | 'error'>('idle')
const errorMessage = ref('')

async function handleSignIn() {
  status.value = 'loading'
  errorMessage.value = ''
  try {
    await login(email.value || undefined)
    await navigateTo('/admin')
  }
  catch (e) {
    status.value = 'error'
    errorMessage.value = e instanceof Error ? e.message : 'Could not sign in.'
  }
}
</script>

<template>
  <div class="mx-auto max-w-sm py-10">
    <h1 class="mb-6 text-2xl font-semibold text-slate-900">
      Sign in
    </h1>

    <form class="flex flex-col gap-4" @submit.prevent="handleSignIn">
      <label class="flex flex-col gap-1 text-sm">
        <span class="text-slate-700">Email (optional)</span>
        <input
          v-model="email"
          type="email"
          autocomplete="username"
          placeholder="admin@example.com"
          class="rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
        >
      </label>

      <button
        type="submit"
        :disabled="status === 'loading'"
        class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400"
      >
        <span v-if="status === 'loading'">Authenticating…</span>
        <span v-else>Sign in with passkey</span>
      </button>

      <p v-if="errorMessage" class="text-sm text-red-600">
        {{ errorMessage }}
      </p>
    </form>
  </div>
</template>
