<h3 class="mb-4">Editar Utilizador</h3>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="/utilizadores/<?= $usuario['id'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome Completo *</label>
                    <input type="text" name="nome" class="form-control" value="<?= View::e($usuario['nome']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" class="form-control" value="<?= View::e($usuario['email']) ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Perfil *</label>
                    <select name="role_id" class="form-select" required>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (int) $usuario['role_id'] === (int) $r['id'] ? 'selected' : '' ?>><?= View::e($r['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <select name="department_id" class="form-select">
                        <option value="">—</option>
                        <?php foreach ($departamentos as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= (int) ($usuario['department_id'] ?? 0) === (int) $d['id'] ? 'selected' : '' ?>><?= View::e($d['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="cargo" class="form-control" value="<?= View::e($usuario['cargo'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="<?= View::e($usuario['telefone'] ?? '') ?>">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="ativo" <?= (int) $usuario['ativo'] === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label">Utilizador Activo</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar Alterações</button>
                <a href="/utilizadores" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
