<?php

/** @var Router $router */

// -------------------------------------------------------------
// Autenticação
// -------------------------------------------------------------
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/esqueci-senha', [AuthController::class, 'esqueciSenhaForm']);
$router->post('/esqueci-senha', [AuthController::class, 'esqueciSenha']);
$router->get('/redefinir-senha', [AuthController::class, 'redefinirSenhaForm']);
$router->post('/redefinir-senha', [AuthController::class, 'redefinirSenha']);
$router->get('/trocar-senha', [AuthController::class, 'trocarSenhaForm']);
$router->post('/trocar-senha', [AuthController::class, 'trocarSenha']);

// -------------------------------------------------------------
// Dashboard
// -------------------------------------------------------------
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

// -------------------------------------------------------------
// Processos (expedientes) - rotas estáticas ANTES das wildcard {id}
// -------------------------------------------------------------
$router->get('/processos', [ProcessController::class, 'index']);
$router->get('/processos/novo', [ProcessController::class, 'createForm']);
$router->post('/processos', [ProcessController::class, 'store']);
$router->get('/processos/{id}', [ProcessController::class, 'show']);
$router->get('/processos/{id}/distribuir', [ProcessController::class, 'distribuirForm']);
$router->post('/processos/{id}/distribuir', [ProcessController::class, 'distribuir']);
$router->post('/processos/{id}/encaminhar', [ProcessController::class, 'encaminhar']);
$router->post('/processos/{id}/devolver', [ProcessController::class, 'devolver']);

// -------------------------------------------------------------
// Anexos (download protegido)
// -------------------------------------------------------------
$router->get('/anexos/{id}/download', [AttachmentController::class, 'download']);

// -------------------------------------------------------------
// Relatórios
// -------------------------------------------------------------
$router->get('/relatorios', [ReportController::class, 'index']);

// -------------------------------------------------------------
// Notificações
// -------------------------------------------------------------
$router->get('/notificacoes', [NotificationController::class, 'index']);
$router->post('/notificacoes/{id}/lida', [NotificationController::class, 'marcarLida']);
$router->post('/notificacoes/marcar-todas', [NotificationController::class, 'marcarTodasLidas']);

// -------------------------------------------------------------
// Administração: Distritos
// -------------------------------------------------------------
$router->get('/distritos', [DistrictController::class, 'index']);
$router->post('/distritos', [DistrictController::class, 'store']);
$router->post('/distritos/{id}', [DistrictController::class, 'update']);
$router->post('/distritos/{id}/eliminar', [DistrictController::class, 'delete']);

// -------------------------------------------------------------
// Administração: Tipos de Processo
// -------------------------------------------------------------
$router->get('/tipos-processo', [ProcessTypeController::class, 'index']);
$router->post('/tipos-processo', [ProcessTypeController::class, 'store']);
$router->post('/tipos-processo/{id}', [ProcessTypeController::class, 'update']);
$router->post('/tipos-processo/{id}/eliminar', [ProcessTypeController::class, 'delete']);

// -------------------------------------------------------------
// Administração: Departamentos
// -------------------------------------------------------------
$router->get('/departamentos', [DepartmentController::class, 'index']);
$router->post('/departamentos/{id}', [DepartmentController::class, 'update']);

// -------------------------------------------------------------
// Administração: Utilizadores
// -------------------------------------------------------------
$router->get('/utilizadores', [UserController::class, 'index']);
$router->get('/utilizadores/novo', [UserController::class, 'createForm']);
$router->post('/utilizadores', [UserController::class, 'store']);
$router->get('/utilizadores/{id}/editar', [UserController::class, 'editForm']);
$router->post('/utilizadores/{id}', [UserController::class, 'update']);
$router->post('/utilizadores/{id}/resetar-senha', [UserController::class, 'resetarSenha']);

// -------------------------------------------------------------
// Administração: Configurações e Auditoria
// -------------------------------------------------------------
$router->get('/configuracoes', [ConfiguracaoController::class, 'index']);
$router->post('/configuracoes', [ConfiguracaoController::class, 'update']);
$router->get('/auditoria', [AuditController::class, 'index']);
