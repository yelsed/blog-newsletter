<script setup lang="ts">
const { user, logout } = useAuth()

async function handleLogout() {
  await logout()
  await navigateTo('/admin/login')
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
      <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-6">
        <NuxtLink to="/admin" class="font-semibold text-slate-900">
          Admin
        </NuxtLink>
        <div v-if="user" class="flex items-center gap-4 text-sm text-slate-600">
          <span>{{ user.email }}</span>
          <button
            type="button"
            class="rounded-md border border-slate-200 px-3 py-1 text-slate-700 hover:bg-slate-100"
            @click="handleLogout"
          >
            Log out
          </button>
        </div>
      </div>
    </header>
    <main class="mx-auto w-full max-w-6xl flex-1 px-6 py-8">
      <slot />
    </main>
  </div>
</template>
