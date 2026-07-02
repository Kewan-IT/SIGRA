-- ============================================================
-- SIGRA - Sistema Integrado de Gestão e Rastreio Administrativo
-- Gabinete do Governador da Zambézia
-- Schema da Base de Dados (MySQL 8.0+ / InnoDB)
-- ============================================================

CREATE DATABASE IF NOT EXISTS sigra_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sigra_database;

-- ------------------------------------------------------------
-- Perfis de Utilizador
-- ------------------------------------------------------------
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(40) NOT NULL UNIQUE,        -- admin, recepcao_dfp, tecnico, chefe_departamento, director_gabinete, gabinete_governador, consulta
    nome VARCHAR(80) NOT NULL,
    descricao VARCHAR(255) DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Departamentos / Gabinetes (os "postos" pelos quais o processo passa)
-- ------------------------------------------------------------
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(40) NOT NULL UNIQUE,  -- dfp, chefe_departamento, director_gabinete, gabinete_governador, tribunal_administrativo
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255) DEFAULT NULL,
    ordem INT DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Utilizadores
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    department_id INT DEFAULT NULL,
    cargo VARCHAR(100) DEFAULT NULL,
    telefone VARCHAR(30) DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    ativo TINYINT(1) DEFAULT 1,
    trocar_senha_obrigatorio TINYINT(1) DEFAULT 0,
    token_recuperacao VARCHAR(100) DEFAULT NULL,
    token_expira DATETIME DEFAULT NULL,
    ultimo_acesso DATETIME DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Distritos da Província da Zambézia
-- ------------------------------------------------------------
CREATE TABLE districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    ativo TINYINT(1) DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tipos de Processo
-- ------------------------------------------------------------
CREATE TABLE process_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    prazo_padrao_dias INT DEFAULT 15,   -- SLA padrão em dias
    ativo TINYINT(1) DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Processos / Expedientes
-- ------------------------------------------------------------
CREATE TABLE processes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_processo VARCHAR(40) NOT NULL UNIQUE,   -- ex: 350/2026
    codigo_interno VARCHAR(30) NOT NULL UNIQUE,     -- código curto p/ QR / consulta
    assunto VARCHAR(255) NOT NULL,
    tipo_id INT NOT NULL,
    district_id INT NOT NULL,
    requerente VARCHAR(150) DEFAULT NULL,
    data_entrada DATE NOT NULL,
    prazo_data DATE DEFAULT NULL,
    funcionario_responsavel_id INT DEFAULT NULL,   -- utilizador que detém o processo agora
    department_atual_id INT NOT NULL,              -- gabinete/departamento onde está agora
    estado_atual VARCHAR(60) NOT NULL DEFAULT 'recebido',
    observacoes TEXT DEFAULT NULL,
    criado_por INT DEFAULT NULL,
    concluido_em DATETIME DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_id) REFERENCES process_types(id),
    FOREIGN KEY (district_id) REFERENCES districts(id),
    FOREIGN KEY (funcionario_responsavel_id) REFERENCES users(id),
    FOREIGN KEY (department_atual_id) REFERENCES departments(id),
    FOREIGN KEY (criado_por) REFERENCES users(id),
    INDEX idx_estado (estado_atual),
    INDEX idx_departamento (department_atual_id),
    INDEX idx_data_entrada (data_entrada)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Movimentações (histórico - nunca apagado)
-- ------------------------------------------------------------
CREATE TABLE process_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    process_id INT NOT NULL,
    de_department_id INT DEFAULT NULL,
    para_department_id INT DEFAULT NULL,
    de_usuario_id INT DEFAULT NULL,
    para_usuario_id INT DEFAULT NULL,
    estado_anterior VARCHAR(60) DEFAULT NULL,
    estado_novo VARCHAR(60) NOT NULL,
    observacao TEXT DEFAULT NULL,
    usuario_id INT NOT NULL,       -- quem executou a acção
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (process_id) REFERENCES processes(id) ON DELETE CASCADE,
    FOREIGN KEY (de_department_id) REFERENCES departments(id),
    FOREIGN KEY (para_department_id) REFERENCES departments(id),
    FOREIGN KEY (de_usuario_id) REFERENCES users(id),
    FOREIGN KEY (para_usuario_id) REFERENCES users(id),
    FOREIGN KEY (usuario_id) REFERENCES users(id),
    INDEX idx_process (process_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Anexos
-- ------------------------------------------------------------
CREATE TABLE attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    process_id INT NOT NULL,
    nome_original VARCHAR(255) NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    tipo VARCHAR(100) DEFAULT NULL,
    tamanho INT DEFAULT NULL,
    enviado_por INT DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (process_id) REFERENCES processes(id) ON DELETE CASCADE,
    FOREIGN KEY (enviado_por) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Notificações
-- ------------------------------------------------------------
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    process_id INT DEFAULT NULL,
    tipo VARCHAR(40) NOT NULL,     -- prazo_expirado, processo_parado, novo_processo, devolvido
    mensagem VARCHAR(255) NOT NULL,
    lida TINYINT(1) DEFAULT 0,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (process_id) REFERENCES processes(id) ON DELETE CASCADE,
    INDEX idx_usuario_lida (usuario_id, lida)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Auditoria (imutável)
-- ------------------------------------------------------------
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT DEFAULT NULL,
    acao VARCHAR(100) NOT NULL,
    tabela_afetada VARCHAR(60) DEFAULT NULL,
    registro_id INT DEFAULT NULL,
    detalhes TEXT DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Configurações gerais do sistema
-- ------------------------------------------------------------
CREATE TABLE configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(60) NOT NULL UNIQUE,
    valor TEXT DEFAULT NULL,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DADOS INICIAIS (SEED)
-- ============================================================

INSERT INTO roles (chave, nome, descricao) VALUES
('admin', 'Administrador', 'Gestão total do sistema'),
('recepcao_dfp', 'Recepção (Secretaria)', 'Regista e encaminha os processos recebidos na Secretaria'),
('tecnico', 'Técnico', 'Analisa e tramita os processos atribuídos'),
('chefe_departamento', 'Chefe do Departamento', 'Verifica e despacha os processos'),
('director_gabinete', 'Director do Gabinete', 'Aprova ou devolve os processos'),
('gabinete_governador', 'Gabinete do Governador', 'Homologa e assina os despachos'),
('consulta', 'Consulta', 'Apenas visualização, sem permissões de edição');

INSERT INTO departments (chave, nome, descricao, ordem) VALUES
('secretaria', 'Secretaria', 'Recepção inicial dos processos/documentos', 0),
('dfp', 'Função Pública', 'Departamento da Função Pública (DFP)', 1),
('tecnico', 'Gabinete Técnico', 'Análise técnica dos processos', 2),
('chefe_departamento', 'Gabinete do Chefe do Departamento', 'Verificação e despacho', 3),
('director_gabinete', 'Gabinete do Director', 'Aprovação do Director do Gabinete do Governador', 4),
('gabinete_governador', 'Gabinete do Governador', 'Homologação e assinatura do Governador', 5),
('tribunal_administrativo', 'Tribunal Administrativo', 'Destino final do processo', 6),
('ugea', 'UGEA', 'Unidade de Gestão Executora das Aquisições', 10),
('planificacao', 'Planificação', 'Departamento de Planificação', 11),
('financas', 'Finanças', 'Departamento de Finanças', 12),
('assessoria', 'Departamento de Assessoria', 'Assessoria Jurídica/Técnica', 13);

INSERT INTO districts (nome) VALUES
('Alto Molócuè'), ('Gurué'), ('Ile'), ('Maganja da Costa'), ('Milange'),
('Mocuba'), ('Mopeia'), ('Morrumbala'), ('Namacurra'), ('Nicoadala'),
('Pebane'), ('Quelimane'), ('Lugela'), ('Gilé'), ('Inhassunge'), ('Chinde'), ('Derre'), ('Luabo'), ('Maquival'), ('Namarroi');

INSERT INTO process_types (nome, prazo_padrao_dias) VALUES
('Nomeação', 20),
('Cessação de Funções', 15),
('Promoção', 20),
('Transferência', 15),
('Recondução', 15),
('Licença', 10),
('Aposentação', 30),
('Outros', 15);

-- Utilizador administrador inicial
-- senha padrão: Admin@2026  (deve ser alterada no primeiro acesso)
INSERT INTO users (nome, email, senha, role_id, department_id, cargo, trocar_senha_obrigatorio)
VALUES ('Administrador do Sistema', 'admin@sigra.gov.mz', '$2y$10$5/yqaJb7BmjkYQ7qnvsfSOMoCinHo05l94Bur63kXq7fIs/LFu1z2', 1, 1, 'Administrador de Sistemas', 1);

INSERT INTO configuracoes (chave, valor) VALUES
('nome_instituicao', 'Gabinete do Governador da Província da Zambézia'),
('nome_sistema', 'SIGRA - Sistema Integrado de Gestão e Rastreio Administrativo'),
('logo', ''),
('sla_alerta_dias', '5'),
('email_notificacoes', '1');
