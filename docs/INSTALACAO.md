# Guia de Instalação e Configuração

## Requisitos
- PHP 8.0+
- MySQL/MariaDB 5.7+
- Composer
- Laragon ou XAMPP

## Passo a Passo

1. **Clonar/Extrair o projeto**
   ```bash
   cd d:/laragon/www/gestao_igreja
   ```

2. **Instalar Dependências**
   ```bash
   composer install
   ```

3. **Configurar Ambiente**
   - Renomeie `.env.example` para `.env`
   - Configure as credenciais do banco:
     ```env
     DB_HOST=localhost
     DB_NAME=gestao_igreja
     DB_USER=root
     DB_PASS=
     ```

4. **Banco de Dados**
   - Crie o banco `gestao_igreja`.
   - Importe o arquivo `gestao_igreja_backup.sql` (opcional para dados legados).
   - O sistema criará a tabela `usuarios` automaticamente se necessário (ou use o setup).

5. **Acesso**
   - URL: `http://localhost/gestao_igreja/public/`
   - Usuário Padrão: `admin@igreja.com`
   - Senha Padrão: `admin123`
