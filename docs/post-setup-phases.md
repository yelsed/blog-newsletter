# Post-Setup Phases: Deployment, K8s Learning, CI/CD

These phases come after the initial code setup (Phases 1 & 2) is complete and working locally.

---

## Phase 3: Production Deployment (Laravel Forge)

### 3.1 Provision Server on Forge

In Forge dashboard:
1. Create a new server on DigitalOcean
2. Server type: **App Server** (includes nginx, PHP, Node.js)
3. Select **PostgreSQL** as the database
4. PHP version: 8.4
5. Node.js: install via Forge (needed for Nuxt)

### 3.2 Deploy Laravel Backend

In Forge:
1. Create a new site: `api.yourdomain.com`
2. Set web root to `/backend/public`
3. Connect GitHub repo (`blog-laravel-nuxt`)
4. Set environment variables (copy from `.env`, update DB credentials to Forge's PostgreSQL)
5. Deploy script:
   ```bash
   cd /home/forge/api.yourdomain.com/backend
   git pull origin $FORGE_SITE_BRANCH

   composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan queue:restart
   ```
6. Enable SSL (Let's Encrypt)
7. Set up a Queue Worker in Forge (for email dispatch via Redis)
8. Set up a Scheduler (for `php artisan schedule:run`)
9. **Security headers** — add to nginx config: HSTS, X-Frame-Options, X-Content-Type-Options, CSP

### 3.3 Deploy Nuxt Frontend

In Forge:
1. Create a second site: `yourdomain.com`
2. Set it up as a **Node application** (or use Forge's "Static/SPA" site with a daemon)
3. Connect same GitHub repo
4. Build + deploy script:
   ```bash
   cd /home/forge/yourdomain.com/frontend
   git pull origin $FORGE_SITE_BRANCH
   npm ci
   npm run build
   ```
5. Create a **Daemon** in Forge to run the Nuxt server:
   - Command: `node /home/forge/yourdomain.com/frontend/.output/server/index.mjs`
   - Directory: `/home/forge/yourdomain.com/frontend`
6. Configure nginx to proxy to the Nuxt server (port 3000)
7. Set `NUXT_PUBLIC_API_BASE=https://api.yourdomain.com/api` as environment variable
8. Enable SSL (Let's Encrypt)

### 3.4 Forge Nginx Config for Nuxt

Forge generates nginx config automatically, but for the Nuxt site update it to proxy to Node:

```nginx
location / {
    proxy_pass http://127.0.0.1:3000;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_cache_bypass $http_upgrade;
}
```

### 3.5 Maizzle Email Templates for Production

Before deploying, ensure email templates are production-ready:
1. Build Maizzle templates: `cd emails && npm run build`
2. Copy built HTML from `emails/build_production/` to `backend/resources/views/emails/`
3. Configure real SMTP provider in Forge env vars (Mailgun, Postmark, or Resend)
4. Test email delivery end-to-end on production

### 3.6 Production Security Checklist

- [ ] SSL on both sites (Let's Encrypt via Forge)
- [ ] Security headers: HSTS, X-Frame-Options DENY, X-Content-Type-Options nosniff, CSP
- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] Database credentials not in repo (Forge env vars only)
- [ ] Rate limiting active on all public endpoints
- [ ] Redis connection secured (bind to localhost)
- [ ] Sanctum configured with production `SANCTUM_STATEFUL_DOMAINS`
- [ ] CORS locked to production domain only
- [ ] Forge firewall: only ports 22, 80, 443 open

### 3.7 Verify Phase 3

```bash
curl https://api.yourdomain.com/api/health
curl https://yourdomain.com
# Test newsletter signup end-to-end on production
# Verify emails are sent and render correctly in major email clients
# Check security headers: curl -I https://api.yourdomain.com
```

---

## Phase 4: Kubernetes Learning (minikube — local only)

This phase is purely for learning K8s concepts. Nothing here goes to production.

### 4.1 Install Tools

```bash
sudo pacman -S kubectl minikube
```

### 4.2 Start Local Cluster

```bash
minikube start --driver=docker
minikube addons enable ingress
minikube addons enable dashboard
```

### 4.3 Dockerfiles Needed

**Backend** — `backend/Dockerfile`:
- Multi-stage: PHP 8.4-fpm-alpine, Composer install, nginx + php-fpm via supervisord
- Supporting files in `backend/docker/`: `nginx.conf`, `supervisord.conf`

**Frontend** — `frontend/Dockerfile`:
- Multi-stage: Node 24-alpine builder (`npm ci` + `npm run build`), runner copies `.output/`
- Runs `node .output/server/index.mjs`

### 4.4 Manifests to Create

- `k8s/secrets.yaml` — APP_KEY, DB credentials
- `k8s/postgres/deployment.yaml` — PostgreSQL with PersistentVolumeClaim
- `k8s/postgres/service.yaml` — ClusterIP service
- `k8s/backend/deployment.yaml` — Laravel container, env vars from secrets, readiness probe on `/api/health`
- `k8s/backend/service.yaml` — ClusterIP on port 80
- `k8s/frontend/deployment.yaml` — Nuxt container, `NUXT_PUBLIC_API_BASE` pointing to backend service
- `k8s/frontend/service.yaml` — ClusterIP on port 3000
- `k8s/ingress.yaml` — NGINX ingress: `newsletter.local` -> frontend, `api.newsletter.local` -> backend

### 4.5 Deploy to Local Cluster

```bash
eval $(minikube docker-env)
docker build -t newsletter-backend:latest ./backend
docker build -t newsletter-frontend:latest ./frontend

kubectl apply -f k8s/secrets.yaml
kubectl apply -f k8s/postgres/
kubectl apply -f k8s/backend/
kubectl apply -f k8s/frontend/
kubectl apply -f k8s/ingress.yaml

kubectl exec deployment/newsletter-backend -- php artisan migrate --force

echo "$(minikube ip)  newsletter.local api.newsletter.local" | sudo tee -a /etc/hosts
```

### 4.6 Verify Phase 4

```bash
kubectl get pods                              # All Running
curl http://api.newsletter.local/api/health   # Backend responds
curl http://newsletter.local                  # Frontend responds
minikube dashboard                            # Visual overview
```

---

## Phase 5: CI/CD (GitHub Actions)

### 5.1 Workflow: `.github/workflows/ci.yml`

**Jobs:**

1. **quality-backend** — runs in parallel:
   - `composer audit` — dependency vulnerability check
   - `./vendor/bin/pint --test` — code style (PSR-12)
   - `./vendor/bin/phpstan analyse` — static analysis (LaraStan level 8)

2. **test-backend** (depends on quality-backend) — Set up PHP 8.4, Composer install, run Pest against PostgreSQL service container

3. **build-emails** — `cd emails && npm ci && npm run build` — verify Maizzle templates compile

4. **test-frontend** — Set up Node 24, `npm ci`, `npm run build` (verify build succeeds)

Forge handles deployment automatically via push-to-deploy webhook on git push to main.

### 5.2 Dependabot

Enable Dependabot in `.github/dependabot.yml` for composer, npm (frontend), and npm (emails) — automated PRs for dependency updates.

### 5.3 Verify Phase 5

```bash
git init && git add . && git commit -m "Initial project setup"
gh repo create blog-laravel-nuxt --private --source=. --push
# Check GitHub Actions tab — quality checks + tests should pass
# Check Forge — auto-deploy should trigger on push to main
```

---

## Key Gotchas for Post-Setup

1. **Forge + monorepo**: Forge deploys the entire repo — the deploy script `cd`s into the right subdirectory. Set web root to `backend/public` for the API site
2. **Forge Nuxt daemon**: If the daemon crashes, Forge restarts it. Make sure `npm run build` completes before the daemon starts (deploy script order matters)
3. **minikube images**: Must use `eval $(minikube docker-env)` before building, and set `imagePullPolicy: Never` in manifests
4. **Maizzle in CI**: Email templates should build in CI to catch broken templates before deploy

---

## Suggested Timeline

| Days | Phase | Deliverable |
|------|-------|-------------|
| 1-3 | Phase 3 | Forge server provisioned, both sites deployed with SSL, security headers, production email working |
| 4-7 | Phase 4 | Dockerfiles + K8s manifests, full stack running in minikube |
| 8-10 | Phase 5 | GitHub Actions CI (quality + tests + email build), Dependabot, Forge auto-deploy on push |
