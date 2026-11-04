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
