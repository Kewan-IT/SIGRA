<h5 class="mb-3 text-center">Definir Nova Senha</h5>
<form method="POST" action="/redefinir-senha">
    <input type="hidden" name="token" value="<?= View::e($token) ?>">
    <div class="mb-3">
        <label class="form-label">Nova senha</label>
        <input type="password" name="nova_senha" class="form-control" required minlength="6" autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirmar senha</label>
        <input type="password" name="confirmar_senha" class="form-control" required minlength="6">
    </div>
    <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
</form>
