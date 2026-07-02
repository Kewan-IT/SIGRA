<h3 class="mb-4">Encaminhar Processo Nº <?= View::e($processo['numero_processo']) ?></h3>

<div class="card stat-card">
    <div class="card-body">
        <p class="text-muted"><?= View::e($processo['assunto']) ?> — <?= View::e($processo['requerente']) ?></p>
        <p class="small text-muted mb-3">
            Sector actual: <strong><?= View::e($processo['departamento_nome']) ?></strong>
            <?php if (!empty($processo['funcionario_nome'])): ?>
                &middot; Responsável actual: <strong><?= View::e($processo['funcionario_nome']) ?></strong>
            <?php endif; ?>
        </p>

        <form method="POST" action="/processos/<?= $processo['id'] ?>/encaminhar">
            <div class="mb-3">
                <label class="form-label">Sector de Destino *</label>
                <select name="department_destino_id" id="setorDestino" class="form-select" required>
                    <option value="">Seleccione o sector...</option>
                    <?php foreach ($sectores as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= View::e($s['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Técnico / Utilizador de Destino *</label>
                <select name="funcionario_destino_id" id="tecnicoDestino" class="form-select" required>
                    <option value="">Seleccione primeiro o sector...</option>
                </select>
                <div class="form-text">A lista é filtrada automaticamente pelo sector escolhido acima.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Observação</label>
                <textarea name="observacao" class="form-control" rows="2" placeholder="Instruções para quem vai receber o processo (opcional)"></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Encaminhar</button>
            <a href="/processos/<?= $processo['id'] ?>" class="btn btn-outline-secondary">Cancelar</a>
        </form>
    </div>
</div>

<script>
(function () {
    var utilizadores = <?= json_encode(array_map(function ($u) {
        return [
            'id' => (int) $u['id'],
            'nome' => $u['nome'],
            'department_id' => $u['department_id'] !== null ? (int) $u['department_id'] : null,
        ];
    }, array_values($utilizadores)), JSON_UNESCAPED_UNICODE) ?>;

    var setorSelect = document.getElementById('setorDestino');
    var tecnicoSelect = document.getElementById('tecnicoDestino');

    function atualizarTecnicos() {
        var setorId = parseInt(setorSelect.value, 10);
        tecnicoSelect.innerHTML = '';

        if (!setorId) {
            tecnicoSelect.innerHTML = '<option value="">Seleccione primeiro o sector...</option>';
            return;
        }

        var doSetor = utilizadores.filter(function (u) { return u.department_id === setorId; });

        if (doSetor.length === 0) {
            tecnicoSelect.innerHTML = '<option value="">Nenhum utilizador neste sector — contacte o admin</option>';
            return;
        }

        tecnicoSelect.innerHTML = '<option value="">Seleccione o utilizador...</option>';
        doSetor.forEach(function (u) {
            var opt = document.createElement('option');
            opt.value = u.id;
            opt.textContent = u.nome;
            tecnicoSelect.appendChild(opt);
        });
    }

    setorSelect.addEventListener('change', atualizarTecnicos);
})();
</script>
