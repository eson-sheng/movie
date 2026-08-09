import { createRouter, createWebHistory } from 'vue-router'

export const router = createRouter({
  history: createWebHistory(),
  scrollBehavior: () => ({ top: 0 }),
  routes: [
    { path: '/', name: 'videos', component: () => import('@/views/MovieListView.vue') },
    {
      path: '/videos/:id',
      name: 'player',
      component: () => import('@/views/MoviePlayerView.vue'),
      props: true,
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})
