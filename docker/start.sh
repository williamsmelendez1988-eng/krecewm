#!/bin/bash
set -e

echo "🚀 Iniciando KreceWM..."

# Crear un archivo .env vacío si no existe para evitar fallas de escritura física de comandos
touch .env

# Configurar puerto dinámico de Railway para Nginx
if [ -n "$PORT" ]; then
    echo "🌐 Configurando Nginx para escuchar en el puerto $PORT..."
    sed -i "s/listen 80 default_server;/listen $PORT default_server;/g" /etc/nginx/sites-available/default
    sed -i "s/listen \[::\]:80 default_server;/listen \[::\]:$PORT default_server;/g" /etc/nginx/sites-available/default
fi

# Generar key si no existe
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force
fi

# Crear enlace simbólico de storage
php artisan storage:link --force 2>/dev/null || true

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders si la base de datos está vacía
echo "🌱 Verificando datos iniciales..."
php artisan db:seed --force 2>/dev/null || true

# Cachear configuración para producción
echo "⚡ Optimizando para producción..."
php artisan optimize

echo "✅ KreceWM listo. Iniciando servidores..."

# Iniciar Supervisor (que maneja Nginx + PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
