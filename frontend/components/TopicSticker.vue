<script setup lang="ts">
const props = defineProps<{
  emoji: string
  bgColor: string
  rotation: number
  top: string
  left?: string
  right?: string
  hideOnMobile?: boolean
}>()

const el = ref<HTMLElement | null>(null)
const rx = ref(0)
const ry = ref(0)
const hovered = ref(false)

const stickerStyle = computed(() => ({
  top: props.top,
  left: props.left ?? 'auto',
  right: props.right ?? 'auto',
  transform: `perspective(500px) rotate(${props.rotation}deg) rotateX(${rx.value}deg) rotateY(${ry.value}deg) scale(${hovered.value ? 1.2 : 1})`,
  transition: hovered.value ? 'transform 0.08s ease-out' : 'transform 0.4s ease-out',
  zIndex: hovered.value ? 50 : undefined,
}))

function onMouseMove(e: MouseEvent) {
  if (!el.value) return
  const r = el.value.getBoundingClientRect()
  ry.value = ((e.clientX - r.left) / r.width - 0.5) * 24
  rx.value = -((e.clientY - r.top) / r.height - 0.5) * 24
}

function onMouseEnter() { hovered.value = true }
function onMouseLeave() { hovered.value = false; rx.value = 0; ry.value = 0 }
</script>

<template>
  <div
    ref="el"
    class="sticker"
    :class="hideOnMobile ? 'hidden sm:block' : ''"
    :style="stickerStyle"
    @mousemove="onMouseMove"
    @mouseenter="onMouseEnter"
    @mouseleave="onMouseLeave"
  >
    <span class="sticker-emoji">{{ emoji }}</span>
  </div>
</template>
