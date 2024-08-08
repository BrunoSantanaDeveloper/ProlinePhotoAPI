Aqui está o README atualizado, incluindo os comandos personalizados que você criou:

---

# **TESTE COR**

## **Introdução**

Este projeto é uma api de gerenciamento de fotos e coordenadas do usuário, construída com Laravel. Ele inclui funcionalidades de autenticação com Sanctum, padrões de arquitetura com Repository e Service, testes unitários, e está configurado para rodar em containers Docker.

## **Tecnologias Utilizadas**

-   PHP 8.\*
-   Laravel 10.x
-   Laravel Sanctum
-   Docker
-   Git
-   Swagger para documentação da API
-   PHPUnit para testes

## **Configuração do Ambiente**

### **Pré-requisitos**

-   Docker instalado
-   Composer instalado

### **Passos para Configuração**

1. **Clone o repositório:**

    ```bash
    git clone https://github.com/BrunoSantanaDeveloper/ProlinePhotoAPI.git
    cd ProlinePhotoAPI
    ```

2. **Instale as dependências do projeto:**

    ```bash
    composer install
    ```

3. **Configure o arquivo `.env`:**

    - Copie o exemplo:
    ```bash
    cp .env.example .env
    ```
    - Atualize as configurações de banco de dados e outras variáveis no arquivo `.env`.


4. **Crie o arquivo sqlite:**
    ```bash
    touch database/database.sqlite
    ```

5. **Instale e configure o Sanctum:**
    ```bash
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    ```

4. **Execute as migrações:**
    ```bash
    php artisan migrate
    ```

    6. **Gere a Key:**
    ```bash
    php artisan key:generate
    ```
## **Uso do Docker**

### **Construir e Rodar o Container**

1. **Crie a imagem Docker personalizada:**

    A imagem Docker foi configurada para incluir todas as dependências necessárias e automatizar a configuração do ambiente.

    ```Dockerfile
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
    ```

2. **Construa e rode o container:**
    ```bash
    docker build -t nome-da-imagem .
    docker run -d -p 8000:80 nome-da-imagem
    ```

## **Endpoints da API**

### **Users**

-   **Registro**: `POST /api/register`
-   **Login**: `POST /api/login`

### **Photos**

-   **Listar Fotos**: `GET /api/photos`
-   **Criar Fotos**: `POST /api/photos`
-   **Obter Fotos**: `GET /api/photos/{id}`
-   **Atualizar Fotos**: `PUT /api/photos/{id}`
-   **Deletar Fotos**: `DELETE /api/photos/{id}`

## **Padrão de Arquitetura**

### **Repository Pattern**

-   **Descrição**: Implementa a lógica de acesso aos dados.
-   **Localização**: `app/Repositories`

### **Services**

-   **Descrição**: Encapsula a lógica de negócios.
-   **Localização**: `app/Services`


## **Testes**

Para rodar os testes unitários:

```bash
php artisan test
```

## **Contribuindo**

-   **Branch Principal**: `main`
-   **Commits e Push**:
    ```bash
    git add .
    git commit -m "Descrição da alteração"
    git push origin nome-da-branch
    ```
-   **Criação de Pull Requests**: Envie um pull request para a `main` após concluir as alterações.

## **Documentação da API**

A documentação completa da API está disponível no Swagger:

-   **URL**: `http://localhost:8000/api/documentacao`

## **Considerações Finais**

Este projeto foi criado com o intuito de fornecer uma base sólida para o desenvolvimento de aplicações Laravel, integrando autenticação, padrões de arquitetura, e containerização com Docker.

---
