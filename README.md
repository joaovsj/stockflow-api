# Guia da API

As rotas estão todas em inglês por questão de convenção e padronização de desenvolvimento, mas tudo aquilo que for exibido para usuário será retornado em português, como por exemplo as mensagens de erros nas validações, serão retornadas em português. Cabe apenas ao responsável pelo Frontend exibí-las.  </br> </br>
Lembrando que os índices devem ser passados da mesma forma como no documento abaixo, vou anexar um link para as requisições do POSTMAN, não esqueça de alterar a variável de ambiente para FATEC, para que ele possa identificá-la. </br> </br>

![image](https://github.com/joaovsj/warehouse-api/assets/113035480/3659e4c7-df9a-44c5-a825-77f3cc935314)


# Guia de Instação 

> PHP^8.1 </br>
> COMPOSER 


1. Após clonar o repositório, abra-o na raiz do projeto e digite o seguinte comando:
```php
composer install
```
2. você verá que existe um arquivo chamado _.env.example_, não apague. Duplique o que já existe e retire o _".example"_
3. Substitua o nome do banco de dados para *warehouse*
  
```php 
DB_DATABASE=warehouse
```

4. Crie o banco de dados com o nome *warehouse* usando a utf8mb4_general_ci
5. Abra-o na raiz do projeto novamente e digite os seguintes comandos:

```php
php artisan key:generate

php artisan migrate

php artisan serve
```
Prontinho, agora é só utilizar!


# Rotas

Para esse projeto está sendo utilIzado o  pacote Sanctum para geração do TOKEN de acesso, não se esqueça de adicioná-lo como Bearer Token para seu acesso.


## Login

| Rota | Método | Função |
|---|---|----|
| `/register` | POST | Cadastra um usuário
| `/login` |  POST | Efetua o login
| `/users` |  GET | Retorna todos os usuários

* Estrutura que a rota de REGISTER expera:

```javascript
{
    "name": "rosa",
    "email": "rosa@gmail.com",
    "password": "12345",
    "password_confirmation": "12345"
}
```

* Estrutura que a rota de REGISTER retorna:

```javascript
{
    "status": true,
    "name": "pedro",
    "email": "pedro@gmail.com",
    "user_id": "Mg==",
    "token": "3|MWPZcwUehnF6JwNcocf7YOWLwjNY9ISN0LJIWp8P22d0efd2"
}
```
O id do usuário também é retornado CRIPOGRAFADO em Base64, para utilizá-lo, você precisa descripografar.
[Como descriptografar?](https://devpleno.com/como-converter-uma-string-em-base64-em-javascript-navegador-e-nodejs)

* Estrutura que a rota de LOGIN expera:
```javascript
{
    "email": "rosa@gmail.com",
    "password": "12345"
}
```



## Trabalhando com os valores

Lembre-se estamos trabalhando com as seguintes entidades:
* Categorias
* Fornecedores
* Unidades
</br>

Sabendo disso você ***DEVE*** cadastrar a categoria ***ANTES*** do produto, para que ele esteja ligado à ela. Em relação a UNIDADE e FORNECEDORES é totalmente ***OPCIONAL*** passá-los.

## Produtos

| Rota | Método | Função |
|---|---|----|
| `/products` |  GET | Lista todos os produtos
| `/products` |  POST | Adiciona um produto
| `/products/1` |  GET | Exibi um produto
| `/products/1` |  PUT | Altera um produto com _id_
| `/products/1` |  DELETE | Deleta um produto com _id_

* Estrutura que ela espera no método de EDIÇÃO e CADASTRAMENTO
 
```javascript
{
    "name": "Manteiga",
    "category_id": 3,
    "provider_id": 3,
    "unity_id": 1
}
```

## Categorias

| Rota | Método | Função |
|---|---|----|
| `/category` |  GET | Lista todos as categorias
| `/category` |  POST | Adiciona uma categoria
| `/category/1` |  GET | Exibi uma categoria
| `/category/1` |  PUT | Altera um categoria com _id_
| `/category/1` |  DELETE | Deleta uma categoria com _id_

* Estrutura que ela espera no método de EDIÇÃO e CADASTRAMENTO
 
```javascript
{
    "name": "Café da Manhã",
    "description": "Teste"
}
```


## Fornecedores

| Rota | Método | Função |
|---|---|----|
| `/providers` |  GET | Lista todos os fornecedores
| `/providers` |  POST | Adiciona um fornecedor
| `/providers/1` |  GET | Exibi um fornecedor
| `/providers/1` |  PUT | Altera um fornecedor com _id_
| `/providers/1` |  DELETE | Deleta um fornecedor com _id_

* Estrutura que ela espera no método de EDIÇÃO e CADASTRAMENTO
 
```javascript
{
    "name": "Guardian Glass"
}
```


## Unidades

| Rota | Método | Função |
|---|---|----|
| `/units` |  GET | Lista todos as unidades
| `/units` |  POST | Adiciona uma unidade
| `/units/1` |  GET | Exibi uma unidade
| `/units/1` |  PUT | Altera uma unidade com _id_
| `/units/1` |  DELETE | Deleta uma unidade com _id_

* Estrutura que ela espera no método de EDIÇÃO e CADASTRAMENTO
 
```javascript
{
    "name": "KG",
    "unity": 100
}
```

## Movimentões

| Rota | Método | Função |
|---|---|----|
| `/movements` |  GET | Lista todos as movimentações
| `/movements` |  POST | Adiciona uma movimentação
| `/movements/1` |  GET | Exibi uma movimentação
| `/movements/1` |  PUT | Altera uma movimentação com _id_
| `/movements/1` |  DELETE | Deleta uma movimentação com _id_

* Estrutura que ela espera no método de EDIÇÃO e CADASTRAMENTO
 
```javascript
{
    "name": "Pão",
    "type": "E",
    "quantity": 15,
    "date": "2023-10-31 21:14:03",
    "product_id": 1,
    "user_id": "Nw=="
}
```




















