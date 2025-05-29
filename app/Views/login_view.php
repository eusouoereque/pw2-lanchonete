<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - Lanchonete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="card login-card shadow">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Login Administrador</h3>

            <?php $validation = \Config\Services::validation(); ?>
            <?php $session = \Config\Services::session(); ?>

            <?php if ($validation->getErrors()) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <?php if ($session->getFlashdata('error')) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $session->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            <?php if ($session->getFlashdata('success')) : ?>
                <div class="alert alert-success" role="alert">
                    <?= $session->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?= form_open(site_url('admin/login/attempt')) ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </div>
            <?= form_close() ?>
        </div>
        <div class="card-footer text-center py-3">
            <small>&copy; <?= date('Y') ?> Lanchonete System</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>