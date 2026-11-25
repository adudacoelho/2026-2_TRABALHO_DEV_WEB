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
                        <label for="loja_id" class="form-label">Loja *</label>
                        <?php $lojaEscolhida = (string) old('loja_id', $estoque['loja_id'] ?? ''); ?>
                        <select id="loja_id" name="loja_id" class="form-select" required>
                            <option value="">Selecione uma loja</option>
                            <?php foreach ($lojas as $loja): ?>
                                <option value="<?= esc($loja['id']) ?>"
                                    <?= $lojaEscolhida === (string) $loja['id'] ? 'selected' : '' ?>>
                                    <?= esc($loja['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="produto_id" class="form-label">Produto *</label>
                        <?php $produtoEscolhido = (string) old('produto_id', $estoque['produto_id'] ?? ''); ?>
                        <select id="produto_id" name="produto_id" class="form-select" required>
                            <option value="">Selecione um produto</option>
                            <?php foreach ($produtos as $produto): ?>
                                <option value="<?= esc($produto['id']) ?>"
                                    <?= $produtoEscolhido === (string) $produto['id'] ? 'selected' : '' ?>>
                                    <?= esc($produto['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade *</label>
                        <input type="number" id="quantidade" name="quantidade" class="form-control" step="1" min="-99999"
                               value="<?= esc(old('quantidade', $estoque['quantidade'])) ?>" required>
                        <div class="form-text">Saldo negativo é permitido e indica venda sem entrada de estoque.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('estoque-lojas') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
