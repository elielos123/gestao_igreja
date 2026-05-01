# 🏛️ Sistema de Gestão de Igreja

Sistema de gestão financeira e administrativa para igrejas.

## 📋 Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx com mod_rewrite habilitado

## 🚀 Instalação Local (Laragon/XAMPP)

1. Clone o repositório
2. Configure o banco de dados MySQL local.
3. Crie um arquivo `.env` na raiz do projeto com as seguintes variáveis:
   ```env
   DB_HOST=localhost
   DB_NAME=gestao_igreja
   DB_USER=root
   DB_PASS=sua_senha
   ```

## ☁️ Deploy na Vercel

Este projeto está configurado para rodar na Vercel usando um PHP Runtime de comunidade.

### Passos para Deploy:

1. **Repositório**: Certifique-se de que o código está no seu GitHub.
2. **Novo Projeto**: Na Vercel, importe o repositório.
3. **Variáveis de Ambiente**: No Dashboard da Vercel (Settings > Environment Variables), adicione as credenciais do seu banco de dados **remoto**:
   - `DB_HOST`: Host do banco remoto (ex: `mysql.servidor.com`)
   - `DB_NAME`: Nome do banco
   - `DB_USER`: Usuário
   - `DB_PASS`: Senha
4. **Deploy**: Clique em Deploy. A Vercel usará o arquivo `vercel.json` para configurar tudo automaticamente.

## 🔒 Segurança

O projeto agora utiliza variáveis de ambiente para maior segurança. 

- O arquivo `app/Config/Database.php` **é versionado**, mas ele não contém senhas. Ele lê as informações do sistema ou do arquivo `.env`.
- **NUNCA** coloque senhas diretamente no código.
- O arquivo `.env` está no `.gitignore` para sua segurança.

## 📊 Funcionalidades

- Gestão de membros e congregações
- Controle de entradas e saídas financeiras
- Relatórios avançados e Dashboard BI
- Sistema de Autenticação com 2FA (Opcional)
- Backup e Importação de dados

## 👤 Autor

**elielos123**
