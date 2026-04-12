export function useApi() {
  const config = useRuntimeConfig()

  async function api<T>(path: string, options?: RequestInit): Promise<T> {
    const response = await $fetch<T>(`${config.public.apiBase}${path}`, {
      ...options,
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...options?.headers,
      },
    })

    return response
  }

  return { api }
}
