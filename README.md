# SWStarter

## Project setup

```shell
cp .env.example .env
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
