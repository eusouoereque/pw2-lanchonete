<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Rotas para Autenticação do Admin
$routes->group('admin', function ($routes) {
    // Rota para exibir o formulário de login
    // URL: seubaseurl/admin/login
    $routes->get('login', 'AuthController::index');

    // Rota para processar a tentativa de login (POST)
    // URL: seubaseurl/admin/login/attempt
    $routes->post('login/attempt', 'AuthController::attemptLogin');

    // Rota para o dashboard do administrador
    // URL: seubaseurl/admin/dashboard
    $routes->get('dashboard', 'AuthController::dashboard');

    // Rota para logout
    // URL: seubaseurl/admin/logout
    $routes->get('logout', 'AuthController::logout');

    // Rotas de Produtos
    // Listar produtos
    $routes->get('products', 'Admin\ProductController::index', ['as' => 'admin.products.index']);
    // Mostrar formulário para novo produto
    $routes->get('products/new', 'Admin\ProductController::create', ['as' => 'admin.products.create']);
    // Salvar novo produto (processar formulário)
    $routes->post('products/store', 'Admin\ProductController::store', ['as' => 'admin.products.store']);
    // Mostrar formulário para editar produto
    $routes->get('products/edit/(:num)', 'Admin\ProductController::edit/$1', ['as' => 'admin.products.edit']);
    // Atualizar produto (processar formulário de edição)
    $routes->post('products/update/(:num)', 'Admin\ProductController::update/$1', ['as' => 'admin.products.update']);
    // Deletar produto (pode ser GET para simplicidade, ou POST/DELETE para melhor semântica)
    $routes->get('products/delete/(:num)', 'Admin\ProductController::delete/$1', ['as' => 'admin.products.delete']);
});
