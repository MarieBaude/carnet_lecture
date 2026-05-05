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
      <div
        v-for="activity in activities"
        :key="activity.id"
        class="flex gap-4 items-start"
      >
        <!-- Icône -->
        <div :class="[
          'w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0',
          iconBg(activity.type)
        ]">
          <component :is="iconByType(activity.type)" class="w-4 h-4" :class="iconColor(activity.type)" />
        </div>

        <div class="flex-1 min-w-0">
          <p class="text-body text-ink">
            <NuxtLink
              v-if="activity.user?.id"
              :to="`/profile/${activity.user.id}`"
              class="font-medium text-ink hover:text-accent"
            >
              {{ activity.user?.name }}
            </NuxtLink>
            <span class="text-ink-2">
              {{ descriptionByType(activity) }}
            </span>
            <NuxtLink
              v-if="activity.book"
              :to="`/books/${activity.book.id}`"
              class="font-serif italic text-ink hover:text-accent"
            >
              {{ activity.book?.title }}
            </NuxtLink>
          </p>
          <p class="text-meta text-ink-3 mt-0.5">{{ relativeTime(activity.created_at) }}</p>
        </div>

        <!-- Mini couverture -->
        <NuxtLink v-if="activity.book" :to="`/books/${activity.book.id}`" class="flex-shrink-0">
          <AppBookCover :variant="activity.book.cover_variant || 1" size="sm" />
        </NuxtLink>
      </div>
    </div>

    <AppEmptyState v-else title="Aucune activité" description="Le fil d'activité est vide pour le moment." />
  </div>
</template>

<script setup>
import { Bookmark, BookOpen, CheckCircle, Heart, Star, Library, RefreshCw } from 'lucide-vue-next'

const props = defineProps({
  activities: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false }
})

const { relativeTime } = useProfile()

const iconByType = (type) => {
  const map = {
    added: Library,
    status_change: RefreshCw,
    completed: CheckCircle,
    rated: Star,
    reviewed: Heart,
    wishlist: Bookmark
  }
  return map[type] || Library
}

const iconBg = (type) => {
  const map = {
    added: 'bg-accent-soft',
    status_change: 'bg-[#ebe6f0]',
    completed: 'bg-[#e2ebe5]',
    rated: 'bg-[#f3e2dc]',
    reviewed: 'bg-accent-soft',
    wishlist: 'bg-[#ebe6f0]'
  }
  return map[type] || 'bg-bg-2'
}

const iconColor = (type) => {
  const map = {
    added: 'text-accent',
    status_change: 'text-[#574d68]',
    completed: 'text-[#3d5a47]',
    rated: 'text-[#7d3a31]',
    reviewed: 'text-accent',
    wishlist: 'text-[#574d68]'
  }
  return map[type] || 'text-ink-3'
}

const descriptionByType = (activity) => {
  const map = {
    added: 'a ajouté ',
    status_change: 'a changé le statut de ',
    completed: 'a terminé ',
    rated: 'a noté ',
    reviewed: 'a chroniqué ',
    wishlist: 'a mis en wishlist '
  }
  return map[activity.type] || 'a interagi avec '
}
</script>