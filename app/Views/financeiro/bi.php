<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BI Financeiro</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
:root {
    --bg:#001f3f; --surface:#002952; --border:rgba(255,255,255,.1);
    --accent:#7FDBFF; --green:#2ECC40; --red:#FF4136; --yellow:#FFDC00;
    --text:#fff; --muted:rgba(255,255,255,.55); --card-radius:16px;
}
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:var(--bg);color:var(--text);min-height:100vh;}

/* ── HEADER ── */
.bi-header{display:flex;align-items:center;justify-content:space-between;padding:14px 26px;background:var(--surface);border-bottom:1px solid var(--border);}
.bi-header h1{font-size:1.3rem;color:var(--accent);letter-spacing:2px;}
.btn-pdf{background:var(--accent);color:#001f3f;border:none;padding:9px 22px;border-radius:30px;font-weight:900;font-size:.82rem;cursor:pointer;letter-spacing:1px;transition:opacity .2s;}
.btn-pdf:hover{opacity:.8;}

/* ── FILTER BAR ── */
.filter-bar{display:flex;flex-wrap:wrap;gap:12px;align-items:center;padding:12px 26px;background:rgba(0,41,82,.75);border-bottom:1px solid var(--border);}
.filter-bar label{font-size:.8rem;color:var(--muted);}
.fi{background:rgba(255,255,255,.08);border:1px solid var(--border);color:#fff;padding:8px 12px;border-radius:8px;font-size:.88rem;}
.btn-aplicar{background:var(--green);color:#fff;border:none;padding:9px 22px;border-radius:8px;font-weight:700;cursor:pointer;}

/* ── GRID ── */
.bi-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;padding:20px 26px;}
.bi-grid .span2{grid-column:1/-1;}

/* ── CARD ── */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--card-radius);padding:20px;display:flex;flex-direction:column;gap:14px;transition:transform .2s;position:relative;}
.card:hover{transform:translateY(-3px);}
.card-title{font-size:.72rem;color:var(--accent);text-transform:uppercase;letter-spacing:1.5px;font-weight:700;}
.card-subtitle{font-size:.78rem;color:var(--muted);margin-top:2px;}
.chart-wrap{position:relative;flex:1;min-height:220px;cursor:pointer;}
.click-hint{position:absolute;bottom:6px;right:10px;font-size:.68rem;color:var(--muted);pointer-events:none;opacity:.7;}

/* ── KPI ── */
.kpi-value{font-size:3.2rem;font-weight:900;color:var(--green);line-height:1;}
.kpi-label{font-size:.82rem;color:var(--muted);margin-top:4px;}

/* ── TABLE ── */
.bi-table{width:100%;border-collapse:collapse;}
.bi-table th{font-size:.72rem;color:var(--accent);text-align:left;padding:7px 10px;border-bottom:1px solid var(--border);}
.bi-table td{padding:9px 10px;border-bottom:1px solid rgba(255,255,255,.04);font-size:.88rem;}
.bi-table tr:hover td{background:rgba(127,219,255,.06);}
.badge{display:inline-block;padding:2px 9px;border-radius:20px;font-size:.72rem;font-weight:700;background:rgba(255,255,255,.1);}
.badge-gold{background:rgba(255,200,0,.2);color:#FFD700;}
.badge-silver{background:rgba(180,180,180,.2);color:#C0C0C0;}
.badge-bronze{background:rgba(180,100,0,.2);color:#CD7F32;}
.valor-entrada{color:var(--green);font-weight:bold;font-family:monospace;}
.valor-saida{color:var(--red);font-weight:bold;font-family:monospace;}

/* ── SPINNER ── */
.spinner{text-align:center;padding:36px;color:var(--muted);font-size:.88rem;}

/* ── DRILL-DOWN MODAL ── */
.dd-overlay{position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:500;display:none;align-items:center;justify-content:center;padding:20px;}
.dd-overlay.open{display:flex;}
.dd-modal{background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:28px;width:100%;max-width:820px;max-height:85vh;display:flex;flex-direction:column;gap:16px;position:relative;}
.dd-modal h3{color:var(--accent);font-size:1.1rem;}
.dd-close{position:absolute;top:14px;right:18px;background:transparent;border:none;color:var(--muted);font-size:1.8rem;cursor:pointer;line-height:1;}
.dd-close:hover{color:#fff;}
.dd-body{overflow-y:auto;flex:1;}
.dd-summary{display:flex;gap:20px;font-size:.85rem;flex-wrap:wrap;color:var(--muted);}
.dd-summary strong{color:#fff;font-size:1rem;}

/* ── DATA-ONLY PDF SECTION ── */
#pdf-dados{display:none;font-family:'Segoe UI',Arial,sans-serif;color:#111;background:#fff;padding:0;}
#pdf-dados .pdf-page{padding:18mm 16mm 14mm 16mm;page-break-after:always;}
#pdf-dados .pdf-page:last-child{page-break-after:auto;}
#pdf-dados .pdf-header{display:flex;justify-content:space-between;align-items:flex-end;border-bottom:2.5px solid #003580;padding-bottom:8px;margin-bottom:18px;}
#pdf-dados .pdf-title{font-size:15pt;font-weight:900;color:#003580;letter-spacing:1px;}
#pdf-dados .pdf-meta{font-size:8pt;color:#666;text-align:right;line-height:1.6;}
#pdf-dados .section-title{font-size:10pt;font-weight:800;color:#003580;text-transform:uppercase;letter-spacing:1px;border-left:4px solid #003580;padding-left:8px;margin:16px 0 8px;}
#pdf-dados table{width:100%;border-collapse:collapse;font-size:8.5pt;margin-bottom:4px;}
#pdf-dados thead tr{background:#003580;color:#fff;}
#pdf-dados th{padding:6px 8px;text-align:left;font-weight:700;font-size:8pt;}
#pdf-dados td{padding:5px 8px;border-bottom:1px solid #e0e0e0;}
#pdf-dados tr:nth-child(even) td{background:#f7f9fc;}
#pdf-dados .right{text-align:right;}
#pdf-dados .bold{font-weight:700;}
#pdf-dados .green{color:#1a7a1a;font-weight:700;}
#pdf-dados .red{color:#c0392b;font-weight:700;}
#pdf-dados .kpi-box{display:inline-block;border:2px solid #003580;border-radius:8px;padding:10px 22px;text-align:center;margin:8px 12px 8px 0;}
#pdf-dados .kpi-box .kpi-n{font-size:26pt;font-weight:900;color:#1a7a1a;}
#pdf-dados .kpi-box .kpi-l{font-size:8pt;color:#666;margin-top:2px;}
#pdf-dados .pdf-footer{font-size:7pt;color:#aaa;border-top:1px solid #e0e0e0;padding-top:6px;margin-top:14px;text-align:center;}

/* ── TXT EXPORT MODAL ── */
#txt-overlay{position:fixed;inset:0;background:rgba(0,0,0,.82);z-index:600;display:none;align-items:center;justify-content:center;}
#txt-overlay.open{display:flex;}
#txt-modal{background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:28px 32px;width:100%;max-width:420px;color:#fff;}
#txt-modal h3{color:var(--accent);margin-bottom:18px;font-size:1.05rem;letter-spacing:1px;}
.txt-check-row{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.07);cursor:pointer;}
.txt-check-row input[type=checkbox]{width:16px;height:16px;accent-color:var(--accent);cursor:pointer;}
.txt-check-row label{cursor:pointer;font-size:.9rem;}
.txt-actions{display:flex;gap:10px;margin-top:20px;}
.btn-txt-gerar{flex:1;background:var(--green);color:#fff;border:none;padding:11px;border-radius:8px;font-weight:800;cursor:pointer;font-size:.9rem;}
.btn-txt-cancel{background:transparent;border:1px solid rgba(255,255,255,.2);color:var(--muted);padding:11px 18px;border-radius:8px;cursor:pointer;font-size:.9rem;}

/* ── PRINT (gráficos) ── */
@media print{
    body{background:#fff!important;color:#000!important;}
    .bi-header,.filter-bar{background:#f5f5f5!important;}
    .btn-pdf,.btn-aplicar,.dd-overlay,.click-hint{display:none!important;}
    .card{border:1px solid #ccc!important;background:#fff!important;break-inside:avoid;}
    .card-title,.bi-table th{color:#0055a4!important;}
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="bi-header">
    <div>
        <h1>⚡ BI FINANCEIRO</h1>
        <span style="font-size:.72rem;color:var(--muted)">Inteligência de Dados — clique nos gráficos para detalhar</span>
    </div>
    <div style="display:flex;gap:10px">
        <button class="btn-pdf" style="background:rgba(46,204,64,.2);color:#2ECC40;border:1px solid rgba(46,204,64,.35)" onclick="exportarBITxt()">⬇ TXT</button>
        <button class="btn-pdf" style="background:rgba(255,255,255,.15);color:#fff" onclick="exportarPDFDados()">📋 PDF DADOS</button>
        <button class="btn-pdf" onclick="exportarPDF()">📊 PDF GRÁFICOS</button>
    </div>
</div>

<!-- TXT SECTION SELECTOR MODAL -->
<div id="txt-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div id="txt-modal">
        <h3>⬇ EXPORTAR DADOS EM TXT</h3>
        <p style="font-size:.78rem;color:var(--muted);margin-bottom:14px">Selecione as seções a incluir no arquivo:</p>
        <div class="txt-check-row"><input type="checkbox" id="chk-mensais" checked><label for="chk-mensais">📅 Total de Entradas por Mês</label></div>
        <div class="txt-check-row"><input type="checkbox" id="chk-congregacoes" checked><label for="chk-congregacoes">🏆 Top 5 Congregações</label></div>
        <div class="txt-check-row"><input type="checkbox" id="chk-semanais" checked><label for="chk-semanais">📆 Análise por Semana do Mês</label></div>
        <div class="txt-check-row"><input type="checkbox" id="chk-fieis" checked><label for="chk-fieis">✝️ Dizimistas Fiéis — Total (KPI)</label></div>
        <div class="txt-check-row"><input type="checkbox" id="chk-fieis-cong" checked><label for="chk-fieis-cong">🏩 Dizimistas Fiéis por Congregação</label></div>
        <div class="txt-check-row"><input type="checkbox" id="chk-topdiz" checked><label for="chk-topdiz">🌟 Top 3 Dizimistas por Congregação</label></div>
        <div class="txt-actions">
            <button class="btn-txt-cancel" onclick="document.getElementById('txt-overlay').classList.remove('open')">Cancelar</button>
            <button class="btn-txt-gerar" onclick="gerarTxtBI()">GERAR E BAIXAR</button>
        </div>
    </div>
</div>

<!-- HIDDEN DATA-ONLY PDF SECTION -->
<div id="pdf-dados"></div>

<!-- FILTER BAR — simplified to one date range -->
<div class="filter-bar">
    <label>Período:</label>
    <input type="date" class="fi" id="f-ini" style="width:148px">
    <span style="color:var(--muted);padding:0 4px">até</span>
    <input type="date" class="fi" id="f-fim" style="width:148px">
    <button class="btn-aplicar" onclick="carregarTodos()">▶ APLICAR</button>
    <span style="font-size:.75rem;color:var(--muted);margin-left:6px">Atalhos:</span>
    <button onclick="setRange('year')" style="background:transparent;border:1px solid var(--border);color:var(--muted);padding:5px 12px;border-radius:6px;cursor:pointer;font-size:.75rem">Este Ano</button>
    <button onclick="setRange('last12')" style="background:transparent;border:1px solid var(--border);color:var(--muted);padding:5px 12px;border-radius:6px;cursor:pointer;font-size:.75rem">Últimos 12m</button>
    <button onclick="setRange('month')" style="background:transparent;border:1px solid var(--border);color:var(--muted);padding:5px 12px;border-radius:6px;cursor:pointer;font-size:.75rem">Este Mês</button>
</div>

<!-- BI GRID -->
<div class="bi-grid" id="bi-content">

    <!-- 1. TOTAL MENSAL -->
    <div class="card span2">
        <div>
            <div class="card-title">📅 Total de Entradas Mensal</div>
            <div class="card-subtitle">Evolução mensal das entradas no período — clique em uma barra para ver o detalhe</div>
        </div>
        <div class="chart-wrap"><canvas id="chart-mensais"></canvas><span class="click-hint">🔍 Clique para detalhar</span></div>
    </div>

    <!-- 2. TOP 5 CONGREGAÇÕES -->
    <div class="card">
        <div>
            <div class="card-title">🏆 Top 5 Congregações</div>
            <div class="card-subtitle">Congregações com maior volume — clique para ver os registros</div>
        </div>
        <div class="chart-wrap"><canvas id="chart-congregacoes"></canvas><span class="click-hint">🔍 Clique para detalhar</span></div>
    </div>

    <!-- 3. ANÁLISE SEMANAL -->
    <div class="card">
        <div>
            <div class="card-title">📆 Entradas por Semana do Mês</div>
            <div class="card-subtitle">Semanas de maior e menor arrecadação — clique para ver registros</div>
        </div>
        <div class="chart-wrap" style="min-height:200px"><canvas id="chart-semanais"></canvas><span class="click-hint">🔍 Clique para detalhar</span></div>
    </div>

    <!-- 4. DIZIMISTAS FIÉIS (KPI) -->
    <div class="card">
        <div>
            <div class="card-title">✝️ Dizimistas Fiéis</div>
            <div class="card-subtitle">Pessoas com ≥ 10 dízimos nos últimos 12 meses</div>
        </div>
        <div style="display:flex;align-items:center;gap:28px;flex:1">
            <div>
                <div class="kpi-value" id="kpi-fieis">—</div>
                <div class="kpi-label">dizimistas ativos</div>
            </div>
            <div class="chart-wrap" style="max-width:170px"><canvas id="chart-dizimistas"></canvas></div>
        </div>
    </div>

    <!-- 5. TOP DIZIMISTAS POR CONGREGAÇÃO -->
    <div class="card span2">
        <div>
            <div class="card-title">🌟 Top 3 Dizimistas por Congregação</div>
            <div class="card-subtitle">Maiores contribuintes por congregação — média por lançamento</div>
        </div>
        <div id="res-top-dizimistas" class="spinner">Carregando…</div>
    </div>

</div>

<!-- DRILL-DOWN MODAL -->
<div class="dd-overlay" id="dd-overlay" onclick="fecharDrill(event)">
    <div class="dd-modal">
        <button class="dd-close" onclick="fecharDrillBtn()">×</button>
        <h3 id="dd-titulo">Detalhamento</h3>
        <div class="dd-summary" id="dd-summary"></div>
        <div class="dd-body" id="dd-body"></div>
    </div>
</div>

<script>
// ──────────── UTILS ────────────
const api = (p) => fetch('api_bi?' + new URLSearchParams(p)).then(r => r.json());
const apiRel = (p) => fetch('api_relatorios?' + new URLSearchParams(p)).then(r => r.json());
const fmt = (v) => parseFloat(v||0).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
const COLORS = ['#7FDBFF','#2ECC40','#FFDC00','#FF4136','#B10DC9','#FF851B','#01FF70'];
let charts = {};
let dadosMensais = [], dadosCongreg = [], dadosSemanais = [], dadosFieis = 0, dadosTopDizimistas = {}, dadosFieisPorCong = [];

function destroyChart(id){if(charts[id]){charts[id].destroy();delete charts[id];}}
function mes(ym){const[y,m]=ym.split('-');return new Date(y,m-1).toLocaleString('pt-BR',{month:'short',year:'2-digit'}).toUpperCase();}
function semanaRange(semana, ini, fim) {
    const map = {'1ª Semana':[1,7],'2ª Semana':[8,14],'3ª Semana':[15,21],'4ª Semana':[22,31]};
    return map[semana] || [1,31];
}

// ──────────── DATE INIT / RANGE ────────────
function initDatas(){
    const ano = new Date().getFullYear();
    document.getElementById('f-ini').value = `${ano}-01-01`;
    document.getElementById('f-fim').value = `${ano}-12-31`;
}
function setRange(type){
    const now = new Date();
    let ini, fim;
    if(type === 'year'){
        ini = `${now.getFullYear()}-01-01`;
        fim = `${now.getFullYear()}-12-31`;
    } else if(type === 'last12'){
        const d = new Date(); d.setFullYear(d.getFullYear()-1);
        ini = d.toISOString().slice(0,10);
        fim = now.toISOString().slice(0,10);
    } else if(type === 'month'){
        ini = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-01`;
        const last = new Date(now.getFullYear(), now.getMonth()+1, 0);
        fim = last.toISOString().slice(0,10);
    }
    document.getElementById('f-ini').value = ini;
    document.getElementById('f-fim').value = fim;
    carregarTodos();
}
function getRange(){ return {ini:document.getElementById('f-ini').value, fim:document.getElementById('f-fim').value};}

// ──────────── DRILL-DOWN ENGINE ────────────
function abrirDrill(titulo, linhas, resumo=''){
    document.getElementById('dd-titulo').textContent = titulo;
    document.getElementById('dd-summary').innerHTML = resumo;
    if(!linhas || !linhas.length){
        document.getElementById('dd-body').innerHTML = '<p style="color:var(--muted);padding:20px;text-align:center">Nenhum registro encontrado.</p>';
    } else {
        let h = `<table class="bi-table"><thead><tr><th>Data</th><th>Nome</th><th>Tipo</th><th style="text-align:right">Valor</th></tr></thead><tbody>`;
        linhas.forEach(d => {
            const v = parseFloat(d.valor||0);
            h += `<tr><td>${new Date((d.data_movimento||d.Data||d.data)+'T12:00:00').toLocaleDateString('pt-BR')}</td>
                  <td>${d.principal||d.nome||d.recebedor||'—'}</td>
                  <td><span class="badge">${d.categoria||d.tipo||d.tipo_saida||'—'}</span></td>
                  <td class="${d.origem==='Saída'?'valor-saida':'valor-entrada'}" style="text-align:right">${fmt(v)}</td></tr>`;
        });
        h += '</tbody></table>';
        document.getElementById('dd-body').innerHTML = h;
    }
    document.getElementById('dd-overlay').classList.add('open');
}
function fecharDrill(e){ if(e.target===document.getElementById('dd-overlay')) document.getElementById('dd-overlay').classList.remove('open'); }
function fecharDrillBtn(){ document.getElementById('dd-overlay').classList.remove('open'); }

// ──────────── 1. MENSAIS (Bar) ────────────
async function carregarMensais(ini, fim){
    const {dados} = await api({tipo:'mensais', inicio:ini, fim:fim});
    dadosMensais = dados;
    const labels = dados.map(d => mes(d.mes));
    const values = dados.map(d => parseFloat(d.total));
    destroyChart('mensais');
    charts['mensais'] = new Chart(document.getElementById('chart-mensais'), {
        type:'bar',
        data:{labels, datasets:[{
            label:'Entradas (R$)', data:values,
            backgroundColor: values.map((_,i)=>`hsla(${200+i*10},80%,60%,.75)`),
            borderRadius:8, borderSkipped:false
        }]},
        options:{
            responsive:true, maintainAspectRatio:false,
            onClick:(e, els) => {
                if(!els.length) return;
                const idx = els[0].index;
                const d = dadosMensais[idx];
                const [y,m] = d.mes.split('-');
                const iniM = `${d.mes}-01`;
                const fimM = `${d.mes}-${new Date(y,m,0).getDate()}`;
                drillMensal(mes(d.mes), iniM, fimM, d.total);
            },
            plugins:{
                legend:{display:false},
                tooltip:{callbacks:{label:(ctx)=>` ${fmt(ctx.raw)}`}}
            },
            scales:{
                x:{ticks:{color:'rgba(255,255,255,.6)'},grid:{color:'rgba(255,255,255,.05)'}},
                y:{ticks:{color:'rgba(255,255,255,.6)',callback:v=>fmt(v)},grid:{color:'rgba(255,255,255,.06)'}}
            }
        }
    });
}
async function drillMensal(label, ini, fim, total){
    const {dados} = await apiRel({tipo_relatorio:'pesquisa', inicio:ini, fim:fim, congregacao:'todas', nome:''});
    const resumo = `<span>Período: <strong>${ini} → ${fim}</strong></span><span>Total: <strong class="valor-entrada">${fmt(total)}</strong></span><span>Registros: <strong>${dados.length}</strong></span>`;
    abrirDrill(`📅 Detalhamento — ${label}`, dados, resumo);
}

// ──────────── 2. TOP CONGREGAÇÕES (Horizontal Bar) ────────────
async function carregarTopCongregacoes(ini, fim){
    const {dados} = await api({tipo:'top_congregacoes', inicio:ini, fim:fim});
    dadosCongreg = dados;
    const labels = dados.map(d=>d.congregacao);
    const vals   = dados.map(d=>parseFloat(d.total));
    destroyChart('congregacoes');
    charts['congregacoes'] = new Chart(document.getElementById('chart-congregacoes'), {
        type:'bar',
        data:{labels, datasets:[{
            label:'Total', data:vals,
            backgroundColor: COLORS.slice(0,labels.length).map(c=>c+'BB'),
            borderRadius:8, borderSkipped:false
        }]},
        options:{
            indexAxis:'y', responsive:true, maintainAspectRatio:false,
            onClick:(e,els) => {
                if(!els.length) return;
                const d = dadosCongreg[els[0].index];
                drillCongregacao(d.congregacao, ini, fim, d.total, d.registros);
            },
            plugins:{
                legend:{display:false},
                tooltip:{callbacks:{label:(ctx)=>` ${fmt(ctx.raw)} | ${dadosCongreg[ctx.dataIndex].registros} registros`}}
            },
            scales:{
                x:{ticks:{color:'rgba(255,255,255,.6)',callback:v=>fmt(v)},grid:{color:'rgba(255,255,255,.05)'}},
                y:{ticks:{color:'#fff'},grid:{color:'rgba(255,255,255,.04)'}}
            }
        }
    });
}
async function drillCongregacao(cong, ini, fim, total, qtd){
    const {dados} = await apiRel({tipo_relatorio:'pesquisa', inicio:ini, fim:fim, congregacao:cong, nome:'', filtro_tipo:'entradas'});
    const resumo = `<span>Congregação: <strong>${cong}</strong></span><span>Total: <strong class="valor-entrada">${fmt(total)}</strong></span><span>Registros: <strong>${qtd}</strong></span>`;
    abrirDrill(`🏆 Detalhamento — ${cong}`, dados, resumo);
}

// ──────────── 3. SEMANAIS (Radar) ────────────
async function carregarSemanais(ini, fim){
    const {dados} = await api({tipo:'semanais', inicio:ini, fim:fim});
    dadosSemanais = dados;
    const labels = dados.map(d=>d.semana);
    const vals   = dados.map(d=>parseFloat(d.total));
    const maxIdx = vals.indexOf(Math.max(...vals));
    const minIdx = vals.indexOf(Math.min(...vals));
    const bgs = vals.map((_,i)=>i===maxIdx?'rgba(46,204,64,.9)':i===minIdx?'rgba(255,65,54,.8)':'rgba(127,219,255,.6)');
    destroyChart('semanais');
    charts['semanais'] = new Chart(document.getElementById('chart-semanais'),{
        type:'radar',
        data:{labels, datasets:[{
            label:'Entradas', data:vals,
            backgroundColor:'rgba(127,219,255,.12)', borderColor:'rgba(127,219,255,.85)',
            pointBackgroundColor:bgs, pointRadius:9, borderWidth:2
        }]},
        options:{
            responsive:true, maintainAspectRatio:false,
            onClick:(e,els) => {
                if(!els.length) return;
                const d = dadosSemanais[els[0].index];
                drillSemanal(d.semana, ini, fim, d.total, d.registros);
            },
            scales:{r:{
                ticks:{color:'rgba(255,255,255,.5)',callback:v=>fmt(v),font:{size:9}},
                grid:{color:'rgba(255,255,255,.08)'},
                pointLabels:{color:'#fff',font:{size:13,weight:'bold'}}
            }},
            plugins:{
                legend:{display:false},
                tooltip:{callbacks:{label:(ctx)=>` ${fmt(ctx.raw)} | ${dadosSemanais[ctx.dataIndex].registros} registros`}}
            }
        }
    });
}
async function drillSemanal(semana, ini, fim, total, qtd){
    // Figure out day range for the week
    const dayMap = {'1ª Semana':[1,7],'2ª Semana':[8,14],'3ª Semana':[15,21],'4ª Semana':[22,31]};
    const [d1,d2] = dayMap[semana] || [1,31];
    // Fetch all records in the period then filter by day
    const {dados} = await apiRel({tipo_relatorio:'pesquisa', inicio:ini, fim:fim, congregacao:'todas', nome:'', filtro_tipo:'entradas'});
    const filtrados = dados.filter(r => {
        const day = new Date((r.data_movimento||'')+'T12:00:00').getDate();
        return day >= d1 && day <= d2;
    });
    const resumo = `<span>Semana: <strong>${semana}</strong></span><span>Total: <strong class="valor-entrada">${fmt(total)}</strong></span><span>Registros: <strong>${qtd}</strong></span>`;
    abrirDrill(`📆 Detalhamento — ${semana}`, filtrados, resumo);
}

// ──────────── 4. DIZIMISTAS FIÉIS ────────────
async function carregarDizimistas(){
    const [{dados: dadosFieisRaw}, {dados: dadosCongRaw}] = await Promise.all([
        api({tipo:'dizimistas_fieis'}),
        api({tipo:'dizimistas_fieis_cong'})
    ]);
    const total = parseInt(dadosFieisRaw.total||0);
    dadosFieis = total;
    dadosFieisPorCong = dadosCongRaw || [];
    document.getElementById('kpi-fieis').textContent = total;
    destroyChart('dizimistas');
    charts['dizimistas'] = new Chart(document.getElementById('chart-dizimistas'),{
        type:'doughnut',
        data:{labels:['Fiéis','Ref.'], datasets:[{
            data:[total, Math.max(Math.round(total*.35),1)],
            backgroundColor:['rgba(46,204,64,.85)','rgba(255,255,255,.08)'], borderWidth:0
        }]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'72%',
            plugins:{legend:{display:false},tooltip:{callbacks:{label:(ctx)=>` ${ctx.raw} pessoas`}}}}
    });
}

// ──────────── 5. TOP DIZIMISTAS POR CONGREGAÇÃO ────────────
async function carregarTopDizimistas(ini, fim){
    const {dados} = await api({tipo:'top_dizimistas', inicio:ini, fim:fim});
    dadosTopDizimistas = dados || {};
    const div = document.getElementById('res-top-dizimistas');
    if(!dados||Object.keys(dados).length===0){
        div.innerHTML='<p style="color:var(--muted);text-align:center;padding:20px">Nenhum dado de dízimo encontrado no período.</p>';
        return;
    }
    const medals=['badge-gold','badge-silver','badge-bronze'], icons=['🥇','🥈','🥉'];
    let html='<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:14px">';
    Object.entries(dados).forEach(([cong,lista])=>{
        html+=`<div style="background:rgba(0,0,0,.22);border-radius:12px;padding:14px">
            <div style="font-weight:700;color:var(--accent);margin-bottom:10px;font-size:.82rem;text-transform:uppercase">${cong}</div>
            <table class="bi-table" style="font-size:.8rem"><thead><tr><th>#</th><th>Nome</th><th>Total</th><th>Média</th></tr></thead><tbody>`;
        lista.forEach((p,i)=>{
            html+=`<tr style="cursor:pointer" onclick="drillDizimista('${p.nome.replace(/'/g,"\\'")}','${ini}','${fim}')">
                <td><span class="badge ${medals[i]}">${icons[i]||i+1}</span></td>
                <td>${p.nome}</td>
                <td class="valor-entrada">${fmt(p.total)}</td>
                <td style="color:var(--yellow)">${fmt(p.media)}</td>
            </tr>`;
        });
        html+='</tbody></table></div>';
    });
    html+='</div>';
    div.innerHTML=html;
}
async function drillDizimista(nome, ini, fim){
    const {dados} = await apiRel({tipo_relatorio:'pesquisa', inicio:ini, fim:fim, nome:nome, congregacao:'todas', filtro_tipo:'entradas'});
    const total = dados.reduce((s,d)=>s+parseFloat(d.valor||0),0);
    const resumo=`<span>Dizimista: <strong>${nome}</strong></span><span>Total: <strong class="valor-entrada">${fmt(total)}</strong></span><span>Registros: <strong>${dados.length}</strong></span>`;
    abrirDrill(`🌟 Histórico — ${nome}`, dados, resumo);
}

// ──────────── LOAD ALL ────────────
async function carregarTodos(){
    const {ini, fim} = getRange();
    document.getElementById('res-top-dizimistas').innerHTML='<div class="spinner">Atualizando…</div>';
    document.getElementById('kpi-fieis').textContent='…';
    await Promise.all([
        carregarMensais(ini, fim),
        carregarTopCongregacoes(ini, fim),
        carregarSemanais(ini, fim),
        carregarDizimistas(),
        carregarTopDizimistas(ini, fim),
    ]);
}

// ──────────── TXT EXPORT ────────────
function exportarBITxt(){
    document.getElementById('txt-overlay').classList.add('open');
}

function gerarTxtBI(){
    const {ini, fim} = getRange();
    const periodo = `${new Date(ini+'T12:00').toLocaleDateString('pt-BR')} a ${new Date(fim+'T12:00').toLocaleDateString('pt-BR')}`;
    const sep  = '='.repeat(68);
    const sep2 = '-'.repeat(68);
    const lines = [];
    const chk = id => document.getElementById(id).checked;

    lines.push('RELATÓRIO BI FINANCEIRO');
    lines.push(sep);
    lines.push(`Período  : ${periodo}`);
    lines.push(`Gerado em: ${new Date().toLocaleString('pt-BR')}`);
    lines.push(sep);

    // 1. Mensais
    if(chk('chk-mensais') && dadosMensais.length){
        lines.push('');
        lines.push('TOTAL DE ENTRADAS POR MÊS');
        lines.push(sep2);
        lines.push(`${'Mês'.padEnd(16)}${'Total (R$)'.padStart(24)}`);
        lines.push(sep2);
        let tot = 0;
        dadosMensais.forEach(d => {
            const v = parseFloat(d.total);
            tot += v;
            lines.push(`${mes(d.mes).padEnd(16)}${fmt(v).padStart(24)}`);
        });
        lines.push(sep2);
        lines.push(`${'TOTAL GERAL'.padEnd(16)}${fmt(tot).padStart(24)}`);
        lines.push(sep2);
    }

    // 2. Top congregações
    if(chk('chk-congregacoes') && dadosCongreg.length){
        lines.push('');
        lines.push('TOP 5 CONGREGAÇÕES POR ENTRADAS');
        lines.push(sep2);
        lines.push(`${'#'.padEnd(4)}${'Congregação'.padEnd(36)}${'Registros'.padStart(10)}${'Total (R$)'.padStart(18)}`);
        lines.push(sep2);
        const medals = ['1º','2º','3º','4º','5º'];
        dadosCongreg.forEach((d,i) => {
            lines.push(`${medals[i].padEnd(4)}${d.congregacao.padEnd(36)}${String(d.registros).padStart(10)}${fmt(d.total).padStart(18)}`);
        });
        lines.push(sep2);
    }

    // 3. Semanais
    if(chk('chk-semanais') && dadosSemanais.length){
        const vals = dadosSemanais.map(d=>parseFloat(d.total));
        const maxV = Math.max(...vals), minV = Math.min(...vals);
        lines.push('');
        lines.push('ANÁLISE POR SEMANA DO MÊS');
        lines.push(sep2);
        lines.push(`${'Semana'.padEnd(18)}${'Registros'.padStart(10)}${'Total (R$)'.padStart(22)}${'Obs'.padStart(10)}`);
        lines.push(sep2);
        dadosSemanais.forEach(d => {
            const v = parseFloat(d.total);
            const obs = v===maxV ? '▲ Maior' : v===minV ? '▼ Menor' : '';
            lines.push(`${d.semana.padEnd(18)}${String(d.registros).padStart(10)}${fmt(v).padStart(22)}${obs.padStart(10)}`);
        });
        lines.push(sep2);
    }

    // 4a. Dizimistas fiéis KPI
    if(chk('chk-fieis')){
        lines.push('');
        lines.push('DIZIMISTAS FIÉIS (≥10 dízimos nos últimos 12 meses)');
        lines.push(sep2);
        lines.push(`Total de dizimistas ativos: ${dadosFieis} pessoa${dadosFieis!==1?'s':''}`);
        lines.push(sep2);
    }

    // 4b. Dizimistas fiéis por congregação
    if(chk('chk-fieis-cong') && dadosFieisPorCong.length){
        lines.push('');
        lines.push('DIZIMISTAS FIÉIS POR CONGREGAÇÃO (≥10 dízimos nos últimos 12 meses)');
        lines.push(sep2);
        lines.push(`${'Congregação'.padEnd(36)}${'Dizimistas Fiéis'.padStart(18)}`);
        lines.push(sep2);
        const totalFieis = dadosFieisPorCong.reduce((s,d)=>s+parseInt(d.total),0);
        dadosFieisPorCong.forEach(d => {
            lines.push(`${d.congregacao.padEnd(36)}${String(d.total).padStart(18)}`);
        });
        lines.push(sep2);
        lines.push(`${'TOTAL'.padEnd(36)}${String(totalFieis).padStart(18)}`);
        lines.push(sep2);
    }

    // 5. Top dizimistas por congregação (com nome e valor do período)
    if(chk('chk-topdiz') && Object.keys(dadosTopDizimistas).length){
        lines.push('');
        lines.push('TOP 3 DIZIMISTAS POR CONGREGAÇÃO');
        lines.push(`Valores referentes ao período: ${periodo}`);
        lines.push(sep2);
        const medals = ['🥇 1º','🥈 2º','🥉 3º'];
        Object.entries(dadosTopDizimistas).forEach(([cong, lista]) => {
            lines.push('');
            lines.push(`  ${cong.toUpperCase()}`);
            lines.push('  ' + '-'.repeat(62));
            lines.push(`  ${'#'.padEnd(8)}${'Nome'.padEnd(30)}${'Total (R$)'.padStart(18)}${'Média (R$)'.padStart(14)}`);
            lines.push('  ' + '-'.repeat(62));
            lista.forEach((p, i) => {
                lines.push(`  ${(medals[i]||String(i+1)+'º').padEnd(8)}${p.nome.padEnd(30)}${fmt(p.total).padStart(18)}${fmt(p.media).padStart(14)}`);
            });
        });
        lines.push('');
        lines.push(sep2);
    }

    lines.push('');
    lines.push('Fim do relatório.');

    document.getElementById('txt-overlay').classList.remove('open');
    const blob = new Blob([lines.join('\n')], {type:'text/plain;charset=utf-8'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `BI_Financeiro_${ini}_${fim}.txt`;
    a.click();
    URL.revokeObjectURL(a.href);
}

// ──────────── PDF GRÁFICOS (improved margins) ────────────
function exportarPDF(){
    html2pdf().set({
        margin:[15,14,14,14],
        filename:'BI_Financeiro_Graficos.pdf',
        image:{type:'jpeg',quality:.96},
        html2canvas:{scale:2, backgroundColor:'#001f3f', useCORS:true, logging:false},
        jsPDF:{unit:'mm', format:'a4', orientation:'landscape'}
    }).from(document.getElementById('bi-content')).save();
}

// ──────────── PDF DADOS (data-only, no charts) ────────────
async function exportarPDFDados(){
    const {ini, fim} = getRange();
    const periodoLabel = `${new Date(ini+'T12:00').toLocaleDateString('pt-BR')} a ${new Date(fim+'T12:00').toLocaleDateString('pt-BR')}`;
    const geradoEm = new Date().toLocaleString('pt-BR');
    const footer = `<div class="pdf-footer">Relatório BI Financeiro — Período: ${periodoLabel} — Gerado em: ${geradoEm}</div>`;

    // Fetch all data in parallel
    const [rMens, rCong, rSem, rFieis, rTopDiz] = await Promise.all([
        api({tipo:'mensais',          inicio:ini, fim:fim}),
        api({tipo:'top_congregacoes', inicio:ini, fim:fim}),
        api({tipo:'semanais',         inicio:ini, fim:fim}),
        api({tipo:'dizimistas_fieis'}),
        api({tipo:'top_dizimistas',   inicio:ini, fim:fim}),
    ]);

    // ── PAGE 1: Mensais + Congregações + Semanais ──
    let p1 = `<div class="pdf-page">
        <div class="pdf-header">
            <div class="pdf-title">⚡ BI FINANCEIRO — RELATÓRIO DE DADOS</div>
            <div class="pdf-meta">Período: <strong>${periodoLabel}</strong><br>Gerado em: ${geradoEm}</div>
        </div>

        <div class="section-title">📅 Total de Entradas por Mês</div>
        <table><thead><tr><th>Mês</th><th class="right">Total (R$)</th></tr></thead><tbody>`;
    let totalGeral = 0;
    (rMens.dados||[]).forEach(d => {
        const v = parseFloat(d.total||0);
        totalGeral += v;
        p1 += `<tr><td>${mes(d.mes)}</td><td class="right green">${fmt(v)}</td></tr>`;
    });
    p1 += `<tr><td class="bold">TOTAL</td><td class="right bold green">${fmt(totalGeral)}</td></tr>
        </tbody></table>

        <div class="section-title">🏆 Top 5 Congregações por Entradas</div>
        <table><thead><tr><th>Congregação</th><th class="right">Registros</th><th class="right">Total (R$)</th></tr></thead><tbody>`;
    (rCong.dados||[]).forEach(d => {
        p1 += `<tr><td>${d.congregacao}</td><td class="right">${d.registros}</td><td class="right green">${fmt(d.total)}</td></tr>`;
    });
    p1 += `</tbody></table>

        <div class="section-title">📆 Entradas por Semana do Mês</div>
        <table><thead><tr><th>Semana</th><th class="right">Registros</th><th class="right">Total (R$)</th></tr></thead><tbody>`;
    const semVals = (rSem.dados||[]).map(d=>parseFloat(d.total||0));
    const semMax = Math.max(...semVals), semMin = Math.min(...semVals);
    (rSem.dados||[]).forEach(d => {
        const v = parseFloat(d.total||0);
        const cls = v===semMax ? 'green' : v===semMin ? 'red' : '';
        p1 += `<tr><td${cls?` class="${cls}"`:''}>${d.semana}${v===semMax?' ▲ Maior':v===semMin?' ▼ Menor':''}</td><td class="right">${d.registros}</td><td class="right${cls?' '+cls:''}">${fmt(v)}</td></tr>`;
    });
    p1 += `</tbody></table>${footer}</div>`;

    // ── PAGE 2: Dizimistas ──
    const fieis = parseInt(rFieis.dados?.total||0);
    let p2 = `<div class="pdf-page">
        <div class="pdf-header">
            <div class="pdf-title">✝️ DIZIMISTAS — ANÁLISE DE FIDELIDADE</div>
            <div class="pdf-meta">Período: <strong>${periodoLabel}</strong><br>Gerado em: ${geradoEm}</div>
        </div>

        <div class="section-title">✝️ Dizimistas Fiéis (≥ 10 dízimos nos últimos 12 meses)</div>
        <div>
            <div class="kpi-box"><div class="kpi-n">${fieis}</div><div class="kpi-l">Dizimistas Ativos</div></div>
        </div>

        <div class="section-title">🌟 Top 3 Dizimistas por Congregação</div>`;
    const topDiz = rTopDiz.dados || {};
    if(Object.keys(topDiz).length === 0){
        p2 += `<p style="color:#999;font-size:9pt;padding:8px">Nenhum dado encontrado no período selecionado.</p>`;
    } else {
        Object.entries(topDiz).forEach(([cong, lista]) => {
            p2 += `<div style="margin-bottom:4px;padding-left:4px;font-size:9pt;font-weight:700;color:#003580">${cong}</div>
            <table style="margin-bottom:12px"><thead><tr><th>#</th><th>Nome</th><th class="right">Qtd. Dízimos</th><th class="right">Total (R$)</th><th class="right">Média (R$)</th></tr></thead><tbody>`;
            const medals = ['🥇','🥈','🥉'];
            lista.forEach((p,i) => {
                p2 += `<tr><td>${medals[i]||i+1}</td><td>${p.nome}</td><td class="right">${p.contagem}</td><td class="right green">${fmt(p.total)}</td><td class="right">${fmt(p.media)}</td></tr>`;
            });
            p2 += `</tbody></table>`;
        });
    }
    p2 += `${footer}</div>`;

    // Inject and render
    const el = document.getElementById('pdf-dados');
    el.innerHTML = p1 + p2;
    el.style.display = 'block';

    await new Promise(r => setTimeout(r, 120)); // let DOM settle

    html2pdf().set({
        margin:[14,13,13,13],
        filename:'BI_Financeiro_Dados.pdf',
        image:{type:'jpeg',quality:.98},
        html2canvas:{scale:2, backgroundColor:'#ffffff', useCORS:true, logging:false},
        jsPDF:{unit:'mm', format:'a4', orientation:'landscape'}
    }).from(el).save().then(() => { el.style.display='none'; el.innerHTML=''; });
}


// ──────────── INIT ────────────
document.addEventListener('DOMContentLoaded', ()=>{ initDatas(); carregarTodos(); });
</script>
</body>
</html>
