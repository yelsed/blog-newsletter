<template>
  <footer class="site-footer">
    <div class="inner">
      {{ $t('footer.copyright', { year: new Date().getFullYear(), brand: $t('header.brand') }) }}
    </div>
  </footer>
</template>

<style scoped>
.site-footer {
  background: var(--bg-surface);
  border-top: 1px solid var(--border);
}

.inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 20px 24px;
  text-align: center;
  font-family: var(--font-body);
  font-size: 0.8rem;
  color: var(--text-muted);
}
</style>
