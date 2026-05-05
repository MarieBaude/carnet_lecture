<template>
  <Teleport to="body">
    <div
      v-if="open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-ink/30 backdrop-blur-sm"
      @click.self="$emit('close')"
    >
      <div class="bg-surface rounded-card shadow-lg border border-line p-6 w-full max-w-md mx-4 animate-fade-in-up">
        <h3 class="font-serif text-xl text-ink mb-4">
          {{ book?.user_status ? 'Modifier' : 'Ajouter à ma bibliothèque' }}
        </h3>

        <!-- Sélecteur de statut -->
        <div class="grid grid-cols-2 gap-2 mb-5">
          <button
            v-for="status in statuses"
            :key="status.value"
            @click="form.status = status.value"
            :class="[
              'flex items-center gap-2 px-3 py-2.5 rounded-btn border text-body transition-all',
              form.status === status.value
                ? 'border-accent bg-accent-soft text-accent'
                : 'border-line text-ink-2 hover:border-accent-soft'
            ]"
          >
            <component :is="status.icon" class="w-4 h-4" />
            {{ status.label }}
          </button>
        </div>

        <!-- Note si "Lu" -->
        <div v-if="form.status === 'read'" class="mb-4">
          <label class="block text-label text-ink-3 font-medium mb-2">Ma note</label>
          <div class="flex gap-1">
            <button
              v-for="star in 5"
              :key="star"
              @click="form.rating = star"
              class="transition-colors"
            >
              <Star
                :class="[
                  'w-6 h-6',
                  star <= form.rating ? 'fill-star text-star' : 'text-[#e1d6c5]'
                ]"
              />
            </button>
          </div>
        </div>

        <!-- Page actuelle si "En cours" -->
        <div v-if="form.status === 'reading' && book?.page_count" class="mb-4">
          <label class="block text-label text-ink-3 font-medium mb-2">
            Page actuelle (sur {{ book.page_count }})
          </label>
          <input
            v-model.number="form.current_page"
            type="number"
            min="0"
            :max="book.page_count"
            class="w-full px-4 py-2.5 bg-bg-2 border border-line rounded-btn text-body text-ink
                   focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft"
          />
        </div>

        <!-- Étagère -->
        <div class="mb-4">
          <label class="block text-label text-ink-3 font-medium mb-2">Étagère</label>
          <select
            v-model="form.shelf_id"
            class="w-full px-4 py-2.5 bg-bg-2 border border-line rounded-btn text-body text-ink
                   focus:outline-none focus:border-accent-2"
          >
            <option :value="null">Aucune</option>
            <option v-for="shelf in shelves" :key="shelf.id" :value="shelf.id">
              {{ shelf.name }}
            </option>
          </select>
        </div>

        <!-- Erreur -->
        <div v-if="error" class="bg-tag-tw-bg text-tag-tw-fg p-3 rounded-btn text-body mb-4">
          {{ error }}
        </div>

        <!-- Actions -->
        <div class="flex gap-3 justify-end">
          <AppButton variant="ghost" @click="$emit('close')">Annuler</AppButton>
          <AppButton @click="handleSubmit" :disabled="loading">
            <Loader2 v-if="loading" class="w-4 h-4 animate-spin" />
            {{ book?.user_status ? 'Modifier' : 'Ajouter' }}
          </AppButton>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { Bookmark, BookOpen, CheckCircle, XCircle, ShoppingBag, Star, Loader2 } from 'lucide-vue-next'

const props = defineProps({
  open: { type: Boolean, default: false },
  book: { type: Object, default: null }
})

const emit = defineEmits(['close', 'saved'])

const { addToLibrary, updateBook, getShelves } = useLibrary()

const loading = ref(false)
const error = ref('')
const shelves = ref([])

const statuses = [
  { label: 'À lire', value: 'wishlist', icon: Bookmark },
  { label: 'Possédé', value: 'owned', icon: ShoppingBag },
  { label: 'En cours', value: 'reading', icon: BookOpen },
  { label: 'Lu', value: 'read', icon: CheckCircle },
  { label: 'Abandonné', value: 'dropped', icon: XCircle }
]

const form = reactive({
  status: 'wishlist',
  rating: 0,
  current_page: 0,
  shelf_id: null
})

// Pré-remplir si déjà dans la bibliothèque
watch(() => props.book, (book) => {
  if (book?.user_status) {
    form.status = book.user_status.status || 'wishlist'
    form.rating = book.user_status.rating || 0
    form.current_page = book.user_status.current_page || 0
    form.shelf_id = book.user_status.shelf_id || null
  } else {
    form.status = 'wishlist'
    form.rating = 0
    form.current_page = 0
    form.shelf_id = null
  }
}, { immediate: true })

const loadShelves = async () => {
  try {
    shelves.value = await getShelves()
  } catch (e) { /* ignore */ }
}

const handleSubmit = async () => {
  if (!props.book) return
  loading.value = true
  error.value = ''

  try {
    const data: Record<string, any> = {
      status: form.status,
      shelf_id: form.shelf_id
    }
    if (form.status === 'read') {
      data.rating = form.rating
    }
    if (form.status === 'reading') {
      data.current_page = form.current_page
    }

    if (props.book.user_status) {
      await updateBook(props.book.user_status.id, data)
    } else {
      await addToLibrary(props.book.id, data)
    }
    emit('saved')
    emit('close')
  } catch (e: any) {
    error.value = e.message || 'Erreur'
  } finally {
    loading.value = false
  }
}

watch(() => props.open, (val) => {
  if (val) loadShelves()
})
</script>