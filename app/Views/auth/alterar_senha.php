<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - Gestão Igreja</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-claro: #3498db;
            --verde: #2ecc71;
            --branco: #ffffff;
            --vermelho: #e74c3c;
        }
        body {
            background: radial-gradient(circle at center, #001f3f 0%, #000a14 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }
        .box {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        h2 { color: var(--azul-claro); margin-bottom: 10px; }
        p { opacity: 0.7; margin-bottom: 25px; font-size: 0.9rem; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        input {
            width: 100%;
            padding: 12px;
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            border-radius: 8px;
            font-size: 1rem;
        }
        input:focus { border-color: var(--azul-claro); outline: none; }
        .btn {
            width: 100%;
            padding: 15px;
            background: var(--verde);
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 1rem;
        }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3); }
        .error { color: var(--vermelho); font-size: 0.85rem; margin-top: 5px; display: none; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Primeiro Acesso</h2>
        <p>Por segurança, você deve definir uma senha definitiva agora.</p>
        
        <form id="formPass">
            <div class="form-group">
                <label>Nova Senha:</label>
                <input type="password" id="senha" required>
            </div>
            <div class="form-group">
                <label>Confirme a Senha:</label>
                <input type="password" id="senha2" required>
                <div id="errMsg" class="error"></div>
            </div>
            <button type="submit" class="btn">DEFINIR SENHA E ACESSAR</button>
        </form>
    </div>

    <script>
        document.getElementById('formPass').addEventListener('submit', async (e) => {
            e.preventDefault();
            const s1 = document.getElementById('senha').value;
            const s2 = document.getElementById('senha2').value;
            const err = document.getElementById('errMsg');

            if (s1 !== s2) {
                err.innerText = "As senhas não coincidem.";
                err.style.display = 'block';
                return;
            }

            try {
                const response = await fetch('index.php?url=alterar_senha_primeiro_acesso', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ senha: s1 })
                });
                const res = await response.json();
                if (res.status === 'success') {
                    window.location.href = 'index.php?url=dashboard';
                } else {
                    err.innerText = res.message;
                    err.style.display = 'block';
                }
            } catch (error) {
                err.innerText = "Erro na conexão.";
                err.style.display = 'block';
            }
        });
    </script>
</body>
</html>
