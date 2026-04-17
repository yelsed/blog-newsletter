function readCookie(name: string): string | null {
  if (!import.meta.client) return null
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
  return match ? decodeURIComponent(match[2]) : null
}

export function useApi() {
  const config = useRuntimeConfig()
  const { locale } = useI18n()

  async function ensureCsrfCookie(): Promise<void> {
    const base = config.public.apiBase as string
    const origin = base.replace(/\/api\/?$/, '')
    await $fetch(`${origin}/sanctum/csrf-cookie`, { credentials: 'include' })
  }

  async function api<T>(path: string, options: Record<string, unknown> = {}): Promise<T> {
    const method = (options.method as string | undefined ?? 'GET').toString().toUpperCase()

    if (method !== 'GET' && method !== 'HEAD') {
      await ensureCsrfCookie()
    }

    const xsrf = readCookie('XSRF-TOKEN')

    const headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'Accept-Language': locale.value,
      ...(xsrf ? { 'X-XSRF-TOKEN': xsrf } : {}),
      ...(options.headers as Record<string, string> | undefined ?? {}),
    }

    const response = await $fetch<T>(`${config.public.apiBase}${path}`, {
      ...options,
      credentials: 'include',
      headers,
    })

    return response
  }

  return { api }
}
