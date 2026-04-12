#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")" && pwd)"
FAILED=0

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

pass() { echo -e "${GREEN}✓ $1${NC}"; }
fail() { echo -e "${RED}✗ $1${NC}"; FAILED=1; }
heading() { echo -e "\n${YELLOW}━━━ $1 ━━━${NC}"; }

# --- Backend checks ---
heading "Backend: Pint (code style)"
if (cd "$ROOT_DIR/backend" && ./vendor/bin/pint --test); then
    pass "Pint"
else
    fail "Pint — run './vendor/bin/pint' in backend/ to fix"
fi

heading "Backend: LaraStan (static analysis)"
if (cd "$ROOT_DIR/backend" && ./vendor/bin/phpstan analyse --memory-limit=512M); then
    pass "LaraStan"
else
    fail "LaraStan"
fi

heading "Backend: Pest (tests)"
if (cd "$ROOT_DIR/backend" && ./vendor/bin/pest --colors=always); then
    pass "Pest"
else
    fail "Pest"
fi

heading "Backend: Composer audit (security)"
if (cd "$ROOT_DIR/backend" && composer audit); then
    pass "Composer audit"
else
    fail "Composer audit — vulnerable dependencies found"
fi

# --- Frontend checks ---
heading "Frontend: Nuxt build"
if (cd "$ROOT_DIR/frontend" && npm run build 2>&1); then
    pass "Nuxt build"
else
    fail "Nuxt build"
fi

# --- Emails checks ---
heading "Emails: Maizzle build"
if (cd "$ROOT_DIR/emails" && npm run build 2>&1); then
    pass "Maizzle build"
else
    fail "Maizzle build"
fi

# --- Summary ---
echo ""
if [ "$FAILED" -eq 0 ]; then
    echo -e "${GREEN}All checks passed.${NC}"
    exit 0
else
    echo -e "${RED}Some checks failed. See above for details.${NC}"
    exit 1
fi
