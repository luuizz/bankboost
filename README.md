# Bankboost

Banco digital fictício criado a partir do projeto da landing do curso Codeboost, agora como aplicação Laravel com foco no backend.

## Stack

- **Laravel 13** (PHP 8.4) com MySQL
- **Site institucional** em Blade + Tailwind 4: home, conta digital, cartão, investimentos, empréstimo, conta PJ, quem somos, carreiras, blog, central de ajuda e páginas legais
- **Ícones** Phosphor no site (`blade-phosphor-icons`) e no painel (`@phosphor-icons/vue`)
- **Painel** do cliente em Inertia + Vue 3 + Tailwind: saldo, extrato com filtros, transferência, Pix e comprovantes
- **Admin** em Inertia + Vue: visão geral, clientes (bloqueio), transações, blog com upload de capa, FAQ, vagas e mensagens
- **API** autenticada com Sanctum: contas, extrato com filtros, transferências e Pix simulados ([docs/api.md](docs/api.md))
- **Deploy** num Droplet da DigitalOcean com Docker Compose, Caddy e GitHub Actions

## Rodando localmente (Sail)

Requer Docker. Todos os comandos rodam dentro do Sail.

```bash
cp .env.example .env
# primeira vez: instala o vendor (e o Sail) usando o Composer de um container
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
    laravelsail/php84-composer:latest composer install --ignore-platform-reqs
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
./vendor/bin/sail npm install
./vendor/bin/sail composer dev
```

A aplicação fica em `http://localhost` (landing) e `http://localhost/painel` (painel; cadastro em `/abrir-conta`). O Sail já cria o banco `testing` usado pelos testes.

O conteúdo do site (posts, FAQ e vagas) vem do `SiteContentSeeder`, que roda no deploy e só preenche tabelas vazias.

Para ter dados de exemplo: `./vendor/bin/sail artisan db:seed` (admin `admin@bankboost.test`; clientes `ana@bankboost.test` e `bruno@bankboost.test`; senha `password` em todos).

Em produção, cadastre-se pelo site e rode `php artisan bankboost:make-admin seu@email.com`.

## Qualidade

```bash
./vendor/bin/sail artisan test   # testes (MySQL)
./vendor/bin/sail pint           # formatação PHP
npm run typecheck                # TypeScript + Vue (vue-tsc)
npm run build                    # build do front
```

O GitHub Actions roda Pint, testes com MySQL 8.4, typecheck, build do Vite e sobe a stack de produção (app + MySQL + Caddy) para conferir o site por HTTPS em todo PR.

## Esteira

`feat/*` → PR para `dev` → PR de release `dev` → `main` → deploy automático no Droplet.

## Deploy (DigitalOcean)

Droplet com Docker Compose (app + MySQL + Caddy com HTTPS automático). Depois que o CI passa na `main`, o workflow **Deploy** publica a imagem no GitHub Container Registry e atualiza o servidor por SSH. Migrations rodam sozinhas na subida.

Passo a passo completo, do Droplet novo ao primeiro deploy: [docs/deploy.md](docs/deploy.md).
