<script setup lang="ts">
import { onMounted, ref } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import MoviePlayer from '@/components/MoviePlayer.vue'
import { getVideo } from '@/api/videos'
import type { Video } from '@/types/video'

const props = defineProps<{ id: string }>()
const video = ref<Video | null>(null)
const error = ref('')

onMounted(async () => {
  try {
    video.value = await getVideo(props.id)
    document.title = `${video.value.name} - 看电影`
  } catch (reason) {
    error.value = reason instanceof Error ? reason.message : '影片加载失败'
  }
})
</script>

<template>
  <main class="page-shell player-page">
    <AppHeader />
    <RouterLink class="back-link" to="/">← 返回影片列表</RouterLink>
    <p v-if="error" class="state-message error">{{ error }}</p>
    <template v-else-if="video">
      <h1 class="player-title" :title="video.name">{{ video.name }}</h1>
      <MoviePlayer :video="video" />
    </template>
    <p v-else class="state-message">正在准备播放器…</p>
  </main>
</template>
