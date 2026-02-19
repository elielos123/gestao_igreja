<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Financeiro - Entradas</title>
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
            color: var(--branco);
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

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
            gap: 25px;
            width: 100%;
            position: relative;
        }

        .input-group {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .input-3d {
            background-color: rgba(255,255,255,0.03);
            color: var(--branco);
            border: 1px solid rgba(255,255,255,0.2);
            border-bottom: 3px solid var(--azul-sombra);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.1s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            width: 100%;
        }
        
        .input-3d::placeholder { color: rgba(255,255,255,0.2); font-size: 0.9rem; }
        .input-3d:focus {
            background-color: var(--branco);
            color: var(--azul-fundo);
            border-color: var(--branco);
            border-bottom: 2px solid var(--branco); 
            transform: translateY(4px); 
            font-weight: 800;
        }

        select.input-3d {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23FFFFFF%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 25px center;
            background-size: 18px auto;
            cursor: pointer;
        }

        .suggestions-list {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: var(--azul-claro);
            border: 1px solid rgba(255,255,255,0.2);
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

        .suggestion-item.selected, .suggestion-item:hover {
            background-color: var(--azul-fundo);
            font-weight: bold;
            padding-left: 30px;
        }

        .flex-grow { flex: 4; }
        .flex-fixed-date { flex: 1.5; min-width: 200px; text-align: center; }
        .flex-congregacao { flex: 3; }
        .flex-tipo { flex: 3; }
        .flex-valor { flex: 2; min-width: 220px; text-align: right; letter-spacing: -1px; font-family: monospace;}

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
            background: rgba(0, 31, 63, 0.95);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 15px;
            padding: 15px;
            display: none;
            max-height: 45vh;
            overflow-y: auto;
            z-index: 50;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
        }

        .history-header {
            display: flex;
            padding: 10px;
            border-bottom: 2px solid rgba(255,255,255,0.2);
            font-weight: bold;
            color: #7FDBFF;
            font-size: 0.9rem;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 10px;
        }
        
        .h-info { display: flex; gap: 15px; align-items: center; width: 100%; }
        .h-data { flex: 1.2; font-size: 0.85rem; opacity: 0.7; }
        .h-recevedor { flex: 3; font-weight: bold; font-size: 0.9rem; }
        .h-congregacao { flex: 2; font-size: 0.8rem; color: #7FDBFF; }
        .h-tipo { flex: 2; font-size: 0.8rem; opacity: 0.8; }
        .h-valor { flex: 1.5; text-align: right; color: var(--verde-sucesso); font-weight: bold; font-size: 1rem; font-family: monospace; }
        
        .h-actions { display: flex; gap: 15px; margin-left: 20px; }
        .btn-action { cursor: pointer; font-size: 0.9rem; text-decoration: underline; opacity: 0.6; }
        .btn-action.edit { color: var(--amarelo-alerta); }
        .btn-action:hover { opacity: 1; }

        #flash-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            background-color: var(--branco);
            color: var(--azul-fundo);
            padding: 30px 60px;
            border-radius: 15px;
            font-size: 2rem;
            font-weight: 900;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 2000;
            border: 5px solid var(--azul-fundo);
        }

        @media (max-width: 768px) {
            .form-container { width: 95%; gap: 15px; }
            .form-row { flex-wrap: wrap; gap: 15px; }
            .flex-grow, .flex-fixed-date, .flex-congregacao, .flex-tipo, .flex-valor { flex: 100%; text-align: left; }
            .input-3d { font-size: 1.3rem; padding: 20px; }
            .h-info { flex-wrap: wrap; gap: 5px; }
            .h-data, .h-nome, .h-congregacao, .h-tipo, .h-valor { flex: auto; width: 100%; }
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
        <h1>ENTRADAS</h1>
    </div>

    <div class="form-wrapper">
        <div class="form-container">
            <input type="hidden" id="edit-id-control">
            
            <div class="form-row">
                <div class="input-group flex-grow">
                    <input type="text" id="nome" class="input-3d" placeholder="Nome do Membro" autocomplete="off" tabindex="1">
                    <div id="lista-nomes" class="suggestions-list"></div>
                </div>

                <input type="text" id="data" class="input-3d flex-fixed-date" placeholder="DD/MM/AAAA" maxlength="10" tabindex="2">
            </div>

            <div class="form-row">
                <div class="input-group flex-congregacao">
                    <input type="text" id="congregacao" class="input-3d" placeholder="Congregação" autocomplete="off" tabindex="3">
                    <div id="lista-congregacao" class="suggestions-list"></div>
                </div>

                <select id="tipo" class="input-3d flex-tipo" tabindex="4">
                    <option value="Dízimo" selected>Dízimo</option>
                    <option value="Oferta Missionária">Oferta Missionária</option>
                    <option value="Dízimo dos Dízimos">Dízimo dos Dízimos</option>
                    <option value="Oferta Especial">Oferta Especial</option>
                    <option value="Outros">Outros</option>
                </select>

                <input type="text" id="valor" class="input-3d flex-valor" placeholder="R$ 0,00" inputmode="numeric" tabindex="5">
            </div>

        </div>
    </div>

    <div class="dock-container">
        <div class="history-list" id="history-list">
            <div class="history-header">
                <span style="flex:1.2">Data</span>
                <span style="flex:3">Nome</span>
                <span style="flex:2">Congregação</span>
                <span style="flex:2">Tipo</span>
                <span style="flex:1.5; text-align:right">Valor</span>
                <span style="width:120px"></span>
            </div>
            <div id="history-content">
                <div style="text-align:center; color:rgba(255,255,255,0.5); padding:20px;">Nenhum registro nesta sessão.</div>
            </div>
        </div>
        <button class="btn-dock" id="btn-dock-main" onclick="toggleDock()">Últimas Inserções ▾</button>
    </div>

    <script>
        const inputs = [
            document.getElementById('nome'),
            document.getElementById('data'),
            document.getElementById('congregacao'),
            document.getElementById('tipo'),
            document.getElementById('valor')
        ];
        
        const hoje = new Date();
        document.getElementById('data').value = hoje.toLocaleDateString('pt-BR');
        document.getElementById('nome').focus();

        function configurarAutocomplete(inputId, listaId, campoBD) {
            const input = document.getElementById(inputId);
            const lista = document.getElementById(listaId);
            let debounceTimer;
            let currentFocus = -1; 

            input.addEventListener('input', function() {
                const termo = this.value;
                clearTimeout(debounceTimer);
                if (termo.length < 2) { lista.style.display = 'none'; return; }
                debounceTimer = setTimeout(async () => {
                    try {
                        const response = await fetch(`financeiro_autocomplete?termo=${termo}&campo=${campoBD}`);
                        const sugestoes = await response.json();
                        lista.innerHTML = '';
                        currentFocus = -1;
                        if (sugestoes.length > 0) {
                            sugestoes.forEach((itemDados, index) => {
                                const item = document.createElement('div');
                                item.className = 'suggestion-item';
                                
                                if (campoBD === 'nome' && typeof itemDados === 'object') {
                                    item.innerHTML = `<strong>${itemDados.nome}</strong> - <small>${itemDados.congregacao}</small>`;
                                    item.dataset.nomeValue = itemDados.nome;
                                    item.dataset.congregacaoValue = itemDados.congregacao;
                                } else {
                                    item.innerText = itemDados;
                                }

                                item.onclick = () => selecionarItem(item, input, lista, campoBD);
                                lista.appendChild(item);
                            });
                            lista.style.display = 'block';
                        } else { lista.style.display = 'none'; }
                    } catch (e) { console.error(e); }
                }, 300);
            });

            input.addEventListener('keydown', function(e) {
                let x = lista.getElementsByClassName("suggestion-item");
                if (lista.style.display !== 'block') return;

                if (e.key === "ArrowDown") {
                    currentFocus++;
                    addActive(x);
                } else if (e.key === "ArrowUp") {
                    currentFocus--;
                    addActive(x);
                } else if (e.key === "Enter") {
                    if (currentFocus > -1) {
                        e.preventDefault();
                        if (x[currentFocus]) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                x[currentFocus].classList.add("selected");
                x[currentFocus].scrollIntoView({ block: "nearest" });
            }

            function removeActive(x) {
                for (let i = 0; i < x.length; i++) {
                    x[i].classList.remove("selected");
                }
            }
        }

        function selecionarItem(item, input, lista, campoBD) {
            if (campoBD === 'nome' && item.dataset.nomeValue) {
                input.value = item.dataset.nomeValue;
                document.getElementById('congregacao').value = item.dataset.congregacaoValue;
            } else {
                input.value = item.innerText;
            }
            lista.style.display = 'none';
            // AJUSTE SOLICITADO: Após autocomplete do nome, vai para DATA
            if (campoBD === 'nome') document.getElementById('data').focus();
            else input.focus();
        }

        configurarAutocomplete('nome', 'lista-nomes', 'nome');
        configurarAutocomplete('congregacao', 'lista-congregacao', 'congregacao');

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const listasAbertas = document.querySelectorAll('.suggestions-list[style*="block"]');
                if (listasAbertas.length > 0) return; 
                e.preventDefault();
                const index = inputs.indexOf(document.activeElement);
                if (index === inputs.length - 1) { tentarSalvar(); } 
                else if (index > -1 && index < inputs.length - 1) { inputs[index + 1].focus(); }
            }
        });

        document.getElementById('data').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, "");
            if (v.length > 2) v = v.slice(0,2) + "/" + v.slice(2);
            if (v.length > 5) v = v.slice(0,5) + "/" + v.slice(5,9);
            e.target.value = v;
        });

        document.getElementById('valor').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, "");
            value = (Number(value) / 100).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            e.target.value = value;
        });

        document.getElementById('nome').addEventListener('blur', function(e) {
            const excecoes = ['de', 'da', 'do', 'dos', 'das', 'e'];
            let palavras = e.target.value.toLowerCase().split(' ');
            if(palavras.length === 1 && palavras[0] === "") return;
            for (let i = 0; i < palavras.length; i++) {
                if (!excecoes.includes(palavras[i]) || i === 0) palavras[i] = palavras[i].charAt(0).toUpperCase() + palavras[i].slice(1);
            }
            e.target.value = palavras.join(' ');
        });

        async function tentarSalvar() {
            const idEdicao = document.getElementById('edit-id-control').value;
            const nome = document.getElementById('nome').value;
            const valor = document.getElementById('valor').value;
            const data = document.getElementById('data').value;
            const congregacao = document.getElementById('congregacao').value;
            const tipo = document.getElementById('tipo').value;

            if (!nome || !valor || !congregacao || !tipo || valor === 'R$ 0,00') {
                alert("Preencha todos os campos obrigatórios.");
                return;
            }

            const payload = { 
                id: idEdicao || null, 
                nome: nome, 
                data: data, 
                valor: valor, 
                congregacao: congregacao, 
                tipo: tipo 
            };

            try {
                const response = await fetch('financeiro_salvar_entrada', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
                const resultado = await response.json();

                if (resultado.status === 'success') {
                    const flash = document.getElementById('flash-message');
                    flash.innerText = idEdicao ? "ATUALIZADO!" : "SALVO!";
                    flash.style.opacity = '1';
                    flash.style.transform = 'translate(-50%, -50%) scale(1)';
                    setTimeout(() => { flash.style.opacity = '0'; flash.style.transform = 'translate(-50%, -50%) scale(0.8)'; }, 1000);
                    
                    if(idEdicao) {
                        const itemAntigo = document.querySelector(`.history-item[data-id="${idEdicao}"]`);
                        if(itemAntigo) itemAntigo.remove();
                    }

                    adicionarAoDock(resultado.id, data, nome, congregacao, tipo, valor);
                    resetarFormulario();
                } else {
                    alert('Erro: ' + resultado.message);
                }
            } catch (error) {
                console.error(error);
                alert('Erro de conexão.');
            }
        }

        function prepararEdicao(id, data, nome, congregacao, tipo, valor) {
            document.getElementById('edit-id-control').value = id;
            document.getElementById('nome').value = nome;
            document.getElementById('data').value = data;
            document.getElementById('congregacao').value = congregacao;
            document.getElementById('tipo').value = tipo;
            document.getElementById('valor').value = valor;
            
            if(document.getElementById('history-list').style.display === 'block') toggleDock();
            document.getElementById('nome').focus();
            
            document.getElementById('btn-dock-main').innerText = "Editando Registro... ▾";
            document.getElementById('btn-dock-main').style.color = "var(--amarelo-alerta)";
        }

        async function deletarEntrada(id, btnElement) {
            if(!confirm("Tem certeza que deseja apagar?")) return;
            try {
                const response = await fetch('financeiro_excluir_entrada', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
                const resultado = await response.json();
                if (resultado.status === 'success') {
                    btnElement.closest('.history-item').remove();
                } else {
                    alert("Erro ao excluir: " + resultado.message);
                }
            } catch (e) {
                alert("Erro de conexão.");
            }
        }

        function resetarFormulario() {
            document.getElementById('edit-id-control').value = '';
            document.getElementById('nome').value = '';
            document.getElementById('valor').value = '';
            document.getElementById('congregacao').value = '';
            document.getElementById('data').value = new Date().toLocaleDateString('pt-BR');
            document.getElementById('btn-dock-main').innerText = "Últimas Inserções ▾";
            document.getElementById('btn-dock-main').style.color = "var(--branco)";
            document.getElementById('nome').focus();
        }

        function toggleDock() {
            const list = document.getElementById('history-list');
            list.style.display = list.style.display === 'block' ? 'none' : 'block';
        }

        function adicionarAoDock(id, data, nome, congregacao, tipo, valor) {
            const content = document.getElementById('history-content');
            if (content.children[0] && content.children[0].innerText.includes('Nenhum')) content.innerHTML = '';
            
            const item = document.createElement('div');
            item.className = 'history-item';
            item.setAttribute('data-id', id);
            item.innerHTML = `
                <div class=\"h-info\">
                    <span class=\"h-data\">${data}</span>
                    <span class=\"h-nome\">${nome}</span>
                    <span class=\"h-congregacao\">${congregacao}</span>
                    <span class=\"h-tipo\">${tipo}</span>
                    <span class=\"h-valor\">${valor}</span>
                </div>
                <div class=\"h-actions\">
                    <span class=\"btn-action edit\" onclick=\"prepararEdicao(${id}, '${data}', '${nome}', '${congregacao}', '${tipo}', '${valor}')\">Editar</span>
                    <span class=\"btn-action\" onclick=\"deletarEntrada(${id}, this)\">Excluir</span>
                </div>
            `;
            content.prepend(item);
        }
    </script>
</body>
</html>