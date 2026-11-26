#!/usr/bin/env bash
# Prepara um Droplet Ubuntu novo para o Bankboost. Rode uma vez, como root:
#   curl -fsSL https://raw.githubusercontent.com/luuizz/bankboost/main/deploy/setup-droplet.sh | bash -s -- "ssh-ed25519 AAAA... github-actions"
#
# O argumento é a chave pública que o GitHub Actions vai usar para entrar como "deploy".
set -euo pipefail

DEPLOY_KEY="${1:?Informe a chave pública SSH do deploy como argumento}"
APP_DIR=/opt/bankboost

echo "==> Atualizando o sistema"
export DEBIAN_FRONTEND=noninteractive
apt-get update -q && apt-get upgrade -yq

echo "==> Instalando Docker"
if ! command -v docker >/dev/null; then
    curl -fsSL https://get.docker.com | sh
fi

echo "==> Swap de 1 GB (ajuda no Droplet de 1 GB de RAM)"
if ! swapon --show | grep -q /swapfile; then
    fallocate -l 1G /swapfile && chmod 600 /swapfile && mkswap /swapfile && swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

echo "==> Usuário deploy"
id deploy >/dev/null 2>&1 || adduser --disabled-password --gecos "" deploy
usermod -aG docker deploy
install -d -m 700 -o deploy -g deploy /home/deploy/.ssh
grep -qxF "$DEPLOY_KEY" /home/deploy/.ssh/authorized_keys 2>/dev/null || echo "$DEPLOY_KEY" >> /home/deploy/.ssh/authorized_keys
chown deploy:deploy /home/deploy/.ssh/authorized_keys && chmod 600 /home/deploy/.ssh/authorized_keys

echo "==> Pasta da aplicação"
install -d -o deploy -g deploy "$APP_DIR"

echo "==> Firewall (SSH, HTTP e HTTPS)"
ufw allow OpenSSH && ufw allow 80/tcp && ufw allow 443/tcp && ufw allow 443/udp
ufw --force enable

echo "==> SSH só com chave"
sed -ri 's/^#?PasswordAuthentication .*/PasswordAuthentication no/' /etc/ssh/sshd_config
systemctl reload ssh || systemctl reload sshd

echo
echo "Pronto. Agora crie $APP_DIR/.env a partir de deploy/.env.production.example"
echo "e rode o workflow Deploy no GitHub (ou faça um merge na main)."
