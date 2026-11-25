<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Unidades</h1>
    <a href="<?= site_url('unidades/nova') ?>" class="btn btn-success">+ Nova unidade</a>
</div>

<form method="get" action="<?= site_url('unidades') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar por unidade ou loja"
               value="<?= esc($busca) ?>" aria-label="Buscar unidade">
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
                    <th scope="col">Identificador</th>
                    <th scope="col">Loja</th>
                    <th scope="col" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($unidades)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Nenhuma unidade encontrada.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($unidades as $unidade): ?>
                    <tr>
                        <td><?= esc($unidade['identificador']) ?></td>
                        <td><?= esc($unidade['loja_nome'] ?? '—') ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('unidades/editar/' . $unidade['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="post" action="<?= site_url('unidades/excluir/' . $unidade['id']) ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Excluir esta unidade?');">
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
