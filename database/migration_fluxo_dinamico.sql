-- ============================================================
-- SIGRA - Migração: Fluxo Dinâmico de Tramitação
-- Introduz a "Secretaria" como ponto único de entrada e liberta
-- o encaminhamento para qualquer sector (deixa de haver sequência fixa).
-- Este script é idempotente: pode ser executado mais do que uma vez.
-- ============================================================

USE sigra_database;

-- ------------------------------------------------------------
-- 1) Novo sector de entrada: Secretaria
-- ------------------------------------------------------------
INSERT INTO departments (chave, nome, descricao, ordem, ativo)
VALUES ('secretaria', 'Secretaria', 'Recepção inicial dos processos/documentos', 0, 1)
ON DUPLICATE KEY UPDATE nome = VALUES(nome), descricao = VALUES(descricao);

-- ------------------------------------------------------------
-- 2) O antigo "DFP" passa a ser apenas mais um sector de destino
-- ------------------------------------------------------------
UPDATE departments
   SET nome = 'Função Pública', descricao = 'Departamento da Função Pública (DFP)'
 WHERE chave = 'dfp';

UPDATE roles
   SET nome = 'Recepção (Secretaria)', descricao = 'Regista e encaminha os processos recebidos na Secretaria'
 WHERE chave = 'recepcao_dfp';

-- ------------------------------------------------------------
-- 3) Novos sectores mencionados pelo utilizador
-- ------------------------------------------------------------
INSERT INTO departments (chave, nome, descricao, ordem, ativo) VALUES
('ugea', 'UGEA', 'Unidade de Gestão Executora das Aquisições', 10, 1),
('planificacao', 'Planificação', 'Departamento de Planificação', 11, 1),
('financas', 'Finanças', 'Departamento de Finanças', 12, 1),
('assessoria', 'Departamento de Assessoria', 'Assessoria Jurídica/Técnica', 13, 1)
ON DUPLICATE KEY UPDATE nome = VALUES(nome), descricao = VALUES(descricao);

-- 'chefe_departamento', 'director_gabinete' ("Gabinete do Director"),
-- 'gabinete_governador' ("Gabinete do Governador") e 'tribunal_administrativo'
-- já existem na tabela departments e continuam disponíveis como sectores
-- normais de destino (deixam de ser etapas obrigatórias fixas).

-- ------------------------------------------------------------
-- 4) Processos antigos que estavam no sector "dfp" com estado
--    "recebido" (ainda não distribuídos) passam a estar na Secretaria,
--    para manter a coerência do novo fluxo de entrada.
-- ------------------------------------------------------------
UPDATE processes p
  JOIN departments dsec ON dsec.chave = 'secretaria'
  JOIN departments ddfp ON ddfp.chave = 'dfp'
   SET p.department_atual_id = dsec.id
 WHERE p.department_atual_id = ddfp.id
   AND p.estado_atual = 'recebido';
