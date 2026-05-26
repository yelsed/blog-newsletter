<script setup lang="ts">
type Shimmer = 'holofoil' | 'prismatic' | 'sparkle'

const props = withDefaults(defineProps<{
  image: string
  color: string
  alt?: string
  size?: number
  shimmer?: Shimmer
}>(), { alt: 'Sticker', size: 180, shimmer: 'holofoil' })

const wrapper = ref<HTMLElement | null>(null)

const rx = ref(0)
const ry = ref(0)
const px = ref(50)
const py = ref(50)
const active = ref(false)

const TILT = 20
const EASE_OUT = 'cubic-bezier(0.23, 1, 0.32, 1)'

const SPARKLE_SM = `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 10'%3E%3Cpath d='M5,0.5 L6.5,3.5 L9.5,5 L6.5,6.5 L5,9.5 L3.5,6.5 L0.5,5 L3.5,3.5 Z' fill='white' opacity='0.9'/%3E%3C/svg%3E")`
const SPARKLE_LG = `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 10'%3E%3Cpath d='M5,0.5 L6.2,3.8 L9.5,5 L6.2,6.2 L5,9.5 L3.8,6.2 L0.5,5 L3.8,3.8 Z' fill='white' opacity='0.65'/%3E%3C/svg%3E")`

function rainbow(angle: number): string {
  return `linear-gradient(${angle}deg,
    oklch(65% 0.28 15)  0%,
    oklch(78% 0.22 60)  14%,
    oklch(84% 0.18 100) 28%,
    oklch(76% 0.24 160) 42%,
    oklch(72% 0.26 210) 57%,
    oklch(62% 0.28 265) 71%,
    oklch(68% 0.26 310) 85%,
    oklch(65% 0.28 360) 100%)`
}

function onMove(e: MouseEvent) {
  if (!wrapper.value) return
  const r = wrapper.value.getBoundingClientRect()
  const xf = (e.clientX - r.left) / r.width
  const yf = (e.clientY - r.top) / r.height
  rx.value = -(yf - 0.5) * TILT * 2
  ry.value = (xf - 0.5) * TILT * 2
  px.value = xf * 100
  py.value = yf * 100
}

function onEnter() { active.value = true }

function onLeave() {
  active.value = false
  rx.value = 0
  ry.value = 0
  px.value = 50
  py.value = 50
}

// Hue cycles 0→360 as cursor moves left→right.
// This drives the coloured drop-shadow on the image, which follows the white border shape.
const hue = computed(() => Math.round((px.value / 100) * 360))

// The image filter: static white outline + dynamic coloured glow on top.
// drop-shadow on this element is computed from the rendered pixels including the white ring,
// so the glow appears on/around the outline rather than the fill.
const imgFilter = computed(() => {
  if (!active.value) return 'url(#sticker-outline)'

  if (props.shimmer === 'holofoil') {
    return `url(#sticker-outline) drop-shadow(0 0 7px oklch(72% 0.28 ${hue.value} / 0.75))`
  }

  if (props.shimmer === 'prismatic') {
    // Three offset glows 120° apart — simulates the colour splitting of a prism
    const h0 = hue.value
    const h1 = (h0 + 120) % 360
    const h2 = (h0 + 240) % 360
    return [
      'url(#sticker-outline)',
      `drop-shadow(0 0 6px oklch(72% 0.28 ${h0} / 0.65))`,
      `drop-shadow(2px 2px 5px oklch(72% 0.28 ${h1} / 0.45))`,
      `drop-shadow(-2px -2px 5px oklch(72% 0.28 ${h2} / 0.45))`,
    ].join(' ')
  }

  // sparkle: bright core glow + tighter white-hot highlight
  return [
    'url(#sticker-outline)',
    `drop-shadow(0 0 10px oklch(78% 0.26 ${hue.value} / 0.8))`,
    `drop-shadow(0 0 3px oklch(94% 0.12 ${hue.value} / 0.9))`,
  ].join(' ')
})

const cardStyle = computed(() => ({
  transform: `perspective(600px) rotateX(${rx.value}deg) rotateY(${ry.value}deg) translateZ(4px)`,
  transition: active.value ? 'transform 0.08s ease-out' : `transform 0.6s ${EASE_OUT}`,
}))

const wrapperStyle = computed(() => {
  const sx = -ry.value * 0.45
  const sy = rx.value * 0.45 + 10
  const blur = 14 + (Math.abs(rx.value) + Math.abs(ry.value)) * 0.25
  return {
    filter: `drop-shadow(${sx}px ${sy}px ${blur}px oklch(0% 0 0 / 0.45))`,
    transition: active.value ? 'filter 0.08s ease-out' : `filter 0.6s ${EASE_OUT}`,
  }
})

const maskAttrs = computed(() => ({
  maskImage: `url(${props.image})`,
  WebkitMaskImage: `url(${props.image})`,
  maskSize: '100% 100%',
  WebkitMaskSize: '100% 100%',
  maskRepeat: 'no-repeat',
  WebkitMaskRepeat: 'no-repeat',
}))

const glareStyle = computed(() => ({
  ...maskAttrs.value,
  background: `radial-gradient(circle at ${px.value}% ${py.value}%, oklch(100% 0 0 / 0.4) 0%, transparent 55%)`,
  opacity: active.value ? 1 : 0,
  mixBlendMode: 'hard-light' as const,
  transition: `opacity ${active.value ? '0.12s' : '0.4s'} ease`,
}))

const colorOverlayStyle = computed(() => ({
  ...maskAttrs.value,
  backgroundColor: props.color,
  mixBlendMode: 'soft-light' as const,
  opacity: 0.18,
}))

// Subtle interior shimmer — the main shimmer lives on the border glow above.
// No contrast() filter: that was causing harsh visible banding.
const shimmerStyle = computed(() => {
  const fade = `opacity ${active.value ? '0.18s' : '0.5s'} ease`
  const base = {
    ...maskAttrs.value,
    mixBlendMode: 'screen' as const,
    opacity: active.value ? 0.18 : 0,
    transition: fade,
  }
  const bgX = 50 + (px.value - 50) * 0.5
  const bgY = 50 + (py.value - 50) * 0.5
  const angle = 130 + ry.value

  if (props.shimmer === 'holofoil') {
    return {
      ...base,
      backgroundImage: rainbow(angle),
      backgroundSize: '400% 400%',
      backgroundPosition: `${bgX}% ${bgY}%`,
      filter: 'saturate(1.3)',
    }
  }

  if (props.shimmer === 'prismatic') {
    const sx = (px.value - 50) * 1.2
    const sy = (py.value - 50) * 1.2
    return {
      ...base,
      backgroundImage: `repeating-linear-gradient(${45 + ry.value * 0.5}deg,
        oklch(65% 0.28 15)  0px,  oklch(78% 0.22 60)  5px,
        oklch(84% 0.18 100) 10px, oklch(76% 0.24 160) 15px,
        oklch(72% 0.26 210) 20px, oklch(62% 0.28 265) 25px,
        oklch(68% 0.26 310) 30px, oklch(65% 0.28 360) 35px)`,
      backgroundPosition: `${sx}px ${sy}px`,
      filter: 'saturate(1.6)',
    }
  }

  // sparkle: star tiles + rainbow — white stars screen to bright flashes at low opacity
  const spX = (px.value - 50) * 0.18
  const spY = (py.value - 50) * 0.18
  return {
    ...base,
    backgroundImage: `${SPARKLE_SM}, ${SPARKLE_LG}, ${rainbow(angle)}`,
    backgroundSize: '18px 18px, 32px 32px, 400% 400%',
    backgroundPosition: `${spX}px ${spY}px, ${-spX * 0.7}px ${-spY * 0.7}px, ${bgX}% ${bgY}%`,
    filter: 'saturate(1.4)',
  }
})
</script>

<template>
  <div
    ref="wrapper"
    class="sticker-card-wrapper"
    :style="wrapperStyle"
    @mousemove="onMove"
    @mouseenter="onEnter"
    @mouseleave="onLeave"
  >
    <div class="sticker-card" :style="cardStyle">
      <img
        :src="image"
        :alt="alt"
        class="sticker-img"
        :style="{ filter: imgFilter }"
        :width="size"
        :height="size"
        draggable="false"
      />
      <div class="sticker-layer" :style="colorOverlayStyle" aria-hidden="true" />
      <div class="sticker-layer" :style="shimmerStyle" aria-hidden="true" />
      <div class="sticker-layer" :style="glareStyle" aria-hidden="true" />
    </div>
  </div>
</template>

<style scoped>
.sticker-card-wrapper {
  display: inline-block;
  cursor: pointer;
}

.sticker-card {
  position: relative;
  transform-style: preserve-3d;
  will-change: transform;
}

.sticker-img {
  display: block;
  width: v-bind('`${size}px`');
  height: auto;
  user-select: none;
  pointer-events: none;
}

.sticker-layer {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

@media (prefers-reduced-motion: reduce) {
  .sticker-card,
  .sticker-card-wrapper {
    transition: none !important;
  }
  .sticker-card-wrapper {
    filter: drop-shadow(0 8px 16px oklch(0% 0 0 / 0.35)) !important;
  }
}
</style>
