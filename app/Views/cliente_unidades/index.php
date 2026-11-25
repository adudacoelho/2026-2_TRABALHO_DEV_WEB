<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Vínculos cliente e unidade</h1>
    <a href="<?= site_url('cliente-unidades/novo') ?>" class="btn btn-success">+ Novo vínculo</a>
</div>

<form method="get" action="<?= site_url('cliente-unidades') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar por cliente, unidade ou loja"
               value="<?= esc($busca) ?>" aria-label="Buscar vínculo">
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
                    <th scope="col">Cliente</th>
                    <th scope="col">Unidade</th>
                    <th scope="col">Loja</th>
                    <th scope="col" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vinculos)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Nenhum vínculo encontrado.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($vinculos as $vinculo): ?>
                    <tr>
                        <td><?= esc($vinculo['cliente_nome']) ?></td>
                        <td><?= esc($vinculo['unidade_nome']) ?></td>
                        <td><?= esc($vinculo['loja_nome'] ?? '—') ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('cliente-unidades/editar/' . $vinculo['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="post" action="<?= site_url('cliente-unidades/excluir/' . $vinculo['id']) ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Excluir este vínculo?');">
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
