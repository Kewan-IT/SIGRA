<h3 class="mb-4">Senha Temporária Gerada</h3>

<div class="card stat-card">
    <div class="card-body">
        <p>Foi gerada uma senha temporária para o utilizador <strong><?= View::e($email) ?></strong>. Comunique-a de forma segura; será exigida a alteração no primeiro acesso.</p>
        <div class="mb-3">
            <label class="form-label">Senha Temporária</label>
            <input type="text" class="form-control fw-bold" value="<?= View::e($senhaTemporaria) ?>" readonly onclick="this.select()">
        </div>
        <a href="/utilizadores" class="btn btn-primary">Voltar à Lista de Utilizadores</a>
    </div>
</div>
