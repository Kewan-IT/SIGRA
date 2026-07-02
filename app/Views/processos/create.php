<h3 class="mb-4">Registar Novo Processo</h3>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="/processos" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nº do Processo</label>
                    <input type="text" name="numero_processo" class="form-control" value="<?= View::e($numeroSugerido) ?>">
                    <small class="text-muted">Sugestão automática — pode editar se necessário.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de Processo *</label>
                    <select name="tipo_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= View::e($t['nome']) ?> (<?= (int) $t['prazo_padrao_dias'] ?> dias)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Distrito de Origem *</label>
                    <select name="district_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($distritos as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= View::e($d['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Assunto *</label>
                    <input type="text" name="assunto" class="form-control" required placeholder="Ex: Nomeação de Director Pedagógico">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Requerente *</label>
                    <input type="text" name="requerente" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data de Entrada *</label>
                    <input type="date" name="data_entrada" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Observações</label>
                    <textarea name="observacoes" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Documentos Anexos</label>
                    <input type="file" name="anexos[]" class="form-control" multiple>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Registar Processo</button>
                <a href="/processos" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
