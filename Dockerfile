FROM dunglas/frankenphp:1-php8.5

# Extensions PHP nécessaires pour Symfony + Doctrine + PostgreSQL
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    intl \
    opcache \
    zip \
    apcu

WORKDIR /app

# Autoriser Composer à exécuter les plugins (ex: symfony/runtime) même en tant que root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copier composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier tout le projet d'abord (évite les soucis d'ordre avec symfony/runtime)
COPY . .

# Installer les dépendances PHP (sans les paquets de dev, prod uniquement, sans scripts qui ont besoin de DB)
RUN composer install --no-dev --no-scripts --no-progress --optimize-autoloader --prefer-dist

# Vérification (visible dans les logs de build) que le composant runtime est bien présent
RUN test -f vendor/autoload_runtime.php && echo "OK: autoload_runtime.php present" || echo "MISSING: autoload_runtime.php"

# Variables d'environnement
ENV APP_ENV=prod

EXPOSE 8000

# Railway assigne un port dynamique via la variable PORT
CMD ["sh", "-c", "frankenphp php-server --listen :${PORT:-8000} --root /app/public"]