<script setup lang="ts">
import { blockDefaults } from '~/types/email'
import type { Block, BlockType } from '~/types/email'
import AddBlockMenu from './AddBlockMenu.vue'
import BlockList from './BlockList.vue'
import PreviewPane from './PreviewPane.vue'

const props = defineProps<{
  initialSubject: string
  initialBlocks: Block[]
  saving: boolean
  sending: boolean
  testing: boolean
  canSend: boolean
}>()

const emit = defineEmits<{
  save: [{ subject: string, blocks: Block[] }]
  send: []
  sendTest: []
}>()

const subject = ref(props.initialSubject)
const blocks = ref<Block[]>([...props.initialBlocks])

watch(() => props.initialSubject, (v) => { subject.value = v })
watch(() => props.initialBlocks, (v) => { blocks.value = [...v] }, { deep: true })

function addBlock(type: BlockType) {
  blocks.value = [...blocks.value, blockDefaults(type)]
}

function handleSave() {
  emit('save', { subject: subject.value, blocks: blocks.value })
}

function handleSend() {
  if (!confirm('Send this email to all verified subscribers? This cannot be undone.')) return
  emit('send')
}
</script>

<template>
  <div class="grid gap-8 lg:grid-cols-2">
    <section class="flex flex-col gap-4">
      <input
        v-model="subject"
        type="text"
        placeholder="Subject"
        class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-lg font-semibold text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
      >

      <BlockList v-model:blocks="blocks" />

      <AddBlockMenu @add="addBlock" />

      <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-200 pt-4">
        <button
          type="button"
          :disabled="saving"
          class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400"
          @click="handleSave"
        >
          {{ saving ? 'Saving…' : 'Save draft' }}
        </button>

        <button
          type="button"
          :disabled="testing || !canSend"
          class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
          @click="emit('sendTest')"
        >
          {{ testing ? 'Queueing…' : 'Send test to me' }}
        </button>

        <button
          type="button"
          :disabled="sending || !canSend"
          class="rounded-md border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50"
          @click="handleSend"
        >
          {{ sending ? 'Sending…' : 'Send to all' }}
        </button>
      </div>
    </section>

    <aside class="flex flex-col gap-2">
      <PreviewPane :subject="subject" :blocks="blocks" />
    </aside>
  </div>
</template>
