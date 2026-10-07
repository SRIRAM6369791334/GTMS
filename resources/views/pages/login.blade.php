<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign In — GTMS Portal</title>
<link rel="shortcut icon" type="image/png" href="{{ asset('images/gtmslogo.png') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
  :root {
    --navy: #1b2653;
    --navy-2: #2a3875;
    --orange: #f28c28;
    --muted: #8b93a7;
  }

  * { box-sizing: border-box; }

  body {
    font-family: 'Inter', sans-serif;
    margin: 0;
    background-color: #fafbfe;
  }

  .login-wrap {
    min-height: 100vh;
    display: flex;
  }

  /* LEFT SIDE — Gradient background & hero illustration */
  .login-art {
    position: relative;
    flex: 3;
    overflow: hidden;
    display: flex;
    align-items: center;
    padding: 4rem;
    background-image: url('{{ asset('images/background.png') }}');
    background-position: center;
    background-size: cover;
  }

  .login-art::before,
  .login-art::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    filter: blur(90px);
    opacity: 0.55;
    z-index: 0;
    pointer-events: none;
  }

  .login-art::before {
    width: 480px;
    height: 480px;
    top: -120px;
    left: -100px;
    background: radial-gradient(circle at 30% 30%, #ffb35c, transparent 70%);
  }

  .login-art::after {
    width: 420px;
    height: 420px;
    bottom: -140px;
    right: -80px;
    background: radial-gradient(circle at 70% 70%, #6d7fe0, transparent 70%);
  }

  .login-art > div {
    position: relative;
    z-index: 1;
  }

  .login-feature {
    display: flex;
    gap: .75rem;
    align-items: flex-start;
    margin-bottom: 1.25rem;
  }
  .login-feature i {
    font-size: 1.1rem;
    background: rgba(255,255,255,.12);
    padding: 8px;
    border-radius: 8px;
  }
  .login-feature .lt {
    font-weight: 600;
    font-size: .85rem;
  }
  .login-feature .ld {
    font-size: .75rem;
    color: #c3cdea;
  }

  /* RIGHT SIDE — Modern Enterprise SaaS Form Panel */
  .login-form-side {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top right, #f1f5f9 0%, #f8fafc 100%);
    padding: 2.5rem 1.5rem;
    position: relative;
  }

  .login-card {
    width: 100%;
    max-width: 430px;
    background: #ffffff;
    border-radius: 16px;
    padding: 2.25rem 2.25rem;
    border: 1px solid #e2e8f0;
    box-shadow: 
      0 1px 2px 0 rgba(15, 23, 42, 0.04),
      0 12px 24px -4px rgba(15, 23, 42, 0.06),
      0 24px 38px -8px rgba(15, 23, 42, 0.04);
    position: relative;
    z-index: 2;
  }

  /* Portal Badge */
  .portal-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: #eff4fe;
    color: var(--navy);
    padding: 0.35rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    border: 1px solid #dbe6fe;
  }

  .badge-pulse {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #10b981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
  }

  /* Form Labels & Micro-hints */
  .auth-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.45rem;
  }

  .auth-label .label-title {
    font-size: 0.82rem;
    font-weight: 600;
    color: #1e293b;
    letter-spacing: -0.01em;
  }

  .auth-label .label-hint {
    font-size: 0.74rem;
    color: #94a3b8;
    font-weight: 500;
  }

  /* Unified Auth Input Group */
  .auth-input-group {
    position: relative;
    display: flex;
    align-items: center;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .auth-input-group:hover {
    border-color: #cbd5e1;
    background: #ffffff;
  }

  .auth-input-group:focus-within {
    background: #ffffff;
    border-color: var(--navy);
    box-shadow: 0 0 0 3.5px rgba(27, 38, 83, 0.12);
  }

  .auth-input-group.is-invalid {
    border-color: #ef4444;
    background: #fff5f5;
  }

  .auth-input-group.is-invalid:focus-within {
    box-shadow: 0 0 0 3.5px rgba(239, 68, 68, 0.15);
  }

  .auth-input-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    padding-left: 14px;
    padding-right: 2px;
    color: #94a3b8;
    font-size: 1.05rem;
    transition: color 0.2s ease;
    user-select: none;
  }

  .auth-input-group:focus-within .auth-input-icon {
    color: var(--navy);
  }

  .auth-input-group .form-control {
    border: none !important;
    background: transparent !important;
    box-shadow: none !important;
    padding: 0.68rem 0.85rem 0.68rem 0.45rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #0f172a;
  }

  .auth-input-group .form-control::placeholder {
    color: #94a3b8;
    font-weight: 400;
  }

  .btn-pw-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: #94a3b8;
    padding: 0 14px;
    font-size: 1.05rem;
    cursor: pointer;
    border-radius: 6px;
    transition: color 0.2s ease, background 0.15s ease;
  }

  .btn-pw-toggle:hover {
    color: var(--navy);
    background: rgba(15, 23, 42, 0.04);
  }

  /* Custom Checkbox */
  .form-check-input {
    border-color: #cbd5e1;
    cursor: pointer;
  }

  .form-check-input:checked {
    background-color: var(--navy);
    border-color: var(--navy);
  }

  .form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(27, 38, 83, 0.12);
  }

  .form-check-label {
    cursor: pointer;
    user-select: none;
    font-size: 0.82rem;
    color: #475569;
    font-weight: 500;
  }

  /* Action Links */
  .auth-forgot-link {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--navy);
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .auth-forgot-link:hover {
    color: #2a3875;
    text-decoration: underline;
  }

  /* Primary Button */
  .btn-auth-submit {
    background: linear-gradient(180deg, #24336e 0%, #1b2653 100%);
    color: #ffffff;
    font-weight: 600;
    font-size: 0.92rem;
    letter-spacing: 0.01em;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 10px;
    padding: 0.72rem 1.25rem;
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 
      inset 0 1px 0 rgba(255, 255, 255, 0.18),
      0 1px 2px 0 rgba(15, 23, 42, 0.08),
      0 6px 14px -2px rgba(27, 38, 83, 0.28);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .btn-auth-submit i {
    transition: transform 0.2s ease;
  }

  .btn-auth-submit:hover {
    background: linear-gradient(180deg, #2a3d85 0%, #1e2c66 100%);
    color: #ffffff;
    box-shadow: 
      inset 0 1px 0 rgba(255, 255, 255, 0.22),
      0 4px 8px 0 rgba(15, 23, 42, 0.1),
      0 10px 20px -3px rgba(27, 38, 83, 0.35);
    transform: translateY(-1px);
  }

  .btn-auth-submit:hover i {
    transform: translateX(3px);
  }

  .btn-auth-submit:active {
    transform: translateY(0);
    box-shadow: 
      inset 0 1px 2px rgba(0, 0, 0, 0.2),
      0 2px 4px 0 rgba(15, 23, 42, 0.06);
  }

  /* Security Trust & Help Footer */
  .auth-trust-footer {
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px dashed #e2e8f0;
    text-align: center;
  }

  .trust-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.76rem;
    color: #64748b;
    font-weight: 500;
  }

  .auth-help-text {
    font-size: 0.77rem;
    color: #94a3b8;
    margin-top: 0.5rem;
    margin-bottom: 0;
  }

  .auth-help-link {
    color: var(--navy);
    font-weight: 600;
    text-decoration: none;
  }

  .auth-help-link:hover {
    text-decoration: underline;
  }

  @media (max-width: 767px) {
    .login-form-side {
      padding: 1.5rem 1rem;
    }
    .login-card {
      padding: 1.75rem 1.25rem;
      border: none;
      box-shadow: none;
      background: transparent;
    }
  }
</style>
</head>
<body>

<div class="login-wrap">
  <!-- Left Side: Hero / Brand Panel -->
  <div class="login-art d-none d-md-flex">
    <div>
      <div class="d-flex align-items-center gap-2 mb-4">
        <img src="{{ asset('images/gtmslogo.png') }}" alt="GTMS Logo" style="width:140px; height:auto;">
      </div>
      <h2 style="font-weight:800; max-width:460px; line-height:1.3; color:#ffffff;">Mining applications made simple.</h2>
      <p style="color:#c3cdea; max-width:440px; font-size:.92rem;" class="mb-4">Manage applications, documents, approvals, and reports all in one place.</p>
      
      <div class="login-feature text-white">
        <i class="bi bi-shield-check"></i>
        <div>
          <div class="lt">Guided Process</div>
          <div class="ld">Step-by-step guidance for fast and accurate submissions.</div>
        </div>
      </div>
      <div class="login-feature text-white">
        <i class="bi bi-geo-alt"></i>
        <div>
          <div class="lt">All-in-One Tracking</div>
          <div class="ld">Track surveys, mining plans, and clearances easily.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Login Form -->
  <div class="login-form-side">
    <div class="login-card">
      <!-- Mobile Brand Header -->
      <div class="d-flex d-md-none align-items-center gap-3 mb-4">
        <img src="{{ asset('images/gtmslogo.png') }}" alt="GTMS Logo" style="width:44px; height:44px; object-fit:contain;">
        <div>
          <div style="font-weight:800; font-size:1.15rem; line-height:1.2; color:var(--navy);">GTMS</div>
          <div style="font-size:0.75rem; color:#64748b;">Mining Portal</div>
        </div>
      </div>

      <!-- Portal Status Badge -->
      <div class="mb-3">
        <span class="portal-badge">
          <span class="badge-pulse"></span>
          <span>GTMS Portal</span>
        </span>
      </div>

      <!-- Heading -->
      <h3 style="font-weight:800; color:#0f172a; font-size:1.55rem; letter-spacing:-0.025em;" class="mb-1">Sign In</h3>
      <p class="text-muted mb-4" style="font-size:0.86rem; line-height:1.45;">Please enter your details to sign in.</p>

      <!-- Flash Notifications -->
      @if (session('success'))
        <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-3 border-0 rounded-3 shadow-sm" style="font-size: 0.84rem; background: #ecfdf5; color: #065f46;" role="alert">
          <i class="bi bi-check-circle-fill me-2 flex-shrink-0 text-success"></i>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3 border-0 rounded-3 shadow-sm" style="font-size: 0.84rem; background: #fef2f2; color: #991b1b;" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0 text-danger"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @if (session('warning'))
        <div class="alert alert-warning d-flex align-items-center py-2 px-3 mb-3 border-0 rounded-3 shadow-sm" style="font-size: 0.84rem; background: #fffbeb; color: #92400e;" role="alert">
          <i class="bi bi-exclamation-circle-fill me-2 flex-shrink-0 text-warning"></i>
          <div>{{ session('warning') }}</div>
        </div>
      @endif

      @if (session('status'))
        <div class="alert alert-info d-flex align-items-center py-2 px-3 mb-3 border-0 rounded-3 shadow-sm" style="font-size: 0.84rem; background: #eff6ff; color: #1e40af;" role="alert">
          <i class="bi bi-info-circle-fill me-2 flex-shrink-0 text-info"></i>
          <div>{{ session('status') }}</div>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3 border-0 rounded-3 shadow-sm" style="font-size: 0.84rem; background: #fef2f2; color: #991b1b;" role="alert">
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}" id="loginForm">
        @csrf
        
        <!-- Identifier Field (Email or User Code) -->
        <div class="mb-3">
          <label for="loginEmail" class="form-label d-block mb-1" style="font-size: 0.82rem; font-weight: 600; color: #1e293b;">
            Email or Employee ID
          </label>
          <div class="auth-input-group @error('email') is-invalid @enderror">
            <span class="auth-input-icon">
              <i class="bi bi-person"></i>
            </span>
            <input type="text" 
                   id="loginEmail" 
                   name="email" 
                   class="form-control" 
                   value="{{ old('email') }}" 
                   placeholder="Enter your email or ID" 
                   required 
                   autofocus 
                   autocomplete="username">
          </div>
          @error('email')
            <div class="text-danger mt-1" style="font-size: 0.78rem; font-weight: 500;">
              <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
            </div>
          @enderror
        </div>

        <!-- Password Field with Show/Hide Eye Toggle -->
        <div class="mb-3">
          <label for="loginPassword" class="form-label d-block mb-1" style="font-size: 0.82rem; font-weight: 600; color: #1e293b;">
            Password
          </label>
          <div class="auth-input-group @error('password') is-invalid @enderror">
            <span class="auth-input-icon">
              <i class="bi bi-lock"></i>
            </span>
            <input type="password" 
                   id="loginPassword" 
                   name="password" 
                   class="form-control" 
                   placeholder="Enter your password" 
                   required 
                   autocomplete="current-password">
            <button class="btn-pw-toggle" 
                    type="button" 
                    id="togglePasswordBtn" 
                    title="Toggle password visibility" 
                    tabindex="-1">
              <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
          </div>
          @error('password')
            <div class="text-danger mt-1" style="font-size: 0.78rem; font-weight: 500;">
              <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
            </div>
          @enderror
        </div>

        <!-- Remember Me and Forgot Password Action -->
        <div class="d-flex justify-content-between align-items-center mb-4 pt-1">
          <div class="form-check">
            <input class="form-check-input" 
                   type="checkbox" 
                   name="remember" 
                   id="rememberMe" 
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="rememberMe">
              Remember me
            </label>
          </div>
          <a href="javascript:void(0)" 
             class="auth-forgot-link" 
             data-bs-toggle="modal" 
             data-bs-target="#forgotPasswordModal">
            Forgot password?
          </a>
        </div>

        <!-- Sign in Button -->
        <button type="submit" id="submitBtn" class="btn-auth-submit">
          <span>Sign In</span>
          <i class="bi bi-arrow-right"></i>
        </button>
      </form>

      <!-- Security & Assistance Trust Footer -->
      <div class="auth-trust-footer">
        <div class="trust-indicator">
          <i class="bi bi-shield-check text-success"></i>
          <span>Protected & Secure Connection</span>
        </div>
        <p class="auth-help-text">
          Need help? <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" class="auth-help-link">Contact Admin</a>
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Forgot Password Assistance Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
      <div class="modal-header py-3 px-4 border-0 pb-0">
        <h6 class="modal-title fw-bold" id="forgotPasswordModalLabel" style="color: var(--navy);">
          <i class="bi bi-shield-lock me-1 text-primary"></i> Forgot Password
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center px-4 py-3">
        <div class="d-inline-flex p-3 rounded-circle bg-light text-primary mb-3">
          <i class="bi bi-key-fill fs-2"></i>
        </div>
        <p class="mb-2 text-dark" style="font-size: 0.95rem; font-weight: 500; line-height: 1.5;">
          Please contact your administrator to reset your password.
        </p>
      </div>
      <div class="modal-footer border-0 pt-0 px-4 pb-3 justify-content-center">
        <button type="button" class="btn btn-auth-submit px-4 py-2" data-bs-dismiss="modal" style="width: auto; min-width: 120px;">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap 5.3.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Interactive Scripts: Eye Toggle & Submit Protection -->
<script>
  // Show / Hide Password Eye Toggle
  document.getElementById('togglePasswordBtn')?.addEventListener('click', function () {
    const passwordInput = document.getElementById('loginPassword');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      toggleIcon.classList.remove('bi-eye');
      toggleIcon.classList.add('bi-eye-slash');
    } else {
      passwordInput.type = 'password';
      toggleIcon.classList.remove('bi-eye-slash');
      toggleIcon.classList.add('bi-eye');
    }
    passwordInput.focus();
  });

  // Submit button double-click safeguard & loading spinner
  document.getElementById('loginForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Signing in...';
    }
  });
</script>

</body>
</html>
