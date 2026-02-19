<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Igreja - Membros</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-sombra: #000a14;
            --branco: #ffffff;
            --verde-sucesso: #2ecc71;
            --azul-claro: #3498db;
            --cinza-texto: #666;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: var(--azul-fundo); color: var(--branco); min-height: 100vh; display: flex; flex-direction: column; }

        .header { padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo-pequena { height: 40px; }
        .btn-voltar { color: var(--branco); text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 600; opacity: 0.8; transition: 0.2s; }
        .btn-voltar:hover { opacity: 1; }

        .container { flex: 1; padding: 30px; max-width: 1400px; margin: 0 auto; width: 100%; }

        /* --- SUB MENU --- */
        .sub-menu { display: flex; gap: 15px; margin-bottom: 30px; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .menu-item { 
            padding: 12px 25px; 
            border-radius: 15px; 
            cursor: pointer; 
            font-weight: 700; 
            color: rgba(255,255,255,0.5); 
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .menu-item.active { background: var(--verde-sucesso); color: white; box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3); }
        .menu-item:hover:not(.active) { color: white; background: rgba(255,255,255,0.05); }

        .section-content { display: none; animation: fadeIn 0.4s ease; }
        .section-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- CARD E TABELA --- */
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border-radius: 24px; padding: 25px; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .actions-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 15px; }
        
        .search-box { flex: 1; position: relative; }
        .search-box input { width: 100%; padding: 14px 20px 14px 50px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); color: var(--branco); outline: none; }
        .search-box svg { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; fill: rgba(255,255,255,0.4); }

        .filter-group { display: flex; gap: 10px; flex: 2; }
        .filter-select { flex: 1; padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: #fff; cursor: pointer; }

        .btn-novo { background: var(--verde-sucesso); color: white; border: none; padding: 14px 25px; border-radius: 15px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px; text-transform: uppercase; font-size: 0.85rem; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 18px; border-bottom: 2px solid rgba(255,255,255,0.1); font-weight: 700; color: rgba(255,255,255,0.6); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
        td { padding: 18px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.95rem; }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .status-ativo { background: rgba(46, 204, 113, 0.15); color: #2ecc71; }
        .status-inativo { background: rgba(231, 76, 60, 0.15); color: #e74c3c; }

        .btn-acao { background: transparent; border: 1px solid rgba(255,255,255,0.1); color: var(--branco); padding: 8px; border-radius: 10px; cursor: pointer; transition: 0.2s; }
        .btn-acao:hover { background: rgba(255,255,255,0.1); border-color: #fff; }
        .btn-acao svg { width: 18px; height: 18px; fill: currentColor; }

        /* --- FORM AUXILIAR --- */
        .aux-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; }
        .form-aux-add { display: flex; gap: 10px; margin-bottom: 20px; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 15px; }
        .form-aux-add input { flex: 1; padding: 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; }
        .lista-aux { list-style: none; max-height: 400px; overflow-y: auto; }
        .item-aux { display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }

        /* --- MODAIS --- */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 2000; justify-content: center; align-items: center; padding: 20px; }
        .modal-content { background: var(--azul-fundo); border: 1px solid rgba(255,255,255,0.15); padding: 35px; border-radius: 24px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; }
        .form-tabs { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
        .tab-btn { background: transparent; border: none; color: rgba(255,255,255,0.5); padding: 10px 20px; cursor: pointer; font-weight: 600; font-size: 0.9rem; border-radius: 10px; }
        .tab-btn.active { color: var(--branco); background: rgba(255,255,255,0.1); }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.3s; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .full-width { grid-column: span 2; }
        .form-group label { font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input, .form-group select, .form-group textarea { padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); color: #fff; outline: none; }
        .modal-footer { margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .btn-cancelar { background: transparent; border: none; color: #999; font-weight: 600; cursor: pointer; }
        .btn-salvar-m { background: var(--verde-sucesso); color: #fff; padding: 12px 30px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* FLASH MESSAGE */
        #flash-membro {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            background-color: var(--azul-claro);
            color: white;
            padding: 25px 50px;
            border-radius: 15px;
            font-size: 1.8rem;
            font-weight: 900;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 3000;
            border: 3px solid rgba(255,255,255,0.3);
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div id="flash-membro">SALVO!</div>

    <header class="header">
        <a href="dashboard" class="btn-voltar">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            VOLTAR AO PAINEL
        </a>
        <img src="img/logo.png" alt="Logo" class="logo-pequena">
        <button class="btn-voltar" style="color: #e67e22; border: 1px solid rgba(230, 126, 34, 0.3); padding: 5px 15px; border-radius: 10px; background: rgba(230, 126, 34, 0.05);" onclick="irParaAba('conflitos-panel')">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            CONFLITOS (<?= count($conflitos) ?>)
        </button>
    </header>

    <div class="container">
        
        <!-- NAVEGAÇÃO INTERNA -->
        <div class="sub-menu">
            <div class="menu-item active" data-target="membros-list">Cadastramento de Membros</div>
            <div class="menu-item" data-target="congregacoes-list">Congregações</div>
            <div class="menu-item" data-target="funcoes-list">Funções Ministeriais</div>
            <div class="menu-item" data-target="cargos-list">Cargos Congregacionais</div>
        </div>

        <!-- SEÇÃO: LISTAGEM DE MEMBROS -->
        <div id="membros-list" class="section-content active">
            <div class="card">
                <div class="actions-bar">
                    <div class="search-box">
                        <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                        <input type="text" id="searchInput" placeholder="Pesquisar por nome ou CPF...">
                    </div>
                    
                    <div class="filter-group">
                        <select id="filterCongregacao" class="filter-select">
                            <option value="">Todas Congregações</option>
                            <?php foreach ($congregacoes as $c): ?>
                                <option value="<?= htmlspecialchars($c['nome']) ?>"><?= htmlspecialchars($c['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filterFuncao" class="filter-select">
                            <option value="">Todas Funções</option>
                            <?php foreach ($funcoes as $f): ?>
                                <option value="<?= htmlspecialchars($f['nome']) ?>"><?= htmlspecialchars($f['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filterCargo" class="filter-select">
                            <option value="">Todos Cargos</option>
                            <?php foreach ($cargos_cong as $cc): ?>
                                <option value="<?= htmlspecialchars($cc['nome']) ?>"><?= htmlspecialchars($cc['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn-novo">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                        Novo Membro
                    </button>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Congregação</th>
                                <th>Função / Cargo</th>
                                <th style="width: 140px;">WhatsApp</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($membros)): ?>
                                <tr><td colspan="6" style="text-align:center; padding:50px; opacity:0.5;">Nenhum membro encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach ($membros as $m): ?>
                                    <tr data-congregacao="<?= htmlspecialchars($m['congregacao']) ?>" data-funcao="<?= htmlspecialchars($m['funcao_eclesiastica'] ?? '') ?>" data-cargo="<?= htmlspecialchars($m['cargo_congregacional'] ?? '') ?>">
                                        <td><?= htmlspecialchars($m['nome']) ?></td>
                                        <td><?= htmlspecialchars($m['congregacao']) ?></td>
                                        <td>
                                            <div style="color: var(--verde-sucesso); font-size: 0.8rem; font-weight: 700;"><?= htmlspecialchars($m['funcao_eclesiastica'] ?? '-') ?></div>
                                            <div style="opacity: 0.6; font-size: 0.75rem;"><?= htmlspecialchars($m['cargo_congregacional'] ?? '-') ?></div>
                                        </td>
                                        <td style="font-size: 0.8rem; opacity: 0.9;">
                                            <?= !empty($m['telefone']) ? htmlspecialchars($m['telefone']) : '<span style="opacity:0.3">-</span>' ?>
                                        </td>
                                        <td><span class="status-badge status-<?= strtolower($m['status']) === 'ativo' ? 'ativo' : 'inativo' ?>"><?= htmlspecialchars($m['status']) ?></span></td>
                                        <td>
                                            <button class="btn-acao btn-editar" title="Editar" data-info='<?= json_encode($m) ?>'>
                                                <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/></svg>
                                            </button>
                                            <button class="btn-acao btn-excluir" title="Excluir" data-id="<?= $m['id'] ?>">
                                                <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5h-5l-1 1H5v2h14V4z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SEÇÃO: CONFLITOS DE IMPORTAÇÃO -->
        <div id="conflitos-panel" class="section-content">
            <div class="card aux-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3>Conflitos Detectados na Importação</h3>
                    <div style="font-size:0.8rem; opacity:0.6;">Dizimistas com nomes compostos ou em múltiplas congregações</div>
                </div>
                
                <div class="table-container" style="max-height: 600px; overflow-y: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome Original</th>
                                <th>Tipo</th>
                                <th>Congregações</th>
                                <th style="text-align:right;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($conflitos)): ?>
                                <tr><td colspan="4" style="text-align:center; padding:30px; opacity:0.5;">Nenhum conflito pendente.</td></tr>
                            <?php else: ?>
                                <?php foreach($conflitos as $c): ?>
                                <tr>
                                    <td style="font-weight:700; color:#e67e22;"><?= htmlspecialchars($c['nome_original']) ?></td>
                                    <td>
                                        <span class="status-badge" style="background:rgba(230, 126, 34, 0.1); color:#e67e22;">
                                            <?= $c['tipo_conflito'] === 'composto' ? 'Composto (E/&)' : 'Duplicidade' ?>
                                        </span>
                                    </td>
                                    <td style="font-size:0.85rem; opacity:0.7;"><?= htmlspecialchars($c['congregacoes_encontradas']) ?></td>
                                    <td style="text-align:right;">
                                        <button onclick="abrirResolverConflito(<?= htmlspecialchars(json_encode($c)) ?>)" class="btn-novo" style="padding:8px 15px; font-size:0.7rem; background:#e67e22; color:white;">RESOLVER</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SEÇÃO: CONGREGAÇÕES -->
        <div id="congregacoes-list" class="section-content">
            <div class="card aux-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3>Gerenciar Congregações</h3>
                    <form action="index.php?url=ajustes_salvar" method="POST" style="display:flex; gap:10px;">
                        <input type="hidden" name="tabela" value="congregacoes">
                        <input type="text" name="nome" placeholder="Nova congregação..." required style="padding:10px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                        <button type="submit" class="btn-novo" style="padding:10px 20px;">ADICIONAR</button>
                    </form>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome da Congregação</th>
                                <th>Qtd. Membros</th>
                                <th style="text-align:right;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($congregacoes as $item): 
                                $qtd = 0;
                                foreach($membros as $mb) { if($mb['congregacao'] == $item['nome']) $qtd++; }
                            ?>
                            <tr>
                                <td style="font-weight:700; color:var(--verde-sucesso); cursor:pointer;" onclick="filtrarPorCongregacao('<?= $item['nome'] ?>')">
                                    <?= htmlspecialchars($item['nome']) ?>
                                </td>
                                <td><span class="status-badge" style="background:rgba(255,255,255,0.05)"><?= $qtd ?> membros</span></td>
                                <td style="text-align:right;">
                                    <button onclick="filtrarPorCongregacao('<?= $item['nome'] ?>')" class="btn-acao" title="Ver Membros" style="border-color:var(--verde-sucesso); color:var(--verde-sucesso); width:auto; padding:0 15px; font-size:0.75rem; font-weight:700;">VER MEMBROS</button>
                                    <button onclick="abrirEdicaoAux('congregacoes', <?= $item['id'] ?>, '<?= htmlspecialchars($item['nome'], ENT_QUOTES) ?>')" class="btn-acao"><svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/></svg></button>
                                    <button onclick="excluirAux('congregacoes', <?= $item['id'] ?>)" class="btn-acao"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12z"/></svg></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SEÇÃO: FUNÇÕES MINISTERIAIS -->
        <div id="funcoes-list" class="section-content">
            <div class="card aux-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3>Funções Ministeriais (Obreiros)</h3>
                    <form action="index.php?url=ajustes_salvar" method="POST" style="display:flex; gap:10px;">
                        <input type="hidden" name="tabela" value="funcoes_eclesiasticas">
                        <input type="text" name="nome" placeholder="Ex: Pastor, Diácono..." required style="padding:10px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                        <button type="submit" class="btn-novo" style="padding:10px 20px;">ADICIONAR</button>
                    </form>
                </div>

                <div class="aux-grid" style="grid-template-columns: 1fr;">
                    <?php 
                    // Ordenar funções alfabeticamente
                    usort($funcoes, function($a, $b) { return strcmp($a['nome'], $b['nome']); });
                    
                    foreach($funcoes as $f): 
                        // Filtrar membros desta função
                        $obreiros = array_filter($membros, function($m) use ($f) {
                            return ($m['funcao_eclesiastica'] ?? '') == $f['nome'];
                        });
                        // Ordenar obreiros alfabeticamente
                        usort($obreiros, function($a, $b) { return strcmp($a['nome'], $b['nome']); });
                    ?>
                    <div style="background:rgba(255,255,255,0.03); border-radius:15px; padding:20px; margin-bottom:20px; border:1px solid rgba(255,255,255,0.05);">
                        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:10px; margin-bottom:15px;">
                            <h4 style="color:var(--verde-sucesso); display:flex; align-items:center; gap:10px;">
                                <?= htmlspecialchars($f['nome']) ?>
                                <span style="font-size:0.7rem; background:rgba(46, 204, 113, 0.2); padding:2px 8px; border-radius:10px;"><?= count($obreiros) ?> Obreiros</span>
                            </h4>
                            <div style="display:flex; gap:10px;">
                                <button onclick="abrirEdicaoAux('funcoes_eclesiasticas', <?= $f['id'] ?>, '<?= htmlspecialchars($f['nome'], ENT_QUOTES) ?>')" class="btn-acao"><svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/></svg></button>
                                <button onclick="excluirAux('funcoes_eclesiasticas', <?= $f['id'] ?>)" class="btn-acao"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12z"/></svg></button>
                            </div>
                        </div>
                        
                        <?php if(empty($obreiros)): ?>
                            <p style="font-size:0.85rem; opacity:0.5;">Nenhum obreiro nesta função.</p>
                        <?php else: ?>
                            <table style="font-size:0.85rem;">
                                <thead>
                                    <tr>
                                        <th>Nome do Obreiro</th>
                                        <th>Congregação</th>
                                        <th>Cargo Congregacional</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($obreiros as $o): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($o['nome']) ?></td>
                                        <td><span style="opacity:0.7"><?= htmlspecialchars($o['congregacao']) ?></span></td>
                                        <td><span style="font-style:italic; opacity:0.6"><?= htmlspecialchars($o['cargo_congregacional'] ?? '-') ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- SEÇÃO: CARGOS CONGREGACIONAIS -->
        <div id="cargos-list" class="section-content">
            <div class="card aux-card">
                <h3>Cargos Congregacionais (Departamentos)</h3>
                <form action="index.php?url=ajustes_salvar" method="POST" class="form-aux-add">
                    <input type="hidden" name="tabela" value="cargos_congregacionais">
                    <input type="text" name="nome" placeholder="Ex: Líder de Jovens, Tesoureiro..." required>
                    <button type="submit" class="btn-novo">+</button>
                </form>
                <ul class="lista-aux">
                    <?php foreach($cargos_cong as $item): ?>
                    <li class="item-aux">
                        <span><?= htmlspecialchars($item['nome']) ?></span>
                        <div style="display:flex; gap:10px;">
                            <button onclick="abrirEdicaoAux('cargos_congregacionais', <?= $item['id'] ?>, '<?= htmlspecialchars($item['nome'], ENT_QUOTES) ?>')" class="btn-acao"><svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/></svg></button>
                            <button onclick="excluirAux('cargos_congregacionais', <?= $item['id'] ?>)" class="btn-acao"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12z"/></svg></button>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>

    <!-- MODAL MEMBRO -->
    <div id="modalMembro" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Novo Membro</h2>
            <form action="index.php?url=membros_salvar" method="POST" id="formMembro">
                <input type="hidden" name="id" id="membroId">
                <div class="form-tabs">
                    <button type="button" class="tab-btn active" data-tab="pessoais">Dados Pessoais</button>
                    <button type="button" class="tab-btn" data-tab="eclesiais">Dados Eclesiais</button>
                    <button type="button" class="tab-btn" data-tab="contato">Endereço e Contato</button>
                </div>

                <div class="tab-content active" id="tab-pessoais">
                    <div class="form-grid">
                        <div class="form-group full-width"><label>Nome Completo</label><input type="text" name="nome" id="membroNome" required></div>
                        <div class="form-group"><label>CPF</label><input type="text" name="cpf" id="membroCpf"></div>
                        <div class="form-group"><label>Nascimento</label><input type="date" name="data_nascimento" id="membroNascimento"></div>
                        <div class="form-group">
                            <label>Sexo</label>
                            <select name="sexo" id="membroSexo"><option value="M">Masculino</option><option value="F">Feminino</option></select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="membroStatus"><option value="Ativo">Ativo</option><option value="Inativo">Inativo</option></select>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-eclesiais">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Congregação</label>
                            <div style="display:flex; gap:8px;">
                                <select name="congregacao" id="membroCongregacao" style="flex:1;">
                                    <option value="">Selecione...</option>
                                    <?php foreach($congregacoes as $c): ?><option value="<?= $c['nome'] ?>"><?= $c['nome'] ?></option><?php endforeach; ?>
                                </select>
                                <button type="button" class="btn-acao" onclick="irParaAba('congregacoes-list')" title="Gerenciar Congregações">+</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Função Ministerial</label>
                            <div style="display:flex; gap:8px;">
                                <select name="funcao_eclesiastica" id="membroFuncao" style="flex:1;">
                                    <option value="">Selecione...</option>
                                    <?php foreach($funcoes as $f): ?><option value="<?= $f['nome'] ?>"><?= $f['nome'] ?></option><?php endforeach; ?>
                                </select>
                                <button type="button" class="btn-acao" onclick="irParaAba('funcoes-list')" title="Gerenciar Funções">+</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Cargo Congregacional</label>
                            <div style="display:flex; gap:8px;">
                                <select name="cargo_congregacional" id="membroCargoCong" style="flex:1;">
                                    <option value="">Selecione...</option>
                                    <?php foreach($cargos_cong as $cc): ?><option value="<?= $cc['nome'] ?>"><?= $cc['nome'] ?></option><?php endforeach; ?>
                                </select>
                                <button type="button" class="btn-acao" onclick="irParaAba('cargos-list')" title="Gerenciar Cargos">+</button>
                            </div>
                        </div>
                        <div class="form-group"><label>Batismo</label><input type="date" name="data_batismo" id="membroBatismo"></div>
                        <div class="form-group full-width"><label>Observações</label><input type="text" name="cargo" id="membroCargo"></div>
                    </div>
                </div>

                <div class="tab-content" id="tab-contato">
                    <div class="form-grid">
                        <div class="form-group"><label>WhatsApp</label><input type="text" name="telefone" id="membroTelefone"></div>
                        <div class="form-group"><label>E-mail</label><input type="email" name="email" id="membroEmail"></div>
                        <div class="form-group full-width"><label>Endereço</label><textarea name="endereco" id="membroEndereco" rows="2"></textarea></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn-salvar-m">Salvar Membro</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL AUXILIAR (EDIÇÃO DE APOIO) -->
    <div id="modalAux" class="modal">
        <div class="modal-content" style="max-width:400px;">
            <h3>Editar Nome</h3><br>
            <form action="index.php?url=ajustes_salvar" method="POST">
                <input type="hidden" name="id" id="auxId">
                <input type="hidden" name="tabela" id="auxTabela">
                <div class="form-group"><input type="text" name="nome" id="auxNome" required></div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" onclick="fecharModalAux()">Cancelar</button>
                    <button type="submit" class="btn-salvar-m">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL RESOLVER CONFLITO -->
    <div id="modalConflito" class="modal">
        <div class="modal-content" style="max-width:500px;">
            <h3 id="conflitoTitle">Resolver Conflito</h3><br>
            <form action="index.php?url=membros_resolver_conflito" method="POST">
                <input type="hidden" name="conflito_id" id="confId">
                <input type="hidden" name="tipo" id="confTipo">
                <input type="hidden" name="nome_original" id="confNomeOriginal">
                <input type="hidden" name="congs_encontradas" id="confCongs">

                <div id="areaDuplicidade" style="display:none;">
                    <p style="margin-bottom:15px; font-size:0.9rem; opacity:0.8;">Este nome aparece em múltiplas congregações. Escolha a congregação correta:</p>
                    <div class="form-group">
                        <label>Congregação</label>
                        <select name="congregacao_escolhida" id="selectCongregacaoConf">
                            <!-- Opções via JS -->
                        </select>
                    </div>
                </div>

                <div id="areaComposto" style="display:none;">
                    <p style="margin-bottom:15px; font-size:0.9rem; opacity:0.8;">Foram identificados múltiplos nomes em uma única entrada. Separe-os por ponto e vírgula (;):</p>
                    <div class="form-group">
                        <label>Nomes Separados</label>
                        <textarea name="nomes_separados" id="areaNomesSeparados" rows="3" style="width:100%; padding:10px; border-radius:10px;"></textarea>
                    </div><br>
                    <div class="form-group">
                        <label>Congregação Padrão</label>
                        <select name="cong_escolhida_composto" id="selectCongregacaoComposto">
                             <!-- Opções via JS -->
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" onclick="fecharModalConflito()">Cancelar</button>
                    <button type="submit" class="btn-salvar-m">Resolver e Importar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // NAVEGAÇÃO DE SEÇÕES
        function irParaAba(targetId) {
            fecharModal();
            const tabBtn = document.querySelector(`.menu-item[data-target="${targetId}"]`);
            if (tabBtn) tabBtn.click();
        }

        document.querySelectorAll('.menu-item').forEach(item => {
            item.onclick = function() {
                const targetId = this.getAttribute('data-target');
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                document.querySelectorAll('.section-content').forEach(s => s.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(targetId).classList.add('active');
                localStorage.setItem('membros_active_tab', targetId);
            };
        });

        // Restaurar aba ativa
        window.onload = () => {
            const lastTab = localStorage.getItem('membros_active_tab');
            if (lastTab) {
                const target = document.querySelector(`.menu-item[data-target="${lastTab}"]`);
                if (target) target.click();
            }
        };

        function showFlash(texto = "SALVO!") {
            const flash = document.getElementById('flash-membro');
            flash.innerText = texto;
            flash.style.opacity = 1;
            flash.style.transform = 'translate(-50%, -50%) scale(1)';
            setTimeout(() => {
                flash.style.opacity = 0;
                flash.style.transform = 'translate(-50%, -50%) scale(0.8)';
            }, 1000);
        }

        // SUBMIT AJAX MEMBRO
        document.getElementById('formMembro').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const resp = await fetch('index.php?url=membros_salvar', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    fecharModal();
                    showFlash();
                    setTimeout(() => location.reload(), 800);
                } else {
                    alert('Erro ao salvar.');
                }
            } catch (err) { alert('Erro na conexão.'); }
        };

        // SUBMIT AJAX AUX
        document.querySelector('#modalAux form').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const resp = await fetch('index.php?url=ajustes_salvar', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    fecharModalAux();
                    showFlash();
                    setTimeout(() => location.reload(), 800);
                }
            } catch (err) {}
        };

        // MODAL MEMBRO
        const modalM = document.getElementById('modalMembro');
        document.querySelector('.btn-novo').onclick = () => {
            document.getElementById('formMembro').reset();
            document.getElementById('membroId').value = '';
            document.getElementById('modalTitle').innerText = 'Novo Membro';
            modalM.style.display = 'flex';
            document.querySelector('.tab-btn[data-tab="pessoais"]').click();
        };

        function fecharModal() { modalM.style.display = 'none'; }

        document.querySelectorAll('.tab-btn').forEach(b => {
            b.onclick = () => {
                document.querySelectorAll('.tab-btn').forEach(x => x.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(x => x.classList.remove('active'));
                b.classList.add('active');
                document.getElementById('tab-' + b.getAttribute('data-tab')).classList.add('active');
            };
        });

        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.onclick = () => {
                const info = JSON.parse(btn.getAttribute('data-info'));
                document.getElementById('membroId').value = info.id;
                document.getElementById('membroNome').value = info.nome;
                document.getElementById('membroCpf').value = info.cpf || '';
                document.getElementById('membroNascimento').value = info.data_nascimento || '';
                document.getElementById('membroSexo').value = info.sexo || 'M';
                document.getElementById('membroStatus').value = info.status || 'Ativo';
                document.getElementById('membroCongregacao').value = info.congregacao || '';
                document.getElementById('membroFuncao').value = info.funcao_eclesiastica || '';
                document.getElementById('membroCargoCong').value = info.cargo_congregacional || '';
                document.getElementById('membroBatismo').value = info.data_batismo || '';
                document.getElementById('membroCargo').value = info.cargo || '';
                document.getElementById('membroTelefone').value = info.telefone || '';
                document.getElementById('membroEmail').value = info.email || '';
                document.getElementById('membroEndereco').value = info.endereco || '';
                
                document.getElementById('modalTitle').innerText = 'Editar Membro';
                modalM.style.display = 'flex';
                document.querySelector('.tab-btn[data-tab="pessoais"]').click();
            };
        });

        document.querySelectorAll('.btn-excluir').forEach(btn => {
            btn.onclick = async () => { 
                if(confirm('Excluir membro?')) {
                    const id = btn.getAttribute('data-id');
                    try {
                        const resp = await fetch(`index.php?url=membros_excluir&id=${id}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const res = await resp.json();
                        if (res.status === 'success') {
                            showFlash("EXCLUÍDO!");
                            setTimeout(() => location.reload(), 800);
                        }
                    } catch (e) {}
                }
            };
        });

        // FILTROS
        const sI = document.getElementById('searchInput');
        const fC = document.getElementById('filterCongregacao');
        const fF = document.getElementById('filterFuncao');
        const fAg = document.getElementById('filterCargo');

        function filt() {
            const t = sI.value.toLowerCase();
            const c = fC.value;
            const f = fF.value;
            const cg = fAg.value;
            
            let count = 0;
            document.querySelectorAll('tbody tr').forEach(tr => {
                if(tr.cells.length < 2) return;
                const match = tr.innerText.toLowerCase().includes(t) && (!c || tr.dataset.congregacao == c) && (!f || tr.dataset.funcao == f) && (!cg || tr.dataset.cargo == cg);
                tr.style.display = match ? '' : 'none';
                if(match) count++;
            });
        }
        [sI, fC, fF, fAg].forEach(el => el.oninput = filt);

        function filtrarPorCongregacao(nome) {
            fC.value = nome;
            irParaAba('membros-list');
            filt();
        }

        // AUXILIARES
        const modalA = document.getElementById('modalAux');
        function abrirEdicaoAux(tab, id, nome) {
            document.getElementById('auxId').value = id;
            document.getElementById('auxTabela').value = tab;
            document.getElementById('auxNome').value = nome;
            modalA.style.display = 'flex';
        }
        function fecharModalAux() { modalA.style.display = 'none'; }
        function excluirAux(t, id) { if(confirm('Excluir item?')) window.location.href=`index.php?url=ajustes_excluir&tabela=${t}&id=${id}&origem=membros`; }

        // CONFLITOS
        const modalC = document.getElementById('modalConflito');
        function abrirResolverConflito(c) {
            document.getElementById('confId').value = c.id;
            document.getElementById('confTipo').value = c.tipo_conflito;
            document.getElementById('confNomeOriginal').value = c.nome_original;
            document.getElementById('confCongs').value = c.congregacoes_encontradas;
            
            const areaDup = document.getElementById('areaDuplicidade');
            const areaComp = document.getElementById('areaComposto');
            const selCong = document.getElementById('selectCongregacaoConf');
            const selCongComp = document.getElementById('selectCongregacaoComposto');

            selCong.innerHTML = '';
            selCongComp.innerHTML = '';
            
            const congs = c.congregacoes_encontradas.split(',').map(x => x.trim());
            congs.forEach(cong => {
                const opt = document.createElement('option');
                opt.value = cong;
                opt.text = cong;
                selCong.add(opt);
                selCongComp.add(opt.cloneNode(true));
            });

            if(c.tipo_conflito === 'composto') {
                areaDup.style.display = 'none';
                areaComp.style.display = 'block';
                document.getElementById('areaNomesSeparados').value = c.nome_original.replace(/\s(e|&)\s/gi, '; ');
            } else {
                areaDup.style.display = 'block';
                areaComp.style.display = 'none';
            }
            
            modalC.style.display = 'flex';
        }
        function fecharModalConflito() { modalC.style.display = 'none'; }

        window.onclick = (e) => { if(e.target == modalM) fecharModal(); if(e.target == modalA) fecharModalAux(); if(e.target == modalC) fecharModalConflito(); };
    </script>
</body>
</html>
