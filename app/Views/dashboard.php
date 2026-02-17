<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Igreja - Dashboard</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-sombra: #000a14;
            --branco: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--azul-fundo);
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; /* Evita rolagem elástica indesejada */
        }

        .container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly; /* Distribui com elegância (melhor que space-between) */
            align-items: center;
            padding: 20px; /* Margem de segurança da borda da tela */
        }

        /* ESTILO DO LOGOTIPO */
        .logo-igreja {
            max-width: 250px;
            height: auto;
            margin-bottom: 10px;
            filter: drop-shadow(0px 4px 10px rgba(0,0,0,0.3));
        }

        /* --- ESTILO BASE DO BOTÃO (LEVEZA) --- */
        .btn {
            background-color: var(--azul-fundo);
            color: var(--branco);
            border: 1px solid rgba(255,255,255,0.15); /* Borda muito sutil */
            border-radius: 16px; /* Mais arredondado = mais amigável */
            
            /* Sombra 3D calibrada (menos agressiva) */
            box-shadow: 
                0px 6px 0px 0px var(--azul-sombra), 
                0px 10px 20px rgba(0,0,0,0.3); 
            
            text-decoration: none;
            font-weight: 700; /* Reduzi de 900 para 700 (menos "gordo") */
            text-transform: uppercase;
            letter-spacing: 1px;
            
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            
            cursor: pointer;
            position: relative;
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
            user-select: none;
        }

        /* --- CLIQUE (INVERSÃO) --- */
        .btn:active, .btn-pressed {
            transform: translateY(6px);
            box-shadow: 0px 0px 0px 0px var(--azul-sombra);
            background-color: var(--branco) !important;
            color: var(--azul-fundo) !important;
            border-color: var(--branco) !important;
        }

        .btn:active svg, .btn-pressed svg {
            fill: var(--azul-fundo) !important;
        }

        /* --- HIERARQUIA DESKTOP (MANTIDA, MAS REFINADA) --- */
        .btn-h1 {
            width: 280px; /* Reduzi um pouco o desktop também para elegância */
            height: 200px;
            font-size: 1.4rem;
            gap: 15px;
        }

        .btn-h2 {
            width: 220px;
            height: 55px;
            font-size: 0.9rem;
            box-shadow: 0px 4px 0px 0px var(--azul-sombra);
        }
        
        .btn-h2:active {
            transform: translateY(4px);
        }

        .center-block {
            display: flex;
            gap: 40px;
            justify-content: center;
            align-items: center;
        }

        svg {
            fill: var(--branco);
            width: 60px; /* Ícone um pouco menor */
            height: 60px;
            transition: fill 0.1s;
            pointer-events: none;
        }

        /* --- O SEGREDO DO MOBILE (DESCOMPRESSÃO) --- */
        @media (max-width: 768px) {
            .container {
                padding: 40px 20px; /* Mais ar nas laterais */
            }

            .logo-igreja {
                max-width: 180px; /* Logo menor no mobile */
            }

            .center-block {
                flex-direction: column;
                gap: 25px; /* Espaço claro entre os botões */
                width: 100%;
            }

            .btn-h1 {
                /* AQUI ESTÁ A CORREÇÃO DA "GORDURA" */
                width: 100%; 
                max-width: 260px; /* Limita a largura (não deixa explodir na tela) */
                height: 140px;    /* Altura drasticamente menor */
                font-size: 1.1rem; /* Fonte menor */
            }
            
            /* Ajuste proporcional do ícone no mobile */
            .btn-h1 svg {
                width: 45px;
                height: 45px;
                margin-bottom: 8px;
            }

            .btn-h2 {
                width: 180px; /* Botões secundários discretos */
                height: 45px;
                font-size: 0.8rem;
                box-shadow: 0px 3px 0px 0px var(--azul-sombra);
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <a href="#" class="btn btn-h2" id="btn-sair">SAIR DO SISTEMA</a>

        <img src="img/logo.png" alt="Logotipo Igreja" class="logo-igreja">

        <div class="center-block">
            <a href="membros" class="btn btn-h1">
                <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                MEMBROS
            </a>
            
            <a href="financeiro" class="btn btn-h1">
                <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                FINANCEIRO
            </a>
        </div>

        <a href="ajustes" class="btn btn-h2">AJUSTES TÉCNICOS</a>

    </div>

    <script>
        document.getElementById('btn-sair').addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            btn.classList.add('btn-pressed'); 
            setTimeout(() => {
                if (confirm("Deseja encerrar a sessão?")) {
                    window.location.href = "logout";
                }
                btn.classList.remove('btn-pressed');
            }, 150);
        });
    </script>
</body>
</html>