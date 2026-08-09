<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <header class="app-header">
    <RouterLink class="brand" to="/" aria-label="返回影片列表">
      <span class="brand-mark">▶</span>
      <span>看电影</span>
    </RouterLink>
  </header>
</template>
