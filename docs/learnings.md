# Learnings

Notes to fold into the Laravel philosophy file over time. Append new sections as they come up; don't prune until the philosophy file absorbs them.

## Controllers — prefer Breeze-style resourceful controllers over many invokable ones

Framing: every flow is a *resource* (even non-CRUD ones like an auth session), and the controller names that resource. Map behaviour onto the standard seven RESTful verbs (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`). Reach for single-action invokable (`__invoke`) controllers **only** when no verb fits naturally — Laravel Breeze itself only uses invokable for `VerifyEmailController` and `EmailVerificationPromptController`.

### Auth flows, Breeze convention

Each flow is a resource; the controller names what is being created/destroyed, and RESTful verbs describe what happens.

- `AuthenticatedSessionController`
  - `create` — returns the WebAuthn authentication challenge (`PublicKeyCredentialRequestOptions` JSON). In a classic Breeze app this is the login *view*; for a WebAuthn/SPA app it is the challenge payload.
  - `store` — verifies the assertion and calls `Auth::login($user)` on the session guard. "Creating" an authenticated session.
  - `destroy` — logs the user out, invalidates the session, regenerates the CSRF token. "Destroying" the session.

- `RegisteredPasskeyController` (or `RegisteredUserController` in password-based Breeze) — gated behind the enrollment feature flag in our case.
  - `create` — returns the WebAuthn attestation challenge.
  - `store` — verifies the attestation and persists the credential.

- Current user endpoint: `UserController@show` mounted at `/api/user`. Matches Sanctum's conventional `Route::get('/user', fn (Request $r) => $r->user())->middleware('auth:sanctum');` example. Returns `UserData` for the authenticated user. One resource ("the authenticated user"), one verb (`show`).

### CRUD flows

A full resource is ONE resourceful controller (`php artisan make:controller Api/Admin/EmailController --api`) with `index` / `show` / `store` / `update` / `destroy`. Auxiliary non-CRUD actions on the same resource (e.g. `preview`, `send`, `send-test`) each get their own small invokable controller — *those* are the cases where invokable is correct.

### Rule of thumb

1. Name the resource first (session, passkey, email, subscription).
2. Try to map the behaviour onto `index/create/store/show/edit/update/destroy`.
3. Only after step 2 fails, reach for an invokable controller.
4. The existing `Newsletter/SubscribeController` is invokable because "subscribe" is a single non-CRUD action with no obvious resource framing — that pattern does NOT generalise to full CRUD features.
