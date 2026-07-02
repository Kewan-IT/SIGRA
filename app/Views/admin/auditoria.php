<h3 class="mb-4">Registo de Auditoria</h3>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr><th>Data/Hora</th><th>Utilizador</th><th>Acção</th><th>Tabela</th><th>Detalhes</th><th>IP</th></tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i:s', strtotime($log['criado_em'])) ?></td>
                        <td><?= View::e($log['usuario_nome'] ?? '—') ?></td>
                        <td><span class="badge bg-secondary"><?= View::e($log['acao']) ?></span></td>
                        <td><?= View::e($log['tabela_afetada'] ?? '—') ?></td>
                        <td><?= View::e($log['detalhes'] ?? '') ?></td>
                        <td><?= View::e($log['ip'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Sem registos.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer text-end">
        <a href="?pagina=<?= max(1, $pagina - 1) ?>" class="btn btn-sm btn-outline-secondary">Anterior</a>
        <a href="?pagina=<?= $pagina + 1 ?>" class="btn btn-sm btn-outline-secondary">Seguinte</a>
    </div>
</div>
