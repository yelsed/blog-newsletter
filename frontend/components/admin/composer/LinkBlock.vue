<script setup lang="ts">
import type { LinkBlock } from '~/types/email'

const block = defineModel<LinkBlock>('block', { required: true })
</script>

<template>
  <div class="flex flex-col gap-2">
    <input v-model="block.label" placeholder="Link label" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
    <input v-model="block.href" placeholder="https://example.com" class="rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
    <div class="flex items-center gap-3 text-xs text-slate-500">
      <label>Align</label>
      <select v-model="block.align" class="rounded border border-slate-200 px-2 py-1">
        <option value="left">
          Left
        </option>
        <option value="center">
          Center
        </option>
        <option value="right">
          Right
        </option>
      </select>
    </div>
  </div>
</template>
