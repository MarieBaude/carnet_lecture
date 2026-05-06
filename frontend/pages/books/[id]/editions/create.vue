<template>
  <div>
    <AppBreadcrumb :items="breadcrumbs" class="mb-6" />

    <h1 class="font-serif text-section text-ink mb-8">Ajouter une édition</h1>

    <form @submit.prevent="handleSubmit" class="max-w-2xl space-y-6">
      <!-- Format -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">
          Format <span class="text-accent">*</span>
        </label>
        <select
          v-model="form.format"
          required
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink focus:outline-none focus:border-accent-2"
        >
          <option value="">Sélectionner un format</option>
          <option value="broché">Broché</option>
          <option value="poche">Poche</option>
          <option value="grand_format">Grand format</option>
          <option value="numérique">Numérique</option>
          <option value="audio">Audio</option>
        </select>
      </div>

      <!-- ISBN -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">ISBN</label>
        <input
          v-model="form.isbn"
          type="text"
          placeholder="ISBN-10 ou ISBN-13"
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                 focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
        />
      </div>

      <!-- Pages -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Nombre de pages</label>
        <input
          v-model.number="form.page_count"
          type="number"
          min="1"
          placeholder="Ex: 320"
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                 focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
        />
      </div>

      <!-- Langue -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Langue</label>
        <select
          v-model="form.language"
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink focus:outline-none focus:border-accent-2"
        >
          <option value="fr">Français</option>
          <option value="en">English</option>
          <option value="es">Español</option>
          <option value="de">Deutsch</option>
          <option value="it">Italiano</option>
          <option value="other">Autre</option>
        </select>
      </div>

      <!-- Date de parution -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Date de parution</label>
        <input
          v-model="form.published_date"
          type="date"
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink focus:outline-none focus:border-accent-2"
        />
      </div>

      <!-- Éditeur -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Éditeur</label>
        <input
          v-model="form.publisher"
          type="text"
          placeholder="Nom de l'éditeur"
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                 focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
        />
      </div>

      <!-- Traducteur -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Traducteur</label>
        <input
          v-model="form.translator"
          type="text"
          placeholder="Nom du traducteur (optionnel)"
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                 focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
        />
      </div>

      <!-- Couverture -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Couverture</label>
        <input type="file" accept="image/*" @change="handleUpload" class="text-body text-ink-2" />
        <div v-if="coverPreview" class="mt-3 w-32">
          <img :src="coverPreview" class="rounded-sm shadow-md" />
        </div>
      </div>

      <!-- Erreur -->
      <div v-if="error" class="bg-tag-tw-bg text-tag-tw-fg p-4 rounded-btn text-body">{{ error }}</div>

      <!-- Actions -->
      <div class="flex gap-3">
        <AppButton type="submit" :disabled="submitting">
          <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
          {{ submitting ? 'Ajout en cours...' : 'Ajouter l\'édition' }}
        </AppButton>
        <AppButton variant="ghost" @click="router.push(`/books/${route.params.id}`)">
          Annuler
        </AppButton>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Loader2 } from 'lucide-vue-next'

definePageMeta({ layout: 'default', middleware: 'auth' })

const route = useRoute()
const router = useRouter()
const { fetch } = useApi()

const coverPreview = ref('')
const submitting = ref(false)
const error = ref('')

const form = reactive({
  format: '',
  isbn: '',
  page_count: null,
  language: 'fr',
  published_date: '',
  publisher: '',
  translator: '',
  cover_image: null
})

const breadcrumbs = computed(() => [
  { label: 'Catalogue', to: '/books' },
  { label: 'Livre', to: `/books/${route.params.id}` },
  { label: 'Ajouter une édition' }
])

const handleUpload = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  coverPreview.value = URL.createObjectURL(file)
  const formData = new FormData()
  formData.append('image', file)
  try {
    const res = await fetch('/upload', { method: 'POST', body: formData, headers: {} })
    form.cover_image = res.data?.url || res.url
  } catch (err) { /* ignore */ }
}

const handleSubmit = async () => {
  if (!form.format) { error.value = 'Le format est obligatoire'; return }

  submitting.value = true
  error.value = ''

  try {
    await fetch(`/books/${route.params.id}/editions`, {
      method: 'POST',
      body: { ...form, page_count: form.page_count || undefined }
    })
    router.push(`/books/${route.params.id}`)
  } catch (e) {
    error.value = e.message || 'Erreur lors de l\'ajout'
  } finally {
    submitting.value = false
  }
}
</script>