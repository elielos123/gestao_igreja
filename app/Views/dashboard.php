<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Igreja - Painel Principal</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-sombra: #000a14;
            --branco: #ffffff;
            --verde-sucesso: #2ecc71;
            --azul-claro: #3498db;
            --laranja: #e67e22;
            --vermelho: #e74c3c;
            --gradiente: linear-gradient(135deg, #001f3f 0%, #000a14 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        body {
            background: var(--gradiente);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--branco);
            overflow-x: hidden;
        }

        .header {
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-img {
            height: 50px;
            filter: drop-shadow(0 0 10px rgba(46, 204, 113, 0.3));
        }

        .user-info {
            text-align: right;
        }

        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeInDown 0.8s ease;
        }

        .welcome-text h1 {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 10px;
            background: linear-gradient(to right, #fff, #2ecc71);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text p {
            color: rgba(255,255,255,0.6);
            font-size: 1.1rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            width: 100%;
            perspective: 1000px;
        }

        .menu-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 30px;
            padding: 40px 30px;
            text-decoration: none;
            color: var(--branco);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .menu-card:hover {
            transform: translateY(-10px) rotateX(5deg);
            background: rgba(255,255,255,0.07);
            border-color: var(--verde-sucesso);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .menu-card:hover::before { opacity: 1; }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.05);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .menu-card:hover .icon-box {
            background: var(--verde-sucesso);
            transform: scale(1.1) rotate(5deg);
        }

        .icon-box svg {
            width: 40px;
            height: 40px;
            fill: var(--branco);
        }

        .card-info { text-align: center; }
        .card-info h3 { font-size: 1.35rem; margin-bottom: 5px; font-weight: 700; }
        .card-info p { font-size: 0.9rem; color: rgba(255,255,255,0.5); }

        .btn-logout {
            margin-top: 50px;
            padding: 12px 30px;
            border-radius: 50px;
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: var(--vermelho);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-logout:hover {
            background: var(--vermelho);
            color: #fff;
            box-shadow: 0 0 20px rgba(231, 76, 60, 0.4);
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .welcome-text h1 { font-size: 2rem; }
            .menu-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo-box">
            <img src="img/logo.png" alt="Logo" class="logo-img">
            <div style="font-weight: 800; font-size: 1.2rem; letter-spacing: -1px;">Gestão<span style="color: var(--verde-sucesso)">Igreja</span></div>
        </div>
        <div class="user-info">
            <p style="font-size: 0.8rem; opacity: 0.5;">Bem-vindo,</p>
            <p style="font-weight: 700;"><?= $_SESSION['usuario_nome'] ?? 'Administrador' ?></p>
        </div>
    </header>

    <main class="main-container">
        
        <div class="welcome-text">
            <h1>Menu Principal</h1>
            <p>Selecione uma área para gerenciar os dados da igreja</p>
        </div>

        <div class="menu-grid">
            
            <!-- USUÁRIOS (ACL) -->
            <?php if (\App\Helpers\Acl::canView('manage_users')): ?>
            <a href="usuarios" class="menu-card" style="grid-column: span 1;">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="card-info">
                    <h3>Usuários e ACL</h3>
                    <p>Gerenciar acessos e permissões</p>
                </div>
            </a>
            <?php endif; ?>

            <!-- MEMBROS -->
            <?php if (\App\Helpers\Acl::canView('view_membros')): ?>
            <a href="membros" class="menu-card" style="grid-column: span 1;">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
                <div class="card-info">
                    <h3>Gestão de Membros</h3>
                    <p>Membros, Congregações e Funções</p>
                </div>
            </a>
            <?php endif; ?>

            <!-- FINANCEIRO -->
            <?php if (\App\Helpers\Acl::canView('view_financeiro')): ?>
            <a href="financeiro" class="menu-card" style="grid-column: span 1;">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
                <div class="card-info">
                    <h3>Financeiro</h3>
                    <p>Dízimos, ofertas e despesas</p>
                </div>
            </a>
            <?php endif; ?>

        </div>

        <a href="logout" class="btn-logout" onclick="return confirm('Deseja realmente sair?')">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M13 3h-2v10h2V3zm4.83 2.17l-1.42 1.42C17.99 7.86 19 9.81 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-2.19 1.01-4.14 2.58-5.42L6.17 5.17C4.23 6.82 3 9.26 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.74-1.23-5.18-3.17-6.83z"/></svg>
            SAIR DO SISTEMA
        </a>

    </main>

</body>
</html>