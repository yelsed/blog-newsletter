export default defineNuxtRouteMiddleware(async (to) => {
  const { user, isAdmin, fetchMe } = useAuth()

  if (!user.value) {
    await fetchMe()
  }

  const isPublicAuthRoute = to.path === '/admin/login' || to.path === '/admin/register'

  if (user.value && isAdmin.value && to.path === '/admin/login') {
    return navigateTo('/admin')
  }

  if ((!user.value || !isAdmin.value) && !isPublicAuthRoute) {
    return navigateTo('/admin/login')
  }
})
