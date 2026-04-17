<script setup lang="ts">
import type { BlockType } from '~/types/email'

defineEmits<{ add: [BlockType] }>()

const open = ref(false)

const options: Array<{ type: BlockType, label: string }> = [
  { type: 'text', label: 'Text' },
  { type: 'link', label: 'Link' },
  { type: 'list', label: 'List' },
  { type: 'image', label: 'Image' },
  { type: 'gif', label: 'GIF' },
  { type: 'button', label: 'Button' },
]
</script>

<template>
  <div class="relative inline-block">
    <button
      type="button"
      class="rounded-md border border-dashed border-slate-300 px-4 py-2 text-sm text-slate-600 hover:border-slate-400 hover:bg-slate-100"
      @click="open = !open"
    >
      + Add block
    </button>
    <ul
      v-if="open"
      class="absolute z-10 mt-1 w-40 overflow-hidden rounded-md border border-slate-200 bg-white shadow-md"
    >
      <li v-for="opt in options" :key="opt.type">
        <button
          type="button"
          class="w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100"
          @click="$emit('add', opt.type); open = false"
        >
          {{ opt.label }}
        </button>
      </li>
    </ul>
  </div>
</template>
