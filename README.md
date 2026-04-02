# Sistema de Vendas

Um sistema completo de Ponto de Venda (PDV) e Gestão de Estoque desenvolvido do zero utilizando PHP Orientado a Objetos (POO), sem a dependência de frameworks externos. 

Este projeto foi construído inspirado em um outro projeto que fiz para um estágio. Fiz baseado em minha memória, então os projetos não são totalmente iguais.

---

## Principais Funcionalidades

* **Ponto de Venda (PDV) Reativo:** Interface de caixa fluida via AJAX (Fetch API), permitindo adicionar itens, alterar quantidades e aplicar descontos sem recarregar a página. Suporte a atalhos de teclado (Ex: `F2` para finalizar venda).
* **Gestão de Inventário e Fornecedores:** Controle de estoque com alertas de nível baixo e relacionamento direto com fornecedores.
* **Controle de Acessos (RBAC):** Sistema de permissões baseado em papéis (`Admin` e `Seller`), blindado via Middlewares no backend.
* **Emissão de Notas Fiscais (Simulada):** Geração de cupons não-fiscais prontos para impressão térmica e vinculados a uma transação de banco de dados.
* **Dashboard e Relatórios:** Acompanhamento de métricas de vendas, faturamento e alertas de reposição.

---

## Arquitetura e Padrões de Projeto

O sistema foi desenhado visando a separação de responsabilidades, manutenibilidade e escalabilidade:

* **MVC (Model-View-Controller):** Separação estrita entre a lógica de negócios, a interface do usuário e o roteamento HTTP.
* **DAO (Data Access Object):** Todo o acesso ao banco de dados foi abstraído em classes DAO, garantindo que as *Models* permaneçam puras e focadas apenas nas regras de negócio.
* **Singleton:** Utilizado na classe de conexão com o banco de dados (`Database`) para garantir uma única instância do PDO durante todo o ciclo de vida da requisição HTTP.
* **Front Controller:** Todas as requisições são centralizadas no `public/index.php`, que inicializa o ambiente e despacha a requisição através de um Roteador customizado.

---

## Segurança Aplicada

A segurança foi tratada como requisito não-funcional crítico em toda a aplicação:

* **Proteção contra CSRF:** Middleware exclusivo que gera e valida tokens criptográficos (`random_bytes` e `hash_equals`) para todas as requisições que alteram estado (POST), incluindo chamadas AJAX.
* **Prevenção contra SQL Injection:** Uso exclusivo de Prepared Statements (PDO) com desativação de emulação (`PDO::ATTR_EMULATE_PREPARES = false`).
* **Proteção de Credenciais:** Senhas protegidas utilizando *hash*. Gestão de variáveis de ambiente via `.env` para que chaves e dados de banco não fiquem expostos no código-fonte.
* **Transações ACID:** O processo de checkout (baixa de estoque, registro de pagamento, gravação da venda e geração de NF) é encapsulado em uma única transação (`beginTransaction` e `commit`), garantindo a integridade dos dados via `rollBack` em caso de falhas de lógica ou infraestrutura.

---

## Testes Automatizados

O sistema conta com uma suíte de testes construída com PHPUnit, dividida em duas frentes:

1. **Testes Unitários:** Validação das regras de negócio matemáticas (cálculos de subtotal e descontos das Models) e blindagem dos Middlewares de Segurança (CSRF e Auth).
2. **Testes de Integração:** Testes complexos executados contra um banco de dados de sandbox (`sales_system_test`). Valida o comportamento dos DAOs, garantindo que constraints do MySQL e controle transacional de estoque funcionem sob estresse.

---

## Instalação e Execução local

### Pré-requisitos
* PHP 8.1 ou superior
* MySQL 8.0 ou MariaDB
* PDO para abstração de bases de dados 
* Composer (Gerenciador de Dependências)
* Bootstrap 5 (frontend)

### Passo a passo:

1. Clone o repositório:  
    ```bash
    git clone https://github.com/fabricio-araujo94/sistema_de_vendas.git && cd sistema_de_vendas
    ```
    
2. Instale dependências:  
    ```bash
    composer install
    ```
    
3. Configure variáveis de ambiente:  
    ```bash
    cp .env.example .env
    ```
    
4. Prepare o banco de dados:
    * Crie um banco de dados chamado sales_system no seu MySQL.
    * Crie o banco de dados *sales_system*. O arquivo `.sql` está na pasta `config`.
    * Crie o banco *sales_system_test* se desejar rodar os testes automatizados.
5. Popule com dados de teste:  
    ```bash
    php seed.php
    ```
    
6. Inicie o servidor embutido:  
    `php -S localhost:8000 -t public/`  
    Acesse em: `http://localhost:8000`
    
## Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request