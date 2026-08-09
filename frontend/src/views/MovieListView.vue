<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import { getVideos } from '@/api/videos'
import type { Video } from '@/types/video'

const route = useRoute()
const router = useRouter()
const videos = ref<Video[]>([])
const loading = ref(true)
const error = ref('')
const keyword = ref(typeof route.query.keyword === 'string' ? route.query.keyword : '')

const filteredVideos = computed(() => {
  const query = keyword.value.trim().toLocaleLowerCase()
  return query ? videos.value.filter((video) => video.name.toLocaleLowerCase().includes(query)) : videos.value
})

watch(keyword, (value) => {
  const query = value.trim() ? { keyword: value } : {}
  router.replace({ query })
})

onMounted(async () => {
  try {
    videos.value = (await getVideos()).items
  } catch (reason) {
    error.value = reason instanceof Error ? reason.message : '影片加载失败'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <main class="page-shell">
    <AppHeader />
    <section class="hero">
      <p class="eyebrow">MY MOVIE LIBRARY</p>
      <h1>今晚想看点什么？</h1>
      <p>从本地影片库中搜索并播放 MP4 或 HLS 视频。</p>
      <label class="search-box">
        <span aria-hidden="true">⌕</span>
        <input v-model="keyword" type="search" placeholder="搜索影片名称" autocomplete="off" />
      </label>
    </section>

    <p v-if="loading" class="state-message">正在读取影片库…</p>
    <p v-else-if="error" class="state-message error">{{ error }}</p>
    <p v-else-if="filteredVideos.length === 0" class="state-message">没有找到匹配的影片。</p>

    <section v-else class="movie-grid" aria-label="影片列表">
      <RouterLink v-for="video in filteredVideos" :key="video.id" class="movie-card" :to="`/videos/${video.id}`">
        <div class="cover">
          <img v-if="video.coverUrl" :src="video.coverUrl" :alt="video.name" loading="lazy" />
          <div v-else class="cover-placeholder">▶</div>
          <span class="play-button">▶</span>
          <span class="format-badge">{{ video.type.toUpperCase() }}</span>
        </div>
        <h2>{{ video.name }}</h2>
      </RouterLink>
    </section>
  </main>
</template>
