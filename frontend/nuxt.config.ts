import tailwindcss from '@tailwindcss/vite'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  app: {
    head: {
      link: [
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap',
        },
      ],
    },
  },

  modules: ['@nuxtjs/i18n'],

  css: ['~/assets/css/main.css'],

  vite: {
    plugins: [tailwindcss()],
    server: {
      allowedHosts: ['omarchy.tail7f3847.ts.net'],
    },
  },

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost/api',
      mediaBaseUrl: process.env.NUXT_PUBLIC_MEDIA_BASE_URL || 'http://localhost/storage/media',
    },
  },

  i18n: {
    defaultLocale: 'en',
    strategy: 'no_prefix',
    locales: [
      { code: 'en', name: 'English', file: 'en.json' },
    ],
    detectBrowserLanguage: {
      useCookie: true,
      cookieKey: 'i18n_locale',
      redirectOn: 'root',
      fallbackLocale: 'en',
    },
  },
})
