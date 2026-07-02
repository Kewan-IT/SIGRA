<h3 class="mb-4">Relatórios e Estatísticas</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted small">Tempo Médio de Tramitação</div>
            <div class="fs-4 fw-bold"><?= $tempoMedio ?> dias</div>
        </div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted small">Processos Concluídos</div>
            <div class="fs-4 fw-bold"><?= (int) $stats['concluidos'] ?></div>
        </div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted small">Processos Atrasados</div>
            <div class="fs-4 fw-bold text-danger"><?= (int) $stats['atrasados'] ?></div>
        </div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted small">Em Andamento</div>
            <div class="fs-4 fw-bold"><?= (int) $stats['em_andamento'] ?></div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card stat-card"><div class="card-body">
            <h6>Processos por Distrito</h6>
            <canvas id="chartDistrito" height="220"></canvas>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card"><div class="card-body">
            <h6>Processos por Tipo</h6>
            <canvas id="chartTipo" height="220"></canvas>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Processos por Mês (<?= $ano ?>)</h6>
                <form method="GET" action="/relatorios" class="d-flex gap-1">
                    <input type="number" name="ano" value="<?= $ano ?>" class="form-control form-control-sm" style="width:90px">
                    <button class="btn btn-sm btn-outline-primary">Ver</button>
                </form>
            </div>
            <canvas id="chartMes" height="220"></canvas>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card"><div class="card-body">
            <h6>Processos por Funcionário</h6>
            <ul class="list-group list-group-flush">
                <?php foreach ($porFuncionario as $f): ?>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <?= View::e($f['nome']) ?> <span class="badge bg-primary rounded-pill"><?= (int) $f['total'] ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($porFuncionario)): ?>
                    <li class="list-group-item px-0 text-muted small">Sem dados.</li>
                <?php endif; ?>
            </ul>
        </div></div>
    </div>
</div>

<div class="mt-3 no-print">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Imprimir Relatório</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const porDistrito = <?= json_encode($porDistrito) ?>;
const porTipo = <?= json_encode($porTipo) ?>;
const porMes = <?= json_encode($porMes) ?>;

new Chart(document.getElementById('chartDistrito'), {
    type: 'bar',
    data: {
        labels: porDistrito.map(d => d.nome),
        datasets: [{ label: 'Processos', data: porDistrito.map(d => d.total), backgroundColor: '#0b3d67' }]
    },
    options: { plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('chartTipo'), {
    type: 'doughnut',
    data: {
        labels: porTipo.map(t => t.nome),
        datasets: [{ data: porTipo.map(t => t.total), backgroundColor: ['#0b3d67','#c9a227','#5a8fb0','#8fbf8f','#d98c8c','#b39ddb','#f2b880'] }]
    }
});

const mesesLabels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
const dadosMes = new Array(12).fill(0);
porMes.forEach(m => { dadosMes[m.mes - 1] = parseInt(m.total); });

new Chart(document.getElementById('chartMes'), {
    type: 'line',
    data: {
        labels: mesesLabels,
        datasets: [{ label: 'Processos', data: dadosMes, borderColor: '#0b3d67', backgroundColor: 'rgba(11,61,103,0.15)', fill: true, tension: 0.3 }]
    },
    options: { plugins: { legend: { display: false } } }
});
</script>
