<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Utilizadores</h3>
    <a href="/utilizadores/novo" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Novo Utilizador</a>
</div>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Departamento</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= View::e($u['nome']) ?></td>
                        <td><?= View::e($u['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= View::e($u['role_nome']) ?></span></td>
                        <td><?= View::e($u['department_nome'] ?? '—') ?></td>
                        <td>
                            <?php if ((int) $u['ativo'] === 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="/utilizadores/<?= $u['id'] ?>/editar" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="POST" action="/utilizadores/<?= $u['id'] ?>/resetar-senha" class="d-inline" data-confirm="Redefinir a senha deste utilizador?">
                                <button type="submit" class="btn btn-sm btn-outline-warning">Resetar Senha</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
