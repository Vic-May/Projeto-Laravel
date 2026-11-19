#!/usr/bin/env bash
# Cria o projeto Laravel "biblioteca", aplica o código da pasta src/ e prepara o banco MySQL.
# Uso:  ./setup.sh [usuario_mysql] [senha_mysql]
set -euo pipefail

AQUI="$(cd "$(dirname "$0")" && pwd)"
DB_USER="${1:-root}"
DB_PASS="${2:-}"

# 1) Projeto Laravel (Etapa 1)
if [ ! -d biblioteca ]; then
  composer create-project laravel/laravel biblioteca
fi
cd biblioteca

# 2) Código da aplicação (migrations, models, controllers, rotas, views, testes)
cp -R "$AQUI/src/." .
rm -f tests/Feature/ExampleTest.php tests/Unit/ExampleTest.php   # testes de exemplo do Laravel

# 3) Configuração do MySQL no .env e no .env.example
set_env() { # chave valor arquivo
  if grep -qE "^[# ]*$1=" "$3"; then
    sed -i -E "s|^[# ]*$1=.*|$1=$2|" "$3"
  else
    echo "$1=$2" >> "$3"
  fi
}
for f in .env .env.example; do
  set_env DB_CONNECTION mysql      "$f"
  set_env DB_HOST       127.0.0.1  "$f"
  set_env DB_PORT       3306       "$f"
  set_env DB_DATABASE   biblioteca "$f"
done
set_env DB_USERNAME "$DB_USER" .env;  set_env DB_USERNAME root .env.example
set_env DB_PASSWORD "$DB_PASS" .env;  set_env DB_PASSWORD ""   .env.example   # nunca versionar senha real

# 4) Banco de dados + migrations (Etapa 1 e 2)
MYSQL_PWD="$DB_PASS" mysql -u"$DB_USER" -e "CREATE DATABASE IF NOT EXISTS biblioteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force      # dados de exemplo (opcional)

# 5) Testes automatizados (SQLite em memória)
php artisan test

echo
echo "Pronto! Inicie com:  cd biblioteca && php artisan serve"
