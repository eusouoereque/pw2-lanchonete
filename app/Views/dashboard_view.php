<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Lanchonete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Lanchonete Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= route_to('admin.products.index') ?>">Produtos</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php $session = \Config\Services::session(); // Garante que a sessão está disponível ?>
                    <?php if ($session && $session->get('is_admin_logged_in')) : ?>
                        <li class="nav-item">
                            <span class="navbar-text me-3">
                                Olá, <?= esc($admin_name ?? $session->get('admin_name')) ?>!
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
        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Bem-vindo ao Painel Administrativo!</h1>
                <p class="col-md-8 fs-4">Aqui você poderá gerenciar os produtos, vendas e outras configurações da lanchonete.</p>
            </div>
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

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Vendas Diárias (Últimos 30 dias)
                    </div>
                    <div class="card-body">
                        <div id="sales_chart_div" style="width: 100%; height: 400px;"></div>
                        <?php if (empty(json_decode($salesChartData, true)) || count(json_decode($salesChartData, true)) <= 1) : ?>
                            <p class="text-center mt-3">Não há dados de vendas suficientes para exibir o gráfico no período selecionado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gerenciar Produtos</h5>
                        <p class="card-text">Adicionar, editar e remover produtos do cardápio.</p>
                        <a href="<?= route_to('admin.products.index') ?>" class="btn btn-primary">Ir para Produtos</a>
                    </div>
                </div>
            </div>
            </div>
    </div>

    <footer class="text-center mt-5 py-3 bg-light">
        <p>&copy; <?= date('Y') ?> Lanchonete System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
      // Carrega a API de visualização e o pacote corechart.
      google.charts.load('current', {'packages':['corechart', 'line']}); // Adicionado 'line' para gráficos de linha explicitamente

      // Define a função de callback para ser executada quando a API do Google Charts estiver carregada.
      google.charts.setOnLoadCallback(drawSalesChart);

      // Função que cria e preenche a tabela de dados, instancia o gráfico de pizza,
      // passa os dados e desenha o gráfico.
      function drawSalesChart() {
        // Pega os dados do PHP (convertidos para JSON)
        var salesDataPHP = <?= $salesChartData ?? '[]' ?>;

        // Verifica se há dados suficientes (além do cabeçalho)
        if (salesDataPHP.length <= 1) {
            // Não desenha o gráfico se não houver dados, a mensagem de "sem dados" no HTML será mostrada.
            // Você pode optar por ocultar o div do gráfico aqui também se preferir.
            document.getElementById('sales_chart_div').innerHTML = '<p class="text-center p-5">Não há dados de vendas para exibir no gráfico para o período selecionado.</p>';
            return;
        }

        // Cria a tabela de dados a partir do array PHP.
        var data = google.visualization.arrayToDataTable(salesDataPHP);

        // Define as opções do gráfico.
        var options = {
          title: 'Performance de Vendas Diárias',
          curveType: 'function', // Para suavizar a linha, opcional
          legend: { position: 'bottom' },
          hAxis: {
            title: 'Dia',
             slantedText: true, // Inclina o texto do eixo horizontal se houver muitos dias
             slantedTextAngle: 45
          },
          vAxis: {
            title: 'Total de Vendas (R$)',
            format: 'currency', // Formata o eixo vertical como moeda
            gridlines: {count: 5}
          },
          series: { // Personalização da série de dados (opcional)
            0: { color: '#007bff' } // Cor da linha de vendas
          },
          animation: { // Animação ao desenhar o gráfico (opcional)
            startup: true,
            duration: 1000,
            easing: 'out',
          }
        };

        // Instancia e desenha o gráfico, passando as opções.
        var chart = new google.visualization.LineChart(document.getElementById('sales_chart_div'));
        chart.draw(data, options);
      }

      // Opcional: Redesenhar o gráfico quando a janela for redimensionada para responsividade
      window.addEventListener('resize', function() {
        drawSalesChart();
      });
    </script>
</body>
</html>