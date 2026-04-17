/**
 * Preview variables surfaced to both Maizzle (for `{{ page.* }}` rendering)
 * and Laravel's dev preview route (via `scripts/copy-to-laravel.js`).
 * Single source of truth — add new template variables here.
 */
export default {
  appName: 'Newsletter',
  subscriberName: 'Preview User',
  verificationUrl: 'http://localhost:3000/newsletter/verify?token=preview-token-abc123',
}
