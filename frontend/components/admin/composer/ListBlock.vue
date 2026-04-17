<script setup lang="ts">
import type { ListBlock } from '~/types/email'

const block = defineModel<ListBlock>('block', { required: true })

function addItem() {
  block.value.items.push('')
}

function removeItem(index: number) {
  block.value.items.splice(index, 1)
  if (block.value.items.length === 0) block.value.items.push('')
}
</script>

<template>
  <div class="flex flex-col gap-2">
    <label class="flex items-center gap-2 text-xs text-slate-500">
      <input v-model="block.ordered" type="checkbox">
      <span>Ordered list</span>
    </label>

    <ul class="flex flex-col gap-2">
      <li v-for="(_, index) in block.items" :key="index" class="flex items-center gap-2">
        <input
          v-model="block.items[index]"
          placeholder="Item"
          class="flex-1 rounded-md border border-slate-200 px-3 py-2 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
        >
        <button type="button" class="rounded px-2 py-1 text-xs text-slate-400 hover:text-red-600" @click="removeItem(index)">
          ✕
        </button>
      </li>
    </ul>

    <button type="button" class="self-start rounded border border-slate-200 px-3 py-1 text-xs text-slate-600 hover:bg-slate-100" @click="addItem">
      + Add item
    </button>
  </div>
</template>
