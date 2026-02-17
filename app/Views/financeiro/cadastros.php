<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Igreja - Cadastros</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-claro: #003366;
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
            color: var(--branco);
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .header {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 30px 5%;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .header h1 {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2.2rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 900;
            text-align: center;
        }

        .btn-voltar {
            background-color: var(--azul-fundo);
            color: var(--branco);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 12px 25px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0px 5px 0px 0px var(--azul-sombra);
            transition: all 0.1s;
        }
        .btn-voltar:active { transform: translateY(5px); box-shadow: none; }

        .container-menu {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .grid-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* Ajustado para 2 colunas */
            gap: 40px;
            width: 80%;
            max-width: 900px;
        }

        /* --- ESTILO BASE DO BOTÃO (PADRÃO DASHBOARD) --- */
        .btn-cad {
            background-color: var(--azul-fundo);
            color: var(--branco);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            height: 240px;
            
            box-shadow: 
                0px 8px 0px 0px var(--azul-sombra), 
                0px 15px 30px rgba(0,0,0,0.4); 
            
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            
            cursor: pointer;
            transition: all 0.15s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .btn-cad:active {
            transform: translateY(8px);
            box-shadow: 0px 0px 0px 0px var(--azul-sombra);
            background-color: var(--branco);
            color: var(--azul-fundo);
        }

        .btn-cad svg {
            fill: var(--branco);
            width: 80px;
            height: 80px;
            margin-bottom: 25px;
            transition: fill 0.1s;
        }

        .btn-cad:active svg {
            fill: var(--azul-fundo);
        }

        @media (max-width: 900px) {
            .grid-buttons {
                grid-template-columns: 1fr;
                max-width: 350px;
                gap: 25px;
            }
            .btn-cad {
                height: 160px;
                flex-direction: row;
                gap: 20px;
                font-size: 1rem;
            }
            .btn-cad svg {
                width: 50px;
                height: 50px;
                margin-bottom: 0;
            }
            .header h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

    <div class="header">
        <a href="financeiro" class="btn-voltar">VOLTAR</a>
        <h1>CADASTROS</h1>
    </div>

    <div class="container-menu">
        <div class="grid-buttons">
            
            <a href="importar_planilha" class="btn-cad">
                <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                IMPORTAÇÃO DE<br>PLANILHAS
            </a>
            
            <a href="importar_nomes" class="btn-cad">
                <svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                IMPORTAÇÃO DE<br>NOMES E DADOS
            </a>

        </div>
    </div>

</body>
</html>