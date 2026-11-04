<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Produtos</h1>
    <a href="<?= site_url('produtos/novo') ?>" class="btn btn-success">+ Novo produto</a>
</div>

<form method="get" action="<?= site_url('produtos') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar por nome ou descrição"
               value="<?= esc($busca) ?>" aria-label="Buscar produto">
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
                    <th scope="col">Nome</th>
                    <th scope="col">Categoria</th>
                    <th scope="col" class="text-end">Preço</th>
                    <th scope="col" class="text-end">Estoque</th>
                    <th scope="col" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produtos)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Nenhum produto encontrado.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td>
                            <?= esc($produto['nome']) ?>
                            <?php if (! empty($produto['descricao'])): ?>
                                <div class="small text-muted"><?= esc($produto['descricao']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($produto['categoria_nome'] ?? 'Sem categoria') ?></td>
                        <td class="text-end"><?= esc(number_to_currency((float) $produto['preco'], 'BRL', 'pt_BR', 2)) ?></td>
                        <td class="text-end">
                            <?php if ((int) $produto['quantidade'] === 0): ?>
                                <span class="badge text-bg-danger">Esgotado</span>
                            <?php else: ?>
                                <?= esc($produto['quantidade']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('produtos/editar/' . $produto['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="post" action="<?= site_url('produtos/excluir/' . $produto['id']) ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Excluir este produto?');">
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
