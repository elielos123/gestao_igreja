<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Igreja - Financeiro</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-sombra: #000a14;
            --branco: #ffffff;
            --verde-sucesso: #2ecc71;
            --azul-claro: #3498db;
            --vermelho-erro: #e74c3c;
            --cinza-texto: #666;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: var(--azul-fundo); color: var(--branco); min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; }

        .header { padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.2); }
        .logo-pequena { height: 35px; }
        .btn-voltar { color: var(--branco); text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 600; opacity: 0.8; transition: 0.2s; font-size: 0.9rem; }
        .btn-voltar:hover { opacity: 1; }

        .container { flex: 1; padding: 15px; max-width: 1400px; margin: 0 auto; width: 100%; }

        /* --- SUB MENU --- */
        .sub-menu { display: flex; gap: 8px; margin-bottom: 20px; background: rgba(0,0,0,0.1); padding: 5px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); overflow-x: auto; }
        .menu-item { 
            padding: 8px 18px; 
            border-radius: 10px; 
            cursor: pointer; 
            font-weight: 700; 
            color: rgba(255,255,255,0.4); 
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .menu-item.active { background: var(--azul-claro); color: white; box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3); }
        .menu-item:hover:not(.active) { color: white; background: rgba(255,255,255,0.05); }

        .section-content { display: none; animation: fadeIn 0.4s ease; }
        .section-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- CARD --- */
        .card { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(5px); border-radius: 15px; padding: 15px; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        
        .iframe-container { width: 100%; height: 75vh; border: none; border-radius: 15px; overflow: hidden; background: rgba(0,0,0,0.1); }
        iframe { width: 100%; height: 100%; border: none; }

    </style>
</head>
<body>

    <header class="header">
        <a href="dashboard" class="btn-voltar">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            VOLTAR AO PAINEL
        </a>
        <div style="display: flex; align-items: center; gap: 20px;">
            <?php if (\App\Helpers\Acl::canView('manage_users')): ?>
            <a href="index.php?url=usuarios" style="color: var(--verde-sucesso); text-decoration: none; font-size: 0.8rem; font-weight: 700;">GERENCIAR USUÁRIOS</a>
            <?php endif; ?>
            <img src="img/logo.png" alt="Logo" class="logo-pequena">
        </div>
    </header>

    <div class="container">
        
        <!-- NAVEGAÇÃO INTERNA -->
        <div class="sub-menu">
            <div class="menu-item active" data-url="financeiro_entradas">Entradas</div>
            <div class="menu-item" data-url="financeiro_saidas">Saídas</div>
            <div class="menu-item" data-url="financeiro_relatorios">Pesquisas &amp; Relatórios</div>
            <div class="menu-item" data-url="financeiro_incongruencias" style="color:var(--vermelho-erro)">⚠️ Incongruências</div>
            <div class="menu-item" data-url="financeiro_cadastros">Importações &amp; Cadastros</div>
            <div class="menu-item" data-url="financeiro_bi" style="color:#7FDBFF">⚡ BI Analytics</div>
        </div>

        <!-- CONTEÚDO DINÂMICO -->
        <div class="card">
            <div class="iframe-container">
                <iframe id="financeiro-iframe" src="index.php?url=financeiro_entradas"></iframe>
            </div>
        </div>

    </div>

    <script>
        document.querySelectorAll('.menu-item').forEach(item => {
            item.onclick = function() {
                // UI
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                // Iframe
                const url = this.getAttribute('data-url');
                document.getElementById('financeiro-iframe').src = 'index.php?url=' + url;
            };
        });

        // Opção para abrir aba específica via URL se necessário
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if(tab) {
            const target = document.querySelector(`.menu-item[data-url*="${tab}"]`);
            if(target) target.click();
        }
    </script>
</body>
</html>