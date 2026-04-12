import { readdirSync, cpSync, renameSync, mkdirSync } from 'node:fs'
import { resolve, dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'

const __dirname = dirname(fileURLToPath(import.meta.url))

const source = resolve(__dirname, '../build_production')
const destination = resolve(__dirname, '../../backend/resources/views/emails')

mkdirSync(destination, { recursive: true })

cpSync(source, destination, { recursive: true })

// Rename .html to .blade.php for Laravel
for (const file of readdirSync(destination)) {
  if (file.endsWith('.html')) {
    const oldPath = join(destination, file)
    const newPath = join(destination, file.replace('.html', '.blade.php'))
    renameSync(oldPath, newPath)
    console.log(`  ${file} → ${file.replace('.html', '.blade.php')}`)
  }
}

console.log(`\nCopied email templates to ${destination}`)
