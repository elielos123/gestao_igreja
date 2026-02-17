# Arquitetura do Sistema

O sistema utiliza o padrão **MVC (Model-View-Controller)** simplificado com um **Front Controller** como roteador.

## Estrutura de Pastas

*   `/app`: Core da aplicação.
    *   `/Config`: Configurações globais e conexão PDO.
    *   `/Controllers`: Lógica de controle e processamento de dados.
    *   `/Models`: Interação direta com o banco de dados.
    *   `/Views`: Arquivos de interface (HTML/PHP).
*   `/public`: Ponto de entrada único.
    *   `index.php`: Roteador principal e carregamento do Autoload.
*   `/vendor`: Bibliotecas de terceiros (Composer).
*   `/docs`: Documentação do projeto.

## Padrões Adotados
- **Autoloading**: PSR-4 (Namespace `App\`).
- **Conexão**: PDO com suporte a transações e Prepared Statements.
- **Configuração**: Dotenv (`.env`) para separação de ambiente e código.
