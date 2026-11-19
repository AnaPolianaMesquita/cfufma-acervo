# Mover uploads de imagens para o Supabase Storage

O código já suporta isso via `FILESYSTEM_DISK=s3` (ver `config/filesystems.php`).
Falta só criar o bucket no Supabase e preencher as envs no Render.

## 1. Supabase — criar o bucket e a chave S3

1. No painel do projeto Supabase: **Storage** → **New bucket**.
   - Nome: `acervo-imagens`
   - Public bucket: **ativado** (o app monta URLs públicas via `Storage::url()`)
2. **Project Settings** → **Storage** → **S3 Access Keys** → **New access key**.
   - Guarde o `Access Key ID` e o `Secret Access Key` (só aparece uma vez).
3. Anote o **Project Reference** (ex: `abcdefghijk`) e a **região** do projeto
   (aparece em Settings → General, ex: `sa-east-1`, `us-east-1`).

## 2. Render — variáveis de ambiente

No serviço `cfufma-acervo` → **Environment**, preencher:

| Variável | Valor |
|---|---|
| `FILESYSTEM_DISK` | `s3` |
| `AWS_ACCESS_KEY_ID` | chave do passo 1.2 |
| `AWS_SECRET_ACCESS_KEY` | secret do passo 1.2 |
| `AWS_BUCKET` | `acervo-imagens` |
| `AWS_DEFAULT_REGION` | região do projeto Supabase (passo 1.3) |
| `AWS_ENDPOINT` | `https://<project-ref>.supabase.co/storage/v1/s3` |
| `AWS_URL` | `https://<project-ref>.supabase.co/storage/v1/object/public/acervo-imagens` |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` |

Essas chaves já estão declaradas em `render.yaml` com `sync: false` — ou seja,
o Render não aceita valor via arquivo, precisa colar manualmente no painel.

## 3. Deploy

Salvar as envs no Render dispara um novo deploy automaticamente. Depois disso:

- Novos uploads em `/acervo` vão direto para o bucket do Supabase.
- Eles passam a sobreviver a restarts/deploys do Render (que usa filesystem
  efêmero no plano free — essa era a causa dos 404 em `/storage/isolados/...`).

## 4. Imagens antigas

Os registros de `isolados` cujo arquivo já foi perdido num deploy anterior
continuam com 404 — o arquivo não existe mais em lugar nenhum, só reenviando
a foto de novo pelo formulário de edição resolve.
