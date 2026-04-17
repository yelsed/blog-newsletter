<template>
  <footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto max-w-3xl px-6 py-6 text-center text-sm text-slate-500">
      {{ $t('footer.copyright', { year: new Date().getFullYear(), brand: $t('header.brand') }) }}
    </div>
  </footer>
</template>
