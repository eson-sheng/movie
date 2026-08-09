import { apiRequest } from './http'
import type { Video } from '@/types/video'

export function getVideos(): Promise<{ items: Video[] }> {
  return apiRequest('/api/v1/videos')
}

export function getVideo(id: string): Promise<Video> {
  return apiRequest(`/api/v1/videos/${encodeURIComponent(id)}`)
}

export function getCsrfToken(): Promise<{ token: string }> {
  return apiRequest('/api/v1/csrf-token')
}
