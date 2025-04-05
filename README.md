# FastFreela API

Esta é a API do **FastFreela**, um aplicativo que conecta contratantes com prestadores de serviço.  
A API foi desenvolvida utilizando **Laravel** e fornece endpoints RESTful para autenticação, gerenciamento de usuários e serviços.

---

## 🔧 Tecnologias Utilizadas

- **PHP 8.0+** – Linguagem principal do backend.
- **Laravel** – Framework PHP moderno e robusto.
- **Laravel Sanctum** – Autenticação de APIs.
- **L5-Swagger** – Geração automática de documentação Swagger/OpenAPI.
- **Composer** – Gerenciador de dependências PHP.
- **MySQL** – Banco de dados relacional.

---

## ⚙️ Requisitos

- PHP >= 8.2
- Composer
- Banco de dados MySQL
- Extensões PHP recomendadas: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`

---

## 🚀 Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/pedrojaraujo/fastfreela-back.git
   cd fastfreela-back
   ```

2. **Instale as dependências**
   ```bash
   composer install
   ```

3. **Copie o arquivo `.env` e configure**
   ```bash
   cp .env.example .env
   ```

4. **Gere a chave da aplicação**
   ```bash
   php artisan key:generate
   ```

5. **Configure o banco de dados no `.env` e rode as migrações**
   ```bash
   php artisan migrate
   ```

6. *(Opcional)*: **Gere a documentação Swagger**
   ```bash
   php artisan l5-swagger:generate
   ```

---

## 📚 Documentação da API (Swagger)

A documentação interativa está disponível após rodar o comando `php artisan l5-swagger:generate`:

📌 Acesse: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

Você pode testar os endpoints diretamente pelo navegador via Swagger UI.

---

## 🔐 Autenticação

- **Login**
    - **POST** `/api/login`
    - **Request:**
      ```json
      {
        "email": "user@example.com",
        "password": "password"
      }
      ```
    - **Response:**
      ```json
      {
        "message": "Login feito com sucesso!",
        "token": "token_gerado"
      }
      ```

- **Logout**
    - **POST** `/api/logout`
    - **Autenticação:** Token Bearer
    - **Response:**
      ```json
      {
        "message": "Deslogado com sucesso!"
      }
      ```

---

## 👤 Usuários

- **Registrar Usuário**
    - **POST** `/api/register`
    - **Request:**
      ```json
      {
        "name": "Nome do Usuário",
        "email": "user@example.com",
        "password": "password",
        "user_type": "freelancer"
      }
      ```
    - **Response:**
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
    - **Request:**
      ```json
      {
        "name": "Novo Nome",
        "email": "newemail@example.com",
        "password": "newpassword",
        "user_type": "contractor"
      }
      ```
    - **Response:**
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
    - **Response:**
      ```json
      {
        "message": "Usuário deletado com sucesso!"
      }
      ```

---

## ❗ Tratamento de Erros

| Código | Significado                    |
|--------|--------------------------------|
| 400    | Campos não permitidos          |
| 401    | Credenciais inválidas          |
| 404    | Recurso não encontrado         |
| 422    | Erro de validação              |
| 500    | Erro interno do servidor       |

---

## 🤝 Contribuição

Contribuições são bem-vindas!  
Sinta-se à vontade para abrir **issues**, propor melhorias ou enviar **pull requests**.

---

## 📄 Licença

Este projeto está licenciado sob a licença **MIT**.  
Veja o arquivo [LICENSE](./LICENSE) para mais detalhes.
