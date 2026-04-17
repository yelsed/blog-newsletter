export type BlockAlign = 'left' | 'center' | 'right'

export type TextBlock = { type: 'text', body: string, align: BlockAlign }
export type LinkBlock = { type: 'link', label: string, href: string, align: BlockAlign }
export type ListBlock = { type: 'list', items: string[], ordered: boolean }
export type ImageBlock = { type: 'image', url: string, alt: string, width: number | null, href: string | null }
export type GifBlock = { type: 'gif', url: string, alt: string, width: number | null }
export type ButtonBlock = { type: 'button', label: string, href: string, align: BlockAlign }

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

export function blockDefaults(type: BlockType): Block {
  switch (type) {
    case 'text':   return { type: 'text', body: '', align: 'left' }
    case 'link':   return { type: 'link', label: '', href: '', align: 'left' }
    case 'list':   return { type: 'list', items: [''], ordered: false }
    case 'image':  return { type: 'image', url: '', alt: '', width: null, href: null }
    case 'gif':    return { type: 'gif', url: '', alt: '', width: null }
    case 'button': return { type: 'button', label: '', href: '', align: 'left' }
  }
}
