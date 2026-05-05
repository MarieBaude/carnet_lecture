<template>
  <div>
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 5" :key="i" class="flex gap-4 animate-pulse">
        <div class="w-8 h-8 bg-bg-2 rounded-full" />
        <div class="flex-1 space-y-2">
          <div class="h-4 bg-bg-2 rounded w-3/4" />
          <div class="h-3 bg-bg-2 rounded w-1/4" />
        </div>
      </div>
    </div>

    <div v-else-if="activities.length" class="space-y-4">
      <div v-for="activity in activities" :key="activity.id">
        <!-- Simple -->
        <div
          v-if="isSimple(activity.type)"
          class="flex items-center gap-3 py-2 px-1"
        >
          <component :is="iconByType(activity.type)" class="w-4 h-4 text-ink-3 flex-shrink-0" />
          <p class="text-body text-ink-2 flex-1">
            <NuxtLink v-if="activity.user" :to="`/profile/${activity.user.id}`" class="font-medium text-ink hover:text-accent">
              {{ activity.user.name }}
            </NuxtLink>
            {{ simpleText(activity) }}
            <NuxtLink v-if="activity.book" :to="`/books/${activity.book.id}`" class="font-serif italic text-ink hover:text-accent">
              {{ activity.book.title }}
            </NuxtLink>
          </p>
          <span class="text-meta text-ink-3 flex-shrink-0">{{ relativeTime(activity.created_at) }}</span>
        </div>

        <!-- Important -->
        <div
          v-else
          class="bg-surface border border-line rounded-card p-4 flex gap-4"
        >
          <NuxtLink v-if="activity.book" :to="`/books/${activity.book.id}`" class="flex-shrink-0">
            <AppBookCover :variant="activity.book.cover_variant || 1" size="sm" />
          </NuxtLink>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <NuxtLink v-if="activity.user" :to="`/profile/${activity.user.id}`" class="font-medium text-ink hover:text-accent text-sm">
                {{ activity.user.name }}
              </NuxtLink>
              <span :class="['text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full', badgeClass(activity.type)]">
                {{ typeLabel(activity.type) }}
              </span>
            </div>
            <p class="text-body text-ink-2">
              {{ importantText(activity) }}
              <NuxtLink v-if="activity.book" :to="`/books/${activity.book.id}`" class="font-serif italic text-ink hover:text-accent">
                {{ activity.book.title }}
              </NuxtLink>
            </p>
            <div v-if="activity.type === 'rated' && activity.metadata?.rating" class="flex items-center gap-2 mt-2">
              <AppStarRating :rating="activity.metadata.rating" />
              <span class="text-meta text-ink-3">{{ activity.metadata.rating }}/5</span>
            </div>
            <p v-if="activity.type === 'commented' && activity.metadata?.comment" class="text-body text-ink-3 italic mt-2 line-clamp-2">
              « {{ activity.metadata.comment }} »
            </p>
            <p class="text-meta text-ink-3 mt-2">{{ relativeTime(activity.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <AppEmptyState v-else title="Aucune activité" description="Le fil d'activité est vide pour le moment." />
  </div>
</template>

<script setup>
import { Bookmark, BookmarkMinus, RefreshCw, CheckCircle, Star, MessageSquare, Library } from 'lucide-vue-next'
const { relativeTime } = useProfile()

const props = defineProps({
  activities: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false }
})

const isSimple = (type) => ['added_to_library', 'removed_from_library', 'status_changed'].includes(type)

const iconByType = (type) => {
  const map = {
    added_to_library: Bookmark,
    removed_from_library: BookmarkMinus,
    status_changed: RefreshCw,
    reading_finished: CheckCircle,
    rated: Star,
    commented: MessageSquare
  }
  return map[type] || Library
}

const badgeClass = (type) => {
  const map = {
    reading_finished: 'bg-[#e2ebe5] text-[#3d5a47]',
    rated: 'bg-[#fef3c7] text-[#92400e]',
    commented: 'bg-[#ebe6f0] text-[#574d68]'
  }
  return map[type] || 'bg-bg-2 text-ink-3'
}

const typeLabel = (type) => {
  const map = {
    reading_finished: 'Lecture terminée',
    rated: 'A noté',
    commented: 'A commenté',
    added_to_library: 'Ajouté',
    removed_from_library: 'Retiré',
    status_changed: 'Statut modifié'
  }
  return map[type] || type
}

const simpleText = (activity) => {
  const map = {
    added_to_library: 'a ajouté ',
    removed_from_library: 'a retiré ',
    status_changed: 'a changé le statut de '
  }
  return map[activity.type] || ''
}

const importantText = (activity) => {
  const map = {
    reading_finished: 'a terminé sa lecture de ',
    rated: 'a noté ',
    commented: 'a commenté sur '
  }
  return map[activity.type] || ''
}
</script>