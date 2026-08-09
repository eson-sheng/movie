<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'
import Hls from 'hls.js'
import { apiRequest } from '@/api/http'
import { getCsrfToken } from '@/api/videos'
import type { Video } from '@/types/video'

const props = defineProps<{ video: Video }>()
const container = ref<HTMLElement | null>(null)
const needsPlaybackInteraction = ref(false)
let player: { destroy: () => void; video: HTMLVideoElement } | null = null
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

type DanmakuRecord = [
  time: number,
  type: number,
  color: number,
  author: string,
  text: string,
]

onMounted(async () => {
  csrfToken = (await getCsrfToken()).token
  if (!container.value) return

  const DPlayer = await loadDPlayer()
  const instance = createPlayer(DPlayer, {
    container: container.value,
    autoplay: false,
    loop: true,
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
        apiRequest<DanmakuRecord[]>(`/api/v1/videos/${props.video.id}/danmaku`)
          .then((records) => success(records.map(([time, type, color, author, text]) => ({
            time,
            type,
            color,
            author,
            text,
          }))))
          .catch((reason) => error(reason.message))
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
  }) as { destroy: () => void; video: HTMLVideoElement }

  player = instance
  void startPlayback()
})

onBeforeUnmount(() => player?.destroy())

async function startPlayback(): Promise<void> {
  if (!player) return

  try {
    player.video.defaultMuted = false
    player.video.muted = false
    await player.video.play()
    needsPlaybackInteraction.value = false
  } catch {
    needsPlaybackInteraction.value = true
  }
}

function getViewerId(): string {
  const key = 'movie-viewer-id'
  const savedId = localStorage.getItem(key)
  if (savedId) {
    return savedId
  }
  const id = createViewerId()
  localStorage.setItem(key, id)
  return id
}

function createViewerId(): string {
  if (typeof crypto !== 'undefined' && typeof crypto.getRandomValues === 'function') {
    const bytes = crypto.getRandomValues(new Uint8Array(16))
    return Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0')).join('')
  }

  return `${Date.now().toString(16)}${Math.random().toString(16).slice(2)}`
    .padEnd(32, '0')
    .slice(0, 32)
}

type DPlayerConstructor = new (options: unknown) => {
  destroy: () => void
  video: HTMLVideoElement
}

async function loadDPlayer(): Promise<DPlayerConstructor> {
  const originalLog = console.log

  try {
    console.log = (...args: unknown[]) => {
      if (args.some((arg) => typeof arg === 'string' && arg.includes('DPlayer v'))) {
        return
      }
      originalLog.apply(console, args)
    }

    const module = await import('dplayer')
    return module.default as DPlayerConstructor
  } finally {
    console.log = originalLog
  }
}

function createPlayer(
  DPlayer: DPlayerConstructor,
  options: unknown,
): { destroy: () => void; video: HTMLVideoElement } {
  const instance = new DPlayer(options)

  console.log(
    '\n %c Eson二次开发 %c https://video.shengxuecheng.cn/ \n\n',
    'color: pink; background: #030307; padding: 5px 0;',
    'background: pink; padding: 5px 0;',
  )

  return instance
}
</script>

<template>
  <div class="player-wrapper">
    <div ref="container" class="player-container" />
    <button
      v-if="needsPlaybackInteraction"
      class="sound-play-button"
      type="button"
      @click="startPlayback"
    >
      <span>▶</span>
    </button>
  </div>
</template>

<style scoped>
.player-wrapper {
  position: relative;
}

.sound-play-button {
  position: absolute;
  inset: 50% auto auto 50%;
  z-index: 20;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 22px;
  border: 0;
  border-radius: 999px;
  color: #17131a;
  background: #fff;
  box-shadow: 0 12px 40px #000a;
  cursor: pointer;
  font-weight: 700;
  transform: translate(-50%, -50%);
}

.sound-play-button:hover {
  transform: translate(-50%, -50%) scale(1.04);
}
</style>
