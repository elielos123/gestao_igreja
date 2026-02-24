<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - Gestão Igreja</title>
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

        h1 { margin-bottom: 20px; color: var(--verde-sucesso); }

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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        th { color: rgba(255,255,255,0.5); font-size: 0.8rem; text-transform: uppercase; }

        tr:hover { background: rgba(255,255,255,0.02); }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            align-items: center; justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: var(--azul-fundo);
            padding: 30px;
            border-radius: 15px;
            max-width: 400px;
            width: 90%;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; opacity: 0.7; }
        
        .checkbox-group {
            max-height: 200px;
            overflow-y: auto;
            background: rgba(0,0,0,0.2);
            padding: 10px;
            border-radius: 5px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Usuários e Permissões</h1>
            <div>
                <button class="btn btn-success" onclick="openCreateUserModal()">Novo Usuário</button>
                <a href="index.php?url=usuarios_papeis" class="btn btn-secondary">Gerenciar Papéis</a>
                <a href="index.php?url=dashboard" class="btn btn-secondary">Voltar</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Nível</th>
                    <th>Papéis</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nome']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['nivel'] ?></td>
                        <td><small><?= htmlspecialchars($u['papeis_nomes'] ?: 'Nenhum') ?></small></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="openRolesModal(<?= $u['id'] ?>, '<?= $u['nome'] ?>')">
                                Editar Papéis
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Novo Usuário -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Criar Novo Usuário</h2>
            <form id="userForm">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" id="userNome" required style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:white; border-radius:5px;">
                </div>
                <div class="form-group">
                    <label>E-mail:</label>
                    <input type="email" id="userEmail" required style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:white; border-radius:5px;">
                </div>
                <div class="form-group">
                    <label>Nível:</label>
                    <select id="userNivel" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:white; border-radius:5px;">
                        <option value="secretario">Secretário</option>
                        <option value="tesoureiro">Tesoureiro</option>
                        <option value="pastor">Pastor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Senha Provisória:</label>
                    <input type="password" id="userSenha" required style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:white; border-radius:5px;">
                    <small style="opacity:0.5;">Será solicitado que o usuário mude-a no primeiro acesso.</small>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="flex:1;">Criar</button>
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()" style="flex:1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Papéis -->
    <div id="rolesModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-bottom: 20px;">Editar Papéis</h2>
            <form id="rolesForm">
                <input type="hidden" id="userId">
                <div class="form-group">
                    <label>Selecione os papéis:</label>
                    <div class="checkbox-group">
                        <?php foreach ($papeis as $p): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" name="papeis[]" value="<?= $p['id'] ?>" id="papel_<?= $p['id'] ?>">
                                <label for="papel_<?= $p['id'] ?>" style="margin:0; opacity:1;"><?= htmlspecialchars($p['nome']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="flex:1;">Salvar</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()" style="flex:1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const userModal = document.getElementById('userModal');
        const userForm = document.getElementById('userForm');

        function openCreateUserModal() { userModal.style.display = 'flex'; }
        function closeUserModal() { userModal.style.display = 'none'; }

        userForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const dados = {
                nome: document.getElementById('userNome').value,
                email: document.getElementById('userEmail').value,
                nivel: document.getElementById('userNivel').value,
                senha: document.getElementById('userSenha').value
            };

            try {
                const response = await fetch('index.php?url=usuarios_criar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dados)
                });
                const res = await response.json();
                if (res.status === 'success') {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message);
                }
            } catch (err) {
                alert('Erro ao criar usuário');
            }
        });

        const modal = document.getElementById('rolesModal');
        const form = document.getElementById('rolesForm');
        
        function openRolesModal(id, nome) {
            document.getElementById('userId').value = id;
            document.getElementById('modalTitle').innerText = 'Papéis de ' + nome;
            
            // Reset checkboxes
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            
            // Aqui poderíamos carregar os papéis atuais via AJAX se necessário, 
            // mas para simplificar, vamos deixar o usuário marcar de novo ou melhorar depois.
            
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const userId = document.getElementById('userId').value;
            const selectedPapeis = Array.from(form.querySelectorAll('input[name="papeis[]"]:checked')).map(cb => cb.value);

            try {
                const response = await fetch('index.php?url=usuarios_salvar_papeis', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ usuario_id: userId, papeis: selectedPapeis })
                });
                const res = await response.json();
                if (res.status === 'success') {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message);
                }
            } catch (err) {
                alert('Erro ao salvar permissões');
            }
        });
    </script>
</body>
</html>
