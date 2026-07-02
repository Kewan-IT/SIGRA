<h3 class="mb-4">Configurações do Sistema</h3>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="/configuracoes" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome da Instituição</label>
                    <input type="text" name="nome_instituicao" class="form-control" value="<?= View::e($config['nome_instituicao'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nome do Sistema</label>
                    <input type="text" name="nome_sistema" class="form-control" value="<?= View::e($config['nome_sistema'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dias para Alerta de Processo Parado</label>
                    <input type="number" name="sla_alerta_dias" class="form-control" value="<?= View::e($config['sla_alerta_dias'] ?? '5') ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Logótipo</label>
                    <input type="file" name="logo" class="form-control">
                    <?php if (!empty($config['logo'])): ?>
                        <img src="/<?= View::e($config['logo']) ?>" alt="Logo" style="height:48px" class="mt-2">
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar Configurações</button>
            </div>
        </form>
    </div>
</div>
