# 🏛️ Sistema de Gestão de Igreja

Sistema de gestão financeira e administrativa para igrejas.

## 📋 Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx com mod_rewrite habilitado

## 🚀 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/elielos123/gestao_igreja.git
cd gestao_igreja
```

### 2. Configure o banco de dados

1. Crie um banco de dados MySQL chamado `gestao_igreja`
2. Copie o arquivo de configuração de exemplo:

```bash
cp app/Config/Database.php.example app/Config/Database.php
```

3. Edite `app/Config/Database.php` com suas credenciais:

```php
private $host = "localhost";
private $db_name = "gestao_igreja";
private $username = "seu_usuario";
private $password = "sua_senha";
```

### 3. Importe o banco de dados

Se você tiver um backup SQL, importe-o:

```bash
mysql -u root -p gestao_igreja < seu_backup.sql
```

### 4. Configure o servidor web

#### Usando Laragon (Windows)
- Coloque o projeto em `C:\laragon\www\gestao_igreja`
- Acesse: `http://localhost/gestao_igreja/public/`

#### Usando XAMPP (Windows)
- Coloque o projeto em `C:\xampp\htdocs\gestao_igreja`
- Acesse: `http://localhost/gestao_igreja/public/`

## 🔒 Segurança

⚠️ **IMPORTANTE**: O arquivo `app/Config/Database.php` contém credenciais sensíveis e **NÃO** deve ser commitado no Git.

- ✅ Use `Database.php.example` como template
- ✅ Mantenha `Database.php` apenas localmente
- ✅ Nunca commite senhas ou credenciais

## 📁 Estrutura do Projeto

```
gestao_igreja/
├── app/
│   └── Config/
│       ├── Database.php.example  # Template de configuração
│       └── Database.php          # Suas credenciais (não versionado)
├── public/
│   ├── index.php                 # Página principal
│   ├── teste_db.php              # Teste de conexão
│   └── img/                      # Imagens
└── .gitignore
```

## 🧪 Testando a Conexão

Acesse `http://localhost/gestao_igreja/public/teste_db.php` para verificar se a conexão com o banco de dados está funcionando.

## 📊 Funcionalidades

- Gestão de congregações
- Controle de entradas financeiras
- Relatórios financeiros
- Dashboard administrativo

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto é privado e de uso restrito.

## 👤 Autor

**elielos123**

---

⚠️ **Lembre-se**: Nunca commite o arquivo `app/Config/Database.php` com suas credenciais reais!
