<script setup lang="ts">
const { api } = useApi()
const { t } = useI18n()
const route = useRoute()

const status = ref<'loading' | 'success' | 'error'>('loading')
const message = ref('')

onMounted(async () => {
  const token = route.query.token as string | undefined

  if (!token) {
    status.value = 'error'
    message.value = t('verify.no_token')
    return
  }

  try {
    const response = await api<{ message: string }>(`/newsletter/verify/${token}`)
    status.value = 'success'
    message.value = response.message
  } catch {
    status.value = 'error'
    message.value = t('verify.invalid_link')
  }
})
</script>

<template>
  <div class="mx-auto max-w-3xl px-6 py-16 text-center">
    <div v-if="status === 'loading'" class="text-slate-500">
      {{ $t('verify.loading') }}
    </div>

    <div v-if="status === 'success'">
      <h1 class="text-3xl font-bold tracking-tight mb-4">{{ $t('verify.success_title') }}</h1>
      <p class="text-lg text-slate-600 mb-8">{{ message }}</p>
      <NuxtLink to="/" class="text-sm font-medium text-slate-900 underline underline-offset-4 hover:text-slate-700">
        {{ $t('common.back_home') }}
      </NuxtLink>
    </div>

    <div v-if="status === 'error'">
      <h1 class="text-3xl font-bold tracking-tight mb-4">{{ $t('verify.error_title') }}</h1>
      <p class="text-lg text-slate-600 mb-8">{{ message }}</p>
      <NuxtLink to="/" class="text-sm font-medium text-slate-900 underline underline-offset-4 hover:text-slate-700">
        {{ $t('common.back_home') }}
      </NuxtLink>
    </div>
  </div>
</template>
