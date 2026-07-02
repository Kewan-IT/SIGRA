<h5 class="mb-3 text-center">Recuperar Senha</h5>
<p class="text-muted small text-center">Indique o seu e-mail para receber instruções de recuperação.</p>
<form method="POST" action="/esqueci-senha">
    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary w-100">Enviar</button>
    <div class="text-center mt-3">
        <a href="/login" class="small text-decoration-none">Voltar ao login</a>
    </div>
</form>
