export default defineNuxtConfig({
  ssr: true,
  devtools: { enabled: true },
  components: {
    dirs: [
      { path: '~/components/ui', prefix: '' },
      { path: '~/components/molecules', prefix: '' },
      { path: '~/components/organisms', prefix: '' },
      { path: '~/components/icons', prefix: '' }
    ]
  },

  modules: [
    '@nuxtjs/tailwindcss',
    '@nuxtjs/google-fonts',
    '@pinia/nuxt'
  ],

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api/v1'
    }
  },

  app: {
    head: {
      title: 'Carnet de Lecture',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' }
      ],
      link: [
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap'
        },
        { rel: 'stylesheet', href: '/css/main.css' }
      ]
    }
  }
})