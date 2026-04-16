<template>
  <div class="mx-auto max-w-3xl px-6 py-16">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold tracking-tight mb-4">
        {{ $t('home.title') }}
      </h1>
      <p class="text-lg text-slate-600 max-w-xl mx-auto">
        {{ $t('home.subtitle') }}
      </p>
    </div>

    <div class="mx-auto max-w-md">
      <NewsletterForm />
    </div>
  </div>
</template>
