<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Financeiro - Saídas</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-claro: #003366;
            --azul-sombra: #000a14;
            --branco: #ffffff;
            --verde-sucesso: #2ECC40;
            --vermelho-erro: #FF4136;
            --amarelo-alerta: #FFDC00;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--azul-fundo);
            color: var(--vermelho-erro);
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* --- HEADER --- */
        .header {
            display: none;
        }

        .header h1 {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2.2rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 900;
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

        /* --- FORMULÁRIO --- */
        .form-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
        }

        .form-container {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 10px 0;
        }

        .form-row {
            display: flex;
            gap: 20px;
            width: 100%;
            position: relative;
        }

        .input-group {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .input-3d {
            background-color: rgba(255,255,255,0.02);
            color: var(--vermelho-erro);
            border: 1px solid rgba(255,65,54,0.3);
            border-bottom: 3px solid var(--azul-sombra);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.1s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            width: 100%;
        }
        
        .input-3d::placeholder { color: rgba(255,65,54,0.3); font-size: 0.9rem; }
        .input-3d:focus {
            background-color: var(--branco);
            color: var(--vermelho-erro);
            border-color: var(--vermelho-erro);
            border-bottom: 2px solid var(--vermelho-erro); 
            transform: translateY(4px); 
            font-weight: 800;
        }

        select.input-3d {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23FF4136%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%0-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 25px center;
            background-size: 18px auto;
        }

        .suggestions-list {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: var(--azul-claro);
            border: 2px solid var(--vermelho-erro);
            border-radius: 0 0 12px 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            z-index: 100;
            max-height: 250px;
            overflow-y: auto;
            display: none;
            margin-top: -5px;
        }

        .suggestion-item {
            padding: 10px 20px;
            color: var(--branco);
            font-size: 1rem;
            cursor: pointer;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: background 0.2s;
        }
        .suggestion-item:hover { background-color: var(--vermelho-erro); font-weight: bold; }

        /* --- DOCK / HISTÓRICO --- */
        .dock-container {
            flex-shrink: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 30px;
            background: linear-gradient(to top, var(--azul-fundo) 60%, transparent);
        }

        .btn-dock {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            color: var(--branco);
            padding: 12px 30px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: all 0.2s;
        }

        .history-list {
            position: absolute;
            bottom: 80px;
            width: 90%;
            background: rgba(0, 31, 63, 0.98);
            border: 1px solid var(--vermelho-erro);
            border-radius: 15px;
            padding: 20px;
            display: none;
            max-height: 45vh;
            overflow-y: auto;
            z-index: 50;
            box-shadow: 0 -15px 40px rgba(0,0,0,0.6);
            backdrop-filter: blur(10px);
        }

        .history-header {
            display: flex;
            padding: 10px;
            border-bottom: 2px solid var(--vermelho-erro);
            font-weight: bold;
            color: var(--vermelho-erro);
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 10px;
            color: var(--branco);
        }
        
        .h-info { display: flex; gap: 15px; align-items: center; width: 100%; }
        .h-data { flex: 1; font-size: 0.85rem; opacity: 0.7; }
        .h-recevedor { flex: 3; font-weight: bold; font-size: 0.9rem; }
        .h-desc { flex: 2; font-size: 0.8rem; opacity: 0.8; }
        .h-parcela { flex: 0.8; text-align: center; color: var(--amarelo-alerta); font-weight: bold; font-size: 0.8rem; }
        .h-valor { flex: 1.5; text-align: right; color: var(--vermelho-erro); font-weight: bold; font-size: 1rem; font-family: monospace; }
        
        .btn-action { cursor: pointer; text-decoration: underline; font-size: 0.9rem; margin-left: 15px; color: var(--amarelo-alerta); transition: 0.2s; }
        .btn-action:hover { opacity: 0.7; }

        #flash-message {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            background-color: var(--vermelho-erro);
            color: white;
            padding: 35px 70px;
            border-radius: 15px;
            font-size: 2.2rem;
            font-weight: 900;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 2000;
            border: 5px solid white;
        }

        /* AJUSTE PARA IFRAME */
        @media screen and (min-width: 10px) {
            body.in-iframe .header, body.in-iframe .btn-voltar { display: none !important; }
            body.in-iframe .form-wrapper { padding-top: 20px; }
        }
    </style>
    <script>
        if (window.self !== window.top) {
            document.documentElement.classList.add('in-iframe');
            document.body.classList.add('in-iframe');
        }
    </script>
</head>
<body>

    <div id="flash-message">SALVO!</div>

    <div class="header">
        <a href="financeiro" class="btn-voltar">VOLTAR</a>
        <h1>SAÍDAS</h1>
    </div>

    <div class="form-wrapper">
        <div class="form-container">
            <input type="hidden" id="edit-id-control">
            
            <div class="form-row">
                <div class="input-group" style="flex: 3;">
                    <input type="text" id="recebedor" class="input-3d" placeholder="Recebedor / Empresa" autocomplete="off" tabindex="1">
                    <div id="lista-recebedores" class="suggestions-list"></div>
                </div>
                <input type="text" id="data" class="input-3d" style="flex: 1.2; text-align: center;" placeholder="DD/MM/AAAA" maxlength="10" tabindex="2">
            </div>

            <div class="form-row">
                <div class="input-group" style="flex: 2;">
                    <input type="text" id="descricao" class="input-3d" placeholder="Descrição da Despesa (O que comprou?)" tabindex="3">
                </div>
                <div class="input-group" style="flex: 2;">
                    <input type="text" id="dados_cadastrais" class="input-3d" placeholder="CNPJ / CPF ou Referência" tabindex="4" autocomplete="off">
                    <div id="lista-dados" class="suggestions-list"></div>
                </div>
            </div>

            <div class="form-row">
                <select id="tipo_saida" class="input-3d" style="flex: 1.5;" tabindex="5">
                    <option value="Água/Luz/Tel">Água/Luz/Tel</option>
                    <option value="Aluguel">Aluguel</option>
                    <option value="Manutenção">Manutenção</option>
                    <option value="Preletores">Preletores</option>
                    <option value="Social">Social</option>
                    <option value="Diverso">Diverso</option>
                    <option value="Outros" selected>Outros</option>
                </select>
                <input type="text" id="parcela" class="input-3d" style="flex: 0.8; text-align: center;" placeholder="Qt. Parc" tabindex="6" title="Quantidade total de parcelas">
                <input type="text" id="valor" class="input-3d" style="flex: 1.5; text-align: right;" placeholder="R$ 0,00" inputmode="numeric" tabindex="7">
            </div>
        </div>
    </div>

    <div class="dock-container">
        <div class="history-list" id="history-list">
            <div class="history-header">
                <span style="flex:1">Data</span>
                <span style="flex:3">Recebedor</span>
                <span style="flex:2">Descrição</span>
                <span style="flex:0.8; text-align:center">Parc.</span>
                <span style="flex:1.5; text-align:right">Valor</span>
                <span style="width:100px"></span>
            </div>
            <div id="history-content"></div>
        </div>
        <button class="btn-dock" id="btn-dock-main" onclick="toggleDock()">Últimas Inserções ▾</button>
    </div>

    <script>
        const inputs = [
            document.getElementById('recebedor'),
            document.getElementById('data'),
            document.getElementById('descricao'),
            document.getElementById('dados_cadastrais'),
            document.getElementById('tipo_saida'),
            document.getElementById('parcela'),
            document.getElementById('valor')
        ];

        // Data de hoje automática
        document.getElementById('data').value = new Date().toLocaleDateString('pt-BR');
        document.getElementById('recebedor').focus();

        // Máscara de Data
        document.getElementById('data').addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, "");
            if (v.length > 2) v = v.slice(0,2) + "/" + v.slice(2);
            if (v.length > 5) v = v.slice(0,5) + "/" + v.slice(5,9);
            e.target.value = v;
        });

        // Máscara de Moeda
        document.getElementById('valor').addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, "");
            e.target.value = (Number(v) / 100).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        });

        // Autocomplete de Recebedores
        function configurarAutocomplete(inputId, listaId, campoBD) {
            const input = document.getElementById(inputId);
            const lista = document.getElementById(listaId);
            let debounceTimer;

            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                if (input.value.length < 2) { lista.style.display = 'none'; return; }
                
                debounceTimer = setTimeout(async () => {
                    try {
                        const response = await fetch(`financeiro_autocomplete?termo=${input.value}&campo=${campoBD}`);
                        const dados = await response.json();
                        lista.innerHTML = '';
                        if (dados.length) {
                            dados.forEach(t => {
                                const div = document.createElement('div');
                                div.className = 'suggestion-item';
                                
                                if (typeof t === 'object') {
                                    div.innerHTML = `<strong>${t.label}</strong> ${t.extra ? '<br><small style="opacity:0.7">'+t.extra+'</small>' : ''}`;
                                    div.onclick = () => {
                                        input.value = t.label;
                                        lista.style.display = 'none';
                                        
                                        // Auto-preenchimento cruzado
                                        if (campoBD === 'recebedor' && t.extra) {
                                            document.getElementById('dados_cadastrais').value = t.extra;
                                        } else if (campoBD === 'dados_cadastrais' && t.extra) {
                                            document.getElementById('recebedor').value = t.extra;
                                        }
                                        
                                        input.focus();
                                    };
                                } else {
                                    div.innerText = t;
                                    div.onclick = () => { input.value = t; lista.style.display = 'none'; input.focus(); };
                                }
                                
                                lista.appendChild(div);
                            });
                            lista.style.display = 'block';
                        } else {
                            lista.style.display = 'none';
                        }
                    } catch(e) { console.error("Erro autocomplete"); }
                }, 300);
            });
            document.addEventListener('click', (e) => { if(e.target !== input) lista.style.display = 'none'; });
        }
        configurarAutocomplete('recebedor', 'lista-recebedores', 'recebedor');
        configurarAutocomplete('dados_cadastrais', 'lista-dados', 'dados_cadastrais');

        // Atalho Enter
        document.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                if (document.querySelector('.suggestions-list[style*="block"]')) return;
                const idx = inputs.indexOf(document.activeElement);
                if (idx === inputs.length - 1) tentarSalvar();
                else if (idx > -1) inputs[idx + 1].focus();
            }
        });

        async function tentarSalvar() {
            const idEdicao = document.getElementById('edit-id-control').value;
            const payload = {
                id: idEdicao || null,
                recebedor: document.getElementById('recebedor').value,
                data: document.getElementById('data').value,
                descricao: document.getElementById('descricao').value,
                dados_cadastrais: document.getElementById('dados_cadastrais').value,
                tipo_saida: document.getElementById('tipo_saida').value,
                parcela: document.getElementById('parcela').value,
                valor: document.getElementById('valor').value
            };

            if(!payload.recebedor || payload.valor === 'R$ 0,00') return alert("Preencha o Recebedor e o Valor!");

            try {
                const response = await fetch('financeiro_salvar_saida', { 
                    method: 'POST', 
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload) 
                });
                const res = await response.json();

                if(res.status === 'success') {
                    const flash = document.getElementById('flash-message');
                    flash.innerText = idEdicao ? "ATUALIZADO!" : "SALVO!";
                    flash.style.opacity = 1; flash.style.transform = 'translate(-50%, -50%) scale(1)';
                    setTimeout(() => { flash.style.opacity = 0; flash.style.transform = 'translate(-50%, -50%) scale(0.8)'; }, 1500);

                    if(idEdicao) document.querySelector(`[data-id="${idEdicao}"]`)?.remove();
                    adicionarAoDock(res.id, payload);
                    resetarForm();
                } else {
                    alert("Erro ao salvar: " + res.message);
                }
            } catch (err) { alert("Erro de conexão com o servidor."); }
        }

        function adicionarAoDock(id, p) {
            const content = document.getElementById('history-content');
            const item = document.createElement('div');
            item.className = 'history-item';
            item.setAttribute('data-id', id);
            item.innerHTML = `
                <div class="h-info">
                    <span class="h-data">${p.data}</span>
                    <span class="h-recebedor">${p.recebedor}</span>
                    <span class="h-desc">${p.descricao}</span>
                    <span class="h-parcela">${p.parcela || '-'}</span>
                    <span class="h-valor">${p.valor}</span>
                </div>
                <div class="h-actions">
                    <span class="btn-action edit" onclick="prepararEdicao(${id},'${p.data}','${p.recebedor}','${p.descricao}','${p.dados_cadastrais}','${p.tipo_saida}','${p.parcela}','${p.valor}')">Editar</span>
                </div>`;
            content.prepend(item);
        }

        function prepararEdicao(id, data, rec, desc, cad, tipo, parc, val) {
            document.getElementById('edit-id-control').value = id;
            document.getElementById('recebedor').value = rec;
            document.getElementById('data').value = data;
            document.getElementById('descricao').value = desc;
            document.getElementById('dados_cadastrais').value = cad;
            document.getElementById('tipo_saida').value = tipo;
            document.getElementById('parcela').value = parc;
            document.getElementById('valor').value = val;
            toggleDock();
            document.getElementById('recebedor').focus();
            document.getElementById('btn-dock-main').innerText = "EDITANDO REGISTRO... ▾";
            document.getElementById('btn-dock-main').style.color = "var(--amarelo-alerta)";
        }

        function resetarForm() {
            document.getElementById('edit-id-control').value = "";
            inputs.forEach(i => { if(i.id !== 'data' && i.id !== 'tipo_saida') i.value = ""; });
            document.getElementById('recebedor').focus();
            document.getElementById('btn-dock-main').innerText = "Últimas Inserções ▾";
            document.getElementById('btn-dock-main').style.color = "var(--branco)";
        }

        function toggleDock() {
            const l = document.getElementById('history-list');
            l.style.display = l.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>