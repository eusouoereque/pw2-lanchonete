<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Adicionar Produto') ?> - Lanchonete Admin</title>
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
        <h1><?= esc($title ?? 'Adicionar Novo Produto') ?></h1>

        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger">
                <strong>Ocorreram alguns erros:</strong>
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>


        <?= form_open_multipart(route_to('admin.products.store')) ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Preço (R$) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="price" name="price" value="<?= old('price') ?>" placeholder="Ex: 10.50" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantity_stock" class="form-label">Qtd. em Estoque <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity_stock" name="quantity_stock" value="<?= old('quantity_stock', 0) ?>" required>
                        </div>
                    </div>

                     <div class="mb-3">
                        <label for="category" class="form-label">Categoria</label>
                        <input type="text" class="form-control" id="category" name="category" value="<?= old('category') ?>" placeholder="Ex: Sanduíches, Bebidas">
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label">Imagem do Produto</label>
                        <input type="file" class="form-control" id="image" name="image_file">
                        <small class="form-text text-muted">Deixe em branco se não quiser adicionar/alterar.</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?= old('is_active', 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Produto Ativo</label>
                    </div>
                </div>
            </div>


            <button type="submit" class="btn btn-primary">Salvar Produto</button>
            <a href="<?= route_to('admin.products.index') ?>" class="btn btn-secondary">Cancelar</a>
        <?= form_close() ?>
    </div>

    <footer class="text-center mt-5 py-3 bg-light">
        <p>&copy; <?= date('Y') ?> Lanchonete System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>