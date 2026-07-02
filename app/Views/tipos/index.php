<h3 class="mb-4">Tipos de Processo</h3>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="mb-3">Adicionar Tipo</h6>
                <form method="POST" action="/tipos-processo">
                    <div class="mb-3">
                        <input type="text" name="nome" class="form-control" placeholder="Nome do tipo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Prazo padrão (dias)</label>
                        <input type="number" name="prazo_padrao_dias" class="form-control" value="15" min="1">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card stat-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Nome</th><th>Prazo (dias)</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($tipos as $t): ?>
                            <tr>
                                <form method="POST" action="/tipos-processo/<?= $t['id'] ?>">
                                <td><input type="text" name="nome" value="<?= View::e($t['nome']) ?>" class="form-control form-control-sm border-0"></td>
                                <td style="width:120px"><input type="number" name="prazo_padrao_dias" value="<?= (int) $t['prazo_padrao_dias'] ?>" class="form-control form-control-sm"></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" name="ativo" <?= $t['ativo'] ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Guardar</button>
                                </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
