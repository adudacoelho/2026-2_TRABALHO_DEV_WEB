<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Clientes</h1>
    <a href="<?= site_url('clientes/novo') ?>" class="btn btn-success">+ Novo cliente</a>
</div>

<form method="get" action="<?= site_url('clientes') ?>" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <input type="search" name="busca" class="form-control" placeholder="Buscar por nome, unidade ou CPF"
               value="<?= esc($busca) ?>" aria-label="Buscar cliente">
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
                    <th scope="col">Unidade</th>
                    <th scope="col">CPF</th>
                    <th scope="col">Telefone</th>
                    <th scope="col" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Nenhum cliente encontrado.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= esc($cliente['nome']) ?></td>
                        <td><?= esc($cliente['unidade']) ?></td>
                        <td class="text-nowrap"><?= esc(formatar_cpf($cliente['cpf'])) ?></td>
                        <td class="text-nowrap"><?= esc(formatar_telefone($cliente['telefone'])) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('clientes/editar/' . $cliente['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="post" action="<?= site_url('clientes/excluir/' . $cliente['id']) ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Excluir este cliente?');">
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
