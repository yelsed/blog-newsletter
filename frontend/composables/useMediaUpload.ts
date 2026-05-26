import type { Ref } from 'vue'

export function useMediaUpload(emailId: Ref<number | null>, blockPath: Ref<string>) {
  const config = useRuntimeConfig()
  const { upload } = useApi()

  const uploading = ref(false)
  const uploadError = ref('')
  const previewUrl = ref('')

  // Populate thumbnail for existing blocks and keep in sync with path changes.
  // Only falls back to mediaBaseUrl when no direct URL from the upload response is available.
  watch(blockPath, (path) => {
    if (path && !previewUrl.value) {
      const base = (config.public.mediaBaseUrl as string).replace(/\/$/, '')
      if (base) previewUrl.value = `${base}/${path}`
    }
  }, { immediate: true })

  async function handleFile(event: Event, onUploaded: (path: string) => void): Promise<void> {
    const input = event.target as HTMLInputElement
    const file = input.files?.[0]
    if (!file) return

    uploading.value = true
    uploadError.value = ''

    const formData = new FormData()
    formData.append('file', file)
    if (emailId.value !== null) {
      formData.append('emailId', String(emailId.value))
    }

    try {
      const res = await upload<{ path: string, url: string }>('/admin/media', formData)
      previewUrl.value = res.url
      onUploaded(res.path)
    }
    catch (e) {
      uploadError.value = e instanceof Error ? e.message : 'Upload failed.'
    }
    finally {
      uploading.value = false
      input.value = ''
    }
  }

  return { uploading, uploadError, previewUrl, handleFile }
}
