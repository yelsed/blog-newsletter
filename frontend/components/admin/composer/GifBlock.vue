<script setup lang="ts">
import type { GifBlock } from '~/types/email'

const block = defineModel<GifBlock>('block', { required: true })
</script>

<template>
  <div class="flex flex-col gap-2">
    <input v-model="block.url" placeholder="https://example.com/fun.gif" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
    <input v-model="block.alt" placeholder="Alt text" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
    <div class="flex items-center gap-3 text-xs text-slate-500">
      <label>Width (px)</label>
      <input v-model.number="block.width" type="number" min="1" class="w-24 rounded border border-slate-200 px-2 py-1">
    </div>
  </div>
</template>
