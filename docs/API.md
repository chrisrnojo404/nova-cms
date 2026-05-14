# Nova CMS API

Nova CMS exposes a versioned REST API under `/api/v1` for frontend apps, plugins, and future SaaS integrations.

## Authentication

The API uses Laravel Sanctum bearer tokens.

Login:

```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "admin@nova-cms.test",
  "password": "password",
  "device_name": "local-client"
}
```

Successful responses return:

- `token`
- `token_type`
- `user`

Send the returned token as:

```http
Authorization: Bearer <token>
```

Authenticated helpers:

- `GET /api/v1/auth/me`
- `DELETE /api/v1/auth/logout`

## Public Endpoints

- `GET /api/v1/meta`
- `GET /api/v1/openapi.json`
- `GET /api/v1/settings/public`
- `GET /api/v1/pages`
- `GET /api/v1/pages/{slug}`
- `GET /api/v1/posts`
- `GET /api/v1/posts/{slug}`
- `GET /api/v1/categories`
- `GET /api/v1/categories/{slug}`
- `GET /api/v1/menus`
- `GET /api/v1/menus/{slug}`
- `GET /api/v1/menus/location/{location}`

## Authenticated Endpoints

These require a Sanctum token and the seeded `use api` permission. Write actions also require the matching CMS permission such as `manage pages` or `manage posts`.

- `GET /api/v1/users`
- `GET /api/v1/users/{user}`
- `POST /api/v1/pages`
- `PUT /api/v1/pages/{page}`
- `DELETE /api/v1/pages/{page}`
- `POST /api/v1/posts`
- `PUT /api/v1/posts/{post}`
- `DELETE /api/v1/posts/{post}`
- `POST /api/v1/categories`
- `PUT /api/v1/categories/{category}`
- `DELETE /api/v1/categories/{category}`
- `GET /api/v1/media`
- `POST /api/v1/media`
- `DELETE /api/v1/media/{media}`
- `POST /api/v1/menus`
- `PUT /api/v1/menus/{menu}`
- `DELETE /api/v1/menus/{menu}`
- `POST /api/v1/menus/{menu}/items`
- `PUT /api/v1/menus/{menu}/items/{item}`
- `DELETE /api/v1/menus/{menu}/items/{item}`

## Filtering

List endpoints support practical filters for frontend clients and admin integrations.

- `GET /api/v1/pages?search=about&status=published&per_page=12`
- `GET /api/v1/posts?search=release&category=announcements&author=editor@example.com&per_page=9`
- `GET /api/v1/categories?search=docs`
- `GET /api/v1/menus?location=header`
- `GET /api/v1/media?search=cover&directory=media/uploads&mime_type=image/`
- `GET /api/v1/users?search=editor&role=editor`

`per_page` is capped at `50`.

## Response Shape

Collection endpoints return Laravel API resource pagination payloads:

- `data`
- `links`
- `meta`

Single-resource endpoints return:

- `data`

Category detail returns a composite payload:

- `category`
- `posts`

## Useful Discovery

Use `GET /api/v1/meta` to inspect:

- authentication scheme
- supported capability groups
- filter support
- high-level endpoint inventory

Use `GET /api/v1/openapi.json` when you want a lightweight machine-readable contract for external clients, API tooling, or generated integrations.

## Example Flow

1. Call `GET /api/v1/meta`
2. Optionally load `GET /api/v1/openapi.json` for contract-aware tooling
3. Fetch public settings from `GET /api/v1/settings/public`
4. Load navigation from `GET /api/v1/menus/location/header`
5. Load homepage content from `GET /api/v1/pages` or `GET /api/v1/posts`
6. Authenticate and manage content with bearer-token requests when needed
