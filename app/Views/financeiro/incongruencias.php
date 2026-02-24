<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeiro - Incongruências</title>
    <style>
        :root { 
            --azul-fundo: #001f3f; 
            --azul-claro: #003366; 
            --branco: #ffffff; 
            --verde: #2ECC40; 
            --vermelho: #FF4136; 
            --amarelo: #FFDC00; 
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; outline: none; }
        body { background-color: var(--azul-fundo); color: var(--branco); min-height: 100vh; width: 100vw; display: flex; flex-direction: column; overflow-x: hidden; padding: 20px; }
        
        .header { margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .title-main { color: #7FDBFF; text-transform: uppercase; letter-spacing: 2px; }

        .resultados-area { flex-grow: 1; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 20px; overflow-y: auto; max-width: 1400px; margin: 0 auto; width: 100%; border: 1px solid rgba(255,255,255,0.05); }
        
        .tabela-relatorio { width: 100%; border-collapse: collapse; }
        .tabela-relatorio th { text-align: left; padding: 15px; border-bottom: 2px solid rgba(255,255,255,0.2); color: #7FDBFF; font-size: 0.9rem; }
        .tabela-relatorio td { padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.95rem; }
        
        .valor-entrada { color: var(--verde); font-weight: bold; font-family: monospace; }
        .valor-saida { color: var(--vermelho); font-weight: bold; font-family: monospace; }
        
        .btn-acao { padding: 8px 15px; font-size: 0.8rem; margin-left: 5px; border-radius: 6px; border: none; cursor: pointer; font-weight: bold; transition: all 0.2s; }
        .btn-edit { background: var(--amarelo); color: black; }
        .btn-del { background: var(--vermelho); color: white; }
        .btn-ok { background: #0074D9; color: white; }
        .btn-view { background: var(--branco); color: var(--azul-fundo); }
        .btn-acao:hover { transform: translateY(-2px); opacity: 0.9; }

        .alerta-vazio { background: var(--verde); color: white; padding: 30px; border-radius: 12px; text-align: center; font-size: 1.2rem; font-weight: bold; border: 2px solid rgba(255,255,255,0.2); }

        /* MODAL */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.85); z-index: 1000; display: none; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
        .modal-content { background: var(--azul-fundo); padding: 30px; border-radius: 15px; border: 2px solid #7FDBFF; width: 90%; max-width: 500px; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .form-row { margin-bottom: 15px; }
        .form-row label { display: block; margin-bottom: 5px; color: #7FDBFF; font-size: 0.85rem; font-weight: bold; }
        .form-row input, .form-row select { width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; font-size: 1rem; }
        .form-row input:focus { border-color: var(--branco); background: rgba(255,255,255,0.1); }
        
        .btn-save { width: 100%; padding: 15px; background: var(--verde); border: none; color: white; font-weight: bold; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 1rem; }
        .btn-close { position: absolute; top: 15px; right: 20px; background: transparent; border: none; color: white; font-size: 2rem; cursor: pointer; opacity: 0.5; }
        .btn-close:hover { opacity: 1; }
        
        .hidden { display: none; }
        
        .reason-tag { color: var(--amarelo); font-weight: 600; font-size: 0.85rem; display: block; margin-top: 4px; opacity: 0.8; }

        /* ESTILO PARA IFRAME */
        @media screen and (min-width: 10px) {
            body.in-iframe { padding: 10px; }
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

    <div class="header">
        <h1 class="title-main">Varredura de Incongruências</h1>
        <div id="status-varredura" style="font-size: 0.9rem; opacity: 0.7;">Analisando registros...</div>
    </div>

    <div class="resultados-area" id="lista-incongruencias">
        <div style="text-align:center; padding:50px; opacity:0.5;">Iniciando varredura profunda...</div>
    </div>

    <!-- MODAL DE EDIÇÃO -->
    <div id="modal-edit" class="modal-overlay">
        <div class="modal-content">
            <button class="btn-close" onclick="fecharModal()">&times;</button>
            <h3 style="color: #7FDBFF; margin-bottom: 20px;">Corrigir Registro</h3>
            <input type="hidden" id="edit-id">
            <input type="hidden" id="edit-origem">
            
            <div class="form-row"><label>Data:</label><input type="date" id="edit-data"></div>
            <div class="form-row"><label id="lbl-nome">Nome / Recebedor:</label><input type="text" id="edit-nome"></div>
            <div class="form-row"><label>Valor (R$):</label><input type="number" step="0.01" id="edit-valor"></div>
            
            <div id="fields-entrada">
                <div class="form-row"><label>Congregação:</label><input type="text" id="edit-congregacao"></div>
                <div class="form-row"><label>Tipo de Entrada:</label><input type="text" id="edit-tipo"></div>
            </div>
            
            <div id="fields-saida" class="hidden">
                <div class="form-row"><label>Descrição:</label><input type="text" id="edit-descricao"></div>
                <div class="form-row"><label>Tipo de Saída:</label><input type="text" id="edit-tipo-saida"></div>
            </div>

            <button class="btn-save" onclick="salvarEdicao()">SALVAR ALTERAÇÕES</button>
        </div>
    </div>

    <script>
        const fmtMoeda = (v) => parseFloat(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

        async function carregarIncongruencias() {
            const container = document.getElementById('lista-incongruencias');
            const status = document.getElementById('status-varredura');
            
            try {
                const resp = await fetch('api_relatorios?tipo_relatorio=incongruencias');
                const json = await resp.json();
                
                if(json.status === 'success') {
                    if(!json.dados.length) {
                        container.innerHTML = '<div class="alerta-vazio">✨ TUDO CERTO!<br><span style="font-size:0.9rem; font-weight:normal; opacity:0.8;">Nenhuma incongruência detectada na varredura atual.</span></div>';
                        status.innerText = 'Sistema 100% íntegro';
                        return;
                    }
                    
                    status.innerText = `Encontradas ${json.dados.length} possíveis falhas`;
                    
                    let h = `<table class="tabela-relatorio">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Origem</th>
                                <th>Principal</th>
                                <th>Valor</th>
                                <th>Motivo / Problema</th>
                                <th style="text-align:right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>`;
                    
                    json.dados.forEach(d => {
                        const dataFmt = new Date(d.data_movimento + "T12:00:00").toLocaleDateString('pt-BR');
                        const vFmt = fmtMoeda(d.valor);
                        const corValor = d.origem === 'Entrada' ? 'valor-entrada' : 'valor-saida';
                        
                        h += `<tr>
                            <td>${dataFmt}</td>
                            <td><span style="opacity:0.6; font-size:0.8rem">${d.origem}</span></td>
                            <td style="font-weight:bold">${d.principal}</td>
                            <td class="${corValor}">${vFmt}</td>
                            <td><span class="reason-tag">⚠️ ${d.motivo}</span></td>
                            <td style="text-align:right">
                                <button class="btn-acao btn-ok" onclick="aceitarRegistro(${d.id}, '${d.origem}')">Aceitar</button>
                                <button class="btn-acao btn-edit" onclick="abrirEdicao(${d.id}, '${d.origem}')">Corrigir</button>
                                <button class="btn-acao btn-del" onclick="excluirRegistro(${d.id}, '${d.origem}')">Excluir</button>
                            </td>
                        </tr>`;
                    });
                    
                    container.innerHTML = h + '</tbody></table>';
                }
            } catch (e) {
                container.innerHTML = '<div style="color:var(--vermelho); text-align:center; padding:30px;">Falha na comunicação com o servidor.</div>';
            }
        }

        async function abrirEdicao(id, origem) {
            try {
                const resp = await fetch(`financeiro_buscar_edicao?id=${id}&origem=${origem}`);
                const json = await resp.json();
                
                if(json.status === 'success') {
                    const d = json.dados;
                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-origem').value = origem;
                    document.getElementById('edit-data').value = d.data_movimento;
                    document.getElementById('edit-valor').value = d.valor;
                    document.getElementById('edit-nome').value = (origem === 'Entrada') ? (d.nome || d.principal) : (d.recebedor || d.principal);
                    
                    if(origem === 'Entrada') {
                        document.getElementById('fields-entrada').classList.remove('hidden');
                        document.getElementById('fields-saida').classList.add('hidden');
                        document.getElementById('edit-congregacao').value = d.congregacao || '';
                        document.getElementById('edit-tipo').value = d.tipo || '';
                        document.getElementById('lbl-nome').innerText = "Nome do Membro:";
                    } else {
                        document.getElementById('fields-entrada').classList.add('hidden');
                        document.getElementById('fields-saida').classList.remove('hidden');
                        document.getElementById('edit-descricao').value = d.descricao || '';
                        document.getElementById('edit-tipo-saida').value = d.tipo_saida || '';
                        document.getElementById('lbl-nome').innerText = "Recebedor:";
                    }
                    
                    document.getElementById('modal-edit').style.display = 'flex';
                }
            } catch (e) { alert("Erro ao carregar dados."); }
        }

        async function salvarEdicao() {
            const id = document.getElementById('edit-id').value;
            const origem = document.getElementById('edit-origem').value;
            
            const payload = {
                id: id,
                origem: origem,
                data: document.getElementById('edit-data').value,
                valor: document.getElementById('edit-valor').value
            };

            if(origem === 'Entrada') {
                payload.nome = document.getElementById('edit-nome').value;
                payload.congregacao = document.getElementById('edit-congregacao').value;
                payload.tipo = document.getElementById('edit-tipo').value;
            } else {
                payload.recebedor = document.getElementById('edit-nome').value;
                payload.descricao = document.getElementById('edit-descricao').value;
                payload.tipo_saida = document.getElementById('edit-tipo-saida').value;
            }

            try {
                const resp = await fetch('financeiro_salvar_edicao', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const res = await resp.json();
                if(res.status === 'success') {
                    fecharModal();
                    carregarIncongruencias();
                } else {
                    alert("Erro ao salvar: " + res.message);
                }
            } catch (e) { alert("Erro de conexão."); }
        }

        async function excluirRegistro(id, origem) {
            if(!confirm("Tem certeza que deseja EXCLUIR permanentemente este registro?")) return;
            
            const url = origem === 'Entrada' ? 'financeiro_excluir_entrada' : 'financeiro_excluir_saida';
            
            try {
                const resp = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const res = await resp.json();
                if(res.status === 'success') {
                    carregarIncongruencias();
                } else {
                    alert("Erro ao excluir.");
                }
            } catch (e) { alert("Erro de conexão."); }
        }

        async function aceitarRegistro(id, origem) {
            if(!confirm("Confirma que este registro está correto e não precisa de correção?")) return;
            try {
                const resp = await fetch('financeiro_aceitar_incongruencia', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, origem: origem })
                });
                const res = await resp.json();
                if(res.status === 'success') {
                    carregarIncongruencias();
                } else {
                    alert("Erro ao aceitar registro.");
                }
            } catch (e) { alert("Erro de conexão."); }
        }

        function fecharModal() {
            document.getElementById('modal-edit').style.display = 'none';
        }

        // Iniciar
        document.addEventListener('DOMContentLoaded', carregarIncongruencias);

    </script>
</body>
</html>
