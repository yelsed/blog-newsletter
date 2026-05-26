import { readdirSync, readFileSync, statSync } from 'node:fs'
import { resolve, dirname, join, relative, posix } from 'node:path'
import { fileURLToPath } from 'node:url'

const __dirname = dirname(fileURLToPath(import.meta.url))
const imagesRoot = resolve(__dirname, '../images')

const zone = process.env.BUNNY_STORAGE_ZONE
const endpoint = process.env.BUNNY_STORAGE_ENDPOINT || 'storage.bunnycdn.com'
const apiKey = process.env.BUNNY_STORAGE_API_KEY

if (!zone || !apiKey) {
  console.log('[sync-images] BUNNY_STORAGE_ZONE or BUNNY_STORAGE_API_KEY missing — skipping brand asset sync.')
  process.exit(0)
}

function* walk(dir) {
  for (const entry of readdirSync(dir, { withFileTypes: true })) {
    const fullPath = join(dir, entry.name)
    if (entry.isDirectory()) {
      yield* walk(fullPath)
    } else if (entry.isFile()) {
      yield fullPath
    }
  }
}

let uploaded = 0
let failed = 0

try {
  statSync(imagesRoot)
} catch {
  console.log(`[sync-images] No images directory at ${imagesRoot} — nothing to sync.`)
  process.exit(0)
}

for (const file of walk(imagesRoot)) {
  const relPath = relative(imagesRoot, file).split(/[\\/]/).join('/')
  const remotePath = posix.join('brand', relPath)
  const url = `https://${endpoint}/${zone}/${remotePath}`
  const contents = readFileSync(file)

  const res = await fetch(url, {
    method: 'PUT',
    headers: {
      AccessKey: apiKey,
      'Content-Type': 'application/octet-stream',
    },
    body: contents,
  })

  if (res.ok) {
    uploaded++
    console.log(`  ✓ brand/${relPath}`)
  } else {
    failed++
    const body = await res.text().catch(() => '')
    console.error(`  ✗ brand/${relPath} — ${res.status} ${res.statusText} ${body}`)
  }
}

console.log(`\n[sync-images] uploaded ${uploaded}, failed ${failed}`)

if (failed > 0) {
  process.exit(1)
}
