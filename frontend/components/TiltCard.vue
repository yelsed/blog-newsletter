<script setup lang="ts">
const wrapper = ref<HTMLElement | null>(null)
const rx = ref(0)
const ry = ref(0)
const shineX = ref(50)
const shineY = ref(50)
const active = ref(false)

const cardStyle = computed(() => ({
  transform: `perspective(1200px) rotateX(${rx.value}deg) rotateY(${ry.value}deg)`,
  transition: active.value ? 'transform 0.08s ease-out' : 'transform 0.4s ease-out',
}))

const shineStyle = computed(() => ({
  background: `radial-gradient(circle at ${shineX.value}% ${shineY.value}%, oklch(100% 0 0 / 0.12) 0%, transparent 60%)`,
  opacity: active.value ? 1 : 0,
  transition: 'opacity 0.2s ease',
}))

function onMouseMove(e: MouseEvent) {
  if (!wrapper.value) return
  const r = wrapper.value.getBoundingClientRect()
  const xf = (e.clientX - r.left) / r.width
  const yf = (e.clientY - r.top) / r.height
  ry.value = (xf - 0.5) * 16
  rx.value = -(yf - 0.5) * 16
  shineX.value = xf * 100
  shineY.value = yf * 100
}

function onMouseEnter() { active.value = true }

function onMouseLeave() {
  active.value = false
  rx.value = 0
  ry.value = 0
}

function onOrientation(e: DeviceOrientationEvent) {
  if (active.value || e.gamma === null) return
  ry.value = Math.max(-10, Math.min(10, (e.gamma ?? 0) * 0.5))
  rx.value = Math.max(-10, Math.min(10, ((e.beta ?? 20) - 20) * 0.3))
}

onMounted(() => window.addEventListener('deviceorientation', onOrientation))
onUnmounted(() => window.removeEventListener('deviceorientation', onOrientation))
</script>

<template>
  <div
    ref="wrapper"
    @mousemove="onMouseMove"
    @mouseenter="onMouseEnter"
    @mouseleave="onMouseLeave"
  >
    <div :style="cardStyle" style="position: relative; will-change: transform;">
      <slot />
      <div
        aria-hidden="true"
        style="position: absolute; inset: 0; border-radius: 16px; pointer-events: none;"
        :style="shineStyle"
      />
    </div>
  </div>
</template>
