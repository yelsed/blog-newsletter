<script setup lang="ts">
import type { Block, Email } from '~/types/email'
import ComposerShell from '~/components/admin/composer/ComposerShell.vue'

definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const route = useRoute()
const router = useRouter()
const { api } = useApi()

const idParam = computed(() => String(route.params.id))
const isNew = computed(() => idParam.value === 'new')

const email = ref<Email>({
  id: null,
  subject: '',
  status: 'draft',
  blocks: [],
  sent_at: null,
  created_at: null,
  updated_at: null,
})

const saving = ref(false)
const sending = ref(false)
const testing = ref(false)
const errorMessage = ref('')
const flashMessage = ref('')

if (!isNew.value) {
  try {
    const loaded = await api<Email>(`/admin/emails/${idParam.value}`)
    email.value = { ...loaded, blocks: loaded.blocks ?? [] }
  }
  catch (e) {
    errorMessage.value = e instanceof Error ? e.message : 'Could not load email.'
  }
}

async function handleSave(payload: { subject: string, blocks: Block[] }) {
  saving.value = true
  errorMessage.value = ''
  flashMessage.value = ''
  try {
    if (email.value.id === null) {
      const created = await api<Email>('/admin/emails', {
        method: 'POST',
        body: JSON.stringify(payload),
      })
      email.value = { ...created, blocks: created.blocks ?? [] }
      await router.replace(`/admin/emails/${created.id}`)
    }
    else {
      const updated = await api<Email>(`/admin/emails/${email.value.id}`, {
        method: 'PUT',
        body: JSON.stringify(payload),
      })
      email.value = { ...updated, blocks: updated.blocks ?? [] }
    }
    flashMessage.value = 'Saved.'
  }
  catch (e) {
    errorMessage.value = e instanceof Error ? e.message : 'Save failed.'
  }
  finally {
    saving.value = false
  }
}

async function handleSendTest() {
  if (email.value.id === null) return
  testing.value = true
  errorMessage.value = ''
  flashMessage.value = ''
  try {
    await api(`/admin/emails/${email.value.id}/send-test`, { method: 'POST' })
    flashMessage.value = 'Test send queued.'
  }
  catch (e) {
    errorMessage.value = e instanceof Error ? e.message : 'Test send failed.'
  }
  finally {
    testing.value = false
  }
}

async function handleSend() {
  if (email.value.id === null) return
  sending.value = true
  errorMessage.value = ''
  flashMessage.value = ''
  try {
    await api(`/admin/emails/${email.value.id}/send`, { method: 'POST' })
    flashMessage.value = 'Send queued. Subscribers will receive the email shortly.'
    email.value.status = 'sent'
  }
  catch (e) {
    errorMessage.value = e instanceof Error ? e.message : 'Send failed.'
  }
  finally {
    sending.value = false
  }
}

const canSend = computed(() => email.value.id !== null && email.value.status === 'draft')
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
      <div>
        <NuxtLink to="/admin" class="text-xs text-slate-500 hover:underline">
          ← Back to emails
        </NuxtLink>
        <h1 class="text-2xl font-semibold text-slate-900">
          {{ isNew ? 'New email' : email.subject || 'Untitled' }}
        </h1>
        <p class="text-xs text-slate-500">
          Status: <span class="font-medium">{{ email.status }}</span>
        </p>
      </div>
    </div>

    <p v-if="flashMessage" class="rounded-md bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
      {{ flashMessage }}
    </p>
    <p v-if="errorMessage" class="rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">
      {{ errorMessage }}
    </p>

    <ComposerShell
      :initial-subject="email.subject"
      :initial-blocks="email.blocks"
      :saving="saving"
      :sending="sending"
      :testing="testing"
      :can-send="canSend"
      @save="handleSave"
      @send-test="handleSendTest"
      @send="handleSend"
    />
  </div>
</template>
