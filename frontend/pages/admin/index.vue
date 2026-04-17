<script setup lang="ts">
definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

type EmailSummary = {
  id: number
  subject: string
  status: 'draft' | 'sent'
  sent_at: string | null
  created_at: string
  updated_at: string
}

type Paginated<T> = {
  data: T[]
}

const { api } = useApi()

const { data: emails, refresh, pending } = await useAsyncData(
  'admin.emails',
  () => api<Paginated<EmailSummary>>('/admin/emails'),
)

async function handleDelete(id: number) {
  if (!confirm('Delete this email?')) return
  await api(`/admin/emails/${id}`, { method: 'DELETE' })
  await refresh()
}

const drafts = computed(() => emails.value?.data.filter(e => e.status === 'draft') ?? [])
const sent = computed(() => emails.value?.data.filter(e => e.status === 'sent') ?? [])
</script>

<template>
  <div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold text-slate-900">
        Emails
      </h1>
      <NuxtLink
        to="/admin/emails/new"
        class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
      >
        + New email
      </NuxtLink>
    </div>

    <section v-if="pending">
      <p class="text-sm text-slate-500">
        Loading…
      </p>
    </section>

    <section v-else class="flex flex-col gap-8">
      <div>
        <h2 class="mb-3 text-sm font-medium uppercase tracking-wide text-slate-500">
          Drafts
        </h2>
        <ul v-if="drafts.length" class="divide-y divide-slate-200 overflow-hidden rounded-md border border-slate-200 bg-white">
          <li v-for="email in drafts" :key="email.id" class="flex items-center justify-between px-4 py-3">
            <div>
              <NuxtLink :to="`/admin/emails/${email.id}`" class="font-medium text-slate-900 hover:underline">
                {{ email.subject || 'Untitled' }}
              </NuxtLink>
              <p class="text-xs text-slate-500">
                Updated {{ email.updated_at }}
              </p>
            </div>
            <button
              type="button"
              class="text-xs text-slate-500 hover:text-red-600"
              @click="handleDelete(email.id)"
            >
              Delete
            </button>
          </li>
        </ul>
        <p v-else class="text-sm text-slate-500">
          No drafts yet.
        </p>
      </div>

      <div>
        <h2 class="mb-3 text-sm font-medium uppercase tracking-wide text-slate-500">
          Sent
        </h2>
        <ul v-if="sent.length" class="divide-y divide-slate-200 overflow-hidden rounded-md border border-slate-200 bg-white">
          <li v-for="email in sent" :key="email.id" class="px-4 py-3">
            <NuxtLink :to="`/admin/emails/${email.id}`" class="font-medium text-slate-900 hover:underline">
              {{ email.subject }}
            </NuxtLink>
            <p class="text-xs text-slate-500">
              Sent {{ email.sent_at }}
            </p>
          </li>
        </ul>
        <p v-else class="text-sm text-slate-500">
          Nothing sent yet.
        </p>
      </div>
    </section>
  </div>
</template>
