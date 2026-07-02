<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="bi bi-key me-2"></i>Alterar Senha</h5>
                <p class="text-muted small">Por motivos de segurança, deve definir uma nova senha antes de continuar.</p>
                <form method="POST" action="/trocar-senha">
                    <div class="mb-3">
                        <label class="form-label">Nova senha</label>
                        <input type="password" name="nova_senha" class="form-control" required minlength="6" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="password" name="confirmar_senha" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar Nova Senha</button>
                </form>
            </div>
        </div>
    </div>
</div>
