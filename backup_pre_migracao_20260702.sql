/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: sigra_database
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `nome_original` varchar(255) NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `tamanho` int(11) DEFAULT NULL,
  `enviado_por` int(11) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `process_id` (`process_id`),
  KEY `enviado_por` (`enviado_por`),
  CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`process_id`) REFERENCES `processes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attachments_ibfk_2` FOREIGN KEY (`enviado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(100) NOT NULL,
  `tabela_afetada` varchar(60) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES
(1,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 07:12:18'),
(2,1,'criar','processes',1,'Processo 1/2026 registado','127.0.0.1','2026-07-02 07:14:20'),
(3,1,'criar','users',2,'Utilizador katia123@sigra.gov.mz criado','127.0.0.1','2026-07-02 07:16:11'),
(4,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 07:20:19'),
(5,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 07:20:39'),
(6,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 07:21:02'),
(7,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 09:49:11'),
(8,1,'reset_senha','users',2,'Senha redefinida pelo administrador','127.0.0.1','2026-07-02 09:53:28'),
(9,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 09:53:40'),
(10,2,'login','users',2,'Login efectuado','127.0.0.1','2026-07-02 09:53:53'),
(11,2,'logout','users',2,'Logout efectuado','127.0.0.1','2026-07-02 09:56:56'),
(12,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 09:57:01'),
(13,1,'criar','districts',NULL,'Molumbo','127.0.0.1','2026-07-02 10:26:08'),
(14,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 11:09:49'),
(15,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 11:40:40'),
(16,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 11:40:52'),
(17,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 11:48:01'),
(18,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 11:55:43'),
(19,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 12:07:25'),
(20,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 12:08:15'),
(21,1,'distribuir','processes',1,'Distribuído ao utilizador #2','127.0.0.1','2026-07-02 12:55:24'),
(22,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 12:55:52'),
(23,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 12:57:08'),
(24,1,'reset_senha','users',2,'Senha redefinida pelo administrador','127.0.0.1','2026-07-02 12:57:51'),
(25,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 12:58:14'),
(26,2,'login','users',2,'Login efectuado','127.0.0.1','2026-07-02 12:58:37'),
(27,2,'encaminhar','processes',1,'Enviado ao Chefe do Departamento','127.0.0.1','2026-07-02 13:03:54'),
(28,2,'logout','users',2,'Logout efectuado','127.0.0.1','2026-07-02 13:05:14'),
(29,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 13:05:21'),
(30,1,'criar','users',3,'Utilizador cmota@sigra.gov.mz criado','127.0.0.1','2026-07-02 13:07:07'),
(31,1,'criar','users',4,'Utilizador jjaime@sigra.gov.mz criado','127.0.0.1','2026-07-02 13:08:19'),
(32,1,'reset_senha','users',3,'Senha redefinida pelo administrador','127.0.0.1','2026-07-02 17:53:46'),
(33,1,'logout','users',1,'Logout efectuado','127.0.0.1','2026-07-02 17:53:56'),
(34,3,'login','users',3,'Login efectuado','127.0.0.1','2026-07-02 17:54:19'),
(35,3,'encaminhar','processes',1,'Enviado ao Director do Gabinete','127.0.0.1','2026-07-02 17:55:23'),
(36,3,'logout','users',3,'Logout efectuado','127.0.0.1','2026-07-02 17:56:06'),
(37,1,'login','users',1,'Login efectuado','127.0.0.1','2026-07-02 17:56:27');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracoes`
--

DROP TABLE IF EXISTS `configuracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(60) NOT NULL,
  `valor` text DEFAULT NULL,
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracoes`
--

LOCK TABLES `configuracoes` WRITE;
/*!40000 ALTER TABLE `configuracoes` DISABLE KEYS */;
INSERT INTO `configuracoes` VALUES
(1,'nome_instituicao','Gabinete do Governador da Província da Zambézia','2026-07-02 06:38:19'),
(2,'nome_sistema','SIGRA - Sistema Integrado de Gestão e Rastreio Administrativo','2026-07-02 06:38:19'),
(3,'logo','assets/img/logo.png','2026-07-02 07:19:55'),
(4,'sla_alerta_dias','5','2026-07-02 06:38:19'),
(5,'email_notificacoes','1','2026-07-02 06:38:19');
/*!40000 ALTER TABLE `configuracoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(40) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES
(1,'dfp','Departamento da Função Pública','Recepção, distribuição e envio dos processos',1,1,'2026-07-02 06:38:19'),
(2,'tecnico','Gabinete Técnico','Análise técnica dos processos',2,1,'2026-07-02 06:38:19'),
(3,'chefe_departamento','Gabinete do Chefe do Departamento','Verificação e despacho',3,1,'2026-07-02 06:38:19'),
(4,'director_gabinete','Gabinete do Director','Aprovação do Director do Gabinete do Governador',4,1,'2026-07-02 06:38:19'),
(5,'gabinete_governador','Gabinete do Governador','Homologação e assinatura do Governador',5,1,'2026-07-02 06:38:19'),
(6,'tribunal_administrativo','Tribunal Administrativo','Destino final do processo',6,1,'2026-07-02 06:38:19');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES
(1,'Alto Molócuè',1,'2026-07-02 06:38:19'),
(2,'Gurué',1,'2026-07-02 06:38:19'),
(3,'Ile',1,'2026-07-02 06:38:19'),
(4,'Maganja da Costa',1,'2026-07-02 06:38:19'),
(5,'Milange',1,'2026-07-02 06:38:19'),
(6,'Mocuba',1,'2026-07-02 06:38:19'),
(7,'Mopeia',1,'2026-07-02 06:38:19'),
(8,'Morrumbala',1,'2026-07-02 06:38:19'),
(9,'Namacurra',1,'2026-07-02 06:38:19'),
(10,'Nicoadala',1,'2026-07-02 06:38:19'),
(11,'Pebane',1,'2026-07-02 06:38:19'),
(12,'Quelimane',1,'2026-07-02 06:38:19'),
(13,'Lugela',1,'2026-07-02 06:38:19'),
(14,'Gilé',1,'2026-07-02 06:38:19'),
(15,'Inhassunge',1,'2026-07-02 06:38:19'),
(16,'Chinde',1,'2026-07-02 06:38:19'),
(17,'Derre',1,'2026-07-02 06:38:19'),
(18,'Luabo',1,'2026-07-02 06:38:19'),
(19,'Maquival',1,'2026-07-02 06:38:19'),
(20,'Namarroi',1,'2026-07-02 06:38:19'),
(21,'Molumbo',1,'2026-07-02 10:26:08');
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `process_id` int(11) DEFAULT NULL,
  `tipo` varchar(40) NOT NULL,
  `mensagem` varchar(255) NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `process_id` (`process_id`),
  KEY `idx_usuario_lida` (`usuario_id`,`lida`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`process_id`) REFERENCES `processes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
(1,2,1,'novo_processo','Foi-lhe atribuído o processo 1/2026.',1,'2026-07-02 12:55:24');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `process_movements`
--

DROP TABLE IF EXISTS `process_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `process_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `de_department_id` int(11) DEFAULT NULL,
  `para_department_id` int(11) DEFAULT NULL,
  `de_usuario_id` int(11) DEFAULT NULL,
  `para_usuario_id` int(11) DEFAULT NULL,
  `estado_anterior` varchar(60) DEFAULT NULL,
  `estado_novo` varchar(60) NOT NULL,
  `observacao` text DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `de_department_id` (`de_department_id`),
  KEY `para_department_id` (`para_department_id`),
  KEY `de_usuario_id` (`de_usuario_id`),
  KEY `para_usuario_id` (`para_usuario_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_process` (`process_id`),
  CONSTRAINT `process_movements_ibfk_1` FOREIGN KEY (`process_id`) REFERENCES `processes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `process_movements_ibfk_2` FOREIGN KEY (`de_department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `process_movements_ibfk_3` FOREIGN KEY (`para_department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `process_movements_ibfk_4` FOREIGN KEY (`de_usuario_id`) REFERENCES `users` (`id`),
  CONSTRAINT `process_movements_ibfk_5` FOREIGN KEY (`para_usuario_id`) REFERENCES `users` (`id`),
  CONSTRAINT `process_movements_ibfk_6` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `process_movements`
--

LOCK TABLES `process_movements` WRITE;
/*!40000 ALTER TABLE `process_movements` DISABLE KEYS */;
INSERT INTO `process_movements` VALUES
(1,1,NULL,1,NULL,NULL,NULL,'recebido','Processo registado na recepção (DFP).',1,'2026-07-02 07:14:20'),
(2,1,1,2,NULL,2,'recebido','distribuido_tecnico','Priorise este processo',1,'2026-07-02 12:55:24'),
(3,1,2,3,NULL,NULL,'distribuido_tecnico','enviado_chefe','Ja esta tramitado, peco para assinar a nota de envio',2,'2026-07-02 13:03:54'),
(4,1,3,4,NULL,NULL,'enviado_chefe','enviado_diretor','Enviado ao Director do Gabinete',3,'2026-07-02 17:55:23');
/*!40000 ALTER TABLE `process_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `process_types`
--

DROP TABLE IF EXISTS `process_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `process_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `prazo_padrao_dias` int(11) DEFAULT 15,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `process_types`
--

LOCK TABLES `process_types` WRITE;
/*!40000 ALTER TABLE `process_types` DISABLE KEYS */;
INSERT INTO `process_types` VALUES
(1,'Nomeação',20,1,'2026-07-02 06:38:19'),
(2,'Cessação de Funções',15,1,'2026-07-02 06:38:19'),
(3,'Promoção',20,1,'2026-07-02 06:38:19'),
(4,'Transferência',15,1,'2026-07-02 06:38:19'),
(5,'Recondução',15,1,'2026-07-02 06:38:19'),
(6,'Licença',10,1,'2026-07-02 06:38:19'),
(7,'Aposentação',30,1,'2026-07-02 06:38:19'),
(8,'Outros',15,1,'2026-07-02 06:38:19');
/*!40000 ALTER TABLE `process_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `processes`
--

DROP TABLE IF EXISTS `processes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `processes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_processo` varchar(40) NOT NULL,
  `codigo_interno` varchar(30) NOT NULL,
  `assunto` varchar(255) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `requerente` varchar(150) DEFAULT NULL,
  `data_entrada` date NOT NULL,
  `prazo_data` date DEFAULT NULL,
  `funcionario_responsavel_id` int(11) DEFAULT NULL,
  `department_atual_id` int(11) NOT NULL,
  `estado_atual` varchar(60) NOT NULL DEFAULT 'recebido',
  `observacoes` text DEFAULT NULL,
  `criado_por` int(11) DEFAULT NULL,
  `concluido_em` datetime DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_processo` (`numero_processo`),
  UNIQUE KEY `codigo_interno` (`codigo_interno`),
  KEY `tipo_id` (`tipo_id`),
  KEY `district_id` (`district_id`),
  KEY `funcionario_responsavel_id` (`funcionario_responsavel_id`),
  KEY `criado_por` (`criado_por`),
  KEY `idx_estado` (`estado_atual`),
  KEY `idx_departamento` (`department_atual_id`),
  KEY `idx_data_entrada` (`data_entrada`),
  CONSTRAINT `processes_ibfk_1` FOREIGN KEY (`tipo_id`) REFERENCES `process_types` (`id`),
  CONSTRAINT `processes_ibfk_2` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  CONSTRAINT `processes_ibfk_3` FOREIGN KEY (`funcionario_responsavel_id`) REFERENCES `users` (`id`),
  CONSTRAINT `processes_ibfk_4` FOREIGN KEY (`department_atual_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `processes_ibfk_5` FOREIGN KEY (`criado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processes`
--

LOCK TABLES `processes` WRITE;
/*!40000 ALTER TABLE `processes` DISABLE KEYS */;
INSERT INTO `processes` VALUES
(1,'1/2026','SIGRA-20260702-AD50CA','Nomeacao de diretores das escolas primarias',1,14,'Antonio Baptista','2026-06-29','2026-07-19',2,4,'enviado_diretor','',1,NULL,'2026-07-02 07:14:20','2026-07-02 17:55:23');
/*!40000 ALTER TABLE `processes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(40) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'admin','Administrador','Gestão total do sistema','2026-07-02 06:38:19'),
(2,'recepcao_dfp','Recepção (DFP)','Regista e distribui os processos recebidos','2026-07-02 06:38:19'),
(3,'tecnico','Técnico','Analisa e tramita os processos atribuídos','2026-07-02 06:38:19'),
(4,'chefe_departamento','Chefe do Departamento','Verifica e despacha os processos','2026-07-02 06:38:19'),
(5,'director_gabinete','Director do Gabinete','Aprova ou devolve os processos','2026-07-02 06:38:19'),
(6,'gabinete_governador','Gabinete do Governador','Homologa e assina os despachos','2026-07-02 06:38:19'),
(7,'consulta','Consulta','Apenas visualização, sem permissões de edição','2026-07-02 06:38:19');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `trocar_senha_obrigatorio` tinyint(1) DEFAULT 0,
  `token_recuperacao` varchar(100) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Administrador do Sistema','admin@sigra.gov.mz','$2y$12$aOArvwegy.irL7rj6.TPI.iT0Mxe4uTDeI1x3k9WMQpf4F.fIGAF2',1,1,'Administrador de Sistemas',NULL,NULL,1,0,NULL,NULL,'2026-07-02 19:56:27','2026-07-02 06:38:19','2026-07-02 17:56:27'),
(2,'Katia Francisco','katia123@sigra.gov.mz','$2y$12$GVdRebzn927L5maQarLKpuJFONBZCh0S10cNyOZpi1M6sAo53TEVO',3,1,'Tecnica','868900224',NULL,1,0,NULL,NULL,'2026-07-02 14:58:37','2026-07-02 07:16:11','2026-07-02 12:58:59'),
(3,'Carlos Mota','cmota@sigra.gov.mz','$2y$12$w6im30PupBreLESqEB3/gOUjTeq34UkMnqsSknhZ9chIS3Yx.HR8m',4,3,'Chefe do DFP','874656231',NULL,1,0,NULL,NULL,'2026-07-02 19:54:19','2026-07-02 13:07:07','2026-07-02 17:54:35'),
(4,'Jamal Jaime','jjaime@sigra.gov.mz','$2y$12$uySK7wAcOrP16lJpJv.uYOFQiGyxw3M.4aBqR4ha42ZTVask2htwS',5,4,'Secretario do Diretor','846023894',NULL,1,1,NULL,NULL,NULL,'2026-07-02 13:08:19','2026-07-02 13:08:19');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-02 18:17:44
