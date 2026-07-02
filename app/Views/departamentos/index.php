<h3 class="mb-4">Sectores / Departamentos</h3>
<p class="text-muted small">Estes são os sectores pelos quais os processos podem circular. O fluxo é livre: em cada etapa, quem estiver a tratar do processo escolhe para que sector e utilizador o encaminha.</p>

<div class="card stat-card mb-3">
    <div class="card-body">
        <h6 class="mb-3">Adicionar Sector</h6>
        <form method="POST" action="/departamentos" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Nome do sector *</label>
                <input type="text" name="nome" class="form-control form-control-sm" placeholder="Ex: UGEA, Planificação..." required>
            </div>
            <div class="col-md-6">
                <label class="form-label small mb-1">Descrição</label>
                <input type="text" name="descricao" class="form-control form-control-sm" placeholder="Descrição (opcional)">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-plus-circle me-1"></i> Adicionar</button>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Chave</th><th>Nome</th><th>Descrição</th><th>Estado</th><th></th><th></th></tr></thead>
            <tbody>
                <?php foreach ($departamentos as $d): ?>
                    <tr>
                        <form method="POST" action="/departamentos/<?= $d['id'] ?>">
                        <td><code><?= View::e($d['chave']) ?></code></td>
                        <td><input type="text" name="nome" value="<?= View::e($d['nome']) ?>" class="form-control form-control-sm border-0"></td>
                        <td><input type="text" name="descricao" value="<?= View::e($d['descricao'] ?? '') ?>" class="form-control form-control-sm border-0"></td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="ativo" <?= $d['ativo'] ? 'checked' : '' ?>>
                            </div>
                        </td>
                        <td class="text-end">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Guardar</button>
                        </td>
                        </form>
                        <td class="text-end" style="width:1%">
                            <form method="POST" action="/departamentos/<?= $d['id'] ?>/eliminar" data-confirm="Eliminar o sector '<?= View::e($d['nome']) ?>'? Se já tiver histórico associado, será apenas desactivado.">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
