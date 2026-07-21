<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Igreja - Ajustes Técnicos</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-sombra: #000a14;
            --branco: #ffffff;
            --verde-sucesso: #2ecc71;
            --azul-claro: #3498db;
            --vermelho: #e74c3c;
            --cinza-claro: rgba(255,255,255,0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { 
            background-color: var(--azul-fundo); 
            color: var(--branco); 
            padding: 30px;
            min-height: 100vh;
        }
        
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 40px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 20px;
        }
        .btn-voltar { 
            color: var(--branco); 
            text-decoration: none; 
            font-weight: 600; 
            display: flex; 
            align-items: center; 
            gap: 10px;
            transition: opacity 0.2s;
        }
        .btn-voltar:hover { opacity: 0.7; }

        .ajustes-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 30px; 
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .card { 
            background: rgba(255,255,255,0.03); 
            padding: 25px; 
            border-radius: 24px; 
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
        }

        .card h3 { 
            margin-bottom: 20px; 
            font-size: 1.1rem; 
            color: var(--verde-sucesso);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-add { 
            display: flex; 
            gap: 10px; 
            margin-bottom: 25px; 
            background: rgba(0,0,0,0.2);
            padding: 10px;
            border-radius: 15px;
        }
        .form-add input { 
            flex: 1; 
            padding: 12px 15px; 
            border-radius: 10px; 
            border: 1px solid rgba(255,255,255,0.1); 
            background: rgba(255,255,255,0.05); 
            color: #fff; 
            outline: none;
            font-size: 0.95rem;
        }
        .form-add input:focus { border-color: var(--verde-sucesso); }
        .btn-add { 
            background: var(--verde-sucesso); 
            border: none; 
            color: #fff; 
            width: 45px;
            height: 45px;
            border-radius: 10px; 
            cursor: pointer; 
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.1s;
        }
        .btn-add:active { transform: scale(0.95); }

        .lista-itens { 
            list-style: none; 
            max-height: 400px;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        .lista-itens::-webkit-scrollbar { width: 4px; }
        .lista-itens::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        .item { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 14px 15px; 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            font-size: 0.95rem;
            transition: background 0.2s;
            border-radius: 10px;
        }
        .item:hover { background: rgba(255,255,255,0.03); }
        .item:last-child { border: none; }
        
        .item-actions { display: flex; gap: 8px; }

        .btn-icon { 
            border: none; 
            padding: 8px; 
            border-radius: 8px; 
            cursor: pointer; 
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--branco);
        }
        
        .btn-edit { background: rgba(52, 152, 219, 0.1); color: var(--azul-claro); }
        .btn-edit:hover { background: var(--azul-claro); color: #fff; }
        
        .btn-del { background: rgba(231, 76, 60, 0.1); color: var(--vermelho); }
        .btn-del:hover { background: var(--vermelho); color: #fff; }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: var(--azul-fundo);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .modal-content h3 { margin-bottom: 20px; }
        .modal-footer { margin-top: 25px; display: flex; justify-content: flex-end; gap: 15px; }
        
        .btn { padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; border: none; }
        .btn-cancel { background: transparent; color: #999; }
        .btn-save { background: var(--verde-sucesso); color: #fff; }
    </style>
</head>
<body>

    <div class="header">
        <a href="membros" class="btn-voltar">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            VOLTAR AOS MEMBROS
        </a>
        <h1>Ajustes de Sistema</h1>
        <div style="width: 100px;"></div>
    </div>

    <div class="ajustes-grid">
        
        <!-- Cadastro de Congregações -->
        <div class="card">
            <h3>
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                Congregações
            </h3>
            <form action="index.php?url=ajustes_salvar" method="POST" class="form-add">
                <input type="hidden" name="tabela" value="congregacoes">
                <input type="text" name="nome" placeholder="Adicionar nova..." required>
                <button type="submit" class="btn-add">+</button>
            </form>
            <ul class="lista-itens">
                <?php foreach($congregacoes as $item): ?>
                <li class="item">
                    <span><?= htmlspecialchars($item['nome']) ?></span>
                    <div class="item-actions">
                        <button onclick="abrirEdicao('congregacoes', <?= $item['id'] ?>, '<?= htmlspecialchars($item['nome'], ENT_QUOTES) ?>')" class="btn-icon btn-edit" title="Editar">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        </button>
                        <button onclick="excluir('congregacoes', <?= $item['id'] ?>)" class="btn-icon btn-del" title="Excluir">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </button>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Funções Eclesiásticas -->
        <div class="card">
            <h3>
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                Funções Eclesiásticas
            </h3>
            <form action="index.php?url=ajustes_salvar" method="POST" class="form-add">
                <input type="hidden" name="tabela" value="funcoes_eclesiasticas">
                <input type="text" name="nome" placeholder="Adicionar nova..." required>
                <button type="submit" class="btn-add">+</button>
            </form>
            <ul class="lista-itens">
                <?php foreach($funcoes as $item): ?>
                <li class="item">
                    <span><?= htmlspecialchars($item['nome']) ?></span>
                    <div class="item-actions">
                        <button onclick="abrirEdicao('funcoes_eclesiasticas', <?= $item['id'] ?>, '<?= htmlspecialchars($item['nome'], ENT_QUOTES) ?>')" class="btn-icon btn-edit" title="Editar">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        </button>
                        <button onclick="excluir('funcoes_eclesiasticas', <?= $item['id'] ?>)" class="btn-icon btn-del" title="Excluir">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </button>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Cargos Congregacionais -->
        <div class="card">
            <h3>
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                Cargos Congregacionais
            </h3>
            <form action="index.php?url=ajustes_salvar" method="POST" class="form-add">
                <input type="hidden" name="tabela" value="cargos_congregacionais">
                <input type="text" name="nome" placeholder="Adicionar novo..." required>
                <button type="submit" class="btn-add">+</button>
            </form>
            <ul class="lista-itens">
                <?php foreach($cargos as $item): ?>
                <li class="item">
                    <span><?= htmlspecialchars($item['nome']) ?></span>
                    <div class="item-actions">
                        <button onclick="abrirEdicao('cargos_congregacionais', <?= $item['id'] ?>, '<?= htmlspecialchars($item['nome'], ENT_QUOTES) ?>')" class="btn-icon btn-edit" title="Editar">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        </button>
                        <button onclick="excluir('cargos_congregacionais', <?= $item['id'] ?>)" class="btn-icon btn-del" title="Excluir">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </button>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>

    <!-- MODAL EDIÇÃO -->
    <div id="modalEdit" class="modal">
        <div class="modal-content">
            <h3>Editar Nome</h3>
            <form action="index.php?url=ajustes_salvar" method="POST">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="tabela" id="editTabela">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 10px;">
                    <label style="font-size: 0.8rem; opacity: 0.6;">Novo Nome:</label>
                    <input type="text" name="nome" id="editNome" style="padding: 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); color: #fff; outline: none;" required>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="fecharEdicao()" class="btn btn-cancel">Cancelar</button>
                    <button type="submit" class="btn btn-save">Salvar Alteração</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function excluir(tabela, id) {
            if(confirm('Tem certeza que deseja excluir? Isso pode afetar os membros vinculados.')) {
                window.location.href = `index.php?url=ajustes_excluir&tabela=${tabela}&id=${id}`;
            }
        }

        const modal = document.getElementById('modalEdit');
        
        function abrirEdicao(tabela, id, nome) {
            document.getElementById('editId').value = id;
            document.getElementById('editTabela').value = tabela;
            document.getElementById('editNome').value = nome;
            modal.style.display = 'flex';
        }

        function fecharEdicao() {
            modal.style.display = 'none';
        }

        window.onclick = (e) => { if(e.target == modal) fecharEdicao(); }
    </script>
</body>
</html>
