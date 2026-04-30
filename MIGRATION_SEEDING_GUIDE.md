# Guia de Migrations e Seeders - PEP System

## Overview

Este guia explica como executar as migrations e seeders para preparar o banco de dados do PEP System com todos os dados necessários para testes.

## Estrutura de Migrations

As migrations foram criadas na seguinte ordem de dependência:

### 1. Migrations Básicas (Laravel)
- `0001_01_01_000001_create_cache_table.php` - Cache
- `0001_01_01_000002_create_jobs_table.php` - Jobs
- `2026_02_28_145536_add_role_to_users_table.php` - Adiciona campo 'role' à tabela users
- `2026_02_28_150704_create_personal_access_tokens_table.php` - Tokens Sanctum
- `2026_03_07_172704_create_permission_tables.php` - Spatie Permission tables

### 2. Migrations de Dados Primários
- `2026_03_05_193452_create_cursos_table.php` - Tabela de cursos
- `2026_03_05_193510_create_instituicoes_table.php` - Tabela de instituições parceiras
- `2026_03_05_194701_create_users_table.php` - Tabela de usuários

### 3. Migrations de Relacionamentos
- `2026_03_05_194715_create_curso_instituicao_table.php` - Relacionamento muitos-para-muitos
- `2026_03_05_194725_create_estagios_table.php` - Tabela de estágios

### 4. Migrations de Documentação
- `2026_03_05_194734_create_documentos_table.php` - Documentos de estágio
- `2026_03_05_194745_create_avaliacoes_table.php` - Avaliações

### 5. Migrations de Auditoria
- `2026_03_07_194221_create_logs_table.php` - Logs do sistema

### 6. Migrations Adicionais (Criadas)
- `2026_04_30_000001_create_departamentos_table.php` - Departamentos
- `2026_04_30_000002_create_student_profiles_table.php` - Perfis de estudante
- `2026_04_30_000003_create_staff_profiles_table.php` - Perfis de staff
- `2026_04_30_000004_create_notificacoes_table.php` - Notificações
- `2026_04_30_000005_add_departamento_to_cursos_table.php` - Adiciona departamento aos cursos
- `2026_04_30_000006_add_coordenador_and_dates_to_estagios_table.php` - Adiciona coordenador e datas

## Estrutura de Seeders

Os seeders devem ser executados na seguinte ordem de dependência:

### 1. RolePermissionSeeder
**Responsável por:** Criar roles (admin, coordenador, supervisor, tutor, estudante, chefe_repartição) e permissions.
**Dependências:** Nenhuma (deve rodar primeiro)
**Cria:**
- 6 roles com permissões específicas
- 11 permissions granulares

### 2. DepartamentoSeeder
**Responsável por:** Criar departamentos da universidade.
**Dependências:** RolePermissionSeeder
**Cria:**
- 5 departamentos: Engenharia Informática, Gestão, Engenharia Civil, Ciências Naturais, Humanas

### 3. CursoSeeder
**Responsável por:** Criar cursos vinculados aos departamentos.
**Dependências:** DepartamentoSeeder
**Cria:**
- 7 cursos distribuídos entre os departamentos
- Links com departamentos e durações

### 4. UserSeeder
**Responsável por:** Criar usuários admin e teste para cada role.
**Dependências:** RolePermissionSeeder
**Cria:**
- 1 admin (admin@pep.com / password)
- 5 usuários teste (1 por role)

### 5. StaffProfileSeeder
**Responsável por:** Criar perfis de staff (chefes, coordenadores, tutores, supervisores).
**Dependências:** DepartamentoSeeder, UserSeeder
**Cria:**
- 1 chefe de repartição por departamento
- 2 coordenadores por departamento
- 3 tutores por departamento
- 5 supervisores externos (sem departamento)

### 6. StudentProfileSeeder
**Responsável por:** Criar perfis de estudante com histórico académico.
**Dependências:** UserSeeder, CursoSeeder
**Cria:**
- 5 estudantes por curso (15 total)
- Números de estudante únicos
- Média e elegibilidade para estágio aleatórias

### 7. InstituicaoSeeder
**Responsável por:** Criar instituições parceiras para estágios.
**Dependências:** Nenhuma (simples dados)
**Cria:**
- 5 instituições com dados de contacto

### 8. EstagioSeeder
**Responsável por:** Criar estágios ligando estudantes a supervisores/tutores.
**Dependências:** StudentProfileSeeder, StaffProfileSeeder, InstituicaoSeeder
**Cria:**
- ~10 estágios em andamento
- ~5 estágios concluídos

## Como Executar

### Pré-requisitos
1. Laravel 11+ instalado
2. PHP 8.1+ configurado
3. Banco de dados configurado em `.env`
4. Migrations existentes criadas

### Passo 1: Executar Migrations (Criar Schema)
```bash
# Executar todas as migrations
php artisan migrate

# Ou forçar se já existe (CUIDADO - apaga dados!)
php artisan migrate:fresh
```

### Passo 2: Executar Seeders (Popular Dados)
```bash
# Executar todos os seeders em ordem
php artisan db:seed

# Ou forçar refresh + seed
php artisan migrate:fresh --seed
```

### Passo 3: Verificar Dados
```bash
# Verificar usuários criados
php artisan tinker
>>> User::all()->count()
>>> User::with('roles')->get()

# Verificar departamentos
>>> Departamento::all()

# Verificar estágios
>>> Estagio::with('estagiario', 'supervisor', 'tutor')->get()
```

## Dados Criados

### Usuários
- **Admin:** admin@pep.com / password
- **Teste:** 1 usuário por role

### Departamentos
1. Engenharia Informática
2. Gestão
3. Engenharia Civil
4. Ciências Naturais
5. Humanas

### Cursos (por Departamento)
- Engenharia Informática: Licenciatura (3 anos), Mestrado (2 anos)
- Gestão: Licenciatura (3 anos), Mestrado (2 anos)
- Engenharia Civil: Licenciatura (4 anos)
- Ciências Naturais: Licenciatura (3 anos)
- Humanas: Licenciatura (3 anos)

### Staff
- 5 Chefes de Repartição (1 por departamento)
- 10 Coordenadores (2 por departamento)
- 15 Tutores (3 por departamento, com departamento)
- 5 Supervisores Externos (sem departamento)

### Estudantes
- 15 estudantes (5 por curso)
- Números: EST-{curso_id}-{i}
- Média aleatória entre 12-18
- Elegibilidade aleatória

### Instituições
1. Banco de Moçambique (ativa)
2. Vodacom Moçambique (ativa)
3. INCM (ativa)
4. Empresa Consultoria & Auditoria (ativa)
5. Câmara Municipal (suspensa)

### Estágios
- ~10 em andamento (últimos 2 meses)
- ~5 concluídos (com nota final 14-18)

## Troubleshooting

### Erro: "Foreign key constraint failed"
**Causa:** Seeders rodaram em ordem errada
**Solução:** Deletar dados e rodar `php artisan migrate:fresh --seed`

### Erro: "Role does not exist"
**Causa:** RolePermissionSeeder não foi executado
**Solução:** Verificar se RolePermissionSeeder é chamado primeiro

### Erro: "Unique constraint violated"
**Causa:** Seeder rodou duas vezes
**Solução:** Usar `firstOrCreate` ou rodar `migrate:fresh`

## Ambiente de Testes

Após completar migrations + seeders:

```bash
# Iniciar servidor Laravel
php artisan serve

# Fazer login de teste
POST /api/auth/login
{
  "email": "admin@pep.com",
  "password": "password"
}

# Listar estagios (protegido)
GET /api/internships/estagios
Authorization: Bearer {token}
```

## Estrutura Esperada do Banco

```
departamentos
├── id, nome, descricao, timestamps

cursos
├── id, nome, codigo, departamento_id, duracao_anos, timestamps

users
├── id, name, email, password, email_verified_at, timestamps

staff_profiles
├── id, user_id, departamento_id, cargo, telefone, timestamps

student_profiles
├── id, user_id, numero_estudante, curso_id, ano_academico, media, elegivel_estagio, timestamps

instituicoes
├── id, nome, nuit, endereco, telefone, email, ponto_focal_nome, ponto_focal_contacto, status, timestamps

estagios
├── id, estagiario_id, supervisor_id, tutor_id, coordenador_id, instituicao_id, curso_id
├── estado, data_inicio, data_fim, nota_final, timestamps

documentos
├── id, estagio_id, tipo, estado, caminho, timestamps

avaliacoes
├── id, estagio_id, tipo, nota, comentario, timestamps

notificacoes
├── id, user_id, titulo, mensagem, lida_em, timestamps
```

## Notas de Segurança

- Dados de teste com senha "password" - NÃO usar em produção
- Usar `migrate:fresh` apenas em desenvolvimento
- Configurar `.env` com banco de dados correto antes de migrar
- Fazer backup antes de rodar `migrate:fresh`

## Próximos Passos

1. ✅ Migrations e Seeders criados
2. ⏳ Executar `php artisan migrate`
3. ⏳ Executar `php artisan db:seed`
4. ⏳ Testar endpoints da API com Postman/Insomnia
5. ⏳ Validar policies e autorização
