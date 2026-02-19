<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestão de Igreja</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text: #1f2937;
            --text-muted: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--bg);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: radial-gradient(circle at 20% 20%, rgba(79, 70, 229, 0.05) 0%, transparent 50%),
                              radial-gradient(circle at 80% 80%, rgba(79, 70, 229, 0.05) 0%, transparent 50%);
        }

        .login-card {
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 64px;
            height: 64px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        h1 {
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        p {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 1rem;
        }

        button:hover {
            background: var(--primary-hover);
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: none;
            text-align: center;
        }

        .loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="header">
            <div class="logo">GI</div>
            <h1>Bem-vindo de volta</h1>
            <p>Acesse sua conta para gerenciar a igreja</p>
        </div>

        <div id="error" class="error-message"></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" placeholder="seu@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" placeholder="••••••••" required>
            </div>
            <button type="submit" id="btnSubmit">
                <span id="btnText">Entrar</span>
                <div id="loader" class="loader"></div>
            </button>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnText = document.getElementById('btnText');
        const loader = document.getElementById('loader');
        const errorDiv = document.getElementById('error');

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            errorDiv.style.display = 'none';
            btnText.style.display = 'none';
            loader.style.display = 'block';
            btnSubmit.disabled = true;

            const email = document.getElementById('email').value;
            const senha = document.getElementById('password').value;

            try {
                const response = await fetch('index.php?url=autenticar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, senha })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    window.location.href = 'index.php?url=dashboard';
                } else {
                    throw new Exception(result.message);
                }
            } catch (err) {
                errorDiv.innerText = err.message || 'Erro ao realizar login. Tente novamente.';
                errorDiv.style.display = 'block';
                btnText.style.display = 'block';
                loader.style.display = 'none';
                btnSubmit.disabled = false;
            }
        });
    </script>
</body>
</html>
