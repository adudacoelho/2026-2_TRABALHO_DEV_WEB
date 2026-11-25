<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Categorias::index');

$routes->group('categorias', static function ($routes) {
    $routes->get('', 'Categorias::index');
    $routes->get('nova', 'Categorias::nova');
    $routes->post('salvar', 'Categorias::salvar');
    $routes->get('editar/(:num)', 'Categorias::editar/$1');
    $routes->post('atualizar/(:num)', 'Categorias::atualizar/$1');
    $routes->post('excluir/(:num)', 'Categorias::excluir/$1');
});

$routes->group('produtos', static function ($routes) {
    $routes->get('', 'Produtos::index');
    $routes->get('novo', 'Produtos::novo');
    $routes->post('salvar', 'Produtos::salvar');
    $routes->get('editar/(:num)', 'Produtos::editar/$1');
    $routes->post('atualizar/(:num)', 'Produtos::atualizar/$1');
    $routes->post('excluir/(:num)', 'Produtos::excluir/$1');
});

$routes->group('fornecedores', static function ($routes) {
    $routes->get('', 'Fornecedores::index');
    $routes->get('novo', 'Fornecedores::novo');
    $routes->post('salvar', 'Fornecedores::salvar');
    $routes->get('editar/(:num)', 'Fornecedores::editar/$1');
    $routes->post('atualizar/(:num)', 'Fornecedores::atualizar/$1');
    $routes->post('excluir/(:num)', 'Fornecedores::excluir/$1');
});

$routes->group('clientes', static function ($routes) {
    $routes->get('', 'Clientes::index');
    $routes->get('novo', 'Clientes::novo');
    $routes->post('salvar', 'Clientes::salvar');
    $routes->get('editar/(:num)', 'Clientes::editar/$1');
    $routes->post('atualizar/(:num)', 'Clientes::atualizar/$1');
    $routes->post('excluir/(:num)', 'Clientes::excluir/$1');
});

$routes->group('lojas', static function ($routes) {
    $routes->get('', 'Lojas::index');
    $routes->get('nova', 'Lojas::novo');
    $routes->post('salvar', 'Lojas::salvar');
    $routes->get('editar/(:num)', 'Lojas::editar/$1');
    $routes->post('atualizar/(:num)', 'Lojas::atualizar/$1');
    $routes->post('excluir/(:num)', 'Lojas::excluir/$1');
});

$routes->group('franquias', static function ($routes) {
    $routes->get('', 'Franquias::index');
    $routes->get('nova', 'Franquias::novo');
    $routes->post('salvar', 'Franquias::salvar');
    $routes->get('editar/(:num)', 'Franquias::editar/$1');
    $routes->post('atualizar/(:num)', 'Franquias::atualizar/$1');
    $routes->post('excluir/(:num)', 'Franquias::excluir/$1');
});

$routes->group('estoque-lojas', static function ($routes) {
    $routes->get('', 'EstoqueLojas::index');
    $routes->get('novo', 'EstoqueLojas::novo');
    $routes->post('salvar', 'EstoqueLojas::salvar');
    $routes->get('editar/(:num)', 'EstoqueLojas::editar/$1');
    $routes->post('atualizar/(:num)', 'EstoqueLojas::atualizar/$1');
    $routes->post('excluir/(:num)', 'EstoqueLojas::excluir/$1');
});

$routes->group('estoque-movimentacoes', static function ($routes) {
    $routes->get('', 'EstoqueMovimentacoes::index');
});

$routes->group('venda-produtos', static function ($routes) {
    $routes->get('', 'VendaProdutos::index');
    $routes->get('opcoes', 'VendaProdutos::opcoes');
    $routes->get('nova', 'VendaProdutos::novo');
    $routes->post('salvar', 'VendaProdutos::salvar');
    $routes->get('editar/(:num)', 'VendaProdutos::editar/$1');
    $routes->post('atualizar/(:num)', 'VendaProdutos::atualizar/$1');
    $routes->post('excluir/(:num)', 'VendaProdutos::excluir/$1');
});

$routes->group('unidades', static function ($routes) {
    $routes->get('', 'Unidades::index');
    $routes->get('nova', 'Unidades::novo');
    $routes->post('salvar', 'Unidades::salvar');
    $routes->get('editar/(:num)', 'Unidades::editar/$1');
    $routes->post('atualizar/(:num)', 'Unidades::atualizar/$1');
    $routes->post('excluir/(:num)', 'Unidades::excluir/$1');
});

$routes->group('cliente-unidades', static function ($routes) {
    $routes->get('', 'ClienteUnidades::index');
    $routes->get('novo', 'ClienteUnidades::novo');
    $routes->post('salvar', 'ClienteUnidades::salvar');
    $routes->get('editar/(:num)', 'ClienteUnidades::editar/$1');
    $routes->post('atualizar/(:num)', 'ClienteUnidades::atualizar/$1');
    $routes->post('excluir/(:num)', 'ClienteUnidades::excluir/$1');
});
