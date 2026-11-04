<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <h1 class="h3 mb-3"><?= esc($titulo) ?></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= esc($acao) ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" id="nome" name="nome" class="form-control" maxlength="150"
                               value="<?= esc(old('nome', $cliente['nome'])) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="cpf" class="form-label">CPF *</label>
                            <input type="text" id="cpf" name="cpf" class="form-control" maxlength="14"
                                   placeholder="000.000.000-00" inputmode="numeric"
                                   value="<?= esc(old('cpf', formatar_cpf($cliente['cpf']))) ?>" required>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="unidade" class="form-label">Unidade *</label>
                            <input type="text" id="unidade" name="unidade" class="form-control" maxlength="30"
                                   placeholder="Apto 302"
                                   value="<?= esc(old('unidade', $cliente['unidade'])) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" id="telefone" name="telefone" class="form-control" maxlength="15"
                                   placeholder="(27) 99999-9999" inputmode="tel"
                                   value="<?= esc(old('telefone', formatar_telefone($cliente['telefone']))) ?>">
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" maxlength="150"
                                   value="<?= esc(old('email', $cliente['email'])) ?>">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('clientes') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
