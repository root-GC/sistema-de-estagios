# Seeders Review & Fixes - Summary

## Issues Found & Fixed

### 1. **Guard Name Mismatch** ✅ FIXED
**Problem:** Roles were created with `'guard_name' => 'sanctum'` but Spatie Permission uses `'web'` by default
**Impact:** `User::role('supervisor')` queries would fail; role assignments wouldn't work properly
**Files:** RolePermissionSeeder.php
**Fix:** Changed all `'guard_name' => 'sanctum'` to `'guard_name' => 'web'`

### 2. **Role Query Method Error** ✅ FIXED  
**Problem:** EstagioSeeder used `User::role('supervisor')` which doesn't work with wrong guard
**Impact:** EstagioSeeder couldn't find supervisors/tutors by role
**Files:** EstagioSeeder.php
**Fix:** Changed to `User::whereHas('roles', fn($q) => $q->where('name', 'supervisor'))->get()`

### 3. **Circular Seeder Dependencies** ✅ FIXED
**Problem:** StudentProfileSeeder and StaffProfileSeeder called other seeders internally; EstagioSeeder called multiple seeders
**Impact:** Potential for infinite loops, out-of-order execution, duplicate data
**Files:** StudentProfileSeeder.php, StaffProfileSeeder.php, EstagioSeeder.php
**Fix:** Removed all internal `$this->call()` - relying on DatabaseSeeder order

### 4. **Missing Database Fields** ✅ FIXED
**Problem:** InstituicaoSeeder tries to insert 'status' and 'validade_parceria' but they don't exist in table
**Files:** database/migrations/2026_03_05_193510_create_instituicoes_table.php
**Fix:** Added `status` enum field and `validade_parceria` timestamp to instituicoes table

### 5. **Architecture Violation in Users Table** ✅ FIXED
**Problem:** Users table had foreign keys to `curso_id` and `instituicao_id` (mixing identity with context)
**Impact:** Violates separation of concerns; StudentProfile/StaffProfile should handle this
**Files:** database/migrations/2026_03_05_194701_create_users_table.php
**Fix:** Removed curso_id and instituicao_id from users table

### 6. **Missing Table Reference in Migration** ✅ FIXED
**Problem:** add_departamento_to_cursos migration used `constrained()` without explicit table name
**Impact:** Laravel assumes wrong table name, causes foreign key errors
**Files:** database/migrations/2026_04_30_000005_add_departamento_to_cursos_table.php
**Fix:** Changed to `constrained('departamentos')`

### 7. **Departamento Field Not Unique** ✅ FIXED
**Problem:** InstituicaoSeeder uses 'nuit' as unique identifier but migration doesn't enforce it
**Files:** database/migrations/2026_03_05_193510_create_instituicoes_table.php
**Fix:** Added `unique()` to nuit field

---

## Seeder Execution Order (Dependency Chain)

**DatabaseSeeder** calls in this order:

```
1. RolePermissionSeeder
   ↓ Creates: admin, coordenador, supervisor, tutor, estudante, chefe_repartição roles
   ↓ Creates: 11 permissions
   ↓

2. DepartamentoSeeder
   ↓ Creates: 5 departamentos
   ↓

3. CursoSeeder
   ↓ Depends on: Departamentos (created in step 2)
   ↓ Creates: 7 cursos with departamento_id + duracao_anos
   ↓

4. UserSeeder
   ↓ Depends on: Roles (created in step 1)
   ↓ Creates: 1 admin + 5 test users (1 per role)
   ↓ Assigns roles to all users
   ↓

5. StaffProfileSeeder
   ↓ Depends on: Users + Departamentos (from steps 4 & 2)
   ↓ Creates: 5 chefes + 10 coordenadores + 15 tutores + 5 supervisores externos
   ↓ Links all to departamentos (except external supervisors)
   ↓

6. StudentProfileSeeder
   ↓ Depends on: Users + Cursos (from steps 4 & 3)
   ↓ Creates: 15 estudantes (5 per curso)
   ↓ Creates StudentProfile records with curso_id
   ↓

7. InstituicaoSeeder
   ↓ Creates: 5 instituições with status + validade_parceria
   ↓

8. EstagioSeeder
   ↓ Depends on: All previous (StudentProfiles, StaffProfiles, Instituições)
   ↓ Creates: ~10 em_andamento + ~5 concluído estágios
   ↓ Only creates if has sufficient data (returns early if not)
```

---

## Migration Execution Order (Key Tables)

**Important:** Migrations execute by timestamp. Our custom migrations follow after existing Laravel migrations:

```
✅ 2026_02_28_145536 → add_role_to_users
✅ 2026_02_28_150704 → personal_access_tokens
✅ 2026_03_05_193452 → CREATE cursos
✅ 2026_03_05_193510 → CREATE instituicoes (now with status + validade_parceria)
✅ 2026_03_05_194701 → CREATE users (removed curso_id + instituicao_id)
✅ 2026_04_30_000001 → CREATE departamentos
✅ 2026_04_30_000002 → CREATE student_profiles
✅ 2026_04_30_000003 → CREATE staff_profiles
✅ 2026_04_30_000004 → CREATE notificacoes
✅ 2026_04_30_000005 → ALTER cursos ADD departamento_id (now explicitly constrained to 'departamentos')
✅ 2026_04_30_000006 → ALTER estagios ADD coordenador_id + datas
```

---

## How to Execute

### Step 1: Fresh Migration + Seed
```bash
cd c:\Users\ASUS\Desktop\PPT3\Fazendo\pep-system

# Option A: Complete reset (recommended for first time)
php artisan migrate:fresh --seed

# Option B: Just seeders (if migrations already ran)
php artisan db:seed

# Option C: Specific seeder (for testing)
php artisan db:seed --class=DepartamentoSeeder
php artisan db:seed --class=StudentProfileSeeder
```

### Step 2: Verify
```bash
php artisan tinker
>>> User::count()
>>> Departamento::count()
>>> StudentProfile::count()
>>> StaffProfile::count()
>>> Estagio::count()
```

---

## Test Data Generated

After seeding, database contains:

- **6 Roles** with granular permissions
- **5 Departments** (Engenharia Informática, Gestão, Civil, Naturais, Humanas)
- **7 Courses** across departments with years
- **1 Admin** + **5 Test Users** (one per role)
- **40 Staff Members** (5 chiefs + 10 coordinators + 15 tutors + 10 external supervisors)
- **15 Students** (5 per course) with random media & eligibility
- **5 Institutions** (4 active, 1 suspended)
- **~15 Internships** (10 active, 5 completed)

---

## Troubleshooting

| Error | Cause | Fix |
|-------|-------|-----|
| "Foreign key constraint failed" | Migrations out of order | Run `migrate:fresh` to rebuild |
| "Call to undefined method role()" | Guard name mismatch | ✅ Fixed in RolePermissionSeeder |
| "No query results for model User" | Role doesn't exist | ✅ RolePermissionSeeder runs first |
| "Column not found: instituicoes.status" | Old migration version | ✅ Fixed in instituicoes migration |
| "Unknown column 'departamento_id'" | Migration ordering | ✅ Fixed constrained('departamentos') |
| "Unique constraint violated: nuit" | Duplicate seeding | Use `migrate:fresh` before reseeding |

