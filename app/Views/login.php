<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestão de Igreja</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php
        $recaptchaSiteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
        $hasRecaptcha = !empty($recaptchaSiteKey) && $recaptchaSiteKey !== 'COLOQUE_SUA_SITE_KEY_AQUI';
    ?>
    <?php if ($hasRecaptcha): ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($recaptchaSiteKey) ?>"></script>
    <?php endif; ?>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text: #1f2937;
            --text-muted: #6b7280;
            --green: #16a34a;
            --red: #dc2626;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',sans-serif; }
        body {
            background: var(--bg);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(79,70,229,.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(79,70,229,.06) 0%, transparent 50%);
        }
        .login-card {
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1);
            width: 100%; max-width: 420px;
            animation: fadeIn .45s ease-out;
        }
        @keyframes fadeIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .header { text-align:center; margin-bottom:2rem; }
        .logo {
            width:64px; height:64px; background:var(--primary); border-radius:12px;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 1rem; color:#fff; font-size:1.5rem; font-weight:bold;
        }
        h1 { color:var(--text); font-size:1.5rem; font-weight:700; margin-bottom:.5rem; }
        .subtitle { color:var(--text-muted); font-size:.875rem; }
        .form-group { margin-bottom:1.2rem; }
        label { display:block; font-size:.875rem; font-weight:500; color:var(--text); margin-bottom:.4rem; }
        input[type=email], input[type=password], input[type=text] {
            width:100%; padding:.75rem; border:1px solid #d1d5db;
            border-radius:.5rem; font-size:1rem; transition:all .2s; outline:none;
        }
        input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(79,70,229,.1); }
        /* ─── Password strength bar ─── */
        .strength-bar-wrap { margin-top:6px; display:flex; gap:4px; }
        .strength-seg { flex:1; height:4px; border-radius:4px; background:#e5e7eb; transition:background .3s; }
        .strength-seg.active-1 { background:#ef4444; }
        .strength-seg.active-2 { background:#f97316; }
        .strength-seg.active-3 { background:#eab308; }
        .strength-seg.active-4 { background:var(--green); }
        .strength-hint { font-size:.76rem; margin-top:4px; min-height:16px; }
        /* ─── Button ─── */
        .btn-primary {
            width:100%; padding:.8rem; background:var(--primary); color:#fff;
            border:none; border-radius:.5rem; font-size:1rem; font-weight:600;
            cursor:pointer; transition:background .2s; margin-top:1rem;
            display:flex; align-items:center; justify-content:center; gap:8px;
        }
        .btn-primary:hover { background:var(--primary-hover); }
        .btn-primary:disabled { opacity:.65; cursor:not-allowed; }
        /* ─── Loader ─── */
        .loader {
            display:none; width:18px; height:18px;
            border:3px solid rgba(255,255,255,.35);
            border-radius:50%; border-top-color:#fff;
            animation:spin 1s linear infinite;
        }
        @keyframes spin{to{transform:rotate(360deg)}}
        /* ─── Alerts ─── */
        .alert {
            padding:.75rem; border-radius:.5rem; font-size:.875rem;
            margin-bottom:1rem; display:none; text-align:center;
        }
        .alert-error { background:#fee2e2; color:var(--red); }
        .alert-success { background:#dcfce7; color:var(--green); }
        /* ─── Step 2 (TOTP) ─── */
        #step2 { display:none; }
        .totp-input {
            letter-spacing:.4em; font-size:1.6rem; text-align:center;
            font-weight:700; font-family:monospace;
        }
        .back-link {
            display:block; text-align:center; margin-top:.8rem;
            font-size:.82rem; color:var(--primary); cursor:pointer;
            background:none; border:none; width:100%;
        }
        .back-link:hover { text-decoration:underline; }
        /* ─── reCAPTCHA badge helper ─── */
        .recaptcha-note { font-size:.68rem; color:var(--text-muted); text-align:center; margin-top:1rem; }
        .recaptcha-note a { color:var(--primary); }
        /* ─── Security indicators ─── */
        .security-badges {
            display:flex; gap:8px; justify-content:center; flex-wrap:wrap;
            margin-top:1.5rem; padding-top:1rem; border-top:1px solid #f3f4f6;
        }
        .badge {
            font-size:.68rem; color:var(--text-muted);
            display:flex; align-items:center; gap:3px;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="header">
        <div class="logo">GI</div>
        <h1>Bem-vindo de volta</h1>
        <p class="subtitle">Acesse sua conta para gerenciar a igreja</p>
    </div>

    <div id="alertBox" class="alert alert-error"></div>

    <!-- ── STEP 1: email + password ── -->
    <div id="step1">
        <form id="loginForm" novalidate>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" placeholder="seu@email.com" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" placeholder="••••••••" autocomplete="current-password" required>
                <div class="strength-bar-wrap" id="strengthBar" style="display:none">
                    <div class="strength-seg" id="s1"></div>
                    <div class="strength-seg" id="s2"></div>
                    <div class="strength-seg" id="s3"></div>
                    <div class="strength-seg" id="s4"></div>
                </div>
                <div class="strength-hint" id="strengthHint" style="display:none"></div>
            </div>
            <button type="submit" class="btn-primary" id="btnSubmit">
                <span id="btnText">Entrar</span>
                <div class="loader" id="loader"></div>
            </button>
        </form>
    </div>

    <!-- ── STEP 2: TOTP code ── -->
    <div id="step2">
        <div style="text-align:center; margin-bottom:1.5rem">
            <div style="font-size:2.5rem; margin-bottom:.5rem">🔐</div>
            <h2 style="font-size:1.1rem; color:var(--text); margin-bottom:.3rem">Autenticação de Dois Fatores</h2>
            <p class="subtitle">Digite o código de 6 dígitos do Google Authenticator</p>
        </div>
        <div class="form-group">
            <label for="totpCode">Código</label>
            <input type="text" id="totpCode" class="totp-input"
                   placeholder="000000" maxlength="6" inputmode="numeric"
                   pattern="[0-9]*" autocomplete="one-time-code">
        </div>
        <button class="btn-primary" id="btnTotp" onclick="verificarTotp()">
            <span id="btnTotpText">Verificar</span>
            <div class="loader" id="loaderTotp"></div>
        </button>
        <button class="back-link" onclick="voltarStep1()">← Voltar ao login</button>
    </div>

    <?php if ($hasRecaptcha): ?>
    <p class="recaptcha-note">
        Protegido por reCAPTCHA. <a href="https://policies.google.com/privacy" target="_blank">Privacidade</a> &amp;
        <a href="https://policies.google.com/terms" target="_blank">Termos</a>.
    </p>
    <?php endif; ?>

    <div class="security-badges">
        <span class="badge">🔒 Criptografia AES</span>
        <?php if ($hasRecaptcha): ?><span class="badge">🤖 reCAPTCHA v3</span><?php endif; ?>
        <span class="badge">🔑 2FA Disponível</span>
    </div>
</div>

<script>
const RECAPTCHA_SITE_KEY = <?= json_encode($hasRecaptcha ? $recaptchaSiteKey : '') ?>;
let tempToken2fa = null;

// ─── STEP 1 ────────────────────────────────────────────────
const loginForm  = document.getElementById('loginForm');
const btnSubmit  = document.getElementById('btnSubmit');
const btnText    = document.getElementById('btnText');
const loaderEl   = document.getElementById('loader');
const alertBox   = document.getElementById('alertBox');

function showAlert(msg, type='error'){
    alertBox.textContent = msg;
    alertBox.className = 'alert alert-' + type;
    alertBox.style.display = 'block';
}
function hideAlert(){ alertBox.style.display = 'none'; }
function setLoading(btn, loaderDiv, textEl, loading){
    btn.disabled = loading;
    loaderDiv.style.display = loading ? 'block' : 'none';
    textEl.style.display    = loading ? 'none'  : 'inline';
}

// Password strength indicator (only on focus)
document.getElementById('password').addEventListener('focus', () => {
    document.getElementById('strengthBar').style.display = 'flex';
    document.getElementById('strengthHint').style.display = 'block';
});
document.getElementById('password').addEventListener('input', () => {
    const v = document.getElementById('password').value;
    let score = 0;
    if(v.length >= 8)               score++;
    if(/[A-Z]/.test(v))             score++;
    if(/[0-9]/.test(v))             score++;
    if(/[^A-Za-z0-9]/.test(v))     score++;
    const labels = ['','Muito fraca','Fraca','Razoável','Forte'];
    const colors = ['','active-1','active-2','active-3','active-4'];
    [1,2,3,4].forEach(i => {
        const seg = document.getElementById('s'+i);
        seg.className = 'strength-seg' + (i <= score ? ' ' + colors[score] : '');
    });
    document.getElementById('strengthHint').textContent = score > 0 ? `Força: ${labels[score]}` : '';
    document.getElementById('strengthHint').style.color = score <= 1 ? '#ef4444' : score === 2 ? '#f97316' : score === 3 ? '#ca8a04' : '#16a34a';
});

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideAlert();
    setLoading(btnSubmit, loaderEl, btnText, true);

    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('password').value;

    try {
        let recaptcha_token = '';
        if (RECAPTCHA_SITE_KEY) {
            recaptcha_token = await grecaptcha.execute(RECAPTCHA_SITE_KEY, {action: 'login'});
        }

        const resp = await fetch('index.php?url=autenticar', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({email, senha, recaptcha_token})
        });
        const data = await resp.json();

        if (data.status === 'success') {
            window.location.href = 'index.php?url=dashboard';
        } else if (data.status === 'password_change_required') {
            window.location.href = 'index.php?url=alterar_senha_view';
        } else if (data.status === '2fa_required') {
            tempToken2fa = data.temp_token;
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            document.getElementById('totpCode').focus();
        } else {
            showAlert(data.message || 'Erro ao realizar login.');
        }
    } catch(err) {
        showAlert('Erro de conexão. Tente novamente.');
    } finally {
        setLoading(btnSubmit, loaderEl, btnText, false);
    }
});

// ─── STEP 2 ────────────────────────────────────────────────
async function verificarTotp() {
    const codigo = document.getElementById('totpCode').value.replace(/\D/g, '');
    if (codigo.length !== 6) { showAlert('Digite os 6 dígitos do código.'); return; }
    hideAlert();

    const btn  = document.getElementById('btnTotp');
    const load = document.getElementById('loaderTotp');
    const txt  = document.getElementById('btnTotpText');
    setLoading(btn, load, txt, true);

    try {
        const resp = await fetch('index.php?url=verificar2fa', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({temp_token: tempToken2fa, codigo})
        });
        const data = await resp.json();
        if (data.status === 'success') {
            window.location.href = 'index.php?url=dashboard';
        } else {
            showAlert(data.message || 'Código inválido.');
            document.getElementById('totpCode').value = '';
            document.getElementById('totpCode').focus();
        }
    } catch(err) {
        showAlert('Erro de conexão. Tente novamente.');
    } finally {
        setLoading(btn, load, txt, false);
    }
}

// Allow Enter key in TOTP field
document.getElementById('totpCode').addEventListener('keydown', e => {
    if (e.key === 'Enter') verificarTotp();
});

function voltarStep1() {
    tempToken2fa = null;
    document.getElementById('totpCode').value = '';
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
    hideAlert();
}
</script>
</body>
</html>
