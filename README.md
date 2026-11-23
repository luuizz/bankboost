# Bankboost

Banco digital fictício criado a partir do projeto da landing do curso Codeboost, agora como aplicação Laravel com foco no backend.

## Stack

- **Laravel 13** (PHP 8.4) com MySQL
- **Landing** em Blade + SCSS + TypeScript (Vite)
- **Painel** em Inertia + Vue 3 *(em construção)*
- **API** autenticada com Sanctum *(em construção)*
- **Deploy** no Render (Docker, plano free) com banco MySQL compatível

## Rodando localmente (Sail)

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

A aplicação fica em `http://localhost`. O Sail já cria o banco `testing` usado pelos testes.

## Qualidade

```bash
./vendor/bin/sail artisan test   # testes (MySQL)
./vendor/bin/sail pint           # formatação PHP
npm run typecheck                # TypeScript
npm run build                    # build do front
```

O GitHub Actions roda Pint, testes com MySQL 8.4, typecheck, build do Vite e o build da imagem Docker em todo PR.

## Esteira

`feat/*` → PR para `dev` → PR de release `dev` → `main` → deploy automático no Render.

## Deploy (Render)

O `render.yaml` descreve o serviço web em Docker. No início do container rodam `config:cache`, `route:cache`, `view:cache` e `migrate --force`.

Variáveis que precisam existir no Render:

| Variável | Observação |
| --- | --- |
| `APP_KEY` | gerar com `php artisan key:generate --show` |
| `APP_URL` | URL pública do serviço |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | dados do banco MySQL |
| `MYSQL_ATTR_SSL_CA` | já vem como `/etc/ssl/certs/ca-certificates.crt` (CA pública) |
| `MYSQL_SSL_CA_PEM` | opcional: conteúdo do certificado da CA, para provedores com CA própria |

Banco gratuito sugerido: **TiDB Cloud Starter** (compatível com MySQL, porta `4000`, TLS obrigatório).
