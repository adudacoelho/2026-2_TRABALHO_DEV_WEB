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
                        <?php $clienteEscolhido = (string) old('cliente_id', $venda['cliente_id'] ?? ''); ?>
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

                    <div class="row">
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="loja_id" class="form-label">Loja *</label>
                            <?php $lojaEscolhida = (string) old('loja_id', $venda['loja_id'] ?? ''); ?>
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
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="produto_id" class="form-label">Produto *</label>
                            <?php $produtoEscolhido = (string) old('produto_id', $venda['produto_id'] ?? ''); ?>
                            <select id="produto_id" name="produto_id" class="form-select" required>
                                <option value="">Selecione um produto</option>
                                <?php foreach ($produtos as $produto): ?>
                                    <?php $precoProduto = (float) $produto['preco']; ?>
                                    <option value="<?= esc($produto['id']) ?>"
                                            data-preco="<?= esc(number_format($precoProduto, 2, '.', '')) ?>"
                                        <?= $produtoEscolhido === (string) $produto['id'] ? 'selected' : '' ?>>
                                        <?= esc($produto['nome']) ?> — R$ <?= esc(number_format($precoProduto, 2, ',', '.')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="unidade_id" class="form-label">Unidade do cliente *</label>
                        <?php $unidadeEscolhida = (string) old('unidade_id', $venda['unidade_id'] ?? ''); ?>
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

                    <div class="row">
                        <div class="col-12 col-sm-4 mb-3">
                            <label for="data" class="form-label">Data e hora *</label>
                            <?php $dataPadrao = str_replace(' ', 'T', substr((string) $venda['data'], 0, 16)); ?>
                            <input type="datetime-local" id="data" name="data" class="form-control"
                                   value="<?= esc(old('data', $dataPadrao)) ?>" required>
                        </div>
                        <div class="col-12 col-sm-4 mb-3">
                            <label for="quantidade" class="form-label">Quantidade *</label>
                            <input type="number" id="quantidade" name="quantidade" class="form-control" step="1" min="1"
                                   value="<?= esc(old('quantidade', $venda['quantidade'])) ?>" required>
                        </div>
                        <div class="col-12 col-sm-4 mb-3">
                            <label for="preco_unitario" class="form-label">Preço unit. (R$) *</label>
                            <input type="number" id="preco_unitario" name="preco_unitario" class="form-control" step="0.01" min="0.01"
                                   value="<?= esc(old('preco_unitario', $venda['preco_unitario'])) ?>" required>
                            <div class="form-text">Preenchido com o preço do produto; pode ser ajustado.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('venda-produtos') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cliente = document.getElementById('cliente_id');
    const loja = document.getElementById('loja_id');
    const unidade = document.getElementById('unidade_id');
    const produto = document.getElementById('produto_id');
    const preco = document.getElementById('preco_unitario');
    const endpoint = <?= json_encode(site_url('venda-produtos/opcoes'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    function preencher(select, itens, placeholder, manter) {
        const atual = String(manter || '');
        const existe = itens.some(function (item) { return String(item.id) === atual; });

        select.replaceChildren(new Option(placeholder, ''));
        itens.forEach(function (item) {
            select.add(new Option(item.nome, item.id));
        });

        // Só mantém a seleção se ela ainda for válida; caso contrário, volta ao
        // placeholder para o usuário escolher uma opção permitida.
        if (atual && existe) {
            select.value = atual;
        }
    }

    async function buscar(params) {
        const resposta = await fetch(endpoint + '?' + params.toString());

        if (! resposta.ok) {
            return { lojas: [], unidades: [] };
        }

        return resposta.json();
    }

    function paramsCliente() {
        return new URLSearchParams({ cliente_id: cliente.value });
    }

    async function atualizarUnidades(manter) {
        if (! cliente.value || ! loja.value) {
            preencher(unidade, [], 'Selecione uma unidade', manter);
            return;
        }

        const params = paramsCliente();
        params.set('loja_id', loja.value);
        const dados = await buscar(params);

        preencher(unidade, dados.unidades, 'Selecione uma unidade', manter);

        if (! unidade.value && dados.unidades.length === 1) {
            unidade.value = dados.unidades[0].id;
        }
    }

    async function atualizarLojas(manterLoja, manterUnidade) {
        if (! cliente.value) {
            preencher(loja, [], 'Selecione uma loja', manterLoja);
            await atualizarUnidades(manterUnidade);
            return;
        }

        const dados = await buscar(paramsCliente());
        preencher(loja, dados.lojas, 'Selecione uma loja', manterLoja);

        if (! loja.value && dados.lojas.length === 1) {
            loja.value = dados.lojas[0].id;
        }

        await atualizarUnidades(manterUnidade);
    }

    cliente.addEventListener('change', function () {
        loja.value = '';
        unidade.value = '';
        atualizarLojas();
    });

    loja.addEventListener('change', function () {
        unidade.value = '';
        atualizarUnidades();
    });

    // No cadastro os campos começam vazios; na edição as seleções atuais são preservadas.
    atualizarLojas(loja.value, unidade.value);

    function aplicarPrecoDoProduto() {
        const selecionado = produto.options[produto.selectedIndex];
        const valor = selecionado ? selecionado.dataset.preco : '';

        if (valor) {
            preco.value = valor;
        }
    }

    produto.addEventListener('change', aplicarPrecoDoProduto);

    // Na edição o preço congelado da venda não deve ser sobrescrito.
    if (preco.value.trim() === '') {
        aplicarPrecoDoProduto();
    }
});
</script>

<?= $this->endSection() ?>
