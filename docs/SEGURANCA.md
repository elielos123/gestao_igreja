# Segurança e Autenticação

## Proteção de Acesso
O sistema implementa uma camada de segurança robusta no `LoginController`:
- **Sessões**: Gerenciadas via `PHPSESSID`.
- **Middleware**: O método `LoginController::checkAuth()` valida se o usuário está logado antes de processar qualquer rota privada no `index.php`.

## Armazenamento de Senhas
- **Criptografia**: As senhas são armazenadas como hashes utilizando o algoritmo **BCRYPT** (`password_hash`).
- **Verificação**: Realizada através do `password_verify`.

## Níveis de Acesso (ACL)
O sistema está preparado para os seguintes níveis (em implementação de UI):
1. `admin`: Acesso total.
2. `pastor`: Relatórios e visualização.
3. `tesoureiro`: Entradas e saídas financeiras.
4. `secretario`: Membros e cadastros básicos.
