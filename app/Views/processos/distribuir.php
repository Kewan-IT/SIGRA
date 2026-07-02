<h3 class="mb-4">Distribuir Processo Nº <?= View::e($processo['numero_processo']) ?></h3>

<div class="card stat-card">
    <div class="card-body">
        <p class="text-muted"><?= View::e($processo['assunto']) ?> — <?= View::e($processo['requerente']) ?></p>

        <form method="POST" action="/processos/<?= $processo['id'] ?>/distribuir">
            <div class="mb-3">
                <label class="form-label">Técnico Responsável *</label>
                <select name="funcionario_responsavel_id" class="form-select" required>
                    <option value="">Seleccione o técnico...</option>
                    <?php foreach ($tecnicos as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= View::e($t['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Observação</label>
                <textarea name="observacao" class="form-control" rows="2" placeholder="Instruções para o técnico (opcional)"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Distribuir</button>
            <a href="/processos/<?= $processo['id'] ?>" class="btn btn-outline-secondary">Cancelar</a>
        </form>
    </div>
</div>
