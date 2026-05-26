<script setup lang="ts">
definePageMeta({ layout: 'default' })

type Shimmer = 'holofoil' | 'prismatic' | 'sparkle'

const stickers = [
  { image: '/stickers/star.svg',    color: 'oklch(85% 0.22 85)',  alt: 'Gold star',       rotation: -6 },
  { image: '/stickers/bolt.svg',    color: 'oklch(68% 0.24 260)', alt: 'Lightning bolt',  rotation:  4 },
  { image: '/stickers/heart.svg',   color: 'oklch(70% 0.25 10)',  alt: 'Heart',           rotation: -3 },
  { image: '/stickers/diamond.svg', color: 'oklch(72% 0.28 310)', alt: 'Diamond',         rotation:  7 },
]

const variants: { shimmer: Shimmer; label: string; description: string }[] = [
  {
    shimmer: 'holofoil',
    label: 'Holofoil',
    description: 'Single large gradient. Angle shifts with tilt, position tracks the cursor.',
  },
  {
    shimmer: 'prismatic',
    label: 'Prismatic',
    description: 'Fine repeating stripes. High-contrast colour separation across the surface.',
  },
  {
    shimmer: 'sparkle',
    label: 'Sparkle',
    description: 'Two scales of 4-pointed star tiles over the rainbow base, with parallax between layers.',
  },
]
</script>

<template>
  <div class="lab-page">

    <header class="lab-header animate-entrance">
      <p class="lab-eyebrow">Sticker Lab</p>
      <h1 class="lab-title">Shimmer.</h1>
      <p class="lab-subtitle">Three holographic shimmer techniques. Hover a sticker.</p>
    </header>

    <section
      v-for="(v, vi) in variants"
      :key="v.shimmer"
      class="variant-section"
      :style="{ animationDelay: `${vi * 0.12}s` }"
    >
      <div class="variant-header">
        <span class="variant-index">0{{ vi + 1 }}</span>
        <div>
          <h2 class="variant-label">{{ v.label }}</h2>
          <p class="variant-desc">{{ v.description }}</p>
        </div>
      </div>

      <div class="sticker-row" :aria-label="`${v.label} shimmer stickers`">
        <div
          v-for="(s, si) in stickers"
          :key="s.alt"
          class="sticker-item"
          :style="{
            '--rotation': `${s.rotation}deg`,
            animationDelay: `${vi * 0.12 + si * 0.07}s`,
          }"
        >
          <StickerCard
            :image="s.image"
            :color="s.color"
            :alt="s.alt"
            :shimmer="v.shimmer"
            :size="160"
          />
        </div>
      </div>
    </section>

    <footer class="lab-footer">
      <NuxtLink to="/" class="back-link">← Back to subscribe</NuxtLink>
    </footer>

  </div>
</template>

<style scoped>
.lab-page {
  min-height: calc(100vh - 57px);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 80px;
  padding: 64px 24px 80px;
}

/* ── Header ── */

.lab-header {
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.lab-eyebrow {
  font-family: var(--font-body);
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--accent);
  margin: 0;
}

.lab-title {
  font-family: var(--font-display);
  font-size: clamp(2.5rem, 6vw, 4.5rem);
  font-weight: 800;
  letter-spacing: -0.025em;
  line-height: 1.05;
  color: var(--text);
  margin: 0;
}

.lab-subtitle {
  font-family: var(--font-body);
  font-size: 0.9rem;
  color: var(--text-muted);
  margin: 0;
}

/* ── Variant sections ── */

.variant-section {
  width: 100%;
  max-width: 860px;
  display: flex;
  flex-direction: column;
  gap: 32px;
  animation: fadeSlideUp 0.5s ease-out both;
}

.variant-header {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border);
}

.variant-index {
  font-family: var(--font-display);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: var(--accent);
  padding-top: 3px;
  flex-shrink: 0;
}

.variant-label {
  font-family: var(--font-display);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text);
  margin: 0 0 4px;
  letter-spacing: -0.01em;
}

.variant-desc {
  font-family: var(--font-body);
  font-size: 0.82rem;
  color: var(--text-muted);
  margin: 0;
  line-height: 1.5;
}

/* ── Sticker row ── */

.sticker-row {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 40px 48px;
}

.sticker-item {
  transform: rotate(var(--rotation));
  animation: fadeSlideUp 0.5s ease-out both;
}

/* ── Footer ── */

.lab-footer {
  margin-top: auto;
}

.back-link {
  font-family: var(--font-body);
  font-size: 0.875rem;
  color: var(--text-muted);
  text-decoration: none;
  transition: color 0.2s ease;
}

.back-link:hover {
  color: var(--text);
}

@media (prefers-reduced-motion: reduce) {
  .variant-section,
  .sticker-item {
    animation: none;
  }
}
</style>
