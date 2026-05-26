<script setup lang="ts">
type Shimmer = 'holofoil' | 'prismatic' | 'sparkle'

const stickers: {
  image: string
  color: string
  rotation: number
  top: string
  left?: string
  right?: string
  shimmer: Shimmer
  hideOnMobile: boolean
}[] = [
  // Left column — desktop only
  { image: '/stickers/laptop.svg',  color: 'oklch(50% 0.22 155)', rotation: -4, top: '6%',  left: '3%',  shimmer: 'holofoil',  hideOnMobile: true },
  { image: '/stickers/tv.svg',      color: 'oklch(58% 0.20 50)',  rotation: -9, top: '19%', left: '2%',  shimmer: 'prismatic', hideOnMobile: true },
  { image: '/stickers/bolt.svg',    color: 'oklch(40% 0.15 250)', rotation:  4, top: '33%', left: '4%',  shimmer: 'holofoil',  hideOnMobile: true },
  { image: '/stickers/ball.svg',    color: 'oklch(50% 0.20 155)', rotation: -6, top: '48%', left: '1%',  shimmer: 'sparkle',   hideOnMobile: true },
  { image: '/stickers/star.svg',    color: 'oklch(68% 0.18 85)',  rotation: -8, top: '61%', left: '5%',  shimmer: 'holofoil',  hideOnMobile: true },
  { image: '/stickers/leaf.svg',    color: 'oklch(50% 0.18 155)', rotation:  6, top: '74%', left: '4%',  shimmer: 'prismatic', hideOnMobile: true },
  { image: '/stickers/diamond.svg', color: 'oklch(60% 0.18 30)',  rotation: -3, top: '86%', left: '6%',  shimmer: 'sparkle',   hideOnMobile: true },
  { image: '/stickers/music.svg',   color: 'oklch(57% 0.22 20)',  rotation:  5, top: '91%', left: '22%', shimmer: 'holofoil',  hideOnMobile: true },

  // Right column — desktop only
  { image: '/stickers/music.svg',   color: 'oklch(52% 0.22 240)', rotation:  7, top: '5%',  right: '4%',  shimmer: 'prismatic', hideOnMobile: true },
  { image: '/stickers/bolt.svg',    color: 'oklch(65% 0.20 85)',  rotation: -5, top: '18%', right: '2%',  shimmer: 'sparkle',   hideOnMobile: true },
  { image: '/stickers/robot.svg',   color: 'oklch(45% 0.22 290)', rotation:  8, top: '30%', right: '3%',  shimmer: 'sparkle',   hideOnMobile: true },
  { image: '/stickers/laptop.svg',  color: 'oklch(42% 0.18 155)', rotation: -4, top: '42%', right: '1%',  shimmer: 'prismatic', hideOnMobile: true },
  { image: '/stickers/heart.svg',   color: 'oklch(57% 0.22 20)',  rotation:  6, top: '54%', right: '4%',  shimmer: 'holofoil',  hideOnMobile: true },
  { image: '/stickers/diamond.svg', color: 'oklch(50% 0.22 215)', rotation: -7, top: '66%', right: '2%',  shimmer: 'prismatic', hideOnMobile: true },
  { image: '/stickers/leaf.svg',    color: 'oklch(48% 0.20 175)', rotation:  5, top: '78%', right: '5%',  shimmer: 'holofoil',  hideOnMobile: true },
  { image: '/stickers/star.svg',    color: 'oklch(68% 0.18 85)',  rotation: -4, top: '89%', right: '19%', shimmer: 'sparkle',   hideOnMobile: true },

  // Top center — desktop only
  { image: '/stickers/music.svg',   color: 'oklch(52% 0.20 320)', rotation: -6, top: '2%', left: '30%',  shimmer: 'sparkle',   hideOnMobile: true },
  { image: '/stickers/tv.svg',      color: 'oklch(55% 0.20 35)',  rotation:  9, top: '3%', right: '30%', shimmer: 'prismatic', hideOnMobile: true },

  // Always visible — safe corners on mobile too
  { image: '/stickers/robot.svg', color: 'oklch(45% 0.22 290)', rotation:  7, top: '1%',  right: '3%', shimmer: 'sparkle',  hideOnMobile: false },
  { image: '/stickers/heart.svg', color: 'oklch(57% 0.22 20)',  rotation: -5, top: '93%', left: '3%',  shimmer: 'holofoil', hideOnMobile: false },
]
</script>

<template>
  <div class="subscribe-page">
    <div
      v-for="(s, i) in stickers"
      :key="i"
      class="sticker-pin"
      :class="s.hideOnMobile ? 'hidden sm:block' : ''"
      :style="{
        top: s.top,
        left: s.left ?? 'auto',
        right: s.right ?? 'auto',
        transform: `rotate(${s.rotation}deg)`,
      }"
    >
      <StickerCard
        :image="s.image"
        :color="s.color"
        :shimmer="s.shimmer"
        :size="72"
      />
    </div>

    <main class="content animate-entrance">
      <div class="hero">
        <h1 class="headline">{{ $t('home.title') }}</h1>
        <p class="tagline">{{ $t('home.subtitle') }}</p>
      </div>

      <TiltCard>
        <div class="subscribe-card">
          <NewsletterForm />
        </div>
      </TiltCard>
    </main>
  </div>
</template>

<style scoped>
.subscribe-page {
  position: relative;
  min-height: calc(100vh - 57px);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 64px 24px;
}

.sticker-pin {
  position: absolute;
  z-index: 0;
}

.content {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 460px;
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.hero {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.headline {
  font-family: var(--font-display);
  font-size: clamp(2.5rem, 7vw, 4.5rem);
  font-weight: 800;
  line-height: 1.05;
  letter-spacing: -0.025em;
  color: var(--text);
  margin: 0;
}

.tagline {
  font-family: var(--font-body);
  font-size: 1rem;
  line-height: 1.65;
  color: var(--text-muted);
  max-width: 50ch;
  margin: 0;
}

.subscribe-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 32px;
  box-shadow:
    0 8px 24px oklch(0% 0 0 / 0.06),
    0 2px 6px oklch(0% 0 0 / 0.04);
}
</style>
