<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'
import DPlayer from 'dplayer'
import Hls from 'hls.js'
import { apiRequest } from '@/api/http'
import { getCsrfToken } from '@/api/videos'
import type { Video } from '@/types/video'

const props = defineProps<{ video: Video }>()
const container = ref<HTMLElement | null>(null)
let player: { destroy: () => void } | null = null
let csrfToken = ''

type DanmakuPayload = {
  id: string
  author: string
  time: number
  text: string
  color: number
  type: number
  token?: string
}

onMounted(async () => {
  csrfToken = (await getCsrfToken()).token
  if (!container.value) return

  player = new DPlayer({
    container: container.value,
    autoplay: false,
    screenshot: true,
    airplay: true,
    theme: '#ff5d8f',
    video: {
      url: props.video.playUrl,
      pic: props.video.coverUrl ?? undefined,
      type: props.video.type === 'hls' ? 'customHls' : 'auto',
      customType: {
        customHls(video: HTMLVideoElement) {
          const hls = new Hls()
          hls.loadSource(video.src)
          hls.attachMedia(video)
        },
      },
    },
    danmaku: {
      id: props.video.id,
      api: `/api/v1/videos/${props.video.id}/danmaku`,
      token: csrfToken,
      user: getViewerId(),
      bottom: '15%',
      unlimited: true,
    },
    apiBackend: {
      read({ success, error }: { success: (data: unknown) => void; error: (message: string) => void }) {
        apiRequest(`/api/v1/videos/${props.video.id}/danmaku`).then(success).catch((reason) => error(reason.message))
      },
      send({ data, success, error }: {
        data: DanmakuPayload
        success: () => void
        error: (message: string) => void
      }) {
        apiRequest(`/api/v1/videos/${props.video.id}/danmaku`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
          body: JSON.stringify(data),
        }).then(success).catch((reason) => error(reason.message))
      },
    },
  })
})

onBeforeUnmount(() => player?.destroy())

function getViewerId(): string {
  const key = 'movie-viewer-id'
  const savedId = localStorage.getItem(key)
  if (savedId) {
    return savedId
  }
  const id = crypto.randomUUID().replace(/-/g, '').slice(0, 32)
  localStorage.setItem(key, id)
  return id
}
</script>

<template>
  <div ref="container" class="player-container" />
</template>
