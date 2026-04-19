function readCookie(name: string): string | null {
  if (!import.meta.client) return null
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
  return match ? decodeURIComponent(match[2]) : null
}

export class ApiError extends Error {
  status: number
  errors: Record<string, string[]>
  constructor(message: string, status: number, errors: Record<string, string[]> = {}) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.errors = errors
  }
}

function toApiError(raw: unknown): ApiError {
  const err = raw as {
    status?: number
    statusCode?: number
    data?: { message?: string, errors?: Record<string, string[]> }
    message?: string
  }
  const status = err.status ?? err.statusCode ?? 0
  const data = err.data ?? {}
  const errors = data.errors ?? {}
  const firstFieldMessage = Object.values(errors).flat()[0]
  const message = data.message ?? firstFieldMessage ?? err.message ?? 'Request failed.'
  return new ApiError(message, status, errors)
}

export function useApi() {
  const config = useRuntimeConfig()
  const nuxtApp = useNuxtApp()

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
      'Accept-Language': nuxtApp.$i18n.locale.value,
      ...(xsrf ? { 'X-XSRF-TOKEN': xsrf } : {}),
      ...(options.headers as Record<string, string> | undefined ?? {}),
    }

    try {
      return await $fetch<T>(`${config.public.apiBase}${path}`, {
        ...options,
        credentials: 'include',
        headers,
      })
    }
    catch (e) {
      throw toApiError(e)
    }
  }

  return { api }
}
