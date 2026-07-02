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
<body class="sigra-auth-body">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-11 col-sm-8 col-md-6 col-lg-4">
                <?php $sigraLogo = (new Configuracao())->get('logo', ''); ?>
                <div class="text-center mb-4 text-white">
                    <?php if ($sigraLogo !== ''): ?>
                        <img src="/<?= View::e($sigraLogo) ?>" alt="Logótipo" class="sigra-auth-logo">
                    <?php else: ?>
                        <i class="bi bi-shield-lock-fill" style="font-size:2.5rem;"></i>
                    <?php endif; ?>
                    <h4 class="mt-2 mb-0 fw-bold">SIGRA</h4>
                    <small>Gabinete do Governador da Província da Zambézia</small>
                </div>
                <div class="card shadow-lg border-0 sigra-auth-card">
                    <div class="card-body p-4">
                        <?php if ($msg = Flash::getSucesso()): ?>
                            <div class="alert alert-success py-2"><?= View::e($msg) ?></div>
                        <?php endif; ?>
                        <?php if ($msg = Flash::getErro()): ?>
                            <div class="alert alert-danger py-2"><?= View::e($msg) ?></div>
                        <?php endif; ?>
                        <?= $content ?>
                    </div>
                </div>
                <p class="text-center text-white-50 small mt-3">&copy; <?= date('Y') ?> SIGRA</p>
            </div>
        </div>
    </div>
</body>
</html>
