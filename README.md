# SWStarter

## Project setup

```shell
cp .env.example .env
```

Important `.env` variables to review (if ports or urls are different)
```dotenv
VITE_API_BASE_URL="http://localhost"
FRONTEND_URL="http://localhost:5173"
SANCTUM_STATEFUL_DOMAINS="localhost:5173"
```

Build containers
```bash
docker compose build
```

Start containers
```bash
docker compose up -d
```

Install backend dependencies
```bash
docker compose exec app composer install
```

Install frontend dependencies
```bash
./vendor/bin/sail npm install
```

Create app key
```bash
./vendor/bin/sail artisan key:generate
```

Run migrations
```bash
./vendor/bin/sail artisan migrate
```

Run environment
```bash
./vendor/bin/sail composer dev
```
The command above will run the schedule, process the default queue and serve the UI.

Access the application on http://localhost:5173

---

## Quality tools

Running tests
```bash
./vendor/bin/sail test
```

Running linter
```
./vendor/bin/sail composer check-style
```

Running larastan
```
./vendor/bin/sail composer analyse
```
