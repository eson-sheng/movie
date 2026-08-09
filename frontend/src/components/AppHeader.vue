<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <header class="app-header">
    <RouterLink class="brand" to="/" aria-label="返回影片列表">
      <img
        class="brand-logo"
        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAB+klEQVRYR9WXTVIaURDH//9H4opCsrDMApAbyA3iDeINMgXZyw2AE2Tc54Mj6AnUEyRHQMsF6MIqd1rw2uqZeuOAoRgfUw72juG97l/39NcQBQvV/m3t+8GH6XT0aTwcOR59BswkL76d6z8XTtddNahOy9zXZxzXOiENjgAZ7F797rtDk0YnN+Oqc/fqV+Ssc9gaOROLY47r7XOSXwoBELl4BhAZGpGho7TGnOcVftVjrD1w+mbGtAiEMgeQp7WMujYLQIBLiCRVEOdFfqLeOm0EqiD3FyJQQBXM58B7ARCcgtCM3n7NC/pvH0hHQCAjCFM5EBl5IapIO9lDudSPG1g2EUFS1iSqAFpeVZD25O5z0Hz8WApBfM2GMX9qbYB0a53R9l9bObkBJPOj3jkUIiSwlyUiXmWYfgXLjIwb7S5BHWxJoq5MwqzDKAtAlKQV85dg00G+CYAafqwYrYwuQM30RFYDZJyGyyJwU28HQvxYNOwIcpuGiwBq2BK9dLizJKCeWasKdGWbUXqMu6KXeAFQJLRgax3DjnYOIPrB53YJsOfl1tJLMkj+EjZBfPPqA75Qq6ugiK14w/YB3+D63/OqAn9zL29uBsCk3jmJF4q33wkhOI3Xq4oJStb827n+maxNk0Y7+U7MI+zp785ok9oywda91d2hWHkCzY/DpdtfKJAAAAAASUVORK5CYII="
        alt="看电影"
      />
      <span>看电影</span>
    </RouterLink>
  </header>
</template>
