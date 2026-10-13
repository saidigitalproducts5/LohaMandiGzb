<?php
$base = rtrim(base_url(), '/');
$recaptchaSiteKey = (string) getenv('RECAPTCHA_SITE_KEY');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login via OTP — LOHA Mandi</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;background:linear-gradient(135deg,#1a1d23,#2a2f3a);min-height:100vh;display:flex;align-items:center;justify-content:center;color:#1a1d23;font-size:14px}
        .box{background:#fff;border-radius:16px;width:100%;max-width:420px;padding:26px 28px;box-shadow:0 24px 60px rgba(0,0,0,.35)}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px;margin-bottom:6px}
        .sub{color:#6b7280;font-size:13px;margin-bottom:18px}
        label{display:block;font-weight:600;font-size:13px;margin:12px 0 6px}
        input{width:100%;padding:11px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;outline:0}
        .btn{position:relative;width:100%;margin-top:18px;padding:12px;background:#e02735;color:#fff;border:0;border-radius:8px;font-weight:700;font-size:15px;cursor:pointer;transition:transform .08s ease,background .12s ease,box-shadow .08s ease;user-select:none;-webkit-user-select:none;touch-action:manipulation}
        .btn:hover:not(:disabled){background:#c92331}
        .btn.softpush{transform:scale(.97);background:#b81f2b;box-shadow:inset 0 3px 6px rgba(0,0,0,.25)}
        .btn:active{transform:scale(.97)}
        .btn:disabled{opacity:.6;cursor:not-allowed}
        .btn .spinner{display:none;width:14px;height:14px;margin-left:8px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;vertical-align:-2px;animation:spin .7s linear infinite}
        .btn.loading .spinner{display:inline-block}
        .btn.loading{opacity:.85}
        @keyframes spin{to{transform:rotate(360deg)}}
        .muted{color:#6b7280;font-size:13px;margin-top:12px}
        .small{font-size:12px;color:#9ca3af;margin-top:8px}
    </style>
</head>
<body>
<div class="box">
    <div class="brand"><span style="width:12px;height:12px;border-radius:3px;background:#e02735;display:inline-block;margin-right:8px"></span> LOHA Mandi</div>
    <div class="sub">Login via OTP (Firebase) — buyer / seller</div>

    <div id="stage-send">
        <label>Mobile Number</label>
        <input id="mobile" type="tel" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile" required autofocus>
        <div id="recaptcha-container"></div>
        <button id="sendOtp" class="btn" type="button"><span class="btn-label">Send OTP</span><span class="spinner" aria-hidden="true"></span></button>
        <div class="small">This uses Firebase phone authentication. Paste your Firebase config into this page where indicated.</div>
    </div>

    <div id="stage-verify" style="display:none;margin-top:8px">
        <label>Enter OTP</label>
        <input id="code" type="text" inputmode="numeric" maxlength="6" placeholder="6-digit code">
        <button id="verifyOtp" class="btn" type="button"><span class="btn-label">Verify & Sign In</span><span class="spinner" aria-hidden="true"></span></button>
    </div>

    <div id="msg" class="muted"></div>
</div>

<!-- Firebase SDK (compat) - edit versions if needed -->
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
<!-- reCAPTCHA Enterprise JS (only loaded when RECAPTCHA_SITE_KEY is set) -->
<?php if ($recaptchaSiteKey !== ''): ?>
<script src="https://www.google.com/recaptcha/enterprise.js?render=<?= esc($recaptchaSiteKey) ?>"></script>
<?php endif; ?>

<script>
// Firebase config is populated from server-side environment variables when available.
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

function el(id){return document.getElementById(id)}

// Soft-push visual and loading helpers
function makeSoftPushable(button){
    if (!button) return;
    // pointer events for desktop/mobile
    const add = () => button.classList.add('softpush');
    const rem = () => button.classList.remove('softpush');
    button.addEventListener('pointerdown', add);
    ['pointerup','pointercancel','pointerout','pointerleave'].forEach(evt => button.addEventListener(evt, rem));
    // keyboard feedback
    button.addEventListener('keydown', (e)=>{ if(e.key===' '||e.key==='Enter'){ button.classList.add('softpush'); } });
    button.addEventListener('keyup', ()=>button.classList.remove('softpush'));
}

function setLoading(button, loading){
    if (!button) return;
    if (loading){
        button.disabled = true;
        button.classList.add('loading');
        button.setAttribute('aria-busy','true');
    } else {
        button.disabled = false;
        button.classList.remove('loading');
        button.removeAttribute('aria-busy');
    }
}

if (!window.FIREBASE_CONFIG || Object.keys(window.FIREBASE_CONFIG).length === 0) {
    el('msg').textContent = 'Firebase config not provided. Paste it into this file to enable OTP.';
}

let auth = null;
let confirmationResult = null;
let verifier = null;

function initFirebase(){
    try{
        console.log('[OTP] Initializing Firebase with config:', window.FIREBASE_CONFIG);
        firebase.initializeApp(window.FIREBASE_CONFIG);
        auth = firebase.auth();
        console.log('[OTP] Firebase initialized successfully. Auth:', auth);
    }catch(e){
        console.error('[OTP] Firebase init failed', e);
        el('msg').textContent = 'Firebase init failed: ' + e.message;
    }
}

function showMsg(m){ el('msg').textContent = m; }

function attachHandlers(){
    if (window.FIREBASE_CONFIG && Object.keys(window.FIREBASE_CONFIG).length > 0) {
        if (!auth) initFirebase();
    }

    const sendBtn = el('sendOtp');
    if (sendBtn) {
        makeSoftPushable(sendBtn);
        sendBtn.addEventListener('click', onSendOtp);
    }

    const mobileInput = el('mobile');
    if (mobileInput) {
        mobileInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); onSendOtp(); }
        });
    }

    const verifyBtn = el('verifyOtp');
    if (verifyBtn) {
        makeSoftPushable(verifyBtn);
        verifyBtn.addEventListener('click', onVerifyOtp);
    }
}

let sending = false;
async function onSendOtp(){
    const sendBtn = el('sendOtp');
    if (!sendBtn || sending) return;
    sending = true;

    const m = el('mobile').value.trim();
    if (!/^[6-9][0-9]{9}$/.test(m)) {
        showMsg('Enter a valid 10-digit mobile');
        sending = false;
        return;
    }
    if (!auth) {
        showMsg(window.FIREBASE_CONFIG && Object.keys(window.FIREBASE_CONFIG).length ? 'Firebase not ready. Please retry.' : 'Firebase config missing. Add it to this page to enable OTP.');
        sending = false;
        return;
    }

    setLoading(sendBtn, true);
    showMsg('Preparing reCAPTCHA and sending OTP...');

    // Execute reCAPTCHA Enterprise to get a token for action 'LOGIN'.
    // Best-effort only: a missing/misconfigured Enterprise key must never
    // block the OTP send, so we race it against a 3s timeout.
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
    } catch (rcErr) {
        console.warn('[OTP] reCAPTCHA enterprise execute failed', rcErr);
    }

    // Invisible Firebase reCAPTCHA + phone auth
    try {
        // Build the verifier once and reuse it on retries — recreating it
        // after a failed attempt throws "reCAPTCHA has already been rendered".
        if (!verifier) {
            verifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', { size: 'invisible' });
        }
        // signInWithPhoneNumber() renders + runs the invisible reCAPTCHA
        // internally and then calls sendVerificationCode.
        confirmationResult = await auth.signInWithPhoneNumber('+91' + m, verifier);
        window.__recaptchaEnterpriseToken = recaptchaToken || '';
        showMsg('OTP sent. Enter the code.');
        el('stage-send').style.display = 'none';
        el('stage-verify').style.display = 'block';
        el('code').focus();
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
    const verifyBtn = el('verifyOtp');
    if (!verifyBtn || verifying) return;
    verifying = true;

    const code = el('code').value.trim();
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

// If DOM is already ready, attach immediately; otherwise wait for DOMContentLoaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    attachHandlers();
} else {
    document.addEventListener('DOMContentLoaded', attachHandlers);
}
</script>
</body>
</html>
