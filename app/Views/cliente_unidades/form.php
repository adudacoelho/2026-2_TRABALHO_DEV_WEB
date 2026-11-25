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
                        <label for="cliente_id" class="form-label">Cliente *</label>
                        <?php $clienteEscolhido = (string) old('cliente_id', $vinculo['cliente_id'] ?? ''); ?>
                        <select id="cliente_id" name="cliente_id" class="form-select" required>
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= esc($cliente['id']) ?>"
                                    <?= $clienteEscolhido === (string) $cliente['id'] ? 'selected' : '' ?>>
                                    <?= esc($cliente['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="unidade_id" class="form-label">Unidade *</label>
                        <?php $unidadeEscolhida = (string) old('unidade_id', $vinculo['unidade_id'] ?? ''); ?>
                        <select id="unidade_id" name="unidade_id" class="form-select" required>
                            <option value="">Selecione uma unidade</option>
                            <?php foreach ($unidades as $unidade): ?>
                                <option value="<?= esc($unidade['id']) ?>"
                                    <?= $unidadeEscolhida === (string) $unidade['id'] ? 'selected' : '' ?>>
                                    <?= esc($unidade['identificador']) ?> — <?= esc($unidade['loja_nome'] ?? 'Sem loja') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('cliente-unidades') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
