<template>
  <NuxtLink 
    :to="`/books/${book.id}`"
    class="bg-surface border border-line rounded-card shadow-sm p-4 
           hover:-translate-y-0.5 hover:shadow-md hover:border-accent-soft 
           transition-all duration-200 flex gap-4"
  >
    <AppBookCover :variant="book.cover_variant" size="md" :tome="book.saga?.tome_number" />
    
    <div class="flex-1 min-w-0">
      <h3 class="font-serif text-card-title text-ink truncate">{{ book.title }}</h3>
      <p class="text-body text-ink-2 mt-1">
        {{ book.authors?.map(a => a.name).join(', ') }}
      </p>
      
      <div class="flex flex-wrap gap-1.5 mt-3">
        <AppTag 
          v-for="genre in book.genres?.slice(0, 3)" 
          :key="genre.id"
          :label="genre.name"
          category="genre"
        />
      </div>
      
      <div class="flex items-center gap-3 mt-3 text-meta text-ink-3">
        <div class="flex items-center gap-1">
          <AppStarRating :rating="book.stats?.average_rating || 0" />
          <span>{{ book.stats?.average_rating || '-' }}</span>
        </div>
        <span>{{ book.page_count }} pages</span>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup>
defineProps({
  book: { type: Object, required: true }
})
</script>