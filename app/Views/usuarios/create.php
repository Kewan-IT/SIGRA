<h3 class="mb-4">Novo Utilizador</h3>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="/utilizadores">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome Completo *</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-mail *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Perfil *</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= View::e($r['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <select name="department_id" class="form-select">
                        <option value="">—</option>
                        <?php foreach ($departamentos as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= View::e($d['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="cargo" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Criar Utilizador</button>
                <a href="/utilizadores" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
