# Segurança e Autenticação

## Proteção de Acesso
O sistema implementa uma camada de segurança robusta:
- **Sessões**: Gerenciadas via `PHPSESSID`.
- **Middleware**: O método `LoginController::checkAuth()` valida se o usuário está logado.
- **ACL (Access Control List)**: O sistema utiliza um controle de acesso baseado em papéis (RBAC) e permissões granulares, verificado via `App\Helpers\Acl::check()`.

## Autenticação
- **Identificador**: O login pode ser realizado tanto via **Nome de Usuário** (username) quanto via **E-mail**.
- **Criptografia**: As senhas são armazenadas como hashes utilizando o algoritmo **BCRYPT** (`password_hash`).
- **2FA (Opcional)**: Suporte para Autenticação de Dois Fatores via Google Authenticator.

## Níveis de Acesso e Papéis
A gestão de permissões é feita através de **Papéis** (Roles), que agrupam permissões específicas. 

### Papéis Padrão:
1. **Pastor**: Acesso a relatórios e visualização geral.
2. **Tesouraria**: Gerenciamento completo do módulo financeiro.
3. **Secretaria**: Gerenciamento de membros e cadastros auxiliares.

### Permissões Disponíveis:
- `manage_users`: Criar, editar e excluir usuários.
- `manage_roles`: Criar e editar papéis e suas permissões.
- `view_membros`: Visualizar lista de membros.
- `manage_membros`: Criar, editar e excluir membros.
- `view_financeiro`: Visualizar entradas, saídas e relatórios básicos.
- `manage_financeiro`: Realizar e editar lançamentos financeiros.
- `view_reports`: Acesso ao módulo de BI e relatórios avançados.
- `manage_settings`: Configurações do sistema e cadastros auxiliares.
- `manage_backup`: Realizar exportação e importação de backups.

### Administradores
Usuários com o nível `admin` no banco de dados possuem **acesso total irrestrito**, ignorando as verificações de papéis individuais.
