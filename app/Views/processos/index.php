<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Processos</h3>
    <?php if (Auth::podeRegistarProcesso()): ?>
        <a href="/processos/novo" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Registar Processo</a>
    <?php endif; ?>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="/processos" class="row g-2">
            <div class="col-md-2">
                <input type="text" name="numero_processo" class="form-control form-control-sm" placeholder="Nº Processo" value="<?= View::e($filtros['numero_processo']) ?>">
            </div>
            <div class="col-md-2">
                <input type="text" name="requerente" class="form-control form-control-sm" placeholder="Requerente" value="<?= View::e($filtros['requerente']) ?>">
            </div>
            <div class="col-md-2">
                <select name="district_id" class="form-select form-select-sm">
                    <option value="">Distrito (todos)</option>
                    <?php foreach ($distritos as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= (string) $filtros['district_id'] === (string) $d['id'] ? 'selected' : '' ?>><?= View::e($d['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipo_id" class="form-select form-select-sm">
                    <option value="">Tipo (todos)</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (string) $filtros['tipo_id'] === (string) $t['id'] ? 'selected' : '' ?>><?= View::e($t['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="department_atual_id" class="form-select form-select-sm">
                    <option value="">Gabinete (todos)</option>
                    <?php foreach ($departamentos as $dep): ?>
                        <option value="<?= $dep['id'] ?>" <?= (string) $filtros['department_atual_id'] === (string) $dep['id'] ? 'selected' : '' ?>><?= View::e($dep['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="ano" class="form-control form-control-sm" placeholder="Ano" value="<?= View::e((string) $filtros['ano']) ?>">
            </div>
            <div class="col-md-3 form-check mt-2">
                <input type="checkbox" class="form-check-input" name="atrasados" value="1" id="atrasadosCheck" <?= $filtros['atrasados'] ? 'checked' : '' ?>>
                <label class="form-check-label small" for="atrasadosCheck">Apenas atrasados</label>
            </div>
            <div class="col-md-3 mt-2">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                <a href="/processos" class="btn btn-sm btn-outline-secondary">Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nº Processo</th>
                    <th>Assunto</th>
                    <th>Requerente</th>
                    <th>Distrito</th>
                    <th>Sector Actual</th>
                    <th>Estado</th>
                    <th>Prazo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($processos)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Nenhum processo encontrado.</td></tr>
                <?php endif; ?>
                <?php foreach ($processos as $p): ?>
                    <?php
                        $atrasado = $p['prazo_data'] && $p['prazo_data'] < date('Y-m-d') && $p['estado_atual'] !== 'concluido';
                    ?>
                    <tr>
                        <td class="fw-semibold"><?= View::e($p['numero_processo']) ?></td>
                        <td><?= View::e($p['assunto']) ?></td>
                        <td><?= View::e($p['requerente']) ?></td>
                        <td><?= View::e($p['distrito_nome']) ?></td>
                        <td><?= View::e($p['departamento_nome']) ?></td>
                        <td>
                            <span class="badge badge-estado <?= $p['estado_atual'] === 'concluido' ? 'bg-success' : ($atrasado ? 'bg-danger' : 'bg-info text-dark') ?>">
                                <?= View::e(str_replace('_', ' ', $p['estado_atual'])) ?>
                            </span>
                        </td>
                        <td class="<?= $atrasado ? 'text-danger fw-semibold' : '' ?>">
                            <?= $p['prazo_data'] ? date('d/m/Y', strtotime($p['prazo_data'])) : '—' ?>
                        </td>
                        <td class="text-end">
                            <a href="/processos/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="small text-muted">Total: <?= (int) $total ?> processo(s)</div>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <form method="GET" action="/processos" class="d-flex align-items-center gap-2">
            <?php foreach ($_GET as $k => $v): if ($k !== 'por_pagina'): ?>
                <input type="hidden" name="<?= View::e($k) ?>" value="<?= View::e($v) ?>">
            <?php endif; endforeach; ?>
            <label class="small text-muted mb-0">Por página:</label>
            <select name="por_pagina" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                <option value="10" <?= $porPagina === 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $porPagina === 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $porPagina === 50 ? 'selected' : '' ?>>50</option>
            </select>
        </form>
    </div>
</div>
