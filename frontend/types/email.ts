export type BlockAlign = 'left' | 'center' | 'right'

type WithUid = { _uid: string }

export type TextBlock = WithUid & { type: 'text', body: string, align: BlockAlign }
export type LinkBlock = WithUid & { type: 'link', label: string, href: string, align: BlockAlign }
export type ListBlock = WithUid & { type: 'list', items: string[], ordered: boolean }
export type ImageBlock = WithUid & { type: 'image', url: string, alt: string, width: number | null, href: string | null }
export type GifBlock = WithUid & { type: 'gif', url: string, alt: string, width: number | null }
export type ButtonBlock = WithUid & { type: 'button', label: string, href: string, align: BlockAlign }

export type Block = TextBlock | LinkBlock | ListBlock | ImageBlock | GifBlock | ButtonBlock
export type BlockType = Block['type']

export type EmailStatus = 'draft' | 'sent'

export type Email = {
  id: number | null
  subject: string
  status: EmailStatus
  blocks: Block[]
  sent_at: string | null
  created_at: string | null
  updated_at: string | null
}

function newUid(): string {
  return typeof crypto !== 'undefined' && 'randomUUID' in crypto
    ? crypto.randomUUID()
    : `b_${Math.random().toString(36).slice(2)}_${Date.now().toString(36)}`
}

export function ensureUid<T extends { _uid?: string }>(block: T): T & WithUid {
  return block._uid ? (block as T & WithUid) : { ...block, _uid: newUid() }
}

export function stripUid<T extends Block>(block: T): Omit<T, '_uid'> {
  const { _uid, ...rest } = block
  void _uid
  return rest
}

export function blockDefaults(type: BlockType): Block {
  const _uid = newUid()
  switch (type) {
    case 'text':   return { _uid, type: 'text', body: '', align: 'left' }
    case 'link':   return { _uid, type: 'link', label: '', href: '', align: 'left' }
    case 'list':   return { _uid, type: 'list', items: [''], ordered: false }
    case 'image':  return { _uid, type: 'image', url: '', alt: '', width: null, href: null }
    case 'gif':    return { _uid, type: 'gif', url: '', alt: '', width: null }
    case 'button': return { _uid, type: 'button', label: '', href: '', align: 'left' }
  }
}
