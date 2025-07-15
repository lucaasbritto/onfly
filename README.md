# Sistema de Pedidos de Viagem  

Sistema para cadastro, consulta e gestão de pedidos de viagem corporativas com autenticação, notificações, controle de permissões e execução em ambiente **Docker**

---

## Tecnologias utilizadas
- [Laravel 12]
- [Vue.js 3]
- [Docker + Docker-compose]
- [JWT]
- [MySQL]
- [Vite]
- [Pinia]
- [Axios]
- [Nginx]
- [Bootstrap + Icons]
- [PHPUnit (unitários e feature)]
- [App\Notifications]

## Funcionalidades

- Autenticação com JWT
- Cadastro de pedidos de viagem
- Filtros por status, data e destino
- Filtros por id, solicitantes e administradores (somente admins)
- Aprovação e cancelamento de status (somente admins)
- Notificações de mudança de status
- Controle de permissões (usuário/admin)
- Tela de login integrada ao backend

## Pré-requisitos

Certifique-se de ter as seguintes ferramentas instaladas:

- [Docker instalado]
- [Docker Compose instalado]

---


## Instalação

1. **Clone o repositório**

```bash
git clone https://github.com/lucaasbritto/onfly.git
cd onfly
```

2. **Copie o arquivo de ambiente para produção**
  - cp app/.env.example app/.env

3. **Configure o nome do banco em .env**
```env
DB_DATABASE=laravel
```

4. **Suba os containers com Docker**
  - docker-compose up --build -d

5. **Entre no container**
  - docker exec -it laravel_app_onfly bash

6. **Instale as dependências do PHP**
  - composer install

7. **Gere a chave da aplicação**
  - php artisan key:generate

8. **Gere a chave JWT**
  - php artisan jwt:secret


9. **Rode as migrações e os seeders**
  - php artisan migrate --seed

10. **Os Seeders criam**
  - 1 admin
  - 5 usuários
  - Pedidos de viagens 


## Acessos
  - Front-end: http://localhost:5173
  - Back-end (API): http://localhost:8080/api


## Usuários para Logar no sistema
| Tipo    | Email                            | Senha                      |
|---------|----------------------------------|----------------------------|
| Admin   | admin@teste.com                  | 123456                     |
| Usuario | teste@teste.com                  | 123456                     |


## Rodando os Testes
- Foi criado testes unitarios e testes de feature. 
- Os testes usam o arquivo .env.testing
- Por segurança, o arquivo `.env.testing` **não está incluído no versionamento do Git** (ignorado via `.gitignore`).
- Cada desenvolvedor deve criá-lo localmente com o comando:

1. **Criando o .env.testing**
```bash
cp app/.env.example app/.env.testing
```

2. **Configure o nome do banco em .env.testing**
```env
DB_DATABASE=laravel
```

3. **Acesse o container**
  - docker exec -it laravel_app_onfly bash

4. **Gere a chave da aplicação para testes**
  - php artisan key:generate --env=testing

5. **Gere a chave JWT para testes**
  - php artisan jwt:secret --env=testing

6. **Execute o teste**
  - php artisan test
  - Ou para testar individualmente:
    - php artisan test --filter=TravelRequestServiceTest
    - php artisan test --filter=NotificationServiceTest
    - php artisan test --filter=TravelRequestTest
    - php artisan test --filter=NotificationControllerTest

7. **Testes Cobrem**
  - Criação de pedido de viagem
  - Atualização de status (com regras de permissão)
  - Cancelamento validado por status
  - Notificações (enviar, ler)
  - Filtros e visualização de pedidos
  - Autorização e autenticação

8. **Tipos de Testes**

| Tipo        | Arquivo                          | Cobertura                                   |
|-------------|----------------------------------|---------------------------------------------|
| Unitário    | TravelRequestServiceTest         | Lógica de negócio de pedidos                |
| Unitário    | NotificationServiceTest          | Lógica de negócio de notificações           |
| Feature     | TravelRequestTest                | Rotas e regras de pedidos                   |
| Feature     | NotificationControllerTest       | API de notificações                         |    


## Estrutura de dados obrigatorios do .env

```env
APP_NAME=Laravel
APP_KEY=           # Gerado via php artisan key:generate
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
JWT_SECRET=        # Gerado via php artisan jwt:secret
```
- (Essas informações do banco estão pre configuradas no Docker)


## Endpoints principais

| Método | Rota                         | Ação                                       |
|--------|------------------------------|--------------------------------------------|
| POST   | /api/login                   | Login e geração de token JWT               |
| GET    | /api/me                      | Retorna o usuário autenticado              |
| GET    | /api/requests                | Lista pedidos de viagem (com filtros)      |
| POST   | /api/requests/               | Cria um novo pedido de viagem              |
| GET    | /api/requests/{id}           | Detalhes de um pedido de viagem            |
| PATCH  | /api/requests/{id}           | Atualiza o status do pedido (admin apenas) |
| GET    | /api/notifications           | Lista todas as notificações do usuário     |
| PATCH  | /api/notifications/read      | Marca todas as notificações como lidas     |
| PATCH  | /api/notifications/{id}/read | Marca uma notificação específica como lida |
| GET    | /api/users                   | Lista todos os usuários                    |
| GET    | /api/admins                  | Lista todos os administradores             |



##  Comandos úteis

- Parar containers: `docker-compose down`
- Subir containers: `docker-compose up --build -d`
- Acessar o container: `docker exec -it laravel_app_onfly bash`
- Rodar seeders novamente: `php artisan migrate:fresh --seed`