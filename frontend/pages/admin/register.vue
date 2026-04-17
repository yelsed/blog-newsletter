<script setup lang="ts">
definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const route = useRoute()
const { enrollPasskey } = useAuth()

const token = computed(() => String(route.query.token ?? ''))
const status = ref<'idle' | 'loading' | 'success' | 'error'>(token.value ? 'idle' : 'error')
const errorMessage = ref(token.value ? '' : 'Missing enrollment token.')

async function handleEnroll() {
  if (!token.value) return
  status.value = 'loading'
  errorMessage.value = ''
  try {
    await enrollPasskey(token.value)
    status.value = 'success'
  }
  catch (e) {
    status.value = 'error'
    errorMessage.value = e instanceof Error ? e.message : 'Could not enroll passkey.'
  }
}
</script>

<template>
  <div class="mx-auto max-w-md py-10">
    <h1 class="mb-2 text-2xl font-semibold text-slate-900">
      Enroll a passkey
    </h1>
    <p class="mb-6 text-sm text-slate-600">
      Use your device's biometrics, Face ID, Touch ID, or hardware key to set up password-less sign-in.
    </p>

    <div v-if="status === 'success'" class="flex flex-col gap-3">
      <p class="rounded-md bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
        Passkey registered. You can now sign in with it.
      </p>
      <NuxtLink to="/admin" class="text-sm text-slate-700 underline">
        Go to admin →
      </NuxtLink>
    </div>

    <div v-else class="flex flex-col gap-3">
      <button
        type="button"
        :disabled="status === 'loading' || !token"
        class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400"
        @click="handleEnroll"
      >
        {{ status === 'loading' ? 'Enrolling…' : 'Start passkey enrollment' }}
      </button>

      <p v-if="errorMessage" class="text-sm text-red-600">
        {{ errorMessage }}
      </p>
    </div>
  </div>
</template>
