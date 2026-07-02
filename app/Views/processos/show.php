<?php
$estadoConcluido = $processo['estado_atual'] === 'concluido';
$atrasado = $processo['prazo_data'] && $processo['prazo_data'] < date('Y-m-d') && !$estadoConcluido;
?>

<div class="d-flex justify-content-between align-items-start mb-3 no-print">
    <div>
        <h3 class="mb-0">Processo Nº <?= View::e($processo['numero_processo']) ?></h3>
        <div class="text-muted small">Código interno: <?= View::e($processo['codigo_interno']) ?></div>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer"></i> Imprimir</button>
        <a href="/processos" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card stat-card mb-3">
            <div class="card-body">
                <h6 class="mb-3">Dados do Processo</h6>
                <div class="row g-2 small">
                    <div class="col-6"><strong>Assunto:</strong> <?= View::e($processo['assunto']) ?></div>
                    <div class="col-6"><strong>Tipo:</strong> <?= View::e($processo['tipo_nome']) ?></div>
                    <div class="col-6"><strong>Requerente:</strong> <?= View::e($processo['requerente']) ?></div>
                    <div class="col-6"><strong>Distrito de Origem:</strong> <?= View::e($processo['distrito_nome']) ?></div>
                    <div class="col-6"><strong>Data de Entrada:</strong> <?= date('d/m/Y', strtotime($processo['data_entrada'])) ?></div>
                    <div class="col-6">
                        <strong>Prazo:</strong>
                        <span class="<?= $atrasado ? 'text-danger fw-semibold' : '' ?>">
                            <?= $processo['prazo_data'] ? date('d/m/Y', strtotime($processo['prazo_data'])) : '—' ?>
                            <?= $atrasado ? ' (atrasado)' : '' ?>
                        </span>
                    </div>
                    <div class="col-6"><strong>Sector Actual:</strong> <?= View::e($processo['departamento_nome']) ?></div>
                    <div class="col-6"><strong>Funcionário Responsável:</strong> <?= View::e($processo['funcionario_nome'] ?? '—') ?></div>
                    <div class="col-12"><strong>Estado Actual:</strong>
                        <span class="badge <?= $estadoConcluido ? 'bg-success' : 'bg-info text-dark' ?>">
                            <?= View::e(str_replace('_', ' ', $processo['estado_atual'])) ?>
                        </span>
                    </div>
                    <?php if (!empty($processo['observacoes'])): ?>
                        <div class="col-12"><strong>Observações:</strong> <?= nl2br(View::e($processo['observacoes'])) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!$estadoConcluido && $podeAgir): ?>
        <div class="card stat-card mb-3 no-print">
            <div class="card-body">
                <h6 class="mb-3">Acções de Tramitação</h6>

                <div class="d-flex flex-wrap gap-2 mb-2">
                    <a href="/processos/<?= $processo['id'] ?>/encaminhar" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Encaminhar para Sector/Técnico
                    </a>

                    <form method="POST" action="/processos/<?= $processo['id'] ?>/concluir" data-confirm="Confirma que este processo está concluído?">
                        <input type="hidden" name="observacao" value="Processo concluído.">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle me-1"></i> Concluir Processo
                        </button>
                    </form>
                </div>

                <?php if ($sectorAnterior !== null): ?>
                    <form method="POST" action="/processos/<?= $processo['id'] ?>/devolver" class="d-flex gap-2" data-confirm="Confirma a devolução deste processo a <?= View::e($sectorAnterior['department_nome'] ?? 'o sector anterior') ?>?">
                        <input type="text" name="observacao" class="form-control form-control-sm" placeholder="Motivo da devolução">
                        <button type="submit" class="btn btn-outline-danger text-nowrap">
                            <i class="bi bi-arrow-return-left me-1"></i> Devolver a <?= View::e($sectorAnterior['department_nome'] ?? 'sector anterior') ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card stat-card">
            <div class="card-body">
                <h6 class="mb-3">Documentos Anexos</h6>
                <?php if (empty($anexos)): ?>
                    <p class="text-muted small mb-0">Nenhum documento anexado.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($anexos as $a): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="bi bi-paperclip me-1"></i> <?= View::e($a['nome_original']) ?></span>
                                <a href="/anexos/<?= $a['id'] ?>/download" class="btn btn-sm btn-outline-primary">Descarregar</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="mb-3">Linha do Tempo</h6>
                <div class="timeline">
                    <?php foreach ($historico as $h): ?>
                        <div class="timeline-item">
                            <div class="fw-semibold small"><?= View::e(str_replace('_', ' ', $h['estado_novo'])) ?></div>
                            <div class="text-muted small">
                                <?= date('d/m/Y', strtotime($h['criado_em'])) ?> às <?= date('H:i', strtotime($h['criado_em'])) ?>
                                por <?= View::e($h['executado_por_nome']) ?>
                            </div>
                            <?php if (!empty($h['para_departamento_nome'])): ?>
                                <div class="small text-muted">Gabinete: <?= View::e($h['para_departamento_nome']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($h['observacao'])): ?>
                                <div class="small fst-italic">"<?= View::e($h['observacao']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
