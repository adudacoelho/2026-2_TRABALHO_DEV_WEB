<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($titulo ?? 'Mercadinho') ?> | Mercadinho Autônomo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">Mercadinho Autônomo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav me-auto">
                    <?php
                    $itensMenu = [
                        'categorias'   => 'Categorias',
                        'produtos'     => 'Produtos',
                        'fornecedores' => 'Fornecedores',
                        'clientes'     => 'Clientes',
                    ];
                    ?>
                    <?php foreach ($itensMenu as $caminho => $rotulo): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= url_is($caminho . '*') ? 'active fw-bold' : '' ?>"
                               href="<?= site_url($caminho) ?>"><?= esc($rotulo) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container py-4">

    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('sucesso')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erros')): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ((array) session()->getFlashdata('erros') as $erro): ?>
                    <li><?= esc($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('conteudo') ?>

</main>

<footer class="text-center text-muted py-3">
    <small>Sistema para Mercadinho Autônomo | BSI_DevWeb 2026/2</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
