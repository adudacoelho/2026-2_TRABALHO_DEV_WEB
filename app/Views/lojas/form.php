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
                        <label for="nome" class="form-label">Nome da loja *</label>
                        <input type="text" id="nome" name="nome" class="form-control" maxlength="150"
                               value="<?= esc(old('nome', $loja['nome'])) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="franquia_id" class="form-label">Franquia *</label>
                        <select id="franquia_id" name="franquia_id" class="form-select" required>
                            <option value="">Selecione uma franquia</option>
                            <?php foreach ($franquias as $franquia): ?>
                                <option value="<?= esc($franquia['id']) ?>" <?= (string) old('franquia_id', $loja['franquia_id']) === (string) $franquia['id'] ? 'selected' : '' ?>>
                                    <?= esc($franquia['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" id="endereco" name="endereco" class="form-control" maxlength="255"
                               value="<?= esc(old('endereco', $loja['endereco'])) ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('lojas') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
