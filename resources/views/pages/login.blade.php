<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in — LeaseFlow</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
{{-- <link href="css/style.css" rel="stylesheet"> --}}
</head>

<style>
  /* Add any custom styles here */
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
}

.login-wrap {
  min-height: 100vh;
  display: flex;
}

/* LEFT SIDE — Butter UI gradient background */
.login-art {
  position: relative;
  flex: 3;
  overflow: hidden;
  display: flex;
  align-items: center;
  padding: 4rem;
  /* color: #fff; */
  background-image: url('images/background.png');
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

.form-control {
  border-radius: 8px;
  padding: .6rem .8rem;
  border: 1px solid #dfe3ee;
}
.form-control:focus {
  border-color: var(--navy-2);
  box-shadow: 0 0 0 3px rgba(42,56,117,.12);
}

.btn-navy {
  background: var(--navy);
  color: #fff;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  transition: background .2s ease;
}
.btn-navy:hover {
  background: var(--navy-2);
  color: #fff;
}

@media (max-width: 767px) {
  .login-form-side {
    padding: 1.5rem;
  }
}
</style>
<body>

<div class="login-wrap">
  <div class="login-art d-none d-md-flex">
    <div>
      <div class="d-flex align-items-center gap-2 mb-5">
        <img src="images/gtmslogo.png" alt="LeaseFlow" style="width:150px;">
      </div>
      <h2 style="font-weight:800; max-width:420px; line-height:1.3;color:#c3cdea">Mining Lease Application, from intake to archive.</h2>
      <p style="color:#c3cdea; max-width:420px; font-size:.9rem;">One workspace for client intake, document checklists, MIMAS registration, approvals and reporting.</p>
    </div>
  </div>

  <div class="login-form-side">
    <div style="width:100%; max-width:340px;">
      <div class="d-flex d-md-none align-items-center gap-2 mb-4">
        <span style="width:34px;height:34px;border-radius:8px;background:var(--orange); display:inline-flex; align-items:center; justify-content:center; color:#fff; font-weight:800;">LA</span>
        <span style="font-weight:700;">LeaseFlow</span>
      </div>
      <h4 style="font-weight:800;">Welcome back</h4>
      <p class="text-muted" style="font-size:.85rem;">Sign in to your District Mining Office account.</p>

      @if ($errors->any())
        <div class="alert alert-danger py-2" style="font-size: 0.85rem;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      @if (session('success'))
        <div class="alert alert-success py-2" style="font-size: 0.85rem;">
            {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Email or Employee ID</label>
          <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@gtms.com or LUK_001" required autofocus>
        </div>
        <div class="mb-2">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
            <label class="form-check-label" for="rememberMe" style="font-size: .8rem; color: #555;">
              Remember me
            </label>
          </div>
          <a href="#" style="font-size:.78rem; font-weight:600; color:var(--navy);">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-navy w-100 py-2">Sign in <i class="bi bi-arrow-right"></i></button>
      </form>

      <p class="text-center mt-4" style="font-size:.78rem; color:var(--muted);">Need access? Contact your District Mining Office administrator.</p>
    </div>
  </div>
</div>

</body>
</html>
