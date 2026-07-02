# SIGRA — Sistema Integrado de Gestão e Rastreio Administrativo

Sistema web para registo, tramitação e rastreio de expedientes administrativos
do **Gabinete do Governador da Província da Zambézia**, substituindo os livros
protocolares em papel por um fluxo digital com histórico completo, prazos,
notificações e relatórios.

---

## 1. Visão Geral do Fluxo

O sistema segue fielmente o fluxo real descrito pela instituição:

```
Distrito → DFP (Recepção) → Técnico → Chefe do Departamento → Director do
Gabinete → DFP (retorno) → Gabinete do Governador (Homologação) → DFP
(retorno) → Tribunal Administrativo → Concluído
```

Cada mudança de gabinete fica registada permanentemente na tabela
`process_movements` (nunca apagada), com data, hora, utilizador responsável e
observação — garantindo transparência e responsabilização total.

---

## 2. Arquitectura Técnica

| Componente | Tecnologia |
|---|---|
| Linguagem | PHP 8.x |
| Arquitectura | MVC customizado (sem framework externo) |
| Base de Dados | MySQL 8.0+ / MariaDB 10.11+ (InnoDB) |
| Servidor Web | Apache com mod_rewrite, ou servidor embutido do PHP |
| Interface | HTML5, CSS3, Bootstrap 5, JavaScript (AJAX) |
| Gráficos | Chart.js |
| Geração de PDF | Impressão nativa do browser (`window.print()`) com CSS `@media print` |

### Estrutura de pastas

```
sigra/
├── app/
│   ├── Controllers/     Controladores (um por recurso)
│   ├── Models/          Modelos (PDO, um por tabela)
│   ├── Services/        Serviços (ex: envio de e-mail SMTP)
│   └── Views/           Vistas PHP, organizadas por módulo
│       └── layouts/     base.php (autenticado) e auth.php (login/recuperação)
├── core/                Núcleo do framework (Router, View, Database, Auth...)
├── database/schema.sql  Schema completo + dados iniciais (seed)
├── public/              Document root (index.php, assets, anexos)
├── routes/web.php       Todas as rotas da aplicação
├── bootstrap.php         Carrega .env, sessão e autoload
└── .env.example          Modelo de variáveis de ambiente
```

### Base de Dados — tabelas principais

| Tabela | Finalidade |
|---|---|
| `users` | Utilizadores do sistema |
| `roles` | Perfis (admin, recepcao_dfp, tecnico, chefe_departamento, director_gabinete, gabinete_governador, consulta) |
| `departments` | Os 6 "gabinetes" fixos do fluxo (DFP, Técnico, Chefia, Director, Governador, Tribunal) |
| `districts` | Os 20 distritos da Zambézia |
| `process_types` | Tipos de processo (Nomeação, Cessação de Funções, Promoção, etc.) e prazo padrão em dias |
| `processes` | Cadastro principal dos expedientes |
| `process_movements` | Histórico imutável de tramitação (linha do tempo) |
| `attachments` | Documentos anexados a cada processo |
| `notifications` | Notificações por utilizador |
| `audit_logs` | Auditoria de todas as acções sensíveis |
| `configuracoes` | Configurações gerais (nome da instituição, logótipo, SLA de alerta) |

---

## 3. Instalação (GitHub Codespaces ou local)

### 3.1. Pré-requisitos
- PHP 8.1+ com extensão `pdo_mysql`
- MySQL 8.0+ ou MariaDB 10.11+
- (Opcional) Apache com `mod_rewrite`, ou basta o servidor embutido do PHP

### 3.2. Passos

```bash
# 1. Copiar o ficheiro de ambiente
cp .env.example .env

# 2. Editar .env com as credenciais da base de dados
#    DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Criar a base de dados e importar o schema (cria também os dados iniciais)
mysql -u root -p < database/schema.sql

# 4. IMPORTANTE: gerar o hash real da senha do administrador
php -r "echo password_hash('Admin@2026', PASSWORD_BCRYPT), PHP_EOL;"
# Copiar o resultado e actualizar manualmente:
mysql -u root -p sigra_database -e "UPDATE users SET senha='<hash_gerado>' WHERE email='admin@sigra.gov.mz';"

# 5. Arrancar o servidor de desenvolvimento
php -S 0.0.0.0:8000 -t public public/router.php
```

Aceda a `http://localhost:8000` (ou ao URL público gerado pelo Codespaces).

**Credenciais iniciais:** `admin@sigra.gov.mz` / `Admin@2026`
(a alteração da senha será exigida no primeiro acesso).

### 3.3. Produção com Apache

Aponte o **DocumentRoot** para a pasta `public/`. Não é necessário `.htaccess`
adicional porque o router já resolve todas as rotas via `index.php`; para
Apache, crie um `.htaccess` simples em `public/`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

---

## 4. Perfis de Utilizador e Permissões

| Perfil | Acesso |
|---|---|
| **Administrador** | Acesso total — utilizadores, distritos, tipos, departamentos, configurações, auditoria |
| **Recepção (DFP)** | Registar processos, distribuir ao técnico, encaminhar |
| **Técnico** | Receber, actualizar andamento, encaminhar |
| **Chefe do Departamento** | Verificar, despachar, encaminhar |
| **Director do Gabinete** | Aprovar, devolver, encaminhar |
| **Gabinete do Governador** | Homologar, assinar, encaminhar |
| **Consulta** | Apenas visualização (dashboard, processos, relatórios) |

Novos utilizadores recebem uma **senha temporária gerada automaticamente**
(mostrada em ecrã, pois o envio de e-mail SMTP é opcional — ver `.env`) e são
obrigados a defini-la no primeiro acesso.

---

## 5. Funcionalidades Incluídas

- ✅ Registo completo de expedientes com upload de documentos anexos
- ✅ Fluxo de tramitação fiel ao processo real (DFP → Técnico → Chefe →
  Director → Governador → Tribunal → Concluído), com "Encaminhar" e "Devolver"
- ✅ Linha do tempo (timeline) com todos os movimentos, nunca apagados
- ✅ Pesquisa e filtros: número do processo, requerente, distrito, tipo,
  gabinete actual, estado, ano e "apenas atrasados"
- ✅ Paginação configurável (10 / 20 / 50 por página)
- ✅ Painel (Dashboard) com indicadores em tempo real: recebidos hoje, em
  andamento, concluídos, atrasados, processos por gabinete, últimos movimentos
- ✅ Relatórios com gráficos (Chart.js): por distrito, por tipo, por
  funcionário, por mês, tempo médio de tramitação — com botão de impressão
- ✅ Notificações internas (novo processo atribuído, devolução)
- ✅ Auditoria completa e imutável de todas as acções
- ✅ Gestão de utilizadores, distritos, tipos de processo e departamentos
- ✅ Recuperação de senha via SMTP nativo, com *fallback* de senha temporária
  quando o SMTP não está configurado
- ✅ Alteração de senha obrigatória no primeiro acesso
- ✅ Configurações gerais (nome da instituição, logótipo, SLA de alerta)

## 6. Possíveis Extensões Futuras

Conforme sugerido no documento de requisitos, o sistema foi desenhado para
evoluir facilmente para:

- Código QR por processo físico (a tabela `processes` já tem `codigo_interno`
  pronto para gerar o QR)
- Assinatura electrónica para despachos
- Digitalização e arquivo completo de documentos
- Integração com o correio institucional (o `MailService` já está pronto,
  bastando configurar as variáveis `MAIL_*` no `.env`)
- Alertas automáticos (job agendado/cron) para processos parados há mais de
  N dias, usando a configuração `sla_alerta_dias`
- Exportação de relatórios em PDF/Excel

---

## 7. Notas de Segurança

- Senhas guardadas com `password_hash()` (bcrypt)
- Sessões PHP nativas, com verificação de perfil em cada rota sensível
- Todas as queries usam **PDO com prepared statements** e
  `ATTR_EMULATE_PREPARES = false`
- Auditoria (`audit_logs`) regista utilizador, IP, acção e detalhes de cada
  operação sensível (login, criação, edição, tramitação, reset de senha)
- Os anexos são servidos através de uma rota protegida
  (`/anexos/{id}/download`), nunca por acesso directo à pasta `public/storage`
