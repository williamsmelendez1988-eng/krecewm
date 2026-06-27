#!/bin/bash
# Script de despliegue automatizado para KreceWM
set -e

echo "🚀 Iniciando despliegue de KreceWM..."

# 1. Entrar al directorio del proyecto
# cd /var/www/krecewm

# 2. Descargar última versión
echo "📥 Descargando cambios de Git..."
git pull origin main

# 3. Instalar dependencias de PHP
echo "📦 Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader

# 4. Compilar assets de Frontend
echo "⚡ Compilando frontend (Vite)..."
npm install
npm run build

# 5. Ejecutar migraciones de Base de Datos
echo "🗄️ Ejecutando migraciones de base de datos..."
php artisan migrate --force

# 6. Limpiar y cachear configuración y rutas
echo "🧹 Optimizando caché de Laravel..."
php artisan optimize:clear
php artisan optimize

# 7. Reiniciar colas/queues (si usas redis/database queue)
# php artisan queue:restart

echo "✅ ¡Despliegue completado con éxito!"
