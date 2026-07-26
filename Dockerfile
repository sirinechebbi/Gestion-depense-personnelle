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

# Copier composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers de dépendances d'abord (meilleur cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances PHP (sans les paquets de dev, prod uniquement)
RUN composer install --no-dev --no-scripts --no-progress --optimize-autoloader --prefer-dist

# Copier le reste du projet
COPY . .

# Finaliser l'installation composer (scripts, autoload optimisé)
RUN composer dump-autoload --optimize --no-dev \
    && composer run-script --no-dev post-install-cmd || true

# Variables d'environnement
ENV APP_ENV=prod

EXPOSE 8000

# Railway assigne un port dynamique via la variable PORT
CMD ["sh", "-c", "frankenphp php-server --listen :${PORT:-8000} --root /app/public"]