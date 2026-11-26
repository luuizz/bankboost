# Bankboost

Banco digital fictício criado a partir do projeto da landing do curso Codeboost, agora como aplicação Laravel com foco no backend.

## Stack

- **Laravel 13** (PHP 8.4) com MySQL
- **Site institucional** em Blade + Tailwind 4: home, conta digital, cartão, investimentos, empréstimo, conta PJ, quem somos, carreiras, blog, central de ajuda e páginas legais
- **Ícones** Phosphor no site (`blade-phosphor-icons`) e no painel (`@phosphor-icons/vue`)
- **Painel** do cliente em Inertia + Vue 3 + Tailwind: saldo, extrato com filtros, transferência, Pix e comprovantes
- **Admin** em Inertia + Vue: visão geral, clientes (bloqueio), transações, blog com upload de capa, FAQ, vagas e mensagens
- **API** autenticada com Sanctum: contas, extrato com filtros, transferências e Pix simulados ([docs/api.md](docs/api.md))
- **Deploy** no Render (Docker, plano free) com banco MySQL compatível

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
