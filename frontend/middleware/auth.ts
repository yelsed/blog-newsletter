export default defineNuxtRouteMiddleware(async (to) => {
  const { user, isAdmin, fetchMe } = useAuth()

  if (!user.value) {
    await fetchMe()
  }

  if (!user.value || !isAdmin.value) {
    if (to.path !== '/admin/login' && to.path !== '/admin/register') {
      return navigateTo('/admin/login')
    }
  }
})
