# Micoteca — Sistema de Gerenciamento de Acervo de Fungos (CFUFMA)

Sistema web para gestão do acervo micológico da coleção de fungos da UFMA: cadastro de isolados, importação de planilhas Excel, relatórios e controle de usuários.

> **Status atual: backend real.** Acervo, Importação, Login/Cadastro e Usuários já gravam em banco de dados (SQLite) de verdade. Autenticação, cadastro público e controle de permissões por perfil (Administrador/Curador/Consulta) estão implementados e protegendo as rotas.

---

## Tecnologias usadas

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Banco de dados | SQLite (arquivo `database/database.sqlite`) |
| Importação de Excel | `maatwebsite/excel` (PhpSpreadsheet) |
| Views | Blade (templates do Laravel) |
| Estilo | Tailwind CSS 4 |
| Interatividade | Alpine.js 3 + Axios (chamadas AJAX da importação) |
| Build de assets | Vite 7 + `laravel-vite-plugin` |
| Fonte | Inter (Google Fonts) |

---

## Como ver no navegador

O projeto precisa de **Node.js 20+** (o Vite 7 não roda em versões mais antigas). Se você usa `nvm`, já existe um `.nvmrc` no projeto.

```bash
# 1. Entre na pasta do projeto
cd cfufma-acervo

# 2. Use a versão certa do Node (só na primeira vez pode pedir para instalar)
nvm use

# 3. Instale as dependências (só precisa rodar de novo se composer.json/package.json mudar)
composer install
npm install

# 4. Garanta que existe um .env (copie do .env.example se não existir) e gere a chave
cp -n .env.example .env
php artisan key:generate

# 5. Rode as migrations (cria as tabelas no SQLite)
php artisan migrate

# 6. Suba o servidor de assets (Tailwind/JS) em um terminal
npm run dev

# 7. Em outro terminal, suba o servidor do Laravel
php artisan serve
```

Depois abra **http://127.0.0.1:8000** no navegador — ele redireciona automaticamente para `/dashboard`.

Se preferir gerar os assets uma vez só (sem deixar o `npm run dev` rodando), use `npm run build` no lugar do passo 6.

### Login e cadastro

- `/login` autentica de verdade contra a tabela `users` (senha com hash, sessão real). Todas as telas internas exigem login — sem sessão, você é redirecionado para `/login`.
- `/registrar` permite que qualquer pessoa crie a própria conta (nome, e-mail, senha). Contas criadas por essa tela sempre entram com o perfil **Consulta** (o mais restrito) e já logam automaticamente após o cadastro.
- Para criar a primeira conta (ou uma conta Administrador), use o Tinker:
  ```bash
  php artisan tinker --execute="
  App\Models\User::create([
      'name' => 'Seu Nome',
      'email' => 'seu-email@ufma.br',
      'password' => 'sua-senha',
      'perfil' => 'Administrador',
      'ativo' => true,
  ]);
  "
  ```

### Perfis e permissões

Existem três perfis, cada um com acesso diferente:

| Perfil | Acervo/Relatórios | Importação | Gestão de usuários |
|---|---|---|---|
| Administrador | ✅ | ✅ | ✅ |
| Curador | ✅ | ✅ | ❌ |
| Consulta | ✅ | ❌ | ❌ |

O middleware `perfil` (`app/Http/Middleware/EnsurePerfil.php`) bloqueia com HTTP 403 quem tenta acessar uma rota sem o perfil exigido, e a interface (sidebar/menu) já esconde os links que a pessoa não pode usar. Só um **Administrador** pode criar/editar/ativar/desativar outros usuários e trocar o perfil de alguém, pela tela **Configurações → Usuários**.

---

## Como testar com uma planilha real

1. Acesse **Importar Excel** no menu lateral (`/importacao`).
2. Arraste ou selecione seu arquivo `.xls`/`.xlsx` (até 10 MB).
3. Clique em **Analisar arquivo** — o sistema lê o arquivo de verdade, detecta os cabeçalhos e mostra colunas ausentes, linhas inválidas e duplicidades reais.
4. Clique em **Confirmar importação** — os registros válidos são gravados no banco. Você é levado para a página de detalhes da importação com o resultado real.
5. Os registros aparecem imediatamente em **Acervo**, no **Dashboard** e nos **Relatórios**.

**Cabeçalhos reconhecidos** (com tolerância a acentos/maiúsculas e pequenas variações): Código, Gênero, Espécie, Origem, Meio de cultivo, Data, Conservação, Local, Armazenamento, Autor, Descrição, Foto (link). Uma linha só é rejeitada se faltar **Código**, **Gênero** ou **Espécie**; os demais campos são opcionais. Um **Código** que já existe no banco (ou repetido dentro do próprio arquivo) é tratado como duplicidade e não é importado de novo.

A coluna **Foto** deve conter um link direto para a imagem (ex.: `https://.../foto.jpg`); o sistema baixa e salva a foto durante a importação. Se o link estiver quebrado, não apontar para uma imagem válida, ou a URL não usar `http(s)`, o isolado é importado normalmente, só que sem foto — o link inválido nunca interrompe a importação da linha.

---

## Telas disponíveis

| Rota | Tela | Dados | Acesso |
|---|---|---|---|
| `/login` | Login | real | público |
| `/registrar` | Cadastro de conta | real | público |
| `/dashboard` | Painel geral com estatísticas, gráficos e atalhos | real | autenticado |
| `/importacao` | Upload de planilha (drag & drop) com validação e pré-visualização | real | Administrador, Curador |
| `/importacao/historico` | Histórico de importações | real | Administrador, Curador |
| `/importacao/{id}` | Detalhes de uma importação | real | Administrador, Curador |
| `/acervo` | Listagem do acervo (busca, filtros, ordenação, paginação) | real | autenticado |
| `/acervo/criar` | Cadastro manual de isolado | real | autenticado |
| `/acervo/{id}` | Visualização de um isolado | real | autenticado |
| `/acervo/{id}/editar` | Edição de um isolado | real | autenticado |
| `/relatorios` | Relatórios com filtros e gráficos | real | autenticado |
| `/configuracoes` | Perfil (próprios dados) e alteração de senha | real | autenticado |
| `/configuracoes/usuarios` | Gestão de usuários e perfis | real | Administrador |

---

## O que foi feito

- **Banco de dados real (SQLite)**: tabelas `isolados` (código, gênero, espécie, origem, meio de cultivo, data, conservação, local, armazenamento, autor), `importacoes` (histórico de uploads, com contadores e colunas ausentes/linhas inválidas em JSON) e `users` (nome, e-mail, senha com hash, perfil, status ativo/inativo, último acesso).
- **Autenticação real**: login/logout contra a tabela `users` (`Auth::attempt`, sessão, senha com hash), cadastro público em `/registrar`, todas as rotas internas protegidas por middleware `auth`.
- **Perfis e permissões**: perfis Administrador/Curador/Consulta com middleware `perfil` bloqueando rotas por HTTP 403, e a interface escondendo links que o usuário logado não pode acessar. Só Administrador cria/edita/ativa/desativa outros usuários ou muda o perfil de alguém.
- **Importação real de Excel**: `app/Services/IsoladoImportService.php` lê o arquivo enviado com `maatwebsite/excel`, casa os cabeçalhos da planilha com os campos do sistema (tolerando variações de acento/nome), valida campos obrigatórios, detecta duplicidade por código (no banco e dentro do próprio arquivo) e grava os isolados válidos vinculados ao registro da importação.
- **Acervo real**: busca, filtros por gênero/conservação, ordenação por coluna, paginação, cadastro/edição/exclusão e exclusão em massa gravam e leem direto do banco.
- **Dashboard e Relatórios**: estatísticas, gráficos de barras e filtros calculados a partir dos dados reais do banco.
- **Design system em Blade**: componentes reutilizáveis (`x-button`, `x-card`, `x-input`, `x-select`, `x-modal`, `x-alert`, `x-badge`, `x-pagination`, `x-empty-state`, `x-toast`) usados em todas as telas, com a paleta `#FFFFFF #EAF8F0 #3FAF75 #2F9A66 #6B7280 #1F2937 #F8FAF8`.
- **Layout**: sidebar fixa no desktop / off-canvas no mobile, navbar com menu do usuário, barra de carregamento e toasts globais.

## O que foi feito (sessão de imagem/galeria pública)

- **Foto e descrição por isolado**: upload de imagem (`storage/app/public/isolados`) e campo de descrição em cada registro, exibidos no cadastro/edição/detalhe do Acervo.
- **Galeria pública** (`/galeria`, sem login): grade de espécies com foto/nome/descrição, busca, filtro por gênero e por "com/sem foto", página de detalhe própria. `/` leva quem não está logado direto pra lá.
- **Exportação de relatórios**: os botões "Exportar Excel" e "Exportar PDF" em `/relatorios` agora funcionam de verdade (`maatwebsite/excel` e `barryvdh/laravel-dompdf`), respeitando os filtros aplicados na tela.
- **Recuperação de senha**: fluxo completo (`/esqueci-senha` → e-mail com link → `/redefinir-senha/{token}`), com e-mail em português. Localmente o envio fica só no log (`MAIL_MAILER=log`) — falta configurar um SMTP real para produção.
- **Filtro "com foto / sem foto"** no Acervo interno e na Galeria pública.

## O que foi feito (permissões, importação e limpeza)

- **Importação com foto e descrição**: a planilha aceita agora as colunas "Descrição" e "Foto" (com um link direto pra imagem — o sistema baixa e salva durante a importação). Um link quebrado nunca impede a importação da linha, só deixa o isolado sem foto.
- **Permissões por perfil de verdade**: a tabela em Configurações → Usuários agora persiste no banco (`perfil_permissoes`) e é aplicada de verdade nas rotas de Acervo, Importação e Relatórios via middleware `modulo`. O perfil Administrador tem acesso total fixo (não editável pela interface), para não travar o próprio acesso à tela de permissões.
- Removido o badge "Dados mockados" do painel interno — resquício da fase de protótipo, os dados já são reais.

## O que falta

- **Envio de e-mail real**: hoje o link de recuperação de senha só é gravado em `storage/logs/laravel.log`. Precisa de um provedor SMTP configurado no `.env` (Gmail, SendGrid, institucional da UFMA, etc.) para funcionar em produção.
- Sem limite de tentativas de login (rate limiting) — não há proteção contra força bruta na tela `/login`.
- Deploy em um banco de produção (PostgreSQL), se for o caso — hoje roda em SQLite local.
