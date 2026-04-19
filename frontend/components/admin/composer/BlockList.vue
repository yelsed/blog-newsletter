<script setup lang="ts">
import type { Block } from '~/types/email'
import ButtonBlock from './ButtonBlock.vue'
import GifBlock from './GifBlock.vue'
import ImageBlock from './ImageBlock.vue'
import LinkBlock from './LinkBlock.vue'
import ListBlock from './ListBlock.vue'
import TextBlock from './TextBlock.vue'

const blocks = defineModel<Block[]>('blocks', { required: true })

function move(index: number, dir: -1 | 1) {
  const newIndex = index + dir
  if (newIndex < 0 || newIndex >= blocks.value.length) return
  const copy = [...blocks.value]
  const [item] = copy.splice(index, 1)
  copy.splice(newIndex, 0, item)
  blocks.value = copy
}

function remove(index: number) {
  blocks.value = blocks.value.filter((_, i) => i !== index)
}
</script>

<template>
  <ul class="flex flex-col gap-3">
    <li
      v-for="(block, index) in blocks"
      :key="block._uid"
      class="rounded-md border border-slate-200 bg-white p-4"
    >
      <div class="mb-3 flex items-center justify-between">
        <span class="text-xs font-medium uppercase tracking-wide text-slate-500">
          {{ block.type }}
        </span>
        <div class="flex items-center gap-1 text-xs text-slate-400">
          <button type="button" class="rounded px-2 py-1 hover:bg-slate-100 disabled:opacity-40" :disabled="index === 0" @click="move(index, -1)">
            ↑
          </button>
          <button type="button" class="rounded px-2 py-1 hover:bg-slate-100 disabled:opacity-40" :disabled="index === blocks.length - 1" @click="move(index, 1)">
            ↓
          </button>
          <button type="button" class="rounded px-2 py-1 hover:bg-slate-100 hover:text-red-600" @click="remove(index)">
            ✕
          </button>
        </div>
      </div>
      <TextBlock v-if="block.type === 'text'" v-model:block="blocks[index] as Extract<Block, { type: 'text' }>" />
      <LinkBlock v-else-if="block.type === 'link'" v-model:block="blocks[index] as Extract<Block, { type: 'link' }>" />
      <ListBlock v-else-if="block.type === 'list'" v-model:block="blocks[index] as Extract<Block, { type: 'list' }>" />
      <ImageBlock v-else-if="block.type === 'image'" v-model:block="blocks[index] as Extract<Block, { type: 'image' }>" />
      <GifBlock v-else-if="block.type === 'gif'" v-model:block="blocks[index] as Extract<Block, { type: 'gif' }>" />
      <ButtonBlock v-else-if="block.type === 'button'" v-model:block="blocks[index] as Extract<Block, { type: 'button' }>" />
    </li>
  </ul>
</template>
