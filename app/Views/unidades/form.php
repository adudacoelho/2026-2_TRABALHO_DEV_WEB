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
                        <label for="identificador" class="form-label">Identificador *</label>
                        <input type="text" id="identificador" name="identificador" class="form-control" maxlength="50"
                               placeholder="Apto 302"
                               value="<?= esc(old('identificador', $unidade['identificador'])) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="loja_id" class="form-label">Loja</label>
                        <?php $lojaEscolhida = (string) old('loja_id', $unidade['loja_id'] ?? ''); ?>
                        <select id="loja_id" name="loja_id" class="form-select">
                            <option value="">Sem loja</option>
                            <?php foreach ($lojas as $loja): ?>
                                <option value="<?= esc($loja['id']) ?>"
                                    <?= $lojaEscolhida === (string) $loja['id'] ? 'selected' : '' ?>>
                                    <?= esc($loja['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('unidades') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
