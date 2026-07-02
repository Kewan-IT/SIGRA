<h3 class="mb-4">Painel Geral</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-inbox"></i></div>
                <div>
                    <div class="text-muted small">Recebidos Hoje</div>
                    <div class="fs-4 fw-bold"><?= (int) $stats['recebidos_hoje'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="text-muted small">Em Andamento</div>
                    <div class="fs-4 fw-bold"><?= (int) $stats['em_andamento'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-muted small">Concluídos</div>
                    <div class="fs-4 fw-bold"><?= (int) $stats['concluidos'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="text-muted small">Atrasados</div>
                    <div class="fs-4 fw-bold"><?= (int) $stats['atrasados'] ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h6 class="mb-3">Processos por Gabinete</h6>
                <ul class="list-group list-group-flush">
                    <?php foreach ($stats['por_departamento'] as $dep): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <?= View::e($dep['nome']) ?>
                            <span class="badge bg-primary rounded-pill"><?= (int) $dep['total'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h6 class="mb-3">Últimos Movimentos</h6>
                <?php if (empty($ultimosMovimentos)): ?>
                    <p class="text-muted small mb-0">Ainda não existem movimentos registados.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($ultimosMovimentos as $mov): ?>
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold"><?= View::e($mov['numero_processo']) ?></span>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($mov['criado_em'])) ?></small>
                                </div>
                                <div class="small text-muted">
                                    <?= View::e($mov['para_departamento_nome'] ?? '—') ?>
                                    · por <?= View::e($mov['executado_por_nome']) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
