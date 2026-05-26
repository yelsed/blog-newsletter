<template>
  <header class="site-header">
    <div class="inner">
      <NuxtLink to="/" class="brand">
        {{ $t('header.brand') }}
      </NuxtLink>
    </div>
  </header>
</template>

<style scoped>
.site-header {
  background: var(--bg-surface);
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  z-index: 100;
}

.inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 14px 24px;
  display: flex;
  align-items: center;
}

.brand {
  font-family: var(--font-display);
  font-size: 1.2rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--text);
  text-decoration: none;
  transition: color 0.15s ease;
}

.brand:hover {
  color: var(--accent);
}
</style>
