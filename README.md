# Sistema para Mercadinho Autônomo

Projeto web da turma **BSI_DevWeb 2026/2**, desenvolvido com **CodeIgniter 4**.

**Integrantes:** Apoema Fernandes, Eduarda Coelho, João Pedro, Matheus Abdon e Arthur Miguel.

## Divisão de responsabilidades

- **Eduarda Coelho:** estrutura inicial do projeto, desenvolvimento do módulo de Categorias e criação do repositório no GitHub.
- **Apoema Fernandes:** desenvolvimento do CRUD de Produtos.
- **João Pedro:** desenvolvimento do CRUD de Fornecedores.
- **Matheus Abdon:** desenvolvimento do CRUD de Clientes.
- **Arthur Miguel:** posteriormente, desenvolvimento do Login e gerenciamento de perfis (RF01).

## Tecnologias

- PHP 8.1+ e CodeIgniter 4 (incluído na pasta `system`, sem necessidade de instalação via Composer).
- SQLite (banco de dados local, sem necessidade de servidor).
- Bootstrap 5 (via CDN).

## Como executar no Windows

1. Instale o **Laragon (Full)** e abra o Terminal do Laragon, que já disponibiliza o PHP no PATH.
2. Entre na pasta do projeto:

```bash
cd C:\laragon\www\mercadinho\mercadinho
```

3. Execute o arquivo de inicialização:

```bash
.\iniciar.bat
```

4. Acesse o sistema pelo navegador:

http://localhost:8080

**Observação:** caso ocorra algum erro relacionado às extensões `intl` ou `sqlite3`, abra o arquivo `php.ini` utilizado pelo PHP do Laragon e habilite as seguintes extensões, removendo o `;` do início das linhas:

```ini
extension=intl
extension=sqlite3
extension=pdo_sqlite
```

Após a alteração, reinicie o terminal ou o Laragon.

## Como criar o banco de dados

O banco de dados é criado por meio das **Migrations** e populado pelos **Seeders**. O arquivo `iniciar.bat` automatiza esse processo.

Para executar manualmente, utilize:

```bash
copy env .env
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve --port 8080
```

O banco de dados SQLite fica armazenado em:

```text
writable/mercadinho.db
```

Esse arquivo é local e não deve ser enviado ao repositório GitHub.

Para recriar o banco de dados do zero, exclua o arquivo `writable/mercadinho.db` e execute novamente os comandos de inicialização.

Os Seeders são responsáveis por inserir os dados iniciais nas tabelas.

## Módulos implementados e pendentes

### Implementados

- [x] Categorias
- [x] Produtos (integrados às Categorias)
- [x] Fornecedores
- [x] Clientes

### Pendentes

- [ ] Login e gerenciamento de perfis (RF01)
- [ ] Controle de estoque e movimentações (RF03)
- [ ] Registro de vendas (RF05)
- [ ] Registro de compras de fornecedores (RF14)
- [ ] Relatórios de vendas e de estoque (RF06 e RF15)

## Segurança

O sistema utiliza mecanismos de segurança disponibilizados pelo CodeIgniter 4:

- Filtro `csrf` ativado globalmente em `app/Config/Filters.php`.
- Utilização de `csrf_field()` nos formulários para proteção contra ataques CSRF.
- Utilização de `esc()` nas saídas das Views para prevenção de ataques XSS.
- Operações que alteram dados (criação, edição e exclusão) realizadas por requisições **POST**.
- Configurações armazenadas no arquivo `.env`, que não deve ser enviado ao repositório. O arquivo `env` é utilizado como modelo de configuração.

## Estrutura do projeto (MVC)

O projeto utiliza o padrão arquitetural **Model-View-Controller (MVC)**, separando as responsabilidades da aplicação.

- `app/Models/`: definição das tabelas, campos permitidos (`allowedFields`) e regras de validação dos módulos.
- `app/Controllers/`: recebem as requisições, acionam os Models e selecionam as Views.
- `app/Views/`: contém as interfaces do sistema, incluindo o layout principal (`layouts/base.php`) e pastas específicas para cada módulo.
- `app/Config/Routes.php`: definição e organização das rotas da aplicação.
- `app/Helpers/formatacao_helper.php`: funções auxiliares para formatação de CNPJ, CPF e telefone.
- `app/Database/Migrations/`: arquivos responsáveis pela criação e estruturação das tabelas do banco de dados.
- `app/Database/Seeds/`: arquivos responsáveis pela inserção dos dados iniciais no banco.

## Organização e colaboração no GitHub

O desenvolvimento é realizado de forma colaborativa, com divisão de responsabilidades entre os integrantes.

Cada participante deve trabalhar em sua respectiva funcionalidade, utilizando branches individuais para organizar as alterações.

Após a implementação, as contribuições serão registradas por meio de commits e integradas à branch principal (`main`) mediante Pull Requests.

Essa organização permite acompanhar o histórico de desenvolvimento e identificar as contribuições de cada integrante.