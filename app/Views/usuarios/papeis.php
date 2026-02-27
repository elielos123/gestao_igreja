<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Papéis - Gestão Igreja</title>
    <style>
        :root {
            --azul-fundo: #001f3f;
            --azul-sombra: #000a14;
            --branco: #ffffff;
            --verde-sucesso: #2ecc71;
            --azul-claro: #3498db;
            --laranja: #e67e22;
            --vermelho: #e74c3c;
            --gradiente: linear-gradient(135deg, #001f3f 0%, #000a14 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        body {
            background: var(--gradiente);
            min-height: 100vh;
            color: var(--branco);
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 30px;
        }

        h1 { margin-bottom: 20px; color: var(--laranja); }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }

        .btn-primary { background: var(--azul-claro); color: white; }
        .btn-success { background: var(--verde-sucesso); color: white; }
        .btn-secondary { background: rgba(255,255,255,0.1); color: white; }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .role-card {
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 20px;
        }

        .role-card h3 { margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }

        .permissions-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }

        .perm-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .perm-item input { cursor: pointer; }

        .modal-overlay {
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.8); 
            align-items: center; 
            justify-content: center; 
            z-index: 1000;
        }

        .modal-content {
            background: var(--azul-fundo); 
            padding: 30px; 
            border-radius: 15px; 
            max-width: 400px; 
            width: 90%; 
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .form-control {
            width: 100%; 
            padding: 10px; 
            border-radius: 5px; 
            border: 1px solid rgba(255,255,255,0.1); 
            background: rgba(0,0,0,0.2); 
            color: white;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--azul-claro);
        }
    </style>
</head>
<body>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Configurar Papéis e Permissões</h1>
            <div>
                <button class="btn btn-primary" onclick="openNewRoleModal()">Novo Papel</button>
                <a href="index.php?url=usuarios" class="btn btn-secondary">Voltar aos Usuários</a>
            </div>
        </div>

        <div class="roles-grid">
            <?php foreach ($papeis as $papel): ?>
                <div class="role-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <h3 style="margin-bottom: 5px;">
                            <?= htmlspecialchars($papel['nome']) ?>
                        </h3>
                        <div style="display: flex; gap: 5px;">
                            <button class="btn btn-secondary btn-sm" onclick="openEditRoleModal(<?= $papel['id'] ?>, '<?= addslashes($papel['nome']) ?>', '<?= addslashes($papel['descricao']) ?>')" style="padding: 5px 10px; font-size: 0.7rem;">✏️</button>
                            <?php if ($papel['id'] > 3): // Não permite excluir os 3 básicos facilmente ?>
                                <button class="btn btn-secondary btn-sm" onclick="deleteRole(<?= $papel['id'] ?>)" style="padding: 5px 10px; font-size: 0.7rem; background: rgba(231, 76, 60, 0.2);">🗑️</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p style="font-size: 0.75rem; opacity: 0.6; margin-bottom: 15px; min-height: 1.5em;">
                        <?= htmlspecialchars($papel['descricao'] ?: 'Sem descrição') ?>
                    </p>
                    
                    <form onsubmit="saveRolePermissions(event, <?= $papel['id'] ?>)">
                        <div class="permissions-list">
                            <?php foreach ($permissoes as $perm): 
                                $checked = in_array($perm['id'], $papel['permissoes'] ?? []) ? 'checked' : '';
                            ?>
                                <label class="perm-item">
                                    <input type="checkbox" name="permissoes[]" value="<?= $perm['id'] ?>" <?= $checked ?>>
                                    <span><?= htmlspecialchars($perm['descricao'] ?: $perm['nome']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 20px; font-size: 0.8rem;">
                            Salvar Permissões
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal Novo Papel -->
    <div id="newRoleModal" class="modal-overlay">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Criar Novo Papel</h2>
            <form id="newRoleForm">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; opacity: 0.7;">Nome do Papel</label>
                    <input type="text" id="roleName" class="form-control" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; opacity: 0.7;">Descrição</label>
                    <input type="text" id="roleDesc" class="form-control">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="flex:1;">Criar</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('newRoleModal')" style="flex:1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Papel -->
    <div id="editRoleModal" class="modal-overlay">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Editar Papel</h2>
            <form id="editRoleForm">
                <input type="hidden" id="editRoleId">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; opacity: 0.7;">Nome do Papel</label>
                    <input type="text" id="editRoleName" class="form-control" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; opacity: 0.7;">Descrição</label>
                    <input type="text" id="editRoleDesc" class="form-control">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="flex:1;">Salvar Alterações</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editRoleModal')" style="flex:1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openNewRoleModal() {
            document.getElementById('newRoleModal').style.display = 'flex';
        }

        function openEditRoleModal(id, nome, descricao) {
            document.getElementById('editRoleId').value = id;
            document.getElementById('editRoleName').value = nome;
            document.getElementById('editRoleDesc').value = descricao;
            document.getElementById('editRoleModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById('newRoleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const nome = document.getElementById('roleName').value;
            const descricao = document.getElementById('roleDesc').value;

            try {
                const response = await fetch('index.php?url=usuarios_criar_papel', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nome, descricao })
                });
                const res = await response.json();
                if (res.status === 'success') {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message);
                }
            } catch (err) {
                alert('Erro ao criar papel');
            }
        });

        document.getElementById('editRoleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('editRoleId').value;
            const nome = document.getElementById('editRoleName').value;
            const descricao = document.getElementById('editRoleDesc').value;

            try {
                const response = await fetch('index.php?url=usuarios_atualizar_papel', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, nome, descricao })
                });
                const res = await response.json();
                if (res.status === 'success') {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message);
                }
            } catch (err) {
                alert('Erro ao atualizar papel');
            }
        });

        async function deleteRole(id) {
            if (!confirm('Tem certeza que deseja excluir este papel? Todas as associações com usuários e permissões serão removidas.')) return;

            try {
                const response = await fetch('index.php?url=usuarios_excluir_papel', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const res = await response.json();
                if (res.status === 'success') {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message);
                }
            } catch (err) {
                alert('Erro ao excluir papel');
            }
        }

        async function saveRolePermissions(e, papelId) {
            e.preventDefault();
            const form = e.target;
            const checkedPerms = Array.from(form.querySelectorAll('input[name="permissoes[]"]:checked')).map(cb => cb.value);

            try {
                const response = await fetch('index.php?url=usuarios_salvar_papel_permissoes', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ papel_id: papelId, permissoes: checkedPerms })
                });
                const res = await response.json();
                alert(res.message);
                if (res.status === 'success') location.reload();
            } catch (err) {
                alert('Erro ao salvar permissões do papel');
            }
        }
    </script>
</body>
</html>
