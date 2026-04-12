#!/usr/bin/env bash
set -uo pipefail

INPUT=$(cat)
FILE_PATH=$(echo "$INPUT" | jq -r '.tool_input.file_path // empty')

if [ -z "$FILE_PATH" ]; then
    exit 0
fi

# Only check PHP files inside backend/
BACKEND_DIR="$CLAUDE_PROJECT_DIR/backend"
case "$FILE_PATH" in
    "$BACKEND_DIR"/app/*|"$BACKEND_DIR"/tests/*)
        ;;
    *)
        exit 0
        ;;
esac

cd "$BACKEND_DIR" || exit 0

FAILED=0

# Auto-fix formatting with Pint (safe, idempotent)
./vendor/bin/pint "$FILE_PATH" --quiet 2>/dev/null

# Run LaraStan on the edited file
echo "Running LaraStan on $(basename "$FILE_PATH")..." >&2
if ! ./vendor/bin/phpstan analyse "$FILE_PATH" --no-progress --memory-limit=512M 2>&1 >&2; then
    FAILED=1
fi

if [ "$FAILED" -eq 1 ]; then
    echo "" >&2
    echo "Fix the LaraStan errors above before continuing." >&2
    exit 2
fi

exit 0
