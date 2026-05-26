<script setup lang="ts">
import type { ImageBlock } from '~/types/email'

const block = defineModel<ImageBlock>('block', { required: true })
const props = defineProps<{ emailId: number | null }>()

const { uploading, uploadError, previewUrl, handleFile } = useMediaUpload(
  toRef(props, 'emailId'),
  computed(() => block.value.path),
)

function onFile(e: Event) {
  handleFile(e, (path) => { block.value.path = path })
}
</script>

<template>
  <div class="flex flex-col gap-2">
    <label class="flex flex-col gap-1 text-xs text-slate-500">
      <span>Image file</span>
      <input
        type="file"
        accept="image/png,image/jpeg,image/webp,image/gif"
        :disabled="uploading"
        class="text-sm file:mr-3 file:rounded file:border-0 file:bg-slate-900 file:px-3 file:py-1.5 file:text-white hover:file:bg-slate-800"
        @change="onFile"
      >
    </label>

    <p v-if="uploading" class="text-xs text-slate-500">
      Uploading…
    </p>
    <p v-if="uploadError" class="text-xs text-red-600">
      {{ uploadError }}
    </p>

    <div v-if="block.path" class="flex items-center gap-3 rounded bg-slate-50 p-2 text-xs text-slate-600">
      <img v-if="previewUrl" :src="previewUrl" :alt="block.alt" class="h-12 w-12 rounded object-cover">
      <span class="font-mono">{{ block.path }}</span>
    </div>

    <input v-model="block.alt" placeholder="Alt text" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
    <div class="flex items-center gap-3 text-xs text-slate-500">
      <label>Width (px)</label>
      <input v-model.number="block.width" type="number" min="1" class="w-24 rounded border border-slate-200 px-2 py-1">
    </div>
    <input v-model="block.href" placeholder="Optional link URL" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
  </div>
</template>
