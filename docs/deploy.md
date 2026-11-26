# Deploy na DigitalOcean

Um Droplet roda tudo em Docker Compose:

- **app**: a imagem do Bankboost (Apache + PHP 8.4), publicada no GitHub Container Registry
- **mysql**: MySQL 8.4, com os dados num volume
- **caddy**: proxy com HTTPS automático (Let's Encrypt)

A cada merge na `main`, depois que o CI passa, o workflow **Deploy** monta a imagem, envia para o `ghcr.io` e atualiza o Droplet por SSH. As migrations e o `SiteContentSeeder` rodam na subida do container.

## 1. Criar o Droplet

- Ubuntu 24.04 LTS, plano Basic de **1 GB / 1 vCPU** (o de US$ 6 dá conta; o script cria 1 GB de swap)
- Região: Nova York ou São Francisco (não há região no Brasil)
- Autenticação: sua chave SSH pessoal

## 2. Apontar o subdomínio

No painel do seu domínio, crie um registro **A** com o subdomínio (ex.: `bankboost`) apontando para o IP do Droplet. O HTTPS só é emitido depois que esse registro propagar.

## 3. Chave de deploy

Na sua máquina, gere uma chave só para o GitHub Actions:

```bash
ssh-keygen -t ed25519 -C "github-actions-bankboost" -f ~/.ssh/bankboost_deploy -N ""
```

## 4. Preparar o servidor

Entre como root (`ssh root@IP_DO_DROPLET`) e rode o script, passando a chave **pública**:

```bash
curl -fsSL https://raw.githubusercontent.com/luuizz/bankboost/main/deploy/setup-droplet.sh \
  | bash -s -- "$(cat ~/.ssh/bankboost_deploy.pub)"
```

> O `$(cat ...)` roda na sua máquina, então cole o conteúdo do `.pub` no lugar se estiver dentro do servidor.

Ele instala o Docker, cria o usuário `deploy`, ativa o firewall (22, 80 e 443), cria o swap e desliga o login por senha.

## 5. Criar o `.env` de produção

Ainda no servidor:

```bash
nano /opt/bankboost/.env
```

Cole o conteúdo de [`deploy/.env.production.example`](../deploy/.env.production.example) e preencha:

| Variável | Como gerar |
| --- | --- |
| `APP_KEY` | `echo "base64:$(openssl rand -base64 32)"` |
| `APP_DOMAIN` | o subdomínio, sem `https://` |
| `APP_URL` | `https://` + o subdomínio |
| `DB_PASSWORD`, `DB_ROOT_PASSWORD` | `openssl rand -hex 24` (um para cada) |

Depois: `chown deploy:deploy /opt/bankboost/.env && chmod 600 /opt/bankboost/.env`.

## 6. Secrets no GitHub

Em **Settings → Secrets and variables → Actions**:

| Nome | Tipo | Valor |
| --- | --- | --- |
| `DROPLET_HOST` | Secret | IP do Droplet |
| `DROPLET_SSH_KEY` | Secret | conteúdo de `~/.ssh/bankboost_deploy` (a **privada**) |
| `APP_URL` | Variable | `https://` + o subdomínio (usado para conferir o site no fim do deploy) |

O workflow usa o environment `production`, que o GitHub cria sozinho no primeiro deploy.

## 7. Primeiro deploy

Em **Actions → Deploy → Run workflow** (ou faça um merge na `main`). No fim, o workflow espera o `/up` responder dentro do container e depois pela URL pública.

Para entrar no admin, cadastre-se pelo site e rode no servidor:

```bash
cd /opt/bankboost && docker compose exec app php artisan bankboost:make-admin seu@email.com
```

## Dia a dia

```bash
cd /opt/bankboost
docker compose ps                     # status
docker compose logs -f app            # logs da aplicação
docker compose exec app php artisan bankboost:audit
```

**Voltar uma versão:** cada imagem tem a tag do commit (`ghcr.io/luuizz/bankboost:<sha>`).

```bash
IMAGE_TAG=<sha-anterior> docker compose up -d app
```

Migrations não são desfeitas automaticamente. Se a versão nova criou alguma, confira antes de voltar.

**Backup do banco:**

```bash
docker compose exec mysql sh -c 'mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" --single-transaction bankboost' | gzip > backup-$(date +%F).sql.gz
```

Os uploads (capas do blog) ficam no volume `bankboost_storage`. Os snapshots semanais da DigitalOcean (pagos à parte) cobrem banco e uploads de uma vez.
