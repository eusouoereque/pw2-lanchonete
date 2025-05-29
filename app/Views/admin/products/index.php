<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Gerenciar Produtos') ?> - Lanchonete Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('admin/dashboard') ?>">Lanchonete Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= route_to('admin.products.index') ?>">Produtos</a>
                    </li>
                    </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if ($session && $session->get('is_admin_logged_in')) : ?>
                        <li class="nav-item">
                            <span class="navbar-text me-3">
                                Olá, <?= esc($session->get('admin_name')) ?>!
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-danger" href="<?= site_url('admin/logout') ?>">Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1><?= esc($title ?? 'Gerenciar Produtos') ?></h1>
            <a href="<?= route_to('admin.products.create') ?>" class="btn btn-primary">Adicionar Novo Produto</a>
        </div>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($products) && is_array($products)) : ?>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th> <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Categoria</th>
                        <th>Ativo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) : ?>
                        <tr>
                            <td><?= esc($product['id']) ?></td>
                            <td>
                                <?php if (!empty($product['image_path'])) : ?>
                                    <img src="<?= base_url('uploads/products/' . esc($product['image_path'])) ?>" 
                                        alt="<?= esc($product['name']) ?>" 
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else : ?>
                                    <img src="<?= base_url('uploads/products/default.png') // Ou um placeholder ?> " 
                                        alt="Sem Imagem" 
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td><?= esc($product['name']) ?></td>
                            <td>R$ <?= number_format($product['price'], 2, ',', '.') ?></td>
                            <td><?= esc($product['quantity_stock']) ?></td>
                            <td><?= esc($product['category'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($product['is_active']) : ?>
                                    <span class="badge bg-success">Sim</span>
                                <?php else : ?>
                                    <span class="badge bg-danger">Não</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= route_to('admin.products.edit', $product['id']) ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="<?= route_to('admin.products.delete', $product['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="alert alert-info">Nenhum produto encontrado.</div>
        <?php endif; ?>
    </div>

    <footer class="text-center mt-5 py-3 bg-light">
        <p>&copy; <?= date('Y') ?> Lanchonete System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>