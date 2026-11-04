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
                        <input type="text" id="nome" name="nome" class="form-control" maxlength="100"
                               value="<?= esc(old('nome', $produto['nome'])) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <input type="text" id="descricao" name="descricao" class="form-control" maxlength="255"
                               value="<?= esc(old('descricao', $produto['descricao'])) ?>">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="preco" class="form-label">Preço (R$) *</label>
                            <input type="number" id="preco" name="preco" class="form-control" step="0.01" min="0.01"
                                   value="<?= esc(old('preco', $produto['preco'])) ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="quantidade" class="form-label">Quantidade *</label>
                            <input type="number" id="quantidade" name="quantidade" class="form-control" step="1" min="0"
                                   value="<?= esc(old('quantidade', $produto['quantidade'])) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <?php $categoriaEscolhida = (string) old('categoria_id', $produto['categoria_id'] ?? ''); ?>
                        <select id="categoria_id" name="categoria_id" class="form-select">
                            <option value="">Sem categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= esc($categoria['id']) ?>"
                                    <?= $categoriaEscolhida === (string) $categoria['id'] ? 'selected' : '' ?>>
                                    <?= esc($categoria['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('produtos') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
