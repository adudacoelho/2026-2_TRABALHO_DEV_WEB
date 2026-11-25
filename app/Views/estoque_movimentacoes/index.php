<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Movimentações de estoque</h1>
    <a href="<?= site_url('estoque-lojas') ?>" class="btn btn-outline-secondary">Estoque por loja</a>
</div>

<p class="text-muted small">
    Histórico somente leitura: cada baixa por venda, estorno ou ajuste manual gera um registro aqui.
</p>

<form method="get" action="<?= site_url('estoque-movimentacoes') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar por loja ou produto"
               value="<?= esc($busca) ?>" aria-label="Buscar movimentação">
    </div>
    <div class="col-12 col-sm-auto">
        <?php $origemEscolhida = (string) $origem; ?>
        <select name="origem" class="form-select" aria-label="Filtrar por origem">
            <option value="">Todas as origens</option>
            <?php foreach (['venda' => 'Venda', 'estorno' => 'Estorno', 'ajuste' => 'Ajuste manual'] as $valor => $rotulo): ?>
                <option value="<?= esc($valor) ?>" <?= $origemEscolhida === $valor ? 'selected' : '' ?>>
                    <?= esc($rotulo) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12 col-sm-auto">
        <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Data</th>
                    <th scope="col">Loja</th>
                    <th scope="col">Produto</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Origem</th>
                    <th scope="col" class="text-end">Qtd.</th>
                    <th scope="col" class="text-end">Saldo após</th>
                    <th scope="col">Observação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movimentacoes)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Nenhuma movimentação encontrada.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($movimentacoes as $movimentacao): ?>
                    <?php
                        $quantidade = (int) $movimentacao['quantidade'];
                        $entrada    = $quantidade >= 0;
                    ?>
                    <tr>
                        <td class="text-nowrap"><?= esc($movimentacao['created_at']) ?></td>
                        <td><?= esc($movimentacao['loja_nome']) ?></td>
                        <td><?= esc($movimentacao['produto_nome']) ?></td>
                        <td>
                            <span class="badge <?= $entrada ? 'text-bg-success' : 'text-bg-danger' ?>">
                                <?= $entrada ? 'Entrada' : 'Saída' ?>
                            </span>
                        </td>
                        <td><?= esc($movimentacao['origem']) ?></td>
                        <td class="text-end text-nowrap"><?= $entrada ? '+' : '' ?><?= esc($quantidade) ?></td>
                        <td class="text-end"><?= esc($movimentacao['saldo_apos']) ?></td>
                        <td><?= esc($movimentacao['observacao'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
