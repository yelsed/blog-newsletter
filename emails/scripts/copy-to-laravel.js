import { readdirSync, cpSync, renameSync, mkdirSync, writeFileSync } from 'node:fs'
import { resolve, dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'

const __dirname = dirname(fileURLToPath(import.meta.url))

const source = resolve(__dirname, '../build_production')
const destination = resolve(__dirname, '../../backend/resources/views/emails')

mkdirSync(destination, { recursive: true })

cpSync(source, destination, { recursive: true })

const templates = []

for (const file of readdirSync(destination)) {
  if (file.endsWith('.html')) {
    const oldPath = join(destination, file)
    const bladeName = file.replace('.html', '.blade.php')
    renameSync(oldPath, join(destination, bladeName))
    templates.push(file.replace('.html', ''))
    console.log(`  ${file} → ${bladeName}`)
  }
}

const { default: variables } = await import('../preview-vars.js')

const manifest = {
  templates: templates.sort(),
  variables,
}

const manifestPath = join(destination, 'previews.json')
writeFileSync(manifestPath, JSON.stringify(manifest, null, 2) + '\n')

console.log(`\nCopied email templates to ${destination}`)
console.log(`Wrote preview manifest to ${manifestPath}`)
