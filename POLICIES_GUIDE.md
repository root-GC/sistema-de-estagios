# Guia de Policies - Autorização por Papel

Este guia documenta como as Policies são estruturadas e como usá-las nos Controllers.

## Estrutura das Policies

Todas as policies estão em `app/Policies/` e mapeadas no `AuthServiceProvider`.

### Policies Disponíveis

1. **UserPolicy** - Gestão de utilizadores
   - `view()` - Ver utilizador
   - `create()` - Criar utilizador
   - `update()` - Atualizar utilizador
   - `delete()` - Deletar utilizador
   - `assignRole()` - Atribuir papel
   - `viewLogs()` - Ver logs

2. **DepartamentoPolicy** - Gestão de departamentos
   - `view()` - Ver departamento
   - `create()` - Criar departamento
   - `update()` - Atualizar departamento
   - `delete()` - Deletar departamento

3. **CursoPolicy** - Gestão de cursos
   - `view()` - Ver curso
   - `create()` - Criar curso
   - `update()` - Atualizar curso
   - `delete()` - Deletar curso

4. **InstituicaoPolicy** - Gestão de instituições
   - `view()` - Ver instituição
   - `create()` - Criar instituição
   - `update()` - Atualizar instituição
   - `delete()` - Deletar instituição
   - `approve()` - Aprovar instituição
   - `reject()` - Rejeitar instituição
   - `suspend()` - Suspender instituição

5. **StudentProfilePolicy** - Perfis de estudante
   - `view()` - Ver perfil
   - `create()` - Criar perfil
   - `update()` - Atualizar perfil
   - `delete()` - Deletar perfil

6. **StaffProfilePolicy** - Perfis de staff
   - `view()` - Ver perfil
   - `create()` - Criar perfil
   - `update()` - Atualizar perfil
   - `delete()` - Deletar perfil

7. **EstagioPolicy** - Gestão de estágios
   - `view()` - Ver estágio
   - `create()` - Criar estágio
   - `update()` - Atualizar estágio
   - `delete()` - Deletar estágio
   - `allocateSupervisor()` - Alocar supervisor
   - `assignTutor()` - Atribuir tutor
   - `changeStatus()` - Mudar estado
   - `calculateFinalGrade()` - Calcular nota final
   - `closeInternship()` - Encerrar estágio

8. **DocumentoPolicy** - Gestão de documentos
   - `view()` - Ver documento
   - `create()` - Upload documento
   - `delete()` - Deletar documento
   - `approve()` - Aprovar documento
   - `reject()` - Rejeitar documento
   - `comment()` - Comentar documento
   - `download()` - Download documento

9. **AvaliacaoPolicy** - Gestão de avaliações
   - `view()` - Ver avaliação
   - `create()` - Criar avaliação
   - `update()` - Atualizar avaliação
   - `delete()` - Deletar avaliação
   - `submitTutorEvaluation()` - Submeter como tutor
   - `submitSupervisorEvaluation()` - Submeter como supervisor
   - `calculateAverage()` - Calcular média

10. **PautaPolicy** - Gestão de pautas
    - `view()` - Ver pauta
    - `generate()` - Gerar pauta
    - `exportSigeup()` - Exportar SIGEUP
    - `publish()` - Publicar pauta

## Como Usar nos Controllers

### Autorização Básica

```php
use App\Models\User;

class UserController extends Controller
{
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        return response()->json($user);
    }
    
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        // Criar utilizador...
    }
}
```

### Autorização de Classe

Quando não temos um modelo específico:

```php
public function create(Request $request)
{
    $this->authorize('create', User::class);
}
```

### Autorização de Ação Customizada

```php
public function allocateSupervisor(Request $request, Estagio $estagio)
{
    $this->authorize('allocateSupervisor', $estagio);
}
```

## Regras de Autorização por Papel

### Admin
- ✅ Acesso total a tudo
- ✅ Pode gerir utilizadores, papéis, departamentos
- ✅ Pode aprovar/rejeitar instituições
- ✅ Pode publicar pautas

### Coordenador
- ✅ Vê e gere cursos do seu departamento
- ✅ Cria/atualiza estágios do seu departamento
- ✅ Atribui tutores e supervisores
- ✅ Aloca documentos e avaliações
- ❌ Não pode deletar utilizadores
- ❌ Não pode aprovar instituições

### Supervisor
- ✅ Vê seus próprios estágios
- ✅ Aprova/rejeita documentos
- ✅ Submete avaliações
- ❌ Não pode criar estágios
- ❌ Não pode gerir utilizadores

### Tutor
- ✅ Vê seus próprios estágios
- ✅ Submete avaliações
- ✅ Vê documentos
- ❌ Não pode aprovar documentos
- ❌ Não pode mudar status

### Coordenador
- ✅ Vê cursos do seu departamento
- ✅ Cria estágios
- ✅ Atribui tutores
- ✅ Gera pautas
- ❌ Não pode deletar utilizadores

### Estudante
- ✅ Vê seu próprio estágio
- ✅ Faz upload de documentos
- ✅ Vê suas avaliações
- ❌ Não pode aprovar documentos
- ❌ Não pode criar estágios

## Limite de Supervisor

O limite de 5 estágios por supervisor é verificado em:
- `EstagioController::allocateSupervisor()`

```php
$supervisorInternshipCount = Estagio::where('supervisor_id', $supervisor_id)
    ->whereNotIn('estado', ['concluido', 'cancelado'])
    ->count();

if ($supervisorInternshipCount >= 5) {
    return response()->json(['message' => 'Supervisor already has 5 active internships'], 422);
}
```

## Elegibilidade de Estágio

A elegibilidade de estágio é verificada em:
- `StudentProfileController::checkEligibility()`
- Precisa de `elegivel_estagio = true` em StudentProfile

## Gates Customizados

Alguns gates estão definidos em `AuthServiceProvider`:

```php
Gate::define('view-pauta', function (User $user) {
    return $user->hasAnyRole(['admin', 'coordenador']);
});

Gate::define('publish-pauta', function (User $user) {
    return $user->hasRole('admin');
});
```

## Exemplo Completo

```php
namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Models\Estagio;
use App\Models\Documento;

class EstagioController extends Controller
{
    public function allocateSupervisor(Request $request, Estagio $estagio)
    {
        // 1. Autorizar com policy
        $this->authorize('allocateSupervisor', $estagio);
        
        // 2. Validar input
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id'
        ]);
        
        // 3. Verificar limite de 5 estágios
        $count = Estagio::where('supervisor_id', $validated['supervisor_id'])
            ->whereNotIn('estado', ['concluido', 'cancelado'])
            ->count();
        
        if ($count >= 5) {
            return response()->json([
                'message' => 'Supervisor already has 5 active internships'
            ], 422);
        }
        
        // 4. Atualizar
        $estagio->update(['supervisor_id' => $validated['supervisor_id']]);
        
        // 5. Responder
        return response()->json(['message' => 'Supervisor allocated']);
    }
}
```

## Testes de Autorização

Para testar policies:

```php
public function testUserCannotDeleteOtherUsers()
{
    $user = User::factory()->create();
    $other = User::factory()->create();
    
    $this->actingAs($user)
        ->delete('/api/admin/users/' . $other->id)
        ->assertForbidden();
}

public function testAdminCanDeleteUsers()
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $user = User::factory()->create();
    
    $this->actingAs($admin)
        ->delete('/api/admin/users/' . $user->id)
        ->assertOk();
}
```

## Troubleshooting

### "This action is unauthorized"
Significa que a policy retornou `false`. Verifique:
1. O utilizador tem o papel correto?
2. A policy está correctamente mapeada?
3. O `authorize()` está nos locais certos?

### Como debugar?
```php
// No controller
dd(auth()->user()->hasRole('admin'));
dd(auth()->user()->can('view', $model));
```

## Próximos Passos

1. Adicionar `authorize()` a todos os controllers
2. Criar testes de autorização
3. Adicionar middleware para auditoria
4. Implementar rate limiting
