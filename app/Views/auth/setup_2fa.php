<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar 2FA — Gestão de Igreja</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- QR code renderer (browser-side) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root{--primary:#4f46e5;--green:#16a34a;--red:#dc2626;--bg:#f3f4f6;}
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif;}
        body{background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
        .card{background:#fff;padding:2.5rem;border-radius:1rem;box-shadow:0 10px 25px -5px rgba(0,0,0,.1);width:100%;max-width:480px;}
        h1{font-size:1.3rem;font-weight:700;color:#1f2937;margin-bottom:.3rem;}
        .subtitle{color:#6b7280;font-size:.875rem;margin-bottom:2rem;}
        .step{display:none;} .step.active{display:block;}
        .alert{padding:.75rem;border-radius:.5rem;font-size:.875rem;margin-bottom:1rem;display:none;text-align:center;}
        .alert-error{background:#fee2e2;color:var(--red);}
        .alert-success{background:#dcfce7;color:var(--green);}
        .qr-wrap{display:flex;justify-content:center;margin:1.5rem 0;padding:16px;background:#fff;border:2px solid #e5e7eb;border-radius:12px;}
        .secret-box{background:#f3f4f6;border-radius:8px;padding:10px 14px;font-family:monospace;letter-spacing:.12em;font-size:.95rem;text-align:center;word-break:break-all;margin-bottom:1rem;color:#1f2937;}
        .form-group{margin-bottom:1.2rem;}
        label{display:block;font-size:.875rem;font-weight:500;color:#1f2937;margin-bottom:.4rem;}
        input[type=text]{width:100%;padding:.75rem;border:1px solid #d1d5db;border-radius:.5rem;font-size:1.5rem;text-align:center;letter-spacing:.4em;font-family:monospace;outline:none;}
        input[type=text]:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,.1);}
        .btn{width:100%;padding:.8rem;border:none;border-radius:.5rem;font-size:1rem;font-weight:600;cursor:pointer;transition:.2s;margin-top:.5rem;}
        .btn-primary{background:var(--primary);color:#fff;} .btn-primary:hover{background:#4338ca;}
        .btn-danger{background:var(--red);color:#fff;} .btn-danger:hover{background:#b91c1c;}
        .btn-back{background:transparent;border:1px solid #d1d5db;color:#6b7280;margin-top:.5rem;}
        .steps-title{font-size:.8rem;color:#6b7280;margin-bottom:.5rem;}
        ol{padding-left:1.5rem;font-size:.9rem;color:#374151;line-height:1.9;}
        ol li strong{color:#1f2937;}
        .status-badge{display:inline-flex;align-items:center;gap:6px;background:#f3f4f6;border-radius:20px;padding:5px 12px;font-size:.8rem;font-weight:600;margin-bottom:1rem;}
        .dot{width:9px;height:9px;border-radius:50%;background:#d1d5db;}
        .dot.green{background:var(--green);}
        .separator{border:none;border-top:1px solid #f3f4f6;margin:1.5rem 0;}
    </style>
</head>
<body>
<div class="card">
    <div style="margin-bottom:1.5rem">
        <h1>🔐 Autenticação de Dois Fatores</h1>
        <p class="subtitle">Proteja sua conta com o Google Authenticator</p>
    </div>

    <div id="alertBox" class="alert"></div>

    <!-- ── STATUS PANEL ── -->
    <div id="statusPanel">
        <div id="statusBadge" class="status-badge">
            <div id="statusDot" class="dot"></div>
            <span id="statusText">Verificando...</span>
        </div>

        <!-- If 2FA disabled: show activate button -->
        <div id="panelDisabled">
            <p style="font-size:.88rem;color:#374151;margin-bottom:1.2rem">
                Com o 2FA ativo, além da senha, você precisará de um código temporário gerado pelo app
                <strong>Google Authenticator</strong> (ou compatível) em cada login.
            </p>
            <button class="btn btn-primary" onclick="iniciarSetup()">➕ Ativar 2FA</button>
        </div>

        <!-- If 2FA enabled: show disable button -->
        <div id="panelEnabled" style="display:none">
            <p style="font-size:.88rem;color:#374151;margin-bottom:1.2rem">
                O 2FA está <strong>ativo</strong> na sua conta. Cada login exigirá um código do Google Authenticator.
            </p>
            <button class="btn btn-danger" onclick="confirmarDesativar()">🗑️ Desativar 2FA</button>
        </div>

        <hr class="separator">
        <a href="index.php?url=dashboard" style="font-size:.85rem;color:var(--primary);">← Voltar ao painel</a>
    </div>

    <!-- ── SETUP STEPS ── -->
    <div id="setupPanel" style="display:none">
        <p class="steps-title">Siga os passos:</p>
        <ol>
            <li>Instale o <strong>Google Authenticator</strong> no seu telefone</li>
            <li>Toque em <strong>"+"</strong> e escolha <strong>"Ler QR code"</strong></li>
            <li>Escaneie o código abaixo:</li>
        </ol>

        <div class="qr-wrap">
            <div id="qrcode"></div>
        </div>

        <p style="font-size:.75rem;color:#6b7280;text-align:center;margin-bottom:.5rem">Ou insira o código manualmente:</p>
        <div class="secret-box" id="secretDisplay">—</div>

        <div class="form-group">
            <label for="confirmCode">4. Digite o código de 6 dígitos do app para confirmar:</label>
            <input type="text" id="confirmCode" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*">
        </div>
        <button class="btn btn-primary" onclick="confirmarSetup()">✅ Confirmar e Ativar</button>
        <button class="btn btn-back" onclick="cancelarSetup()">Cancelar</button>
    </div>
</div>

<script>
let currentStatus = false;

async function carregarStatus() {
    const resp = await fetch('index.php?url=get2fa_setup');
    const data = await resp.json();
    if (data.status !== 'success') { showAlert(data.message, 'error'); return; }

    currentStatus = data.totp_ativo;
    document.getElementById('statusDot').className = 'dot' + (currentStatus ? ' green' : '');
    document.getElementById('statusText').textContent = currentStatus ? '2FA Ativo' : '2FA Desativado';
    document.getElementById('panelDisabled').style.display = currentStatus ? 'none' : 'block';
    document.getElementById('panelEnabled').style.display  = currentStatus ? 'block' : 'none';

    if (!currentStatus) {
        // Render QR already (secret was generated server-side as totp_temp)
        renderQR(data.qr_uri, data.secret);
    }
}

function renderQR(uri, secret) {
    document.getElementById('qrcode').innerHTML = '';
    new QRCode(document.getElementById('qrcode'), {text: uri, width: 180, height: 180, correctLevel: QRCode.CorrectLevel.M});
    document.getElementById('secretDisplay').textContent = secret;
}

async function iniciarSetup() {
    document.getElementById('statusPanel').style.display = 'none';
    document.getElementById('setupPanel').style.display = 'block';
    // Call again to ensure latest secret
    const resp = await fetch('index.php?url=get2fa_setup');
    const data = await resp.json();
    if (data.status === 'success') renderQR(data.qr_uri, data.secret);
    document.getElementById('confirmCode').focus();
}

async function confirmarSetup() {
    const codigo = document.getElementById('confirmCode').value.replace(/\D/g,'');
    if (codigo.length !== 6) { showAlert('Digite os 6 dígitos do código.', 'error'); return; }
    hideAlert();

    const resp = await fetch('index.php?url=confirmar2fa', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({codigo})
    });
    const data = await resp.json();
    if (data.status === 'success') {
        showAlert('2FA ativado com sucesso! 🎉', 'success');
        document.getElementById('setupPanel').style.display = 'none';
        document.getElementById('statusPanel').style.display = 'block';
        carregarStatus();
    } else {
        showAlert(data.message, 'error');
        document.getElementById('confirmCode').value = '';
        document.getElementById('confirmCode').focus();
    }
}

function cancelarSetup() {
    document.getElementById('setupPanel').style.display = 'none';
    document.getElementById('statusPanel').style.display = 'block';
}

async function confirmarDesativar() {
    if (!confirm('Tem certeza que deseja desativar o 2FA? Sua conta ficará menos segura.')) return;
    const resp = await fetch('index.php?url=desativar2fa', {
        method: 'POST', headers: {'Content-Type':'application/json'}, body: '{}'
    });
    const data = await resp.json();
    if (data.status === 'success') {
        showAlert('2FA desativado.', 'success');
        carregarStatus();
    } else {
        showAlert(data.message, 'error');
    }
}

function showAlert(msg, type) {
    const box = document.getElementById('alertBox');
    box.textContent = msg;
    box.className = 'alert alert-' + type;
    box.style.display = 'block';
}
function hideAlert() { document.getElementById('alertBox').style.display = 'none'; }

// Allow Enter on code input
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('confirmCode').addEventListener('keydown', e => {
        if (e.key === 'Enter') confirmarSetup();
    });
    carregarStatus();
});
</script>
</body>
</html>
