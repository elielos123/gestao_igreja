<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios Financeiros</title>
    <style>
        /* --- ESTILOS GERAIS (TELA) --- */
        :root { --azul-fundo: #001f3f; --azul-claro: #003366; --branco: #ffffff; --verde: #2ECC40; --vermelho: #FF4136; --amarelo: #FFDC00; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; outline: none; }
        body { background-color: var(--azul-fundo); color: var(--branco); height: 100vh; width: 100vw; display: flex; flex-direction: column; overflow: hidden; }
        
        /* CABEÇALHO TELA */
        .header { display: none !important; }
        .container-menu { flex: 1; display: flex; justify-content: center; align-items: center; padding: 10px; }
        
        .btn-voltar { background-color: var(--azul-fundo); color: var(--branco); border: 2px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 10px 25px; text-decoration: none; font-weight: bold; color: white; }
        
        .container-menu { flex-grow: 1; display: flex; justify-content: center; align-items: center; }
        .grid-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; width: 95%; max-width: 900px; }
        .btn-relatorio { background: var(--azul-fundo); border: 1px solid rgba(255,255,255,0.2); border-bottom: 4px solid #000a14; border-radius: 12px; height: 70px; color: white; font-size: 0.9rem; font-weight: bold; cursor: pointer; text-transform: uppercase; display: flex; align-items: center; justify-content: center; transition: all 0.2s; padding: 10px; text-align: center; line-height: 1.2; }
        .btn-relatorio:hover { background: var(--azul-claro); transform: translateY(-2px); border-color: white; }

        /* DOCKS */
        .dock-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: var(--azul-fundo); z-index: 100; display: flex; flex-direction: column; transform: translateY(100%); transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); padding: 20px; }
        .dock-active { transform: translateY(0); }
        .dock-header { text-align: center; margin-bottom: 20px; cursor: pointer; }
        .dock-header h2 { font-size: 2rem; color: #7FDBFF; text-decoration: underline; }

        .filtros-container { display: flex; gap: 15px; justify-content: center; margin-bottom: 20px; flex-wrap: wrap; }
        .input-filtro, .select-filtro { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); padding: 15px; color: white; border-radius: 8px; font-size: 1rem; }
        .select-filtro option { background-color: var(--azul-fundo); }
        .btn-visualizar { background: var(--branco); color: var(--azul-fundo); border: none; padding: 15px 40px; font-weight: bold; border-radius: 8px; cursor: pointer; }

        /* ÁREA DO RELATÓRIO */
        .resultados-area { flex-grow: 1; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 20px; overflow-y: auto; max-width: 1400px; margin: 0 auto; width: 100%; }
        .tabela-relatorio { width: 100%; border-collapse: collapse; min-width: 800px; }
        .tabela-relatorio th { text-align: left; padding: 15px; border-bottom: 2px solid rgba(255,255,255,0.2); color: #7FDBFF; }
        .tabela-relatorio td { padding: 8px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        
        /* ESTILOS DE AGRUPAMENTO */
        .grupo-header td { background-color: rgba(255,255,255,0.15); font-weight: bold; color: var(--amarelo); text-transform: uppercase; letter-spacing: 1px; padding-top: 20px; }
        .grupo-mes td { background-color: rgba(127, 219, 255, 0.1); color: #7FDBFF; font-weight: bold; font-style: italic; }
        .grupo-subtotal td { background-color: rgba(0,0,0,0.3); font-weight: bold; text-align: right; color: white; border-top: 1px dashed rgba(255,255,255,0.3); }
        
        /* LINHA TOTAL FINAL */
        .linha-total-final td { background-color: var(--branco) !important; color: var(--azul-fundo) !important; font-weight: 900 !important; font-size: 1.4rem !important; text-align: right !important; padding: 20px !important; border: 4px solid var(--azul-claro) !important; }

        /* CORES DE VALORES */
        .valor-entrada { color: var(--verde); font-weight: bold; font-family: monospace; }
        .valor-saida { color: var(--vermelho); font-weight: bold; font-family: monospace; }
        .tag-tipo { padding: 4px 10px; border-radius: 15px; font-size: 0.85rem; background: rgba(255,255,255,0.1); }
        
        /* BOTÕES */
        .btn-acao { padding: 5px 15px; font-size: 0.8rem; margin-left: 5px; border-radius: 4px; border: none; cursor: pointer; font-weight: bold; }
        .btn-edit { background: var(--amarelo); color: black; }
        .btn-del { background: var(--vermelho); color: white; }
        .btn-imprimir { background: #0074D9; color: white; border: none; padding: 15px 40px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-left: 10px; }
        
        /* MODAL EDIT */
        .modal-edit-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.8); z-index: 200; display: none; justify-content: center; align-items: center; }
        .modal-edit { background: var(--azul-fundo); padding: 30px; border-radius: 15px; border: 2px solid #7FDBFF; width: 90%; max-width: 500px; position: relative; }
        .form-edit-row { margin-bottom: 15px; }
        .form-edit-row label { display: block; margin-bottom: 5px; color: #7FDBFF; font-size: 0.9rem; }
        .form-edit-row input, .form-edit-row select { width: 100%; padding: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; border-radius: 8px; }
        
        /* BOTOES MODAL */
        .btn-save { width: 100%; padding: 15px; background: var(--verde); border: none; color: white; font-weight: bold; border-radius: 8px; cursor: pointer; margin-top: 10px; transition: opacity 0.2s; }
        .btn-modal-del { width: 100%; padding: 12px; background: transparent; border: 2px solid var(--vermelho); color: var(--vermelho); font-weight: bold; border-radius: 8px; cursor: pointer; margin-top: 15px; text-transform: uppercase; transition: all 0.2s; }
        .btn-modal-del:hover { background: var(--vermelho); color: white; }
        
        .btn-close { position: absolute; top: 15px; right: 20px; background: transparent; border: none; color: white; font-size: 2rem; cursor: pointer; }
        .hidden { display: none; }

        /* --- ESTILOS DE IMPRESSÃO --- */
        @media print {
            @page { size: landscape; margin: 10mm; } 
            body { background-color: white !important; color: black !important; overflow: visible !important; height: auto !important; display: block !important; }
            .header, .container-menu, .dock-header, .filtros-container, .btn-acao, .btn-voltar, .btn-imprimir { display: none !important; }
            .dock-overlay { position: static !important; transform: none !important; width: 100% !important; height: auto !important; background: white !important; display: block !important; }
            .resultados-area { background: white !important; border: none !important; width: 100% !important; max-width: 100% !important; overflow: visible !important; }
            .tabela-relatorio { border: 1px solid black !important; width: 100% !important; font-size: 10pt !important; border-collapse: collapse !important; }
            .tabela-relatorio th { background-color: #eee !important; color: black !important; border: 1px solid black !important; padding: 5px !important; }
            .tabela-relatorio td { border: 1px solid black !important; color: black !important; padding: 4px !important; }
            .grupo-header td { background-color: #f0f0f0 !important; font-weight: bold !important; border: 2px solid black !important; }
            .grupo-mes td { background-color: #f9f9f9 !important; font-weight: bold !important; font-style: italic !important; }
            .grupo-subtotal td { background-color: #ffffff !important; font-weight: bold !important; border-top: 2px solid black !important; text-align: right !important; }
            .linha-total-final td { background-color: #cccccc !important; font-size: 16pt !important; border: 4px solid black !important; color: black !important; text-align: right !important; -webkit-print-color-adjust: exact; }
            .print-header { display: flex !important; flex-direction: column; align-items: center; margin-bottom: 20px; border-bottom: 2px solid black; }
            .logo-impressao { height: 70px; margin-bottom: 10px; }
            .area-assinaturas { display: block !important; margin-top: 40px; }
            .linha-superior { display: flex; justify-content: space-around; width: 100%; margin-bottom: 50px;}
            .assinatura-box { width: 250px; text-align: center; border-top: 1px solid black; padding-top: 5px; font-weight: bold; text-transform: uppercase; font-size: 0.8rem; }
        }

        /* ESTILOS ESPECÍFICOS PARA O BALANÇO MENSAL NA TELA */
        .balanco-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 20px; border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); }
        .balanco-table th { background: rgba(127, 219, 255, 0.1); color: #7FDBFF; padding: 15px; text-align: left; font-size: 0.9rem; text-transform: uppercase; }
        .balanco-table td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 1.1rem; }
        .balanco-table tr:last-child td { border-bottom: none; font-weight: 900; background: rgba(255,255,255,0.02); }
        .balanco-val-pos { color: var(--verde); font-family: monospace; }
        .balanco-val-neg { color: var(--vermelho); font-family: monospace; }

        @media print {
            .balanco-table { border: 2px solid #333 !important; color: black !important; }
            .balanco-table th { background: #f0f0f0 !important; color: black !important; border-bottom: 2px solid #333 !important; }
            .balanco-table td { border-bottom: 1px solid #ccc !important; color: black !important; }
            .balanco-val-pos, .balanco-val-neg { color: black !important; }
            .print-footer { display: block !important; position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 0.7rem; color: #666; }

            /* ── RELATÓRIO SIMPLIFICADO: print-only cleanup ── */
            /* Oculta barra de progresso, mantém só o % em texto */
            #res-simplificado .tabela-relatorio td div { display: none !important; }
            /* Mantém o span do percentual visível como texto simples */
            #res-simplificado .tabela-relatorio td span { display: inline !important; font-size: 9pt !important; color: #333 !important; }
            /* Oculta coluna # (1ª coluna do thead e tbody) */
            #res-simplificado .tabela-relatorio thead tr th:first-child,
            #res-simplificado .tabela-relatorio tbody tr td:first-child { display: none !important; }
        }

        .print-header, .area-assinaturas { display: none; } 

        /* AJUSTE PARA IFRAME */
        @media screen and (min-width: 10px) {
            body.in-iframe .header, body.in-iframe .btn-voltar { display: none !important; }
            body.in-iframe .container-menu { padding-top: 20px; }
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
        <a href="dashboard" class="btn-voltar">VOLTAR</a>
        <div style="display:flex; align-items:center; gap:15px;">
            <img src="img/logo.png" alt="Logo" class="logo-tela" onerror="this.style.display='none'">
            <h1 class="title-main">RELATÓRIOS</h1>
        </div>
        <div style="width: 80px;"></div>
    </div>

    <div class="container-menu">
        <div class="grid-buttons" style="grid-template-columns:1fr 1fr;">
            <button class="btn-relatorio" onclick="abrirDock('pesquisa')">PESQUISA</button>
            <button class="btn-relatorio" onclick="abrirDock('detalhada')">VISUALIZAÇÃO<br>DETALHADA</button>
            <button class="btn-relatorio" onclick="abrirDock('balanco')">BALANÇO<br>MENSAL (AUDITORIA)</button>
            <button class="btn-relatorio" onclick="abrirDock('incongruencias')">BUSCA DE<br>INCONGRUÊNCIAS</button>
            <button class="btn-relatorio" onclick="abrirDock('simplificado')" style="grid-column:1/-1;background:rgba(46,204,64,.12);border-color:rgba(46,204,64,.4);color:#2ECC40">📊 RELATÓRIO SIMPLIFICADO<br><span style="font-size:.75em;font-weight:normal;opacity:.8">Totais por congregação · maior para menor</span></button>
        </div>
    </div>

    <div id="dock-pesquisa" class="dock-overlay">
        <div class="dock-header" onclick="fecharDock('pesquisa')"><h2>PESQUISA</h2><span>(Fechar)</span></div>
        <div class="filtros-container">
            <input type="text" id="p-nome" class="input-filtro" placeholder="Nome / Recebedor" style="width: 250px;">
            <select id="p-congregacao" class="select-filtro"><option value="todas">Todas</option></select>
            <input type="date" id="p-inicio" class="input-filtro">
            <input type="date" id="p-fim" class="input-filtro">
            <button class="btn-visualizar" onclick="executarPesquisa()">VISUALIZAR</button>
        </div>
        <div class="resultados-area" id="res-pesquisa"></div>
    </div>

    <div id="dock-detalhada" class="dock-overlay">
        <div class="dock-header" onclick="fecharDock('detalhada')"><h2>VISUALIZAÇÃO DETALHADA</h2><span>(Fechar)</span></div>
        <div class="filtros-container">
            <select id="d-tipo" class="select-filtro">
                <option value="entradas">Apenas Entradas</option>
                <option value="saidas">Apenas Saídas</option>
                <option value="ambos">Entradas e Saídas</option>
            </select>
            <select id="d-congregacao" class="select-filtro"><option value="todas">Todas</option></select>
            <input type="date" id="d-inicio" class="input-filtro">
            <input type="date" id="d-fim" class="input-filtro">
            <select id="d-ordem" class="select-filtro">
                <option value="data">Ordem por Data</option>
                <option value="nome">Ordem Alfabética</option>
                <option value="valor">Ordem por Valor</option>
            </select>
            <button class="btn-visualizar" onclick="executarDetalhada()">GERAR LISTA</button>
            <button class="btn-imprimir" onclick="window.print()">IMPRIMIR</button>
        </div>
        <div class="print-header">
            <img src="img/logo.png" alt="Logo" class="logo-impressao">
            <h2>RELATÓRIO DETALHADO POR CONGREGAÇÃO</h2>
            <p id="detalhe-periodo-texto">Relatório Financeiro</p>
        </div>
        <div class="resultados-area" id="res-detalhada"></div>
    </div>

    <div id="dock-balanco" class="dock-overlay">
        <div class="dock-header" onclick="fecharDock('balanco')"><h2>BALANÇO MENSAL</h2><span>(Fechar)</span></div>
        <div class="filtros-container">
            <input type="date" id="b-inicio" class="input-filtro">
            <input type="date" id="b-fim" class="input-filtro">
            <button class="btn-visualizar" onclick="executarBalanco()">GERAR BALANÇO</button>
            <button class="btn-imprimir" onclick="window.print()">IMPRIMIR</button>
        </div>
        <div class="print-header">
            <img src="img/logo.png" alt="Logo" class="logo-impressao">
            <h1 style="color: black; margin-bottom: 5px;">BALANÇO FINANCEIRO MENSAL</h1>
            <p style="color: black; opacity: 0.7; font-size: 0.9rem;">Relatório de Auditoria e Fechamento Mensal</p>
        </div>
        <div class="resultados-area" id="res-balanco"></div>
        
        <div class="area-assinaturas" style="margin-top: 60px;">
            <div style="display: flex; justify-content: space-around; width: 100%;">
                <div class="assinatura-box">Tesouraria</div>
                <div class="assinatura-box">Conselho Fiscal</div>
                <div class="assinatura-box">Pastor Presidente</div>
            </div>
        </div>
    </div>

    <div id="dock-incongruencias" class="dock-overlay">
        <div class="dock-header" onclick="fecharDock('incongruencias')"><h2>BUSCA DE INCONGRUÊNCIAS</h2><span>(Fechar)</span></div>
        <div class="filtros-container">
            <button class="btn-visualizar" onclick="executarIncongruencias()">INICIAR VARREDURA DO SISTEMA</button>
        </div>
        <div class="resultados-area" id="res-incongruencias"></div>
    </div>

    <!-- DOCK: RELATÓRIO SIMPLIFICADO -->
    <div id="dock-simplificado" class="dock-overlay">
        <div class="dock-header" onclick="fecharDock('simplificado')"><h2>RELATÓRIO SIMPLIFICADO</h2><span>(Fechar)</span></div>
        <div class="filtros-container">
            <!-- TAG-CHIP MULTI CONGREGATION AUTOCOMPLETE -->
            <div style="position:relative;flex:1;max-width:480px">
                <label style="font-size:.75rem;color:rgba(255,255,255,.5);display:block;margin-bottom:4px">Congregações (deixe vazio = todas)</label>
                <div id="s-chips" onclick="document.getElementById('s-cong-input').focus()" style="display:flex;flex-wrap:wrap;gap:5px;align-items:center;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:8px;min-height:52px;cursor:text">
                    <input id="s-cong-input" type="text" placeholder="Digite o nome..." autocomplete="off"
                        style="background:transparent;border:none;color:white;outline:none;font-size:.9rem;flex:1;min-width:120px"
                        oninput="filtrarSugestoesCong(this.value)"
                        onkeydown="handleChipKey(event)">
                </div>
                <div id="s-cong-sugestoes" style="display:none;position:absolute;top:100%;left:0;right:0;background:#002952;border:1px solid rgba(255,255,255,.2);border-radius:8px;max-height:200px;overflow-y:auto;z-index:300;"></div>
            </div>
            <input type="date" id="s-inicio" class="input-filtro">
            <input type="date" id="s-fim" class="input-filtro">
            <button class="btn-visualizar" onclick="executarSimplificado()">GERAR</button>
            <button class="btn-imprimir" onclick="window.print()">IMPRIMIR</button>
            <button class="btn-imprimir" style="background:#27ae60" onclick="exportarSimplificadoTxt()">⬇ TXT</button>
        </div>
        <div class="print-header">
            <img src="img/logo.png" alt="Logo" class="logo-impressao">
            <h2>RELATÓRIO SIMPLIFICADO — TOTAIS POR CONGREGAÇÃO</h2>
            <p id="simpl-periodo-texto">Relatório Financeiro</p>
        </div>
        <div class="resultados-area" id="res-simplificado"></div>
    </div>

    <div id="modal-edit" class="modal-edit-overlay">
        <div class="modal-edit">
            <button class="btn-close" onclick="fecharModalEdit()">&times;</button>
            <h3 id="modal-titulo" style="color: #7FDBFF; margin-bottom: 20px;">Editar Registro</h3>
            <input type="hidden" id="edit-id"><input type="hidden" id="edit-origem">
            
            <div class="form-edit-row"><label>Data:</label><input type="date" id="edit-data"></div>
            <div class="form-edit-row"><label id="lbl-nome">Nome / Recebedor:</label><input type="text" id="edit-nome"></div>
            <div class="form-edit-row"><label>Valor (R$):</label><input type="number" step="0.01" id="edit-valor"></div>
            
            <div id="fields-entrada">
                <div class="form-edit-row"><label>Congregação:</label><input type="text" id="edit-congregacao"></div>
                <div class="form-edit-row"><label>Tipo de Entrada:</label><input type="text" id="edit-tipo"></div>
            </div>
            
            <div id="fields-saida" class="hidden">
                <div class="form-edit-row"><label>Descrição:</label><input type="text" id="edit-descricao"></div>
                <div class="form-edit-row"><label>Tipo de Saída:</label><input type="text" id="edit-tipo-saida"></div>
            </div>

            <button class="btn-save" onclick="salvarEdicao()">SALVAR ALTERAÇÕES</button>
            <button class="btn-modal-del" onclick="excluirRegistroDoModal()">ELIMINAR REGISTRO</button>
        </div>
    </div>

    <script>
        let dockAtual = ''; // Para saber qual dock atualizar

        const fmtMoeda = (v) => parseFloat(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        const fmtNum = (v) => parseFloat(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 });

        function abrirDock(id) { 
            dockAtual = id;
            document.getElementById('dock-'+id).classList.add('dock-active'); 
        }
        function fecharDock(id) { 
            document.getElementById('dock-'+id).classList.remove('dock-active'); 
            dockAtual = '';
        }

        async function carregarCongregacoes() {
            try {
                const resp = await fetch('financeiro_lista_congregacoes');
                const json = await resp.json();
                if(json.status === 'success') {
                    ['p-congregacao', 'd-congregacao'].forEach(id => {
                        const sel = document.getElementById(id);
                        sel.innerHTML = '<option value="todas">Todas</option>';
                        json.dados.forEach(c => { sel.innerHTML += `<option value="${c}">${c}</option>`; });
                    });
                }
            } catch(e) {}
        }
        // FUNÇÃO PARA DEFINIR DATAS PADRÃO (INÍCIO E FIM DO ANO ATUAL)
        function definirDatasPadrao() {
            const ano = new Date().getFullYear();
            const inicio = `${ano}-01-01`;
            const fim = `${ano}-12-31`;
            
            ['p-inicio', 'd-inicio', 'b-inicio'].forEach(id => document.getElementById(id).value = inicio);
            ['p-fim', 'd-fim', 'b-fim'].forEach(id => document.getElementById(id).value = fim);
        }

        document.addEventListener('DOMContentLoaded', () => {
            carregarCongregacoes();
            definirDatasPadrao();
        });

        // FUNÇÃO PARA ATUALIZAR OS RESULTADOS SEM RECARREGAR A PÁGINA
        function atualizarResultados() {
            if(dockAtual === 'pesquisa') executarPesquisa();
            if(dockAtual === 'detalhada') executarDetalhada();
            if(dockAtual === 'incongruencias') executarIncongruencias();
        }

        async function executarPesquisa() {
            const n=document.getElementById('p-nome').value, c=document.getElementById('p-congregacao').value, i=document.getElementById('p-inicio').value, f=document.getElementById('p-fim').value;
            const resp = await fetch(`api_relatorios?tipo_relatorio=pesquisa&inicio=${i}&fim=${f}&nome=${n}&congregacao=${c}`);
            const json = await resp.json();
            const div = document.getElementById('res-pesquisa');
            if(json.status==='success') { renderizarTabela(json.dados, div); }
        }

        function renderizarTabela(dados, div) {
            if(!dados.length) { div.innerHTML = "Sem dados."; return; }
            // Build congregation totals
            const totCong = {};
            let totalEntradas = 0, totalSaidas = 0;
            dados.forEach(d => {
                const ori = d.origem || (d.recebedor ? 'Saída' : 'Entrada');
                const label = ori === 'Entrada' ? (d.congregacao || d.info_extra || 'Sem Congregação') : 'SAÍDAS / DESPESAS';
                if(!totCong[label]) totCong[label] = { entradas: 0, saidas: 0 };
                const v = parseFloat(d.valor);
                if(ori === 'Entrada') { totCong[label].entradas += v; totalEntradas += v; }
                else { totCong[label].saidas += v; totalSaidas += v; }
            });

            // Summary section by congregation
            const congKeys = Object.keys(totCong).sort();
            let resumo = `<table class="tabela-relatorio" style="margin-bottom:20px">
                <thead><tr><th>Congregação</th><th style='text-align:right;color:var(--verde)'>Entradas</th><th style='text-align:right;color:var(--vermelho)'>Saídas</th><th style='text-align:right'>Saldo</th></tr></thead><tbody>`;
            congKeys.forEach(cong => {
                const saldo = totCong[cong].entradas - totCong[cong].saidas;
                resumo += `<tr class="grupo-subtotal" style='background:rgba(0,0,0,0.2)'>
                    <td style='text-align:left'>${cong}</td>
                    <td class='valor-entrada' style='text-align:right'>${fmtMoeda(totCong[cong].entradas)}</td>
                    <td class='valor-saida' style='text-align:right'>${fmtMoeda(totCong[cong].saidas)}</td>
                    <td class='${saldo>=0?"valor-entrada":"valor-saida"}' style='text-align:right;font-weight:bold'>${fmtMoeda(saldo)}</td>
                </tr>`;
            });
            const saldoGeral = totalEntradas - totalSaidas;
            resumo += `<tr class="linha-total-final">
                <td style='text-align:left'>TOTAL GERAL</td>
                <td>${fmtMoeda(totalEntradas)}</td>
                <td>${fmtMoeda(totalSaidas)}</td>
                <td>${fmtMoeda(saldoGeral)}</td>
            </tr></tbody></table>`;

            // Detail table
            let h = `<table class="tabela-relatorio"><thead><tr><th>Data</th><th>Nome</th><th>Tipo</th><th>Valor</th><th class="btn-acao-header">Ações</th></tr></thead><tbody>`;
            dados.forEach(d => {
                const ori = d.origem || (d.recebedor ? 'Saída' : 'Entrada');
                h += `<tr><td>${new Date(d.data_movimento + "T12:00:00").toLocaleDateString()}</td><td>${d.principal || d.nome}</td><td>${d.categoria}</td><td class="${ori==='Entrada'?'valor-entrada':'valor-saida'}">${fmtMoeda(d.valor)}</td><td><button class="btn-acao btn-edit" onclick="prepararEdicao(${d.id}, '${ori}')">Editar</button><button class="btn-acao btn-del" onclick="excluirRegistro(${d.id}, '${ori}')">Excluir</button></td></tr>`;
            });
            div.innerHTML = resumo + h + '</tbody></table>';
        }

        async function executarDetalhada() {
            const ini=document.getElementById('d-inicio').value, fim=document.getElementById('d-fim').value, tipo=document.getElementById('d-tipo').value, cong=document.getElementById('d-congregacao').value, ord=document.getElementById('d-ordem').value;
            document.getElementById('detalhe-periodo-texto').innerText = `Período: ${ini||'...'} até ${fim||'Hoje'} - Filtro: ${cong}`;
            const resp = await fetch(`api_relatorios?tipo_relatorio=detalhada&inicio=${ini}&fim=${fim}&filtro_tipo=${tipo}&congregacao=${cong}&ordem=${ord}`);
            const json = await resp.json();
            if(json.status === 'success') { renderizarAgrupado(json.dados, document.getElementById('res-detalhada')); }
        }

        function renderizarAgrupado(dados, div) {
            if(!dados.length) { div.innerHTML = 'Nenhum dado.'; return; }
            const estrutura = {};
            let totalEntradas = 0, totalSaidas = 0;
            dados.forEach(d => {
                let cong = (d.origem === 'Saída') ? 'DESPESAS / SAÍDAS' : (d.congregacao || 'GERAL');
                const dataObj = new Date(d.data_movimento + "T12:00:00");
                const mesRef = dataObj.toLocaleString('pt-BR', { month: 'long', year: 'numeric' }).toUpperCase();
                if(!estrutura[cong]) estrutura[cong] = { meses: {}, totalEntradas: 0, totalSaidas: 0 };
                if(!estrutura[cong].meses[mesRef]) estrutura[cong].meses[mesRef] = { itens: [], totalMes: 0 };
                estrutura[cong].meses[mesRef].itens.push(d);
                const v = parseFloat(d.valor);
                estrutura[cong].meses[mesRef].totalMes += v;
                if(d.origem === 'Saída') {
                    estrutura[cong].totalSaidas += v;
                    totalSaidas += v;
                } else {
                    estrutura[cong].totalEntradas += v;
                    totalEntradas += v;
                }
            });

            // Summary table per congregation
            const congKeys = Object.keys(estrutura).sort();
            let resumo = `<table class="tabela-relatorio" style="margin-bottom:20px">
                <thead><tr><th>Congregação</th><th style='text-align:right;color:var(--verde)'>Entradas</th><th style='text-align:right;color:var(--vermelho)'>Saídas</th><th style='text-align:right'>Saldo</th></tr></thead><tbody>`;
            congKeys.forEach(cong => {
                const saldo = estrutura[cong].totalEntradas - estrutura[cong].totalSaidas;
                resumo += `<tr class="grupo-subtotal" style='background:rgba(0,0,0,0.2)'>
                    <td style='text-align:left'>${cong}</td>
                    <td class='valor-entrada' style='text-align:right'>${fmtMoeda(estrutura[cong].totalEntradas)}</td>
                    <td class='valor-saida' style='text-align:right'>${fmtMoeda(estrutura[cong].totalSaidas)}</td>
                    <td class='${saldo>=0?"valor-entrada":"valor-saida"}' style='text-align:right;font-weight:bold'>${fmtMoeda(saldo)}</td>
                </tr>`;
            });
            const saldoGeral = totalEntradas - totalSaidas;
            resumo += `<tr class="linha-total-final">
                <td style='text-align:left'>TOTAL GERAL</td>
                <td>${fmtMoeda(totalEntradas)}</td>
                <td>${fmtMoeda(totalSaidas)}</td>
                <td>${fmtMoeda(saldoGeral)}</td>
            </tr></tbody></table>`;

            // Detail table grouped by congregation and month
            let h = `<table class="tabela-relatorio"><thead><tr><th>Data</th><th>Nome/Descrição</th><th>Tipo</th><th style="text-align:right">Valor</th><th class="btn-acao-header">Ações</th></tr></thead><tbody>`;
            congKeys.forEach(cong => {
                h += `<tr class="grupo-header"><td colspan="5">${cong}</td></tr>`;
                Object.keys(estrutura[cong].meses).forEach(mes => {
                    h += `<tr class="grupo-mes"><td colspan="5">${mes}</td></tr>`;
                    estrutura[cong].meses[mes].itens.forEach(i => {
                        const ori = i.origem || (i.recebedor ? 'Saída' : 'Entrada');
                        h += `<tr><td>${new Date(i.data_movimento + "T12:00:00").toLocaleDateString()}</td><td>${i.principal || i.nome}</td><td>${i.categoria}</td><td style="text-align:right" class="${i.origem==='Saída'?'valor-saida':'valor-entrada'}">${fmtMoeda(i.valor)}</td><td><button class="btn-acao btn-edit" onclick="prepararEdicao(${i.id}, '${ori}')">Editar</button><button class="btn-acao btn-del" onclick="excluirRegistro(${i.id}, '${ori}')">Excluir</button></td></tr>`;
                    });
                    h += `<tr class="grupo-subtotal"><td colspan="3">SUBTOTAL ${mes}:</td><td colspan="2" style='text-align:right'>${fmtMoeda(estrutura[cong].meses[mes].totalMes)}</td></tr>`;
                });
                const totSaldo = estrutura[cong].totalEntradas - estrutura[cong].totalSaidas;
                h += `<tr class="grupo-subtotal" style="background:rgba(0,0,0,0.4)">`;
                h += `<td>TOTAL ${cong}</td>`;
                h += `<td class='valor-entrada' style='text-align:right'>${fmtMoeda(estrutura[cong].totalEntradas)}</td>`;
                h += `<td class='valor-saida' style='text-align:right'>${fmtMoeda(estrutura[cong].totalSaidas)}</td>`;
                h += `<td class='${totSaldo>=0?"valor-entrada":"valor-saida"}' style='text-align:right;font-weight:bold'>${fmtMoeda(totSaldo)}</td>`;
                h += `<td></td></tr>`;
            });
            h += `<tr class="linha-total-final">
                <td style='text-align:left'>TOTAL GERAL</td>
                <td>${fmtMoeda(totalEntradas)}</td>
                <td>${fmtMoeda(totalSaidas)}</td>
                <td>${fmtMoeda(saldoGeral)}</td>
                <td></td>
            </tr>`;
            h += '</tbody></table>';
            div.innerHTML = resumo + h;
        }

        async function executarBalanco() {
            const i=document.getElementById('b-inicio').value, f=document.getElementById('b-fim').value;
            const resp = await fetch(`api_relatorios?tipo_relatorio=balanco&inicio=${i}&fim=${f}`);
            const json = await resp.json();
            if(json.status==='success') {
                const dadosBalanco = json.dados; let meses = dadosBalanco.map(d => d.mes_ref);
                let lEnt = '', lSai = '', lSal = ''; let tEnt=0, tSai=0, tSal=0;
                dadosBalanco.forEach(d => {
                    lEnt += `<td>${fmtMoeda(d.entradas)}</td>`; 
                    lSai += `<td>${fmtMoeda(d.saidas)}</td>`; 
                    const saldoCls = d.saldo >= 0 ? 'balanco-val-pos' : 'balanco-val-neg';
                    lSal += `<td class="${saldoCls}">${fmtMoeda(d.saldo)}</td>`;
                    tEnt += parseFloat(d.entradas); tSai += parseFloat(d.saidas); tSal += parseFloat(d.saldo);
                });
                
                const saldoFinalCls = tSal >= 0 ? 'balanco-val-pos' : 'balanco-val-neg';
                
                document.getElementById('res-balanco').innerHTML = `
                    <div style="margin-bottom: 20px; font-size: 0.9rem; opacity: 0.7;">Gerado em: ${new Date().toLocaleString()}</div>
                    <table class="balanco-table">
                        <thead>
                            <tr>
                                <th style="width: 200px">DESCRIÇÃO</th>
                                ${meses.map(m => `<th>${m}</th>`).join('')}
                                <th style="background: rgba(127, 219, 255, 0.2)">TOTAL ACUMULADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight: 700">ENTRADAS (+)</td>
                                ${lEnt}
                                <td style="font-weight: 900; color: var(--verde);">${fmtMoeda(tEnt)}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700">SAÍDAS (-)</td>
                                ${lSai}
                                <td style="font-weight: 900; color: var(--vermelho);">${fmtMoeda(tSai)}</td>
                            </tr>
                            <tr style="background: rgba(255,255,255,0.05)">
                                <td style="font-weight: 900; text-transform: uppercase;">SALDO LÍQUIDO (=)</td>
                                ${lSal}
                                <td class="${saldoFinalCls}" style="font-size: 1.4rem; border-left: 2px solid rgba(255,255,255,0.1)">${fmtMoeda(tSal)}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top: 30px; font-size: 0.85rem; opacity: 0.6; line-height: 1.6;">
                        * Este documento apresenta o resumo consolidado das movimentações financeiras no período selecionado.<br>
                        * Registros de entradas e saídas foram conferidos conforme o sistema de gestão eclesiástica.
                    </div>
                `;
            }
        }

        async function executarIncongruencias() {
            const div = document.getElementById('res-incongruencias');
            div.innerHTML = '<p style="text-align:center; padding:20px;">Varrendo o banco de dados em busca de falhas... Aguarde.</p>';
            
            try {
                const resp = await fetch(`api_relatorios?tipo_relatorio=incongruencias`);
                const json = await resp.json();
                
                if(json.status === 'success') {
                    if(!json.dados.length) {
                        div.innerHTML = '<div style="background: var(--verde); color: white; padding: 20px; border-radius: 10px; text-align: center;"><b>SISTEMA SAUDÁVEL:</b> Nenhuma incongruência detectada nos registros atuais.</div>';
                        return;
                    }
                    
                    let h = `<p style="color: var(--amarelo); margin-bottom: 10px;">Foram encontrados ${json.dados.length} registros com possíveis erros:</p>`;
                    h += `<table class="tabela-relatorio"><thead><tr><th>Data</th><th>Principal</th><th>Problema Detectado</th><th class="btn-acao-header">Ações</th></tr></thead><tbody>`;
                    
                    json.dados.forEach(d => {
                        h += `<tr>
                            <td>${new Date(d.data_movimento + "T12:00:00").toLocaleDateString()}</td>
                            <td>${d.principal}</td>
                            <td style="color: var(--amarelo)">${d.motivo}</td>
                            <td>
                                <button class="btn-edit btn-acao" onclick="prepararEdicao(${d.id}, '${d.origem}')">Corrigir</button>
                            </td>
                        </tr>`;
                    });
                    div.innerHTML = h + '</tbody></table>';
                }
            } catch (e) {
                div.innerHTML = '<p style="color: var(--vermelho)">Erro ao conectar com o servidor para varredura.</p>';
            }
        }

        async function prepararEdicao(id, origem) {
            const resp = await fetch(`financeiro_buscar_edicao?id=${id}&origem=${origem}`);
            const json = await resp.json();
            if(json.status==='success') {
                const d = json.dados; document.getElementById('edit-id').value = id; document.getElementById('edit-origem').value = origem;
                document.getElementById('edit-data').value = d.data_movimento; document.getElementById('edit-valor').value = d.valor;
                document.getElementById('edit-nome').value = (origem==='Entrada') ? (d.nome || d.principal) : (d.recebedor || d.principal);
                if(origem==='Entrada') {
                    document.getElementById('fields-entrada').classList.remove('hidden'); document.getElementById('fields-saida').classList.add('hidden');
                    document.getElementById('edit-congregacao').value = d.congregacao || d.info_extra; document.getElementById('edit-tipo').value = d.tipo || d.categoria;
                } else {
                    document.getElementById('fields-entrada').classList.add('hidden'); document.getElementById('fields-saida').classList.remove('hidden');
                    document.getElementById('edit-descricao').value = d.descricao || d.info_extra; document.getElementById('edit-tipo-saida').value = d.tipo_saida || d.categoria;
                }
                document.getElementById('modal-edit').style.display = 'flex';
            }
        }

        async function salvarEdicao() {
            const origem = document.getElementById('edit-origem').value;
            const payload = {
                id: document.getElementById('edit-id').value, origem: origem,
                data: document.getElementById('edit-data').value, valor: document.getElementById('edit-valor').value
            };

            // MAPEAMENTO CORRETO DOS NOMES DE CAMPOS
            if(origem === 'Entrada') {
                payload.nome = document.getElementById('edit-nome').value;
                payload.congregacao = document.getElementById('edit-congregacao').value;
                payload.tipo = document.getElementById('edit-tipo').value;
            } else {
                payload.recebedor = document.getElementById('edit-nome').value;
                payload.descricao = document.getElementById('edit-descricao').value;
                payload.tipo_saida = document.getElementById('edit-tipo-saida').value;
            }

            const resp = await fetch('financeiro_salvar_edicao', { method:'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            const res = await resp.json(); 
            if(res.status==='success') { 
                alert('Atualizado com sucesso!'); 
                fecharModalEdit();
                atualizarResultados(); // ATUALIZA SEM RECARREGAR
            }
        }

        async function excluirRegistro(id, origem) {
            if(!confirm("Deseja realmente eliminar este registro permanentemente?")) return false;
            const url = origem === 'Entrada' ? 'financeiro_excluir_entrada' : 'financeiro_excluir_saida';
            const resp = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: id }) });
            const res = await resp.json(); 
            if(res.status === 'success') { 
                alert("Registro eliminado!"); 
                atualizarResultados(); // ATUALIZA SEM RECARREGAR
                return true;
            }
            return false;
        }

        async function excluirRegistroDoModal() {
            const id = document.getElementById('edit-id').value;
            const ori = document.getElementById('edit-origem').value;
            if(await excluirRegistro(id, ori)) {
                fecharModalEdit();
            }
        }

        function fecharModalEdit() { document.getElementById('modal-edit').style.display = 'none'; }

        // ══════════════ RELATÓRIO SIMPLIFICADO ══════════════
        let todasCongregacoes = [];   // all known congregation names
        let chipsSelecionados = [];   // currently selected chips

        // Initialise autocomplete data once dock opens
        function initSimplificadoAutocomplete() {
            if (todasCongregacoes.length) return;
            fetch('financeiro_lista_congregacoes')
                .then(r => r.json())
                .then(j => { if (j.status === 'success') todasCongregacoes = j.dados; });
        }

        // Wire abrirDock to init autocomplete when simplificado opens
        const _origAbrirDock = window.abrirDock;
        window.abrirDock = function(id) {
            _origAbrirDock && _origAbrirDock(id);
            if (id === 'simplificado') {
                initSimplificadoAutocomplete();
                // Set default dates if empty
                const ano = new Date().getFullYear();
                if (!document.getElementById('s-inicio').value)
                    document.getElementById('s-inicio').value = `${ano}-01-01`;
                if (!document.getElementById('s-fim').value)
                    document.getElementById('s-fim').value = `${ano}-12-31`;
            }
        };

        function filtrarSugestoesCong(val) {
            const box = document.getElementById('s-cong-sugestoes');
            const q = val.trim().toLowerCase();
            if (!q) { box.style.display = 'none'; return; }
            const r = todasCongregacoes.filter(c => c.toLowerCase().includes(q) && !chipsSelecionados.includes(c));
            if (!r.length) { box.style.display = 'none'; return; }
            box.innerHTML = r.map(c => `<div onclick="adicionarChip('${c.replace(/'/g,"\\'")}');document.getElementById('s-cong-input').value='';filtrarSugestoesCong('');"
                style="padding:10px 14px;cursor:pointer;color:#fff;font-size:.88rem;border-bottom:1px solid rgba(255,255,255,.06)"
                onmouseover="this.style.background='rgba(127,219,255,.15)'" onmouseout="this.style.background=''">${c}</div>`).join('');
            box.style.display = 'block';
        }

        function adicionarChip(nome) {
            if (chipsSelecionados.includes(nome)) return;
            chipsSelecionados.push(nome);
            renderChips();
        }

        function removerChip(nome) {
            chipsSelecionados = chipsSelecionados.filter(c => c !== nome);
            renderChips();
        }

        function renderChips() {
            const container = document.getElementById('s-chips');
            // Remove old chips (keep input)
            container.querySelectorAll('.chip-tag').forEach(el => el.remove());
            const input = document.getElementById('s-cong-input');
            chipsSelecionados.forEach(nome => {
                const chip = document.createElement('span');
                chip.className = 'chip-tag';
                chip.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:rgba(127,219,255,.2);border:1px solid rgba(127,219,255,.4);border-radius:20px;padding:3px 10px;font-size:.8rem;color:#7FDBFF;white-space:nowrap';
                chip.innerHTML = `${nome} <span onclick="removerChip('${nome.replace(/'/g,"\\'")}');event.stopPropagation();" style="cursor:pointer;font-weight:bold;color:rgba(255,255,255,.6);font-size:.9rem">×</span>`;
                container.insertBefore(chip, input);
            });
        }

        function handleChipKey(e) {
            const inp = document.getElementById('s-cong-input');
            if ((e.key === 'Enter' || e.key === ',') && inp.value.trim()) {
                e.preventDefault();
                const val = inp.value.trim().replace(',','');
                // If exact match in list, add it; otherwise try partial
                const match = todasCongregacoes.find(c => c.toLowerCase() === val.toLowerCase())
                           || todasCongregacoes.find(c => c.toLowerCase().includes(val.toLowerCase()));
                if (match) { adicionarChip(match); inp.value = ''; filtrarSugestoesCong(''); }
            }
            if (e.key === 'Backspace' && inp.value === '' && chipsSelecionados.length) {
                removerChip(chipsSelecionados[chipsSelecionados.length - 1]);
            }
            if (e.key === 'Escape') document.getElementById('s-cong-sugestoes').style.display = 'none';
        }

        // Close suggestions when clicking outside
        document.addEventListener('click', e => {
            if (!e.target.closest('#s-chips') && !e.target.closest('#s-cong-sugestoes'))
                document.getElementById('s-cong-sugestoes').style.display = 'none';
        });

        async function executarSimplificado() {
            const ini  = document.getElementById('s-inicio').value;
            const fim  = document.getElementById('s-fim').value;
            const div  = document.getElementById('res-simplificado');
            div.innerHTML = '<p style="text-align:center;padding:30px;opacity:.6">Carregando…</p>';

            const congParam = chipsSelecionados.join(',');
            const params = new URLSearchParams({ inicio: ini, fim: fim });
            if (congParam) params.set('congregacoes', congParam);

            const resp = await fetch('api_rel_simplificado?' + params);
            const json = await resp.json();
            if (json.status !== 'success' || !json.dados.length) {
                div.innerHTML = '<p style="text-align:center;padding:30px;opacity:.6">Nenhum dado encontrado para o período.</p>';
                return;
            }

            document.getElementById('simpl-periodo-texto').textContent =
                `Período: ${new Date(ini+'T12:00').toLocaleDateString('pt-BR')} a ${new Date(fim+'T12:00').toLocaleDateString('pt-BR')}` +
                (chipsSelecionados.length ? ` | Congregações: ${chipsSelecionados.join(', ')}` : ' | Todas as congregações');

            const dados = json.dados;
            const max = parseFloat(dados[0].total);
            const totalGeral = dados.reduce((s,d) => s + parseFloat(d.total), 0);

            let h = `<table class="tabela-relatorio">
                <thead><tr>
                    <th style="width:40px">#</th>
                    <th>Congregação</th>
                    <th style="text-align:right">Registros</th>
                    <th style="text-align:right;color:var(--verde)">Total Entradas</th>
                    <th>Proporção</th>
                </tr></thead><tbody>`;

            dados.forEach((d, i) => {
                const v = parseFloat(d.total);
                const pct = Math.round((v / max) * 100);
                const pctTotal = ((v / totalGeral) * 100).toFixed(1);
                const medal = i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : `${i+1}º`;
                const barColor = i === 0 ? 'var(--verde)' : i < 3 ? '#7FDBFF' : 'rgba(255,255,255,.3)';
                h += `<tr>
                    <td style="text-align:center;font-weight:bold;font-size:1rem">${medal}</td>
                    <td style="font-weight:600">${d.congregacao}</td>
                    <td style="text-align:right;opacity:.7">${d.registros}</td>
                    <td class="valor-entrada" style="text-align:right;font-size:1rem">${fmtMoeda(v)}</td>
                    <td style="min-width:160px">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="flex:1;background:rgba(255,255,255,.08);border-radius:4px;height:8px">
                                <div style="width:${pct}%;height:100%;border-radius:4px;background:${barColor};transition:width .6s"></div>
                            </div>
                            <span style="font-size:.78rem;color:rgba(255,255,255,.6);min-width:38px">${pctTotal}%</span>
                        </div>
                    </td>
                </tr>`;
            });

            h += `</tbody></table>
            <table class="tabela-relatorio" style="margin-top:16px">
                <tbody>
                    <tr class="linha-total-final">
                        <td style="text-align:left">TOTAL GERAL</td>
                        <td style="text-align:right">${dados.reduce((s,d)=>s+parseInt(d.registros),0)} registros</td>
                        <td style="text-align:right">${fmtMoeda(totalGeral)}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>`;

            div.innerHTML = h;
        }

        function exportarSimplificadoTxt() {
            const div = document.getElementById('res-simplificado');
            if (!div || !div.querySelector('table')) {
                alert('Gere o relatório primeiro antes de exportar.'); return;
            }
            const ini  = document.getElementById('s-inicio').value;
            const fim  = document.getElementById('s-fim').value;
            const periodoFmt = `${new Date(ini+'T12:00').toLocaleDateString('pt-BR')} a ${new Date(fim+'T12:00').toLocaleDateString('pt-BR')}`;
            const filtro = chipsSelecionados.length ? chipsSelecionados.join(', ') : 'Todas as congregações';

            const rows = div.querySelectorAll('.tabela-relatorio tbody tr');
            const sep  = '-'.repeat(72);
            const lines = [];
            lines.push('RELATÓRIO SIMPLIFICADO — TOTAIS POR CONGREGAÇÃO');
            lines.push(sep);
            lines.push(`Período  : ${periodoFmt}`);
            lines.push(`Filtro   : ${filtro}`);
            lines.push(`Gerado em: ${new Date().toLocaleString('pt-BR')}`);
            lines.push(sep);
            lines.push(`${'Posição'.padEnd(10)}${'Congregação'.padEnd(36)}${'Total (R$)'.padStart(18)}${'%'.padStart(8)}`);
            lines.push(sep);

            let totalRegs = 0, totalVal = 0;
            rows.forEach((tr, i) => {
                const tds = tr.querySelectorAll('td');
                if (tds.length < 5) return; // skip total row from inner table
                const pos   = (i+1) + 'º';
                const cong  = (tds[1].textContent||'').trim();
                const regs  = (tds[2].textContent||'').trim();
                const valor = (tds[3].textContent||'').trim();
                // span inside last td has the % text
                const pct   = (tds[4].querySelector('span')?.textContent||'').trim();
                totalRegs  += parseInt(regs) || 0;
                lines.push(`${pos.padEnd(10)}${cong.padEnd(36)}${valor.padStart(18)}${pct.padStart(8)}`);
            });
            lines.push(sep);
            // grand total row — value is in 3rd td (index 2), last td is empty
            const totalRow = div.querySelector('.linha-total-final');
            const totalValTxt = totalRow ? totalRow.querySelectorAll('td')[2]?.textContent?.trim() : '';
            const totalRegsTxt = String(totalRegs);
            lines.push(`${'TOTAL'.padEnd(10)}${''.padEnd(36)}${totalValTxt.padStart(18)}${'100.0%'.padStart(8)}`);
            lines.push(sep);

            const blob = new Blob([lines.join('\n')], { type: 'text/plain;charset=utf-8' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `relatorio_simplificado_${ini}_${fim}.txt`;
            a.click();
            URL.revokeObjectURL(a.href);
        }
    </script>
</body>
</html>