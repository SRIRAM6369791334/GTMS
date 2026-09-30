<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in — GTMS Mining Statutory Portal</title>
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

  /* RIGHT SIDE — Form panel */
  .login-form-side {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fafbfe;
    padding: 2rem;
  }

  .form-label {
    font-size: .82rem;
    font-weight: 600;
    color: #333;
  }

  .form-control, .input-group-text {
    border-radius: 8px;
    padding: .6rem .8rem;
    border: 1px solid #dfe3ee;
  }
  .form-control:focus {
    border-color: var(--navy-2);
    box-shadow: 0 0 0 3px rgba(42,56,117,.12);
  }

  .input-group .form-control:focus {
    z-index: 3;
  }

  .btn-navy {
    background: var(--navy);
    color: #fff;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    transition: background .2s ease, transform .1s ease;
  }
  .btn-navy:hover {
    background: var(--navy-2);
    color: #fff;
  }
  .btn-navy:active {
    transform: scale(0.99);
  }

  @media (max-width: 767px) {
    .login-form-side {
      padding: 1.5rem;
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
      <h2 style="font-weight:800; max-width:460px; line-height:1.3; color:#ffffff;">Mining Lease Application, from intake to archive.</h2>
      <p style="color:#c3cdea; max-width:440px; font-size:.92rem;" class="mb-4">One centralized workspace for client intake, document checklists, MIMAS statutory registration, approvals and reporting.</p>
      
      <div class="login-feature text-white">
        <i class="bi bi-shield-check"></i>
        <div>
          <div class="lt">Statutory Compliance Workflow</div>
          <div class="ld">Government of Tamil Nadu Mining & Geology department regulatory alignment.</div>
        </div>
      </div>
      <div class="login-feature text-white">
        <i class="bi bi-geo-alt"></i>
        <div>
          <div class="lt">Statewide Concession Tracking</div>
          <div class="ld">DGPS & Drone surveys, Mining Plans, and Environmental Clearances in one place.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Login Form -->
  <div class="login-form-side">
    <div style="width:100%; max-width:360px;">
      <!-- Mobile Brand Header -->
      <div class="d-flex d-md-none align-items-center gap-3 mb-4">
        <img src="{{ asset('images/gtmslogo.png') }}" alt="GTMS Logo" style="width:48px; height:48px; object-fit:contain;">
        <div>
          <div style="font-weight:800; font-size:1.15rem; line-height:1.2; color:var(--navy);">GTMS</div>
          <div style="font-size:0.75rem; color:var(--muted);">District Mining Office Portal</div>
        </div>
      </div>

      <h4 style="font-weight:800; color:#1a1f36;" class="mb-1">Welcome back</h4>
      <p class="text-muted mb-3" style="font-size:.85rem;">Sign in to your District Mining Office account.</p>

      <!-- Flash Notifications -->
      @if (session('success'))
        <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-3" style="font-size: 0.85rem;" role="alert">
          <i class="bi bi-check-circle-fill me-2 flex-shrink-0 text-success"></i>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3" style="font-size: 0.85rem;" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0 text-danger"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @if (session('warning'))
        <div class="alert alert-warning d-flex align-items-center py-2 px-3 mb-3" style="font-size: 0.85rem;" role="alert">
          <i class="bi bi-exclamation-circle-fill me-2 flex-shrink-0 text-warning"></i>
          <div>{{ session('warning') }}</div>
        </div>
      @endif

      @if (session('status'))
        <div class="alert alert-info d-flex align-items-center py-2 px-3 mb-3" style="font-size: 0.85rem;" role="alert">
          <i class="bi bi-info-circle-fill me-2 flex-shrink-0 text-info"></i>
          <div>{{ session('status') }}</div>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.85rem;" role="alert">
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
          <label for="loginEmail" class="form-label">Email or Employee ID</label>
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted" id="emailAddon">
              <i class="bi bi-person"></i>
            </span>
            <input type="text" 
                   id="loginEmail" 
                   name="email" 
                   class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" 
                   placeholder="admin@gtms.com or LUK_001" 
                   required 
                   autofocus 
                   autocomplete="username"
                   aria-describedby="emailAddon">
          </div>
          @error('email')
            <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
          @enderror
        </div>

        <!-- Password Field with Show/Hide Eye Toggle -->
        <div class="mb-2">
          <label for="loginPassword" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted" id="passAddon">
              <i class="bi bi-lock"></i>
            </span>
            <input type="password" 
                   id="loginPassword" 
                   name="password" 
                   class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" 
                   placeholder="••••••••" 
                   required 
                   autocomplete="current-password"
                   aria-describedby="passAddon">
            <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" 
                    type="button" 
                    id="togglePasswordBtn" 
                    title="Toggle password visibility" 
                    style="border-color:#dfe3ee;">
              <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
          </div>
          @error('password')
            <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
          @enderror
        </div>

        <!-- Remember Me and Forgot Password Action -->
        <div class="d-flex justify-content-between align-items-center mb-3 pt-1">
          <div class="form-check">
            <input class="form-check-input" 
                   type="checkbox" 
                   name="remember" 
                   id="rememberMe" 
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="rememberMe" style="font-size: .8rem; color: #555;">
              Remember me
            </label>
          </div>
          <a href="javascript:void(0)" 
             class="text-decoration-none" 
             data-bs-toggle="modal" 
             data-bs-target="#forgotPasswordModal" 
             style="font-size:.78rem; font-weight:600; color:var(--navy);">
            Forgot password?
          </a>
        </div>

        <!-- Sign in Button -->
        <button type="submit" id="submitBtn" class="btn btn-navy w-100 py-2">
          Sign in <i class="bi bi-arrow-right ms-1"></i>
        </button>
      </form>

      <p class="text-center mt-4" style="font-size:.78rem; color:var(--muted);">
        Need access? Contact your District Mining Office administrator.
      </p>
    </div>
  </div>
</div>

<!-- Forgot Password Assistance Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
      <div class="modal-header py-3 px-4 border-0 pb-0">
        <h6 class="modal-title fw-bold" id="forgotPasswordModalLabel" style="color: var(--navy);">
          <i class="bi bi-shield-lock me-1 text-primary"></i> Password Reset
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center px-4 py-3">
        <div class="d-inline-flex p-3 rounded-circle bg-light text-primary mb-3">
          <i class="bi bi-key-fill fs-2"></i>
        </div>
        <p class="mb-2 text-dark" style="font-size: 0.95rem; font-weight: 500; line-height: 1.5;">
          Please contact your District Mining Office Administrator to reset your password or unlock your account.
        </p>
      </div>
      <div class="modal-footer border-0 pt-0 px-4 pb-3 justify-content-center">
        <button type="button" class="btn btn-navy px-4 py-1" data-bs-dismiss="modal" style="min-width: 100px;">OK</button>
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
