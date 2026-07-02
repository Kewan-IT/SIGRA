<h5 class="mb-3 text-center">Senha Temporária Gerada</h5>
<p class="text-muted small text-center">
    O envio de e-mail não está configurado neste ambiente. Utilize a senha
    temporária abaixo para entrar (ser-lhe-á pedido para a alterar).
</p>
<div class="mb-3">
    <label class="form-label">E-mail</label>
    <input type="text" class="form-control" value="<?= View::e($email) ?>" readonly>
</div>
<div class="mb-3">
    <label class="form-label">Senha temporária</label>
    <input type="text" class="form-control fw-bold text-center" value="<?= View::e($senhaTemporaria) ?>" readonly onclick="this.select()">
</div>
<a href="/login" class="btn btn-primary w-100">Ir para o login</a>
