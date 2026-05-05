<template>
  <div class="bg-surface border border-line rounded-card p-5">
    <textarea
      v-model="body"
      rows="3"
      placeholder="Partagez votre avis sur ce livre…"
      class="w-full px-4 py-3 bg-bg-2 border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
             focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all resize-none"
    />
    <div class="flex justify-between items-center mt-3">
      <span class="text-meta text-ink-3">{{ body.length }} caractères</span>
      <AppButton size="sm" @click="submit" :disabled="!body.trim() || submitting">
        <Loader2 v-if="submitting" class="w-3.5 h-3.5 animate-spin" />
        Publier
      </AppButton>
    </div>
    <div v-if="error" class="mt-2 text-sm text-tag-tw-fg">{{ error }}</div>
  </div>
</template>

<script setup>
import { Loader2 } from 'lucide-vue-next'
const { fetch } = useApi()
const emit = defineEmits(['published'])

const props = defineProps({
  bookId: { type: [Number, String], required: true }
})

const body = ref('')
const submitting = ref(false)
const error = ref('')

const submit = async () => {
  if (!body.value.trim()) return
  submitting.value = true
  error.value = ''
  try {
    await fetch(`/books/${props.bookId}/comments`, {
      method: 'POST',
      body: { body: body.value }
    })
    body.value = ''
    emit('published')
  } catch (e) {
    error.value = e.message || 'Erreur'
  } finally {
    submitting.value = false
  }
}
</script>