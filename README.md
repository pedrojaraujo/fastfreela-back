# FastFreela API

Esta é a API do FastFreela, um aplicativo que conecta contratantes com prestadores de serviço. A API foi desenvolvida utilizando PHP com o framework Laravel, e fornece endpoints para autenticação de usuários, registro, atualização e exclusão de usuários.

## Tecnologias Utilizadas

- **PHP**: Linguagem de programação principal.
- **Laravel**: Framework PHP utilizado para desenvolvimento da API.
- **Composer**: Gerenciador de dependências para PHP.
- **Sanctum**: Pacote Laravel para autenticação de API.

## Requisitos

- PHP >= 8.0
- Composer
- Banco de dados MySQL.

## Instalação

1. Clone o repositório:
    ```sh
    git clone https://github.com/pedrojaraujo/fastfreela-back.git
    cd fastfreela-back
    ```

2. Instale as dependências do PHP:
    ```sh
    composer install
    ```

3. Configure o arquivo `.env`:
    ```sh
    cp .env.example .env
    ```

4. Gere a chave da aplicação:
    ```sh
    php artisan key:generate
    ```

5. Configure o banco de dados no arquivo `.env` e execute as migrações:
    ```sh
    php artisan migrate
    ```

## Endpoints

### Autenticação

- **Login**
    - **POST** `/api/login`
    - Request:
        ```json
        {
            "email": "user@example.com",
            "password": "password"
        }
        ```
    - Response:
        ```json
        {
            "message": "Login feito com sucesso!",
            "token": "token_gerado"
        }
        ```

- **Logout**
    - **POST** `/api/logout`
    - Request: Bearer Token no header
    - Response:
        ```json
        {
            "message": "Deslogado com sucesso!"
        }
        ```

### Usuários

- **Registrar Usuário**
    - **POST** `/api/register`
    - Request:
        ```json
        {
            "name": "Nome do Usuário",
            "email": "user@example.com",
            "password": "password",
            "user_type": "freelancer"
        }
        ```
    - Response:
        ```json
        {
            "message": "Usuário criado com sucesso!",
            "user": {
                "id": 1,
                "name": "Nome do Usuário",
                "email": "user@example.com",
                "user_type": "freelancer"
            },
            "token": "token_gerado"
        }
        ```

- **Atualizar Usuário**
    - **PUT** `/api/users/{id}`
    - Request:
        ```json
        {
            "name": "Novo Nome",
            "email": "newemail@example.com",
            "password": "newpassword",
            "user_type": "contractor"
        }
        ```
    - Response:
        ```json
        {
            "message": "Usuário atualizado com sucesso!",
            "user": {
                "id": 1,
                "name": "Novo Nome",
                "email": "newemail@example.com",
                "user_type": "contractor"
            }
        }
        ```

- **Deletar Usuário**
    - **DELETE** `/api/users/{id}`
    - Response:
        ```json
        {
            "message": "Usuário deletado com sucesso!"
        }
        ```

## Tratamento de Erros

A API retorna códigos de status HTTP apropriados para indicar o sucesso ou falha das operações. Em caso de erro, a resposta incluirá uma mensagem de erro e, em alguns casos, detalhes adicionais.

- **400**: Campos não permitidos na requisição
- **401**: Credenciais inválidas
- **404**: Usuário não encontrado
- **422**: Erro de validação
- **500**: Erro interno do servidor

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues e pull requests.

## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.
