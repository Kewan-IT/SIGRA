<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Notificações</h3>
    <form method="POST" action="/notificacoes/marcar-todas">
        <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar todas como lidas</button>
    </form>
</div>

<div class="card stat-card">
    <div class="list-group list-group-flush">
        <?php if (empty($notificacoes)): ?>
            <div class="list-group-item text-muted small text-center py-4">Sem notificações.</div>
        <?php endif; ?>
        <?php foreach ($notificacoes as $n): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center <?= (int) $n['lida'] === 0 ? 'bg-light' : '' ?>">
                <div>
                    <div><?= View::e($n['mensagem']) ?></div>
                    <small class="text-muted">
                        <?= date('d/m/Y H:i', strtotime($n['criado_em'])) ?>
                        <?php if (!empty($n['numero_processo'])): ?>
                            · <a href="/processos/<?= $n['process_id'] ?>">Ver processo <?= View::e($n['numero_processo']) ?></a>
                        <?php endif; ?>
                    </small>
                </div>
                <?php if ((int) $n['lida'] === 0): ?>
                    <form method="POST" action="/notificacoes/<?= $n['id'] ?>/lida">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Marcar lida</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
