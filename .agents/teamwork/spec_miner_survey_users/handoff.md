# Specification Mining & Architectural Audit Report: User Lifecycle & Dual RBAC

**Author:** `spec_miner_survey_users`  
**Milestone:** M1 — Architectural Audit & Survey  
**Date:** 2026-09-28  
**Target:** GTMS User Management, Authentication, and Dual Role-Based Access Control (R3 Compliance)  
**Deliverable Path:** `C:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_users\handoff.md`

---

## Features Discovered

| # | Category | Feature | Description | Inputs | Outputs | Error Behavior | Discovered Via |
|---|----------|---------|-------------|--------|---------|----------------|----------------|
| 1 | Auth | Guest Login View | Displays login view or redirects authenticated user to dashboard | `GET /` or `GET /login` | HTTP 200 `view('pages.login')` OR HTTP 302 redirect to `/dashboard` | None | `routes/web.php:27-28`, `AuthController.php:11-17` |
| 2 | Auth | Dual-Identifier Login | Authenticates user via either canonical email (e.g. `admin@gtms.com`) or employee `user_code` (e.g. `LUK_001`) | `POST /login` with `email` (string), `password` (string), `remember` (boolean optional) | HTTP 302 redirect to intended `/dashboard` with regenerated session | 302 back with error `'No account found with this email or User ID.'` (missing user), `'Your account is inactive. Please contact administrator.'` (status != 1), or `'Incorrect password entered.'` (bad pass) | `routes/web.php:29`, `AuthController.php:19-53` |
| 3 | Auth | Remember Me Authentication | Establishes persistent login cookie when remember checkbox is checked | `POST /login` with `remember=1` or `remember="on"` | Persistent auth cookie, `remember_token` preserved | Standard credential failure redirects back | `AuthController.php:26, 45`, `login.blade.php:196` |
| 4 | Auth | User Logout | Terminates session, invalidates token, regenerates CSRF token, and redirects to root | `POST /logout` (Auth required, CSRF token) | HTTP 302 redirect to `/` with flash message `'Logged out successfully.'` | 419 Page Expired if CSRF invalid, 302 to login if unauthenticated | `routes/web.php:36`, `AuthController.php:55-62` |
| 5 | User Admin | User Directory View | Renders user directory with roles, branches, and permission metrics | `GET /user` (Auth required, permission `users.view`) | HTTP 200 `view('pages.authentication.users.index')` with `$users`, `$role`, `$branch` | 401/302 to `/login` if guest; 403 Forbidden if user lacks `users.view` | `routes/web.php:137`, `UserController.php:13-19` |
| 6 | User Admin | User Provisioning & Auto `user_code` | Creates user record and automatically derives `user_code` using pattern `'LUK_' . str_pad($id, 3, '0', STR_PAD_LEFT)` | `POST /useradd` (Auth required, permission `users.create`): `name`, `email`, `password`, `role_id`, `branch_id`, `image`, `mobile_num`, `status` | HTTP 200 JSON `{ status: 1, message: "User Added Successfully", data: User }` | 422 JSON validation errors (name/email/password/role/branch required, email unique, password min:6, image mime/size) | `routes/web.php:139`, `UserController.php:21-71` |
| 7 | User Admin | Dual Role Synchronization (Create) | Synchronizes direct relational column `users.role_id` and Spatie pivot table `model_has_roles` on user creation | `POST /useradd` with `role_id` | `users.role_id` set to `$request->role_id`; `model_has_roles` synced with `$role->name` | 422 error if `role_id` does not exist in `roles` table | `UserController.php:36, 59-62` |
| 8 | User Admin | Avatar File Upload (Create) | Uploads profile image to `public/uploads/users/` with timestamped filename | `POST /useradd` with multipart `image` file | Image moved to `public/uploads/users/{timestamp}.ext`, stored in `users.image` | 422 validation failure if mime not in jpeg,png,jpg,gif,webp or size > 2048 KB | `UserController.php:29, 44-49` |
| 9 | User Admin | User Update & Dual Role Sync | Updates user details and synchronizes both `users.role_id` and Spatie `model_has_roles` | `POST /useredit` (Auth required, permission `users.edit`): `id`, `name`, `email`, `role_id`, `branch_id`, optional `password`, `status`, `mobile_num` | HTTP 200 JSON `{ status: 1, message: "User Updated Successfully", data: User }` | 422 JSON validation error if email exists on another user or fields invalid | `routes/web.php:140`, `UserController.php:73-125` |
| 10 | User Admin | Avatar Replacement & Old File Unlink | Uploads replacement avatar and automatically deletes existing file from disk | `POST /useredit` with new `image` file | Old avatar file deleted via `unlink()`; new image saved to `public/uploads/users/` | None; ignores unlink if file does not exist on disk | `UserController.php:100-108` |
| 11 | User Admin | Self-Deletion Prevention Guard | Prevents the currently authenticated user from deleting their own user account | `POST /userdelete` with `id == Auth::id()` | HTTP 200 JSON `{ status: 0, message: "You cannot delete your own account." }` | Soft failure response with status 0, operation rejected | `routes/web.php:141`, `UserController.php:135-140` |
| 12 | User Admin | Last Admin Deletion Guard | Blocks deletion of an Admin user if only one user possesses the `Admin` role | `POST /userdelete` with user having `Admin` role when `User::role('Admin')->count() <= 1` | HTTP 200 JSON `{ status: 0, message: "The last Admin account cannot be deleted." }` | Soft failure response with status 0, operation rejected | `UserController.php:142-147` |
| 13 | User Admin | User Deletion & Avatar Cleanup | Permanently deletes user record from database and removes avatar file from disk | `POST /userdelete` with valid non-self, non-last-admin user `id` | HTTP 200 JSON `{ status: 1, message: "User Deleted Successfully" }`; avatar unlinked from disk | 422/404 if `id` does not exist | `UserController.php:149-160` |
| 14 | Security | Gate Super-Admin Bypass | Grants users with `'Admin'` or `'Super Admin'` roles unrestricted bypass on all permission checks | Authenticated user has role `Admin` or `Super Admin` | Gate checks return `true` before checking individual permissions | Returns `null` if user does not have those roles, falling back to Spatie checks | `AppServiceProvider.php:26-28` |
| 15 | Multi-Tenancy | BranchScope Multi-Tenancy | Automatically scopes queries on branch-aware models to `$user->branch_id` unless user is an Admin (`role_id === 1` or has role `Admin`/`Super Admin`) | Non-admin user querying models with `BelongsToBranch` trait | Adds SQL `WHERE {table}.branch_id = ?` to queries | Admin users bypass the scope and see all records statewide | `app/Models/Scopes/BranchScope.php:10-25` |

---

## Edge Cases

| # | Feature | Input | Observed Behavior |
|---|---------|-------|-------------------|
| 1 | Dual Login | Input string contains email syntax with whitespace e.g. `" admin@gtms.com "` | Exact query `User::where('email', ...)->orWhere('user_code', ...)` does not auto-trim; returns 302 with `'No account found with this email or User ID.'` |
| 2 | Dual Login | Identifier matches valid user, but `users.status = 0` | Returns 302 with `'Your account is inactive. Please contact administrator.'` on key `'email'`. Password check is never executed, preventing timing attacks on inactive accounts. |
| 3 | Dual Login | Identifier matches valid user, but password does not match | Resolves `$user->email`, runs `Auth::attempt(['email' => $user->email, 'password' => $credentials['password']])`, fails, and returns 302 with `'Incorrect password entered.'` on key `'password'` (preserving `'email'`). |
| 4 | Dual Login | Inputting `user_code` in lowercase (e.g. `luk_001` vs `LUK_001`) | In MySQL (default utf8mb4_unicode_ci / collation ci), lookup is case-insensitive, so `luk_001` resolves `LUK_001`. In strict SQLite/PostgreSQL binary collations, case matching would fail. |
| 5 | Provisioning | Provisioning user with ID reaching 4 or more digits (e.g. `id = 1000`) | Pattern `'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT)` does not truncate length >= 3; produces `LUK_1000` without throwing error. |
| 6 | Provisioning | Image upload with valid image extension but invalid MIME | Validator `'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'` fails at Laravel's `image` rule, returning 422 JSON validation error. |
| 7 | User Update | Updating user without providing a new password | If `$request->filled('password')` evaluates to false, existing `password` and `show_password` fields remain untouched. |
| 8 | User Update | Updating user and keeping identical email | Validator `'email' => 'required|email|unique:users,email,' . $request->id` explicitly ignores the target user ID, avoiding false duplicate validation errors. |
| 9 | User Deletion | Self-deletion attempt by Admin | `if (auth()->id() == $user->id)` catches it before last-admin check; returns `{ status: 0, message: "You cannot delete your own account." }`. |
| 10 | User Deletion | Deleting the last Admin account | When only one user has role `Admin`, `User::role('Admin')->count() <= 1` evaluates to true; returns `{ status: 0, message: "The last Admin account cannot be deleted." }`. |
| 11 | User Deletion | User record has `image` file registered in database, but file is missing on disk | `if ($user->image && file_exists(public_path('uploads/users/' . $user->image)))` safely guards `unlink()`; skips unlink and proceeds to `$user->delete()`. |
| 12 | Dual RBAC Sync | Updating user with a `role_id` that exists in `roles` table | `$user->role_id` is updated, and `$user->syncRoles([$role->name])` replaces existing roles in Spatie's `model_has_roles`, ensuring single active Spatie role matches `users.role_id`. |

---

## 1. Observation

### 1.1 Complete Catalog of User and Auth Routes

Directly verified in `routes/web.php` (lines 25-36 and lines 135-142):

```php
// Guest Authentication Routes (routes/web.php:26-31)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated System Routes (routes/web.php:34-36)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User routes (routes/web.php:135-142)
    Route::middleware('permission:users.view')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
    });
    Route::post('useradd', [UserController::class, 'store'])->name('useradd')->middleware('permission:users.create');
    Route::post('useredit', [UserController::class, 'update'])->name('useredit')->middleware('permission:users.edit');
    Route::post('userdelete', [UserController::class, 'destroy'])->name('userdelete')->middleware('permission:users.delete');
});
```

| HTTP Verb | URI Path | Route Name | Controller Action | Middleware Pipeline |
|-----------|----------|------------|-------------------|---------------------|
| `GET` | `/` | `login` | `App\Http\Controllers\AuthController@showLogin` | `['web', 'guest']` |
| `GET` | `/login` | *(unnamed)* | `App\Http\Controllers\AuthController@showLogin` | `['web', 'guest']` |
| `POST` | `/login` | `login.post` | `App\Http\Controllers\AuthController@login` | `['web', 'guest']` |
| `POST` | `/logout` | `logout` | `App\Http\Controllers\AuthController@logout` | `['web', 'auth']` |
| `GET` | `/user` | `user.index` | `App\Http\Controllers\UserController@index` | `['web', 'auth', 'permission:users.view']` |
| `POST` | `/useradd` | `useradd` | `App\Http\Controllers\UserController@store` | `['web', 'auth', 'permission:users.create']` |
| `POST` | `/useredit` | `useredit` | `App\Http\Controllers\UserController@update` | `['web', 'auth', 'permission:users.edit']` |
| `POST` | `/userdelete` | `userdelete` | `App\Http\Controllers\UserController@destroy` | `['web', 'auth', 'permission:users.delete']` |

*Note on Middleware Aliases (`bootstrap/app.php:14-18`):*
- `'permission'` maps to `\Spatie\Permission\Middleware\PermissionMiddleware::class`.
- `'role'` maps to `\Spatie\Permission\Middleware\RoleMiddleware::class`.
- `'role_or_permission'` maps to `\Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class`.

---

### 1.2 Detailed Audit of `AuthController`

Directly verified in `app/Http/Controllers/AuthController.php` (lines 1-64):

#### 1.2.1 `showLogin()` (Lines 11-17)
```php
public function showLogin()
{
    if (Auth::check()) {
        return redirect()->intended('/dashboard');
    }
    return view('pages.login');
}
```
- If the visitor is already authenticated (`Auth::check() == true`), immediately redirects to `redirect()->intended('/dashboard')`.
- Otherwise, returns `view('pages.login')`.

#### 1.2.2 `login(Request $request)` (Lines 19-53)
```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
    ]);

    $remember = $request->boolean('remember');

    // Check if user exists by email or user_code
    $user = User::where('email', $credentials['email'])
        ->orWhere('user_code', $credentials['email'])
        ->first();

    if (!$user) {
        return back()->withErrors([
            'email' => 'No account found with this email or User ID.',
        ])->onlyInput('email');
    }

    if ($user->status != 1) {
        return back()->withErrors([
            'email' => 'Your account is inactive. Please contact administrator.',
        ])->onlyInput('email');
    }

    if (Auth::attempt(['email' => $user->email, 'password' => $credentials['password']], $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'password' => 'Incorrect password entered.',
    ])->onlyInput('email');
}
```
**Mechanism Analysis:**
1. **Dual-Identifier Resolution:** The submitted form field is named `'email'`, but its validation rule is `'required|string'`. The query checks `User::where('email', $credentials['email'])->orWhere('user_code', $credentials['email'])->first()`. This allows seamless login with both standard emails (e.g. `admin@gtms.com`) and employee IDs (e.g. `LUK_001`).
2. **Account Status Check:** Explicitly checks `$user->status != 1`. Inactive accounts (`status == 0`) are stopped before password evaluation with `'Your account is inactive. Please contact administrator.'`.
3. **Canonical Authentication Delegation:** Rather than attempting authentication with user-supplied text directly against the auth provider, the system resolves the canonical email `$user->email` and calls `Auth::attempt(['email' => $user->email, 'password' => $credentials['password']], $remember)`.
4. **Session Fixation Defense:** Calls `$request->session()->regenerate()` upon successful authentication.
5. **Remember Me:** Respects `$request->boolean('remember')` passed as second parameter to `Auth::attempt`.
6. **Error Isolation:**
   - Missing account: Error returned under key `'email'`.
   - Inactive account: Error returned under key `'email'`.
   - Incorrect password: Error returned under key `'password'`, preserving `'email'` via `onlyInput('email')`.

#### 1.2.3 `logout(Request $request)` (Lines 55-62)
```php
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('success', 'Logged out successfully.');
}
```
- Logs out auth guard, invalidates session data, regenerates CSRF token, and redirects to `/` with success flash message.

---

### 1.3 Detailed Audit of `UserController`

Directly verified in `app/Http/Controllers/UserController.php` (lines 1-163):

#### 1.3.1 `index()` (Lines 13-19)
```php
public function index()
{
    $users = User::with(['role', 'roles', 'branch'])->get();
    $role = Role::with('permissions')->get();
    $branch = Branch::where('status', 1)->get();
    return view('pages.authentication.users.index', compact('users', 'role', 'branch'));
}
```
- Eager-loads `$users` with both direct `role` relation (`users.role_id`), Spatie `roles` relation (pivot `model_has_roles`), and `branch`.
- Loads active branches (`Branch::where('status', 1)->get()`).
- Guarded by `permission:users.view`.

#### 1.3.2 `store(Request $request)` (Lines 21-71)
```php
public function store(Request $request)
{
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:users,email',
        'password'  => 'required|min:6',
        'role_id'   => 'required|exists:roles,id',
        'branch_id' => 'required|exists:branches,id',
        'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = bcrypt($request->password);
    $user->role_id = $request->role_id;
    $user->branch_id = $request->branch_id;
    $user->status = $request->input('status', 1);
    $user->remember_token = Str::random(10);
    $user->mobile_num = $request->mobile_num;
    $user->show_password = $request->password;

    // Upload Image
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('uploads/users'), $imageName);
        $user->image = $imageName;
    }

    // Save first to get auto-increment ID
    $user->save();

    // Generate User ID (LUK_001, LUK_002, ...)
    $user->user_code = 'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
    $user->save();

    // Sync Spatie Role
    $role = Role::find($request->role_id);
    if ($role) {
        $user->syncRoles([$role->name]);
    }

    $user->load(['role', 'roles', 'branch']);

    return response()->json([
        'status'  => 1,
        'message' => 'User Added Successfully',
        'data'    => $user
    ]);
}
```
**Key Discoveries:**
1. **Two-Stage Persistence Pattern:**
   - First `$user->save()` allocates the database primary auto-increment `id`.
   - Automatic `user_code` algorithm:
     `$user->user_code = 'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT);`
   - Second `$user->save()` commits the generated `user_code`.
2. **Dual Role Synchronization:**
   - Direct column `users.role_id` is assigned directly: `$user->role_id = $request->role_id;`.
   - Spatie pivot `model_has_roles` is synchronized:
     ```php
     $role = Role::find($request->role_id);
     if ($role) {
         $user->syncRoles([$role->name]);
     }
     ```
3. **Avatar Upload:**
   - Stored in `public_path('uploads/users')`.
   - Filename: `time() . '.' . $image->getClientOriginalExtension()`.
   - Validation: `nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048`.
4. **Security Note (`show_password`):**
   - Line 41: `$user->show_password = $request->password;` stores the plaintext password in `users.show_password` alongside the bcrypt hash in `users.password`. This is used by administrators in the UI edit modal (`data-password="{{ $user->show_password }}"`).

#### 1.3.3 `update(Request $request)` (Lines 73-125)
```php
public function update(Request $request)
{
    $request->validate([
        'id'        => 'required|exists:users,id',
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:users,email,' . $request->id,
        'password'  => 'nullable|min:6',
        'role_id'   => 'required|exists:roles,id',
        'branch_id' => 'required|exists:branches,id',
        'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $user = User::findOrFail($request->id);
    $user->name = $request->name;
    $user->email = $request->email;
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
        $user->show_password = $request->password;
    }
    $user->role_id = $request->role_id;
    $user->branch_id = $request->branch_id;
    if ($request->has('status')) {
        $user->status = $request->status;
    }
    $user->mobile_num = $request->mobile_num;

    // Upload Image
    if ($request->hasFile('image')) {
        if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
            unlink(public_path('uploads/users/' . $user->image));
        }
        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('uploads/users'), $imageName);
        $user->image = $imageName;
    }

    $user->save();

    // Sync Spatie Role
    $role = Role::find($request->role_id);
    if ($role) {
        $user->syncRoles([$role->name]);
    }

    $user->load(['role', 'roles', 'branch']);

    return response()->json([
        'status'  => 1,
        'message' => 'User Updated Successfully',
        'data'    => $user
    ]);
}
```
**Key Discoveries:**
1. **Password Optionality:** Password is only updated if `$request->filled('password')`.
2. **Old Avatar Unlinking on Replacement:**
   ```php
   if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
       unlink(public_path('uploads/users/' . $user->image));
   }
   ```
3. **Dual Role Re-Synchronization:** `$user->role_id` and `$user->syncRoles([$role->name])` are both updated.

#### 1.3.4 `destroy(Request $request)` (Lines 127-160)
```php
public function destroy(Request $request)
{
    $request->validate([
        'id' => 'required|exists:users,id',
    ]);

    $user = User::findOrFail($request->id);

    if (auth()->id() == $user->id) {
        return response()->json([
            'status' => 0,
            'message' => 'You cannot delete your own account.',
        ]);
    }

    if ($user->hasRole('Admin') && User::role('Admin')->count() <= 1) {
        return response()->json([
            'status' => 0,
            'message' => 'The last Admin account cannot be deleted.',
        ]);
    }

    // Delete image if exists
    if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
        unlink(public_path('uploads/users/' . $user->image));
    }

    $user->delete();

    return response()->json([
        'status'  => 1,
        'message' => 'User Deleted Successfully',
    ]);
}
```
**Deletion Safeguards:**
1. **Self-Deletion Guard:** `auth()->id() == $user->id` blocks self-destruction, returning `{ status: 0, message: "You cannot delete your own account." }`.
2. **Last Admin Guard:** `$user->hasRole('Admin') && User::role('Admin')->count() <= 1` blocks deletion of the only remaining administrator, returning `{ status: 0, message: "The last Admin account cannot be deleted." }`.
3. **Avatar Cleanup:** Unlinks `$user->image` if present on disk.
4. **Hard Delete:** `User` model does NOT employ `SoftDeletes` trait (`app/Models/User.php:15`). Deletion issues an immediate SQL `DELETE FROM users WHERE id = ?`. Spatie role associations in `model_has_roles` are automatically cleared via foreign key cascade.

---

### 1.4 Database Schema of `users` Table

Verified via migrations (`database/migrations/`):
- `0001_01_01_000000_create_users_table.php`: `id`, `name`, `email` (unique), `email_verified_at`, `password`, `remember_token`, `timestamps`
- `2026_07_06_114649_add_role_id_and_branch_id_to_users_table.php`: `role_id` (foreignId nullable), `branch_id` (foreignId nullable)
- `2026_07_08_070939_add_image_userid_mobile_showpass_to_users_table.php`: `image` (string nullable), `mobile_num` (string(15) nullable), `show_password` (string(255) nullable), `user_id` (unsignedBigInteger foreign nullable, references `users(id) on delete set null`)
- `2026_07_08_075906_add_status_to_users_table.php`: `status` (tinyInteger default 1, comment '1 = Active, 0 = Inactive')
- `2026_07_08_081111_add_user_code_to_users_table.php`: `user_code` (string nullable after `id`)

Current MySQL column list verified via Artisan:
`["id","user_code","user_id","name","email","image","mobile_num","email_verified_at","password","show_password","remember_token","created_at","updated_at","role_id","branch_id","status"]`

---

### 1.5 Multi-Tenancy & Authorization Rules

#### 1.5.1 Gate Bypass in `AppServiceProvider.php` (Lines 26-28)
```php
Gate::before(function ($user, $ability) {
    return $user->hasRole(['Admin', 'Super Admin']) ? true : null;
});
```
Any user having role `'Admin'` or `'Super Admin'` bypasses all permission middleware (`users.view`, `users.create`, etc.).

#### 1.5.2 Multi-Tenancy Scope in `BranchScope.php` (Lines 10-25)
```php
class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            $user = Auth::user();
            // If user has a specific branch assigned and is not a superadmin (role_id !== 1 and not Admin/Super Admin role)
            if (!empty($user->branch_id) && $user->role_id !== 1 && !$user->hasRole(['Admin', 'Super Admin'])) {
                $builder->where($model->getTable() . '.branch_id', $user->branch_id);
            }
        }
    }
}
```
- Non-admin users (`role_id !== 1 && !$user->hasRole(['Admin', 'Super Admin'])`) with `branch_id` assigned are automatically scoped to records where `branch_id == $user->branch_id`.
- Admins with `role_id === 1` OR `hasRole(['Admin', 'Super Admin'])` bypass this restriction entirely.
- Used across: `DgpsSurvey`, `DroneSurvey`, `EcCompliance`, `EnvironmentProject`, `LeaseApplication`, `MineralStockpile`, `MiningApplication`, `PptApplication` via trait `BelongsToBranch`.

---

### 1.6 Existing Test Harness Baseline

Audited files: `phpunit.xml`, `tests/TestCase.php`, `tests/Feature/ApplicationHandlersAndPaymentsTest.php`, `tests/Feature/PptDgpsAndEcComplianceTest.php`.

1. **Database Configuration Override:**
   While `phpunit.xml` defaults to SQLite in-memory (`<env name="DB_CONNECTION" value="sqlite"/>`), both existing test suites explicitly override the default connection in their `setUp()` method:
   ```php
   config([
       'database.default' => 'mysql',
       'database.connections.mysql.database' => 'gtms_data',
   ]);
   ```
   This is essential because the application relies on MySQL tables, raw queries, and pre-seeded database rows.
2. **Database Integrity & Rollback:**
   Both test suites use `use DatabaseTransactions;`. Every test runs inside a database transaction and is automatically rolled back upon completion, leaving the `gtms_data` database untouched.
3. **Authentication State in Tests:**
   Existing tests authenticate an administrative user in `setUp()`:
   ```php
   $user = User::where('email', 'admin@gtms.com')->first() ?: User::first();
   if (!$user) {
       $user = User::factory()->create();
   }
   $this->user = $user;
   $this->actingAs($this->user);
   ```
4. **Collision Prevention Patterns:**
   - To prevent unique key collisions on `users.email`, dynamic tests must use randomized emails: `'test_' . uniqid() . '@example.com'`.
   - On updates, the controller's validation rule `'email' => 'required|email|unique:users,email,' . $request->id` requires passing the target user's `id`.
   - Test suites passed 100%:
     - `ApplicationHandlersAndPaymentsTest`: 6 tests, 68 assertions, 0 failures.
     - `PptDgpsAndEcComplianceTest`: 6 tests, 82 assertions, 0 failures.

---

## 2. Logic Chain

```
[Observation 1.1: routes/web.php]
  ├── Guest routes: GET /, GET /login, POST /login
  ├── Authenticated routes: POST /logout
  └── User CRUD routes: GET /user, POST /useradd, POST /useredit, POST /userdelete
      guarded by permission:users.* middleware

[Observation 1.2: AuthController.php]
  ├── Login accepts 'email' field as string (no email format constraint)
  ├── Dual query: User::where('email', ...)->orWhere('user_code', ...)
  ├── Inactive check: if ($user->status != 1) return error
  └── Invokes Auth::attempt(['email' => $user->email, 'password' => $credentials['password']], $remember)
      └── Explains why dual-identifier login works seamlessly with Laravel's built-in Auth guard

[Observation 1.3: UserController.php]
  ├── store():
  │     ├── Saves first to get autoincrement ID
  │     ├── Calculates user_code: 'LUK_' . str_pad($id, 3, '0', STR_PAD_LEFT)
  │     ├── Dual RBAC sync: assigns users.role_id AND $user->syncRoles([$role->name])
  │     └── Avatar saved to public/uploads/users/ with timestamped name
  ├── update():
  │     ├── Re-synchronizes users.role_id AND $user->syncRoles([$role->name])
  │     └── If new image uploaded, unlinks existing image file from disk
  └── destroy():
        ├── Safeguard 1: auth()->id() == $user->id => error "You cannot delete your own account."
        ├── Safeguard 2: $user->hasRole('Admin') && User::role('Admin')->count() <= 1 => error "The last Admin account cannot be deleted."
        └── Hard delete ($user->delete()) after unlinking avatar file

[Observation 1.5 & 1.6: Harness & Scope]
  ├── Gate::before grants Admin / Super Admin bypass
  ├── BranchScope restricts non-admin users where role_id !== 1 and !hasRole(['Admin', 'Super Admin'])
  └── Existing tests use MySQL gtms_data with DatabaseTransactions rollback
      └── Blueprint for new UserLifecycleTest can execute completely within DatabaseTransactions without corrupting live data.
```

---

## 3. Blueprint for Test Suite: R3 User Lifecycle & Dual RBAC

To satisfy Requirement R3 and ensure zero regressions across existing test suites, the test suite should be implemented in `tests/Feature/UserLifecycleAndRbacTest.php` with the following test methods:

### Group A: Authentication & Dual-Identifier Login (`AuthController`)
1. `test_login_page_is_accessible_to_guests()`
   - GET `/` and GET `/login` return HTTP 200 with view `pages.login`.
2. `test_authenticated_user_is_redirected_to_dashboard_from_login_screen()`
   - Authenticated user visiting GET `/login` is redirected (302) to `/dashboard`.
3. `test_user_can_login_with_canonical_email()`
   - POST `/login` with `['email' => 'admin@gtms.com', 'password' => 'admin123']`.
   - Assert authenticated, redirected to `/dashboard`, session regenerated.
4. `test_user_can_login_with_user_code_identifier()`
   - POST `/login` with `['email' => 'LUK_001', 'password' => 'admin123']`.
   - Assert authenticated as admin, redirected to `/dashboard`.
   - Test with secondary user code e.g. `LUK_002` + `staff123`.
5. `test_login_fails_with_unregistered_identifier()`
   - POST `/login` with `['email' => 'nonexistent@example.com', 'password' => 'secret']`.
   - Assert 302 redirect back, session has error on `'email'`, message: `'No account found with this email or User ID.'`.
6. `test_login_fails_for_inactive_account()`
   - Create user with `status = 0`.
   - POST `/login` with that user's email/code.
   - Assert 302 redirect back, session has error on `'email'`, message: `'Your account is inactive. Please contact administrator.'`.
7. `test_login_fails_with_incorrect_password()`
   - POST `/login` with valid email `'admin@gtms.com'` and invalid password `'wrongpassword'`.
   - Assert 302 redirect back, session has error on `'password'`, message: `'Incorrect password entered.'`.
8. `test_user_logout_invalidates_session_and_redirects()`
   - Authenticate user, POST `/logout`.
   - Assert `Auth::check()` is false, redirected to `/` with success flash message.

### Group B: User Management CRUD & Provisioning (`UserController`)
9. `test_user_index_screen_renders_successfully_for_authorized_users()`
   - Authenticate as Admin, GET `/user`.
   - Assert HTTP 200, view has `users`, `role`, `branch`.
10. `test_user_store_creates_user_with_automatic_user_code_generation()`
    - POST `/useradd` with valid payload (`name`, `email`, `password`, `role_id`, `branch_id`).
    - Assert HTTP 200 JSON `{ status: 1 }`.
    - Retrieve created user from database.
    - Assert `user_code === 'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT)`.
11. `test_user_store_validates_required_fields_and_unique_email()`
    - POST `/useradd` with empty payload. Assert 422 JSON with errors on `name`, `email`, `password`, `role_id`, `branch_id`.
    - POST `/useradd` with existing email `'admin@gtms.com'`. Assert 422 JSON error on `email`.
12. `test_user_store_synchronizes_dual_rbac()`
    - Provision user with role `Staff` (`role_id = 2`).
    - Assert `users.role_id == 2` in database.
    - Assert `$user->hasRole('Staff')` evaluates to `true` (verifying `model_has_roles` entry).
13. `test_user_store_handles_avatar_upload()`
    - POST `/useradd` with `UploadedFile::fake()->image('avatar.jpg')`.
    - Assert image file was saved in `public/uploads/users/` matching timestamp pattern.
    - Clean up uploaded file during test teardown.
14. `test_user_update_modifies_attributes_and_re_synchronizes_dual_rbac()`
    - Create test user with role `Staff`.
    - POST `/useredit` changing `role_id` to `Officer` and updating name/mobile.
    - Assert `users.role_id` is updated to Officer's ID.
    - Assert `$user->hasRole('Officer')` is `true` and `$user->hasRole('Staff')` is `false`.
15. `test_user_update_unlinks_old_avatar_on_replacement()`
    - Create test user with fake avatar in `public/uploads/users/`.
    - POST `/useredit` with a second fake image.
    - Assert old image file no longer exists on disk; new image file exists.
    - Clean up new image file.
16. `test_user_update_preserves_password_when_omitted()`
    - POST `/useredit` with empty/omitted `password`.
    - Assert user's hashed `password` and `show_password` remain unchanged.

### Group C: Deletion Safeguards (`UserController@destroy`)
17. `test_self_deletion_is_blocked()`
    - Authenticate as Admin user A.
    - POST `/userdelete` with `id = $adminA->id`.
    - Assert HTTP 200 JSON `{ status: 0, message: "You cannot delete your own account." }`.
    - Assert user A still exists in database.
18. `test_last_admin_deletion_is_blocked()`
    - Ensure only one user has `Admin` role.
    - Authenticate as another user (or temporarily switch auth).
    - POST `/userdelete` with `id = $soleAdmin->id`.
    - Assert HTTP 200 JSON `{ status: 0, message: "The last Admin account cannot be deleted." }`.
    - Assert admin still exists in database.
19. `test_user_deletion_succeeds_and_cleans_up_avatar()`
    - Create disposable test user with avatar file.
    - Authenticate as Admin.
    - POST `/userdelete` with `id = $testUser->id`.
    - Assert HTTP 200 JSON `{ status: 1, message: "User Deleted Successfully" }`.
    - Assert user deleted from database (`User::find($id)` is null).
    - Assert avatar file was unlinked from disk.

### Group D: Permission Middleware & Multi-Tenancy Scope Gating
20. `test_guest_cannot_access_user_routes()`
    - As guest, GET `/user`, POST `/useradd`, POST `/useredit`, POST `/userdelete`.
    - Assert 302 redirect to `/login` (or 401).
21. `test_unauthorized_user_without_permissions_is_denied_with_403()`
    - Create user with zero permissions (or role lacking `users.*`).
    - Authenticate as this user.
    - Request GET `/user`, POST `/useradd`, POST `/useredit`, POST `/userdelete`.
    - Assert HTTP 403 Forbidden.
22. `test_branch_scope_restricts_regular_users_to_their_branch()`
    - Create non-admin user with `branch_id = 1`.
    - Authenticate as this user.
    - Query model using `BelongsToBranch` (e.g. `LeaseApplication::all()`).
    - Assert all returned records belong to `branch_id = 1`.
23. `test_branch_scope_is_bypassed_for_admin_users()`
    - Authenticate as Admin (`role_id = 1` or role `Admin`).
    - Query `LeaseApplication::all()`.
    - Assert records from different branches are visible.

---

## 4. Caveats

1. **Collation Sensitivity of `user_code`:**
   MySQL default collation (`utf8mb4_unicode_ci`) treats `luk_001` and `LUK_001` as identical. In tests, use the canonical uppercase format (`LUK_001`) to guarantee deterministic cross-environment behavior.
2. **Avatar Filesystem Cleanup:**
   Any test uploading fake images via `UploadedFile::fake()->image()` moves files to `public/uploads/users/`. Tests must register a cleanup callback or `unlink()` created test files to prevent cluttering `public/uploads/users/`.
3. **Plural vs Singular Role Relationships:**
   `app/Models/User.php` defines both `role()` (direct `belongsTo(Role::class, 'role_id')`) and `roles()` (Spatie `morphToMany`). Code must maintain dual synchronization whenever roles change.
4. **Plaintext Password Storage (`show_password`):**
   `show_password` is populated during store and update. While a noted architectural security risk, tests must account for this behavior without failing.

---

## 5. Conclusion

The GTMS Authentication and User Administration architecture has been thoroughly audited with exact code paths, algorithms, and boundary behaviors documented:
1. **Dual-Identifier Login:** Solved in `AuthController@login` by resolving the user record against `email` or `user_code`, verifying `status == 1`, and delegating canonical email authentication to `Auth::attempt`.
2. **User Provisioning & Auto `user_code`:** Two-step persistence in `UserController@store` guarantees the auto-increment ID is padded into the standardized format `'LUK_' . str_pad($id, 3, '0', STR_PAD_LEFT)`.
3. **Dual RBAC Synchronization:** `users.role_id` and Spatie `model_has_roles` are systematically kept in sync on both `store` and `update`.
4. **Avatar Lifecycle & Deletion Safeguards:** Validated image MIME constraints, disk unlinking on replacement and deletion, self-deletion prevention (`auth()->id() == $user->id`), and last-admin destruction blocking (`User::role('Admin')->count() <= 1`).
5. **Test Harness Compatibility:** Feature tests must configure the MySQL connection `gtms_data` within `DatabaseTransactions` to achieve full test fidelity without touching persistent state.

---

## 6. Verification Method

To verify these findings independently:

1. **Verify Route Catalog:**
   ```powershell
   php artisan route:list --path=user
   php artisan route:list --path=login
   ```
2. **Verify Existing Feature Tests Baseline:**
   ```powershell
   php artisan test --filter=ApplicationHandlersAndPaymentsTest
   php artisan test --filter=PptDgpsAndEcComplianceTest
   ```
3. **Inspect Key Source Files:**
   - `app/Http/Controllers/AuthController.php` (lines 19-53 for dual-identifier login)
   - `app/Http/Controllers/UserController.php` (lines 21-71 for provisioning, lines 127-160 for deletion safeguards)
   - `app/Models/Scopes/BranchScope.php` (lines 17-23 for branch isolation and admin bypass)
   - `app/Providers/AppServiceProvider.php` (lines 26-28 for `Gate::before` super-admin bypass)
