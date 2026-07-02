<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGRA - Sistema Integrado de Gestão e Rastreio Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>
<?php
    $roleSlug = Auth::roleSlug();
    $notifModel = new Notification();
    $naoLidas = Auth::check() ? $notifModel->naoLidasCount(Auth::id()) : 0;
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    function sigraNavAtivo(string $path, string $current): string {
        return ($current === $path || str_starts_with($current, $path . '/')) ? 'active' : '';
    }
?>
<div class="d-flex sigra-app">
    <nav class="sigra-sidebar d-flex flex-column p-3">
        <div class="d-flex align-items-center gap-2 mb-4 px-2">
            <i class="bi bi-shield-lock-fill fs-3"></i>
            <div>
                <div class="fw-bold">SIGRA</div>
                <small class="text-white-50">Gov. Zambézia</small>
            </div>
        </div>
        <ul class="nav nav-pills flex-column gap-1">
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/dashboard', $currentPath) ?>" href="/dashboard">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/processos', $currentPath) ?>" href="/processos">
                    <i class="bi bi-folder2-open me-2"></i> Processos
                </a>
            </li>
            <?php if (in_array($roleSlug, ['admin', 'recepcao_dfp'], true)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/processos/novo">
                    <i class="bi bi-plus-circle me-2"></i> Novo Processo
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/relatorios', $currentPath) ?>" href="/relatorios">
                    <i class="bi bi-bar-chart-line me-2"></i> Relatórios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/notificacoes', $currentPath) ?>" href="/notificacoes">
                    <i class="bi bi-bell me-2"></i> Notificações
                    <?php if ($naoLidas > 0): ?><span class="badge bg-danger ms-1"><?= $naoLidas ?></span><?php endif; ?>
                </a>
            </li>

            <?php if ($roleSlug === 'admin'): ?>
            <li class="mt-3 px-2 text-white-50 small text-uppercase">Administração</li>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/utilizadores', $currentPath) ?>" href="/utilizadores">
                    <i class="bi bi-people me-2"></i> Utilizadores
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/distritos', $currentPath) ?>" href="/distritos">
                    <i class="bi bi-geo-alt me-2"></i> Distritos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/tipos-processo', $currentPath) ?>" href="/tipos-processo">
                    <i class="bi bi-tags me-2"></i> Tipos de Processo
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/departamentos', $currentPath) ?>" href="/departamentos">
                    <i class="bi bi-diagram-3 me-2"></i> Departamentos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/auditoria', $currentPath) ?>" href="/auditoria">
                    <i class="bi bi-journal-check me-2"></i> Auditoria
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= sigraNavAtivo('/configuracoes', $currentPath) ?>" href="/configuracoes">
                    <i class="bi bi-gear me-2"></i> Configurações
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="mt-auto px-2">
            <hr class="text-white-50">
            <div class="small text-white-50 mb-1"><?= View::e(Auth::user()['nome'] ?? '') ?></div>
            <div class="small text-white-50 mb-2"><?= View::e(Auth::user()['role_nome'] ?? '') ?></div>
            <a href="/logout" class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right me-1"></i> Sair</a>
        </div>
    </nav>

    <main class="sigra-content flex-grow-1">
        <div class="p-4">
            <?php if ($msg = Flash::getSucesso()): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= View::e($msg) ?>
                </div>
            <?php endif; ?>
            <?php if ($msg = Flash::getErro()): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= View::e($msg) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
