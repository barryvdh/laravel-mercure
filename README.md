# Laravel Broadcasting over Mercure 1.0

Demo app for the [Mercure protocol 1.0](https://mercure.rocks) support in Laravel Broadcasting and Laravel Echo. Three channels on one page, all multiplexed over a single Server-Sent Events connection:

- **Public** (`ticks`): plain publish/subscribe round trip.
- **Private** (`room.{user}`): delivery gated by the subscriber authorization cookie. Log in as the other user in a second browser to see messages *not* arrive there.
- **Presence** (`lobby`): "who's here" built on the hub's [subscription API](https://mercure.rocks/docs/hub/concepts/active-subscriptions) — every connect/disconnect is itself an update, with the member payload attached.

No WebSocket server to run: the Mercure hub is a single binary (here, a Docker container), and browsers use native `EventSource`.

## Requirements

The framework and Echo changes are submitted upstream as [laravel/framework PR #61474](https://github.com/laravel/framework/pull/61474) and [laravel/echo PR #549](https://github.com/laravel/echo/pull/549). Until they are merged, the work lives on branches across four sibling checkouts, wired together with Composer path repositories and an npm `file:` dependency:

```
├── framework        dunglas/framework, branch feat/mercure-broadcaster
├── symfony-mercure  symfony/mercure PRs #139/#140, branch mercure-1.0-webtoken-factory
├── echo             dunglas/echo, branch feat/mercure-connector
└── laravel-mercure  this repository
```

Plus PHP 8.3+, Composer, Docker, Node.js, and pnpm (for the echo workspace).

## Setup

```bash
docker compose up -d                   # Mercure hub on http://localhost:3001

composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed             # seeds the two demo users, Alice and Bob

(cd ../echo && pnpm install && pnpm build)
npm install
npm run build

php artisan serve                      # http://localhost:8000
```

Open http://localhost:8000, log in as Alice, then open a second browser (or a private window — the two sessions must not share cookies) and log in as Bob.

## Where to look

- [`config/broadcasting.php`](config/broadcasting.php) — the `mercure` connection and every supported option.
- [`docker-compose.yml`](docker-compose.yml) — the hub configuration: `anonymous` (public channels without a token), `subscriptions` (the presence primitive), CORS, and the dev cookie name.
- [`routes/channels.php`](routes/channels.php) — standard Laravel channel authorizers; the presence one returns the member payload embedded in the JWT grant.
- [`app/Events/`](app/Events/) — standard `ShouldBroadcast` events, nothing Mercure-specific.
- [`resources/views/mercure-demo.blade.php`](resources/views/mercure-demo.blade.php) — the Echo client code: `channel()`, `private()`, `join()`.

The `GET /login-as/{user}` route is password-less demo scaffolding, gated to the `local` environment.
