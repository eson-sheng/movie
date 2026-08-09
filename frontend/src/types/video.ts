export type VideoType = 'mp4' | 'hls'

export interface Video {
  id: string
  name: string
  type: VideoType
  playUrl: string
  coverUrl: string | null
  danmakuEnabled: boolean
}

export interface ApiResponse<T> {
  code: number
  message: string
  data: T
}
