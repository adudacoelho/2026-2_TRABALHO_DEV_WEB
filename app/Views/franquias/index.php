<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Franquias</h1>
    <a href="<?= site_url('franquias/nova') ?>" class="btn btn-success">+ Nova franquia</a>
</div>

<form method="get" action="<?= site_url('franquias') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar franquia"
               value="<?= esc($busca) ?>" aria-label="Buscar franquia">
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
                <?php if (empty($franquias)): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">Nenhuma franquia encontrada.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($franquias as $franquia): ?>
                    <tr>
                        <td><?= esc($franquia['nome']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('franquias/editar/' . $franquia['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="post" action="<?= site_url('franquias/excluir/' . $franquia['id']) ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Excluir esta franquia? Isso pode afetar as lojas associadas.');">
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
