<h3 class="mb-4">Departamentos / Gabinetes</h3>
<p class="text-muted small">Estes representam as etapas fixas do fluxo de tramitação do Gabinete do Governador. A ordem não pode ser alterada aqui, apenas o nome, descrição e estado.</p>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Ordem</th><th>Chave</th><th>Nome</th><th>Descrição</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($departamentos as $d): ?>
                    <tr>
                        <form method="POST" action="/departamentos/<?= $d['id'] ?>">
                        <td><?= (int) $d['ordem'] ?></td>
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
