<script setup lang="ts">
import type { Block } from '~/types/email'
import { stripUid } from '~/types/email'

const props = defineProps<{
  subject: string
  blocks: Block[]
}>()

const { api } = useApi()
const html = ref('')
const pending = ref(false)
const error = ref('')

let debounce: ReturnType<typeof setTimeout> | null = null

async function refresh() {
  if (!props.subject && props.blocks.length === 0) {
    html.value = ''
    return
  }
  pending.value = true
  error.value = ''
  try {
    const res = await api<{ html: string }>('/admin/emails/preview', {
      method: 'POST',
      body: JSON.stringify({ subject: props.subject, blocks: props.blocks.map(stripUid) }),
    })
    html.value = res.html
  }
  catch (e) {
    error.value = e instanceof Error ? e.message : 'Preview failed.'
  }
  finally {
    pending.value = false
  }
}

watch(
  () => [props.subject, props.blocks] as const,
  () => {
    if (debounce) clearTimeout(debounce)
    debounce = setTimeout(refresh, 400)
  },
  { immediate: true, deep: true },
)
</script>

<template>
  <div class="flex h-full flex-col gap-2">
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-medium uppercase tracking-wide text-slate-500">
        Preview
      </h2>
      <span v-if="pending" class="text-xs text-slate-400">Rendering…</span>
    </div>
    <p v-if="error" class="text-xs text-red-600">
      {{ error }}
    </p>
    <iframe
      :srcdoc="html"
      class="min-h-[600px] w-full flex-1 rounded-md border border-slate-200 bg-white"
    />
  </div>
</template>
