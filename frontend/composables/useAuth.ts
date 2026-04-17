import { startAuthentication, startRegistration } from '@simplewebauthn/browser'

export type AuthUser = {
  id: number
  name: string
  email: string
  roles: string[]
}

export function useAuth() {
  const user = useState<AuthUser | null>('auth.user', () => null)
  const { api } = useApi()

  const isAuthenticated = computed(() => user.value !== null)
  const isAdmin = computed(() => user.value?.roles.includes('admin') ?? false)

  async function fetchMe(): Promise<AuthUser | null> {
    try {
      const me = await api<AuthUser>('/user')
      user.value = me
      return me
    }
    catch {
      user.value = null
      return null
    }
  }

  async function login(email?: string): Promise<void> {
    const options = await api<PublicKeyCredentialRequestOptionsJSON>('/auth/session', {
      method: 'GET',
      ...(email ? { query: { email } } : {}),
    })

    const assertion = await startAuthentication({ optionsJSON: options })

    await api('/auth/session', {
      method: 'POST',
      body: JSON.stringify(assertion),
    })

    await fetchMe()
  }

  async function logout(): Promise<void> {
    await api('/auth/session', { method: 'DELETE' })
    user.value = null
  }

  async function enrollPasskey(token: string): Promise<AuthUser> {
    const me = await api<AuthUser>(`/auth/enroll/${encodeURIComponent(token)}`, {
      method: 'POST',
    })
    user.value = me

    const options = await api<PublicKeyCredentialCreationOptionsJSON>('/auth/passkey')
    const attestation = await startRegistration({ optionsJSON: options })

    await api('/auth/passkey', {
      method: 'POST',
      body: JSON.stringify(attestation),
    })

    return me
  }

  return { user, isAuthenticated, isAdmin, fetchMe, login, logout, enrollPasskey }
}

type PublicKeyCredentialRequestOptionsJSON = Parameters<typeof startAuthentication>[0]['optionsJSON']
type PublicKeyCredentialCreationOptionsJSON = Parameters<typeof startRegistration>[0]['optionsJSON']
