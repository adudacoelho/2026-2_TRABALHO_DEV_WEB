<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Estoque por loja</h1>
    <a href="<?= site_url('estoque-lojas/novo') ?>" class="btn btn-success">+ Novo estoque</a>
</div>

<form method="get" action="<?= site_url('estoque-lojas') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar por loja ou produto"
               value="<?= esc($busca) ?>" aria-label="Buscar estoque">
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
                    <th scope="col">Loja</th>
                    <th scope="col">Produto</th>
                    <th scope="col" class="text-end">Quantidade</th>
                    <th scope="col" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estoques)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Nenhum estoque encontrado.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($estoques as $estoque): ?>
                    <tr>
                        <td><?= esc($estoque['loja_nome']) ?></td>
                        <td><?= esc($estoque['produto_nome']) ?></td>
                        <td class="text-end">
                            <?php if ((int) $estoque['quantidade'] < 0): ?>
                                <span class="badge text-bg-danger">Negativo: <?= esc($estoque['quantidade']) ?></span>
                            <?php elseif ((int) $estoque['quantidade'] === 0): ?>
                                <span class="badge text-bg-danger">Esgotado</span>
                            <?php else: ?>
                                <?= esc($estoque['quantidade']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('estoque-lojas/editar/' . $estoque['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="post" action="<?= site_url('estoque-lojas/excluir/' . $estoque['id']) ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Excluir este estoque?');">
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
