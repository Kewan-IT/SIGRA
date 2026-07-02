<h5 class="mb-3 text-center">Entrar no Sistema</h5>
<form method="POST" action="/login">
    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" required autofocus placeholder="seu.email@zambezia.gov.mz">
    </div>
    <div class="mb-3">
        <label class="form-label">Senha</label>
        <input type="password" name="senha" class="form-control" required placeholder="••••••••">
    </div>
    <button type="submit" class="btn btn-primary w-100">Entrar</button>
    <div class="text-center mt-3">
        <a href="/esqueci-senha" class="small text-decoration-none">Esqueceu-se da senha?</a>
    </div>
</form>
