<template>
  <div class="min-h-screen bg-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Brand -->
      <div class="text-center mb-8">
        <h1 class="font-serif text-4xl text-ink mb-2">Carnet de Lecture</h1>
        <p class="text-ink-3 text-body">Connectez-vous à votre bibliothèque</p>
      </div>

      <!-- Card -->
      <div class="bg-surface rounded-card shadow-sm border border-line p-8">
        <!-- Tabs -->
        <div class="flex mb-8 border-b border-line">
          <button 
            @click="activeTab = 'login'"
            :class="[
              'flex-1 pb-3 text-center font-medium transition-all duration-300 tab-underline',
              activeTab === 'login' ? 'active text-accent' : 'text-ink-3 hover:text-ink'
            ]"
          >
            Connexion
          </button>
          <button 
            @click="activeTab = 'register'"
            :class="[
              'flex-1 pb-3 text-center font-medium transition-all duration-300 tab-underline',
              activeTab === 'register' ? 'active text-accent' : 'text-ink-3 hover:text-ink'
            ]"
          >
            Inscription
          </button>
        </div>

        <!-- Login Form -->
        <form v-if="activeTab === 'login'" @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="block text-label text-ink-3 font-medium mb-2">Email</label>
            <div class="relative">
              <input 
                v-model="loginForm.email"
                type="email"
                required
                placeholder="votre@email.com"
                class="w-full px-4 py-3 bg-bg-2 border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                       focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
              >
              <Mail class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-3" />
            </div>
          </div>

          <div>
            <label class="block text-label text-ink-3 font-medium mb-2">Mot de passe</label>
            <div class="relative">
              <input 
                v-model="loginForm.password"
                type="password"
                required
                placeholder="••••••••"
                class="w-full px-4 py-3 bg-bg-2 border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                       focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
              >
              <Key class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-3" />
            </div>
          </div>

          <div v-if="loginError" class="bg-tag-tw-bg text-tag-tw-fg p-3 rounded-btn text-body">
            {{ loginError }}
          </div>

          <button 
            type="submit"
            :disabled="loading"
            class="w-full bg-accent text-white font-medium py-3 px-6 rounded-btn shadow-btn
                   hover:bg-[#7a2f2f] hover:shadow-btn-hover hover:-translate-y-0.5 
                   transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Se connecter</span>
            <span v-else class="flex items-center justify-center gap-2">
              <Loader2 class="w-4 h-4 animate-spin" />
              Connexion...
            </span>
          </button>
        </form>

        <!-- Register Form -->
        <form v-if="activeTab === 'register'" @submit.prevent="handleRegister" class="space-y-5">
          <div>
            <label class="block text-label text-ink-3 font-medium mb-2">Nom</label>
            <div class="relative">
              <input 
                v-model="registerForm.name"
                type="text"
                required
                placeholder="Votre nom"
                class="w-full px-4 py-3 bg-bg-2 border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                       focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
              >
              <User class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-3" />
            </div>
          </div>

          <div>
            <label class="block text-label text-ink-3 font-medium mb-2">Email</label>
            <div class="relative">
              <input 
                v-model="registerForm.email"
                type="email"
                required
                placeholder="votre@email.com"
                class="w-full px-4 py-3 bg-bg-2 border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                       focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
              >
              <Mail class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-3" />
            </div>
          </div>

          <div>
            <label class="block text-label text-ink-3 font-medium mb-2">Mot de passe</label>
            <div class="relative">
              <input 
                v-model="registerForm.password"
                type="password"
                required
                placeholder="••••••••"
                class="w-full px-4 py-3 bg-bg-2 border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                       focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
              >
              <Key class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-3" />
            </div>
          </div>

          <div>
            <label class="block text-label text-ink-3 font-medium mb-2">Confirmer le mot de passe</label>
            <div class="relative">
              <input 
                v-model="registerForm.password_confirmation"
                type="password"
                required
                placeholder="••••••••"
                class="w-full px-4 py-3 bg-bg-2 border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                       focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
              >
              <Shield class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-3" />
            </div>
          </div>

          <div v-if="registerError" class="bg-tag-tw-bg text-tag-tw-fg p-3 rounded-btn text-body">
            {{ registerError }}
          </div>

          <button 
            type="submit"
            :disabled="loading"
            class="w-full bg-accent text-white font-medium py-3 px-6 rounded-btn shadow-btn
                   hover:bg-[#7a2f2f] hover:shadow-btn-hover hover:-translate-y-0.5 
                   transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Créer mon compte</span>
            <span v-else class="flex items-center justify-center gap-2">
              <Loader2 class="w-4 h-4 animate-spin" />
              Inscription...
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Mail, Key, User, Shield, Loader2 } from 'lucide-vue-next'

definePageMeta({
  layout: 'public',
  middleware: 'auth'
})

const authStore = useAuthStore()
const router = useRouter()

const activeTab = ref('login')
const loading = ref(false)
const loginError = ref('')
const registerError = ref('')

const loginForm = reactive({
  email: '',
  password: ''
})

const registerForm = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const handleLogin = async () => {
  loading.value = true
  loginError.value = ''
  try {
    await authStore.login(loginForm.email, loginForm.password)
    router.push('/')
  } catch (error) {
    loginError.value = error.message || 'Email ou mot de passe incorrect'
  } finally {
    loading.value = false
  }
}

const handleRegister = async () => {
  loading.value = true
  registerError.value = ''
  try {
    await authStore.register(
      registerForm.name,
      registerForm.email,
      registerForm.password,
      registerForm.password_confirmation
    )
    router.push('/')
  } catch (error) {
    registerError.value = error.message || 'Erreur lors de l\'inscription'
  } finally {
    loading.value = false
  }
}
</script>