<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Vendas</h1>
    <a href="<?= site_url('venda-produtos/nova') ?>" class="btn btn-success">+ Nova venda</a>
</div>

<form method="get" action="<?= site_url('venda-produtos') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar por cliente, loja ou produto"
               value="<?= esc($busca) ?>" aria-label="Buscar venda">
    </div>
    <div class="col-12 col-sm-auto">
        <button type="submit" class="btn btn-outline-secondary w-100">Buscar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Data</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Loja</th>
                    <th scope="col">Produto</th>
                    <th scope="col">Unidade</th>
                    <th scope="col" class="text-end">Qtd.</th>
                    <th scope="col" class="text-end">Preço unit.</th>
                    <th scope="col" class="text-end">Total</th>
                    <th scope="col" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vendas)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Nenhuma venda encontrada.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($vendas as $venda): ?>
                    <tr>
                        <td class="text-nowrap"><?= esc(date('d/m/Y H:i', strtotime($venda['data']))) ?></td>
                        <td><?= esc($venda['cliente_nome']) ?></td>
                        <td><?= esc($venda['loja_nome']) ?></td>
                        <td><?= esc($venda['produto_nome']) ?></td>
                        <td><?= esc($venda['unidade_nome'] ?? '—') ?></td>
                        <td class="text-end"><?= esc($venda['quantidade']) ?></td>
                        <td class="text-end"><?= esc(number_to_currency((float) $venda['preco_unitario'], 'BRL', 'pt_BR', 2)) ?></td>
                        <td class="text-end">
                            <?= esc(number_to_currency((float) $venda['quantidade'] * (float) $venda['preco_unitario'], 'BRL', 'pt_BR', 2)) ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('venda-produtos/editar/' . $venda['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="post" action="<?= site_url('venda-produtos/excluir/' . $venda['id']) ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Excluir esta venda?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
