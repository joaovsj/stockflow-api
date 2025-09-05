#!/bin/bash

# Esperar o MySQL estar pronto
echo "⏳ Aguardando MySQL..."
until nc -z -v -w30 mysql 3306
do
  echo "Ainda não está pronto, tentando novamente..."
  sleep 5
done

# Executar migrations + seeders
echo "🚀 Rodando migrations e seeders..."
php artisan migrate --seed --force

# Iniciar Apache
echo "✅ Subindo Apache..."
apache2-foreground
