<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Lojas</h1>
    <a href="<?= site_url('lojas/nova') ?>" class="btn btn-success">+ Nova loja</a>
</div>

<form method="get" action="<?= site_url('lojas') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar loja"
               value="<?= esc($busca) ?>" aria-label="Buscar loja">
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
                    <th scope="col" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lojas)): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">Nenhuma loja encontrada.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($lojas as $loja): ?>
                    <tr>
                        <td><?= esc($loja['nome']) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('lojas/editar/' . $loja['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="post" action="<?= site_url('lojas/excluir/' . $loja['id']) ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Excluir esta loja? Isso pode afetar produtos associados.');">
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
