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
                        <label for="nome" class="form-label">Nome / Razão social *</label>
                        <input type="text" id="nome" name="nome" class="form-control" maxlength="150"
                               value="<?= esc(old('nome', $fornecedor['nome'])) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <input type="text" id="descricao" name="descricao" class="form-control" maxlength="255"
                               value="<?= esc(old('descricao', $fornecedor['descricao'])) ?>">
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="cnpj" class="form-label">CNPJ *</label>
                            <input type="text" id="cnpj" name="cnpj" class="form-control" maxlength="18"
                                   placeholder="00.000.000/0000-00" inputmode="numeric"
                                   value="<?= esc(old('cnpj', formatar_cnpj($fornecedor['cnpj']))) ?>" required>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" id="telefone" name="telefone" class="form-control" maxlength="15"
                                   placeholder="(27) 99999-9999" inputmode="tel"
                                   value="<?= esc(old('telefone', formatar_telefone($fornecedor['telefone']))) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" id="endereco" name="endereco" class="form-control" maxlength="255"
                               value="<?= esc(old('endereco', $fornecedor['endereco'])) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" id="email" name="email" class="form-control" maxlength="150"
                               value="<?= esc(old('email', $fornecedor['email'])) ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('fornecedores') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
