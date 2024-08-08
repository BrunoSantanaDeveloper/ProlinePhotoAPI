# Use a imagem oficial do PHP com Apache
FROM php:8.1-apache

# Instala as extensões necessárias do PHP
RUN docker-php-ext-install pdo pdo_mysql

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Clone o repositório diretamente dentro do container
RUN apt-get update && apt-get install -y git
RUN git clone https://Rgpsico@bitbucket.org/Rgpsico/teste_tecnico.git .

# Instala as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Copia o .env.example para .env e gera a key do Laravel
RUN cp .env.example .env && php artisan key:generate

# Define as permissões corretas para o diretório de armazenamento e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ativa o módulo de reescrita do Apache (necessário para o Laravel)
RUN a2enmod rewrite

# Expõe a porta 80
EXPOSE 80

# Executa as migrações e seeders ao iniciar o container
CMD php artisan migrate --seed && apache2-foreground
