<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Igreja - Financeiro</title>
    <style>
        /* --- DESIGN SYSTEM (Mesmo do Dashboard Principal) --- */
        :root {
            --azul-fundo: #001f3f;
            --azul-sombra: #000a14;
            --branco: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--azul-fundo);
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .container {
            width: 100%;
            height: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            align-items: center;
            padding: 20px;
        }

        /* --- BOTÕES (CSS SÓLIDO SEM BORDAS BRANCAS) --- */
        .btn {
            background-color: var(--azul-fundo);
            color: var(--branco);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 14px;
            
            /* Efeito 3D Escuro */
            box-shadow: 
                0px 6px 0px 0px var(--azul-sombra), 
                0px 8px 15px rgba(0,0,0,0.3); 
            
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            
            cursor: pointer;
            position: relative;
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
            user-select: none;
        }

        /* Hover Desktop */
        .btn:hover {
            transform: translateY(-2px);
            border-color: rgba(255,255,255,0.3);
        }

        /* --- CLIQUE (INVERSÃO DE CORES) --- */
        .btn:active, .btn-pressed {
            transform: translateY(6px);
            box-shadow: 0px 0px 0px 0px var(--azul-sombra); /* Sombra some */
            background-color: var(--branco) !important;
            color: var(--azul-fundo) !important;
            border-color: var(--branco) !important;
        }

        .btn:active svg, .btn-pressed svg {
            fill: var(--azul-fundo) !important;
        }

        /* --- GRID CENTRAL (4 ITENS) --- */
        .grid-financeiro {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr; /* 4 colunas no desktop */
            gap: 20px;
            width: 100%;
            max-width: 900px;
        }

        /* Botões H1 (Centrais) */
        .btn-h1 {
            height: 160px; /* Altura equilibrada */
            font-size: 1rem;
            gap: 12px;
        }

        /* Botões H2 (Topo e Base) */
        .btn-h2 {
            width: 240px;
            height: 50px;
            font-size: 0.9rem;
            box-shadow: 0px 4px 0px 0px var(--azul-sombra);
        }
        
        .btn-h2:active {
            transform: translateY(4px);
        }

        /* Ícones */
        svg {
            fill: var(--branco);
            width: 45px;
            height: 45px;
            transition: fill 0.1s;
            pointer-events: none;
        }

        /* --- RESPONSIVIDADE MOBILE (VERTICAL & CLEAN) --- */
        @media (max-width: 900px) {
            .grid-financeiro {
                display: flex; /* Muda para Flexbox */
                flex-direction: column; /* Pilha Vertical */
                gap: 15px;
                width: 100%;
                max-width: 300px; /* Limita largura para não estourar */
            }

            .btn-h1 {
                width: 100%;
                height: 80px; /* Botões mais baixos (retangulares horizontais) */
                flex-direction: row; /* Ícone ao lado do texto */
                justify-content: start;
                padding-left: 30px;
                font-size: 1rem;
            }

            .btn-h1 svg {
                width: 30px;
                height: 30px;
                margin-bottom: 0;
                margin-right: 15px;
            }

            /* No mobile, o grid vira lista. Ajuste do texto */
            .btn-h1 span {
                text-align: left;
            }

            .container {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <a href="dashboard" class="btn btn-h2">
            VOLTAR AO MENU
        </a>

        <div class="grid-financeiro">
            
            <a href="financeiro_entradas" class="btn btn-h1">
                <svg viewBox="0 0 24 24">
                    <path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 10H3V8h18v8z"/>
                    <path d="M12 17l-4-4h3V9h2v4h3l-4 4z"/>
                </svg>
                <span>ENTRADAS</span>
            </a>

            <a href="financeiro_saidas" class="btn btn-h1">
                <svg viewBox="0 0 24 24">
                    <path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 10H3V8h18v8z"/>
                    <path d="M12 7l-4 4h3v4h2v-4h3l-4-4z"/>
                </svg>
                <span>SAÍDAS</span>
            </a>

            <a href="financeiro_bi" class="btn btn-h1">
                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                <span>BI & DADOS</span>
            </a>

            <a href="financeiro_relatorios" class="btn btn-h1">
                <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                <span>RELATÓRIOS</span>
            </a>

        </div>

        <a href="financeiro_cadastros" class="btn btn-h2">
            CADASTRAMENTOS
        </a>

    </div>

    <script>
        // Script simples para feedback visual de clique (Inversão + 3D)
        const botoes = document.querySelectorAll('.btn');
        botoes.forEach(btn => {
            btn.addEventListener('touchstart', () => btn.classList.add('btn-pressed'));
            btn.addEventListener('touchend', () => btn.classList.remove('btn-pressed'));
            btn.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href !== '#') {
                    e.preventDefault();
                    this.classList.add('btn-pressed');
                    setTimeout(() => {
                        window.location.href = href;
                    }, 150);
                }
            });
        });
    </script>
</body>
</html>