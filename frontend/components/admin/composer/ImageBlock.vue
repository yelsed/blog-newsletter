<script setup lang="ts">
import type { ImageBlock } from '~/types/email'

const block = defineModel<ImageBlock>('block', { required: true })
</script>

<template>
  <div class="flex flex-col gap-2">
    <input v-model="block.url" placeholder="https://example.com/image.png" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
    <input v-model="block.alt" placeholder="Alt text" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
    <div class="flex items-center gap-3 text-xs text-slate-500">
      <label>Width (px)</label>
      <input v-model.number="block.width" type="number" min="1" class="w-24 rounded border border-slate-200 px-2 py-1">
    </div>
    <input v-model="block.href" placeholder="Optional link URL" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
  </div>
</template>
