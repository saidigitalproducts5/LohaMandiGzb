<?php
/** Login — buyer/seller sign in: OTP or password. */
$base = rtrim(base_url(), '/');
$errors = $errors ?? [];
$error = session()->getFlashdata('error');
$recaptchaSiteKey = (string) getenv('RECAPTCHA_SITE_KEY');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — LOHA Mandi</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;background:linear-gradient(135deg,#1a1d23,#2a2f3a);min-height:100vh;display:flex;align-items:center;justify-content:center;color:#1a1d23;font-size:14px}
        .box{background:#fff;border-radius:16px;width:100%;max-width:420px;padding:26px 28px;box-shadow:0 24px 60px rgba(0,0,0,.35)}
        .brand{display:flex;align-items:center;gap:10px;margin-bottom:6px}
        .brand img{height:52px;width:auto}
        .sub{color:#6b7280;font-size:13px;margin-bottom:18px}
        label{display:block;font-weight:600;font-size:13px;margin:12px 0 6px}
        input{width:100%;padding:11px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;outline:0}
        input:focus{border-color:#e02735}
        .err{color:#b91c1c;font-size:12.5px;font-weight:600;margin-top:5px}
        .alert{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:8px;padding:11px 14px;margin-bottom:14px;font-weight:600;font-size:13px}
        .tabs{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:20px}
        .tab{border:1px solid #e5e7eb;border-radius:10px;padding:11px 8px;text-align:center;cursor:pointer;font-weight:700;font-size:13.5px;color:#4b5563;background:#fff;transition:.12s}
        .tab.active{border-color:#e02735;background:#fef2f2;color:#e02735}
        .panel{display:none}
        .panel.active{display:block}
        .btn{position:relative;width:100%;margin-top:18px;padding:12px;background:#e02735;color:#fff;border:0;border-radius:8px;font-weight:700;font-size:15px;cursor:pointer;transition:transform .08s ease,background .12s ease;user-select:none;-webkit-user-select:none;touch-action:manipulation}
        .btn:hover:not(:disabled){background:#c92331}
        .btn:active{transform:scale(.97)}
        .btn:disabled{opacity:.6;cursor:not-allowed}
        .btn .spinner{display:none;width:14px;height:14px;margin-left:8px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;vertical-align:-2px;animation:spin .7s linear infinite}
        .btn.loading .spinner{display:inline-block}
        @keyframes spin{to{transform:rotate(360deg)}}
        .muted{color:#6b7280;font-size:13px;margin-top:12px}
        .hint{font-size:12.5px;color:#6b7280;background:#f3f4f6;border-radius:8px;padding:10px 12px;margin-top:14px;line-height:1.5}
        .alt{text-align:center;margin-top:18px;font-size:13.5px;color:#6b7280}
        .alt a{color:#e02735;font-weight:700}
        .back{display:block;text-align:center;margin-top:14px;font-size:13px;color:#9ca3af}
        .back:hover{color:#e02735}
    </style>
</head>
<body>
<div class="box">
    <div class="brand"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi"></div>
    <div class="sub">Sign in to your buyer / seller account</div>

    <?php if ($error): ?><div class="alert"><?= esc($error) ?></div><?php endif; ?>

    <?php foreach (['info' => '#eff6ff|#1d4ed8|#bfdbfe', 'success' => '#ecfdf5|#047857|#a7f3d0', 'error' => '#fef2f2|#b91c1c|#fecaca'] as $fk => $fc):
        $fmsg = session()->getFlashdata('flash_' . $fk);
        if ($fmsg):
            [$fbg, $ffc, $fbd] = explode('|', $fc); ?>
            <div class="alert" style="background:<?= $fbg ?>;color:<?= $ffc ?>;border:1px solid <?= $fbd ?>"><?= esc($fmsg) ?></div>
        <?php endif;
    endforeach; ?>

    <div class="tabs">
        <div class="tab active" data-tab="otp" id="tabOtp">Login via OTP</div>
        <div class="tab" data-tab="pwd" id="tabPwd">Login with Password</div>
    </div>

    <!-- OTP panel -->
    <div class="panel active" id="panel-otp">
        <label>Mobile Number</label>
        <input id="otpMobile" type="tel" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile" required>
        <div id="recaptcha-container"></div>
        <button id="otpSend" class="btn" type="button"><span class="btn-label">Send OTP</span><span class="spinner" aria-hidden="true"></span></button>
        <div id="otpStage" style="display:none;margin-top:8px">
            <label>Enter OTP</label>
            <input id="otpCode" type="text" inputmode="numeric" maxlength="6" placeholder="6-digit code">
            <button id="otpVerify" class="btn" type="button"><span class="btn-label">Verify &amp; Sign In</span><span class="spinner" aria-hidden="true"></span></button>
        </div>
        <div id="otpMsg" class="muted"></div>
    </div>

    <!-- Password panel -->
    <div class="panel" id="panel-pwd">
        <form method="post" action="<?= esc($base) ?>/login">
            <label>Mobile Number</label>
            <input type="tel" name="mobile" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile" required>
            <?php if (isset($errors['mobile'])): ?><div class="err"><?= esc($errors['mobile']) ?></div><?php endif; ?>

            <label>Password</label>
            <input type="password" name="password" required>
            <?php if (isset($errors['password'])): ?><div class="err"><?= esc($errors['password']) ?></div><?php endif; ?>

            <button class="btn" type="submit">Sign In</button>
        </form>
    </div>

    <div class="hint">New here? Logging in via OTP with a new mobile number creates your buyer account automatically.</div>
    <div class="alt">New to Loha Mandi? <a href="<?= esc($base) ?>/register">Create an account</a></div>
    <a class="back" href="<?= esc($base) ?>/">← Back to marketplace</a>
</div>

<!-- Firebase SDK (compat) -->
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
<?php if ($recaptchaSiteKey !== ''): ?>
<script src="https://www.google.com/recaptcha/enterprise.js?render=<?= esc($recaptchaSiteKey) ?>"></script>
<?php endif; ?>

<script>
<?php
    $fb = [
        'apiKey' => getenv('FIREBASE_API_KEY') ?: '',
        'authDomain' => getenv('FIREBASE_AUTH_DOMAIN') ?: '',
        'projectId' => getenv('FIREBASE_PROJECT_ID') ?: '',
        'storageBucket' => getenv('FIREBASE_STORAGE_BUCKET') ?: '',
        'messagingSenderId' => getenv('FIREBASE_MESSAGING_SENDER_ID') ?: '',
        'appId' => getenv('FIREBASE_APP_ID') ?: '',
        'measurementId' => getenv('FIREBASE_MEASUREMENT_ID') ?: '',
    ];
?>
window.FIREBASE_CONFIG = <?= json_encode($fb, JSON_UNESCAPED_SLASHES) ?>;
const RECAPTCHA_SITE_KEY = <?= json_encode($recaptchaSiteKey) ?>;

function $(id){return document.getElementById(id)}

// Tab switching
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        $('panel-' + tab.dataset.tab).classList.add('active');
    });
});

function setLoading(button, loading){
    if (!button) return;
    if (loading){ button.disabled = true; button.classList.add('loading'); button.setAttribute('aria-busy','true'); }
    else { button.disabled = false; button.classList.remove('loading'); button.removeAttribute('aria-busy'); }
}
function showMsg(m){ $('otpMsg').textContent = m; }

if (!window.FIREBASE_CONFIG || Object.keys(window.FIREBASE_CONFIG).length === 0) {
    showMsg('Firebase config not provided. OTP login unavailable.');
}

let auth = null;
let confirmationResult = null;
let verifier = null;

function initFirebase(){
    try{
        console.log('[OTP] Initializing Firebase with config:', window.FIREBASE_CONFIG);
        firebase.initializeApp(window.FIREBASE_CONFIG);
        auth = firebase.auth();
        console.log('[OTP] Firebase initialized successfully.');
    }catch(e){
        console.error('[OTP] Firebase init failed', e);
        showMsg('Firebase init failed: ' + e.message);
    }
}

let sending = false;
async function onSendOtp(){
    const sendBtn = $('otpSend');
    if (!sendBtn || sending) return;
    sending = true;

    const m = $('otpMobile').value.trim();
    if (!/^[6-9][0-9]{9}$/.test(m)) { showMsg('Enter a valid 10-digit mobile'); sending = false; return; }
    if (!auth) {
        showMsg(window.FIREBASE_CONFIG && Object.keys(window.FIREBASE_CONFIG).length ? 'Firebase not ready. Please retry.' : 'Firebase config missing. Add it to this page to enable OTP.');
        sending = false;
        return;
    }

    setLoading(sendBtn, true);
    showMsg('Preparing reCAPTCHA and sending OTP...');

    let recaptchaToken = '';
    try {
        if (RECAPTCHA_SITE_KEY && window.grecaptcha && window.grecaptcha.enterprise && typeof window.grecaptcha.enterprise.execute === 'function') {
            const tokenPromise = grecaptcha.enterprise.ready()
                .then(() => grecaptcha.enterprise.execute(RECAPTCHA_SITE_KEY, { action: 'LOGIN' }))
                .catch(() => '');
            recaptchaToken = await Promise.race([
                tokenPromise,
                new Promise((res) => setTimeout(() => res(''), 3000)),
            ]);
        }
    } catch (rcErr) { console.warn('[OTP] reCAPTCHA enterprise execute failed', rcErr); }

    try {
        if (!verifier) {
            verifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', { size: 'invisible' });
        }
        confirmationResult = await auth.signInWithPhoneNumber('+91' + m, verifier);
        window.__recaptchaEnterpriseToken = recaptchaToken || '';
        showMsg('OTP sent. Enter the code.');
        $('otpStage').style.display = 'block';
        $('otpCode').focus();
    } catch (err) {
        console.error('[OTP] Failed to send OTP:', err);
        showMsg('Failed to send OTP: ' + (err.message || err));
    } finally {
        setLoading(sendBtn, false);
        sending = false;
    }
}

let verifying = false;
async function onVerifyOtp(){
    const verifyBtn = $('otpVerify');
    if (!verifyBtn || verifying) return;
    verifying = true;

    const code = $('otpCode').value.trim();
    if (!/^[0-9]{6}$/.test(code)) { showMsg('Enter the 6-digit code'); verifying = false; return; }
    if (!confirmationResult) { showMsg('No OTP request found'); verifying = false; return; }

    setLoading(verifyBtn, true);
    showMsg('Verifying...');
    try {
        const result = await confirmationResult.confirm(code);
        showMsg('Verification succeeded. Retrieving ID token...');
        try {
            const idToken = await result.user.getIdToken();
            const params = new URLSearchParams();
            params.append('idToken', idToken);
            if (window.__recaptchaEnterpriseToken) params.append('recaptcha_token', window.__recaptchaEnterpriseToken);

            const res = await fetch('<?= esc($base) ?>/login/firebase', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params.toString(),
            });
            const j = await res.json();
            if (res.ok && j.ok) {
                showMsg('Login successful. Redirecting...');
                window.location = j.redirect || '<?= esc($base) ?>/account';
            } else {
                showMsg(j.error || j.message || 'Server login failed.');
            }
        } catch (e) {
            console.error(e);
            showMsg('Failed to get ID token or login: ' + (e.message || e));
        }
    } catch (err) {
        console.error(err);
        showMsg('OTP verification failed: ' + (err.message || err));
    } finally {
        setLoading(verifyBtn, false);
        verifying = false;
    }
}

// Enter-key shortcuts
$('otpMobile').addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); onSendOtp(); } });
$('otpCode').addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); onVerifyOtp(); } });
$('otpSend').addEventListener('click', onSendOtp);
$('otpVerify').addEventListener('click', onVerifyOtp);

if (window.FIREBASE_CONFIG && Object.keys(window.FIREBASE_CONFIG).length > 0) initFirebase();
</script>
</body>
</html>
