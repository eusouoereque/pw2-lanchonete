<?php

namespace App\Controllers;

use App\Models\AdminModel; // Importa o AdminModel
use App\Models\SaleModel;

class AuthController extends BaseController
{
    protected $adminModel;
    protected $saleModel;
    protected $session;

    public function __construct()
    {
        // Carrega o helper URL para usar site_url() e base_url()
        helper('url');
        // Carrega o helper Form para usar nas views, se necessário
        helper('form');

        // Inicializa a sessão
        $this->session = \Config\Services::session();

        // Instancia o AdminModel
        $this->adminModel = new AdminModel();

        $this->saleModel = new SaleModel();
    }

    /**
     * Exibe a página de login.
     */
    public function index()
    {
        // Se o admin já estiver logado, redireciona para o dashboard
        if ($this->session->get('is_admin_logged_in')) {
            return redirect()->to(site_url('admin/dashboard'));
        }
        return view('login_view');
    }

    /**
     * Processa a tentativa de login.
     */
    public function attemptLogin()
    {
        // Regras de validação para o formulário
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]' // Ajuste min_length se necessário
        ];

        // Mensagens de erro personalizadas (opcional)
        $messages = [
            'email' => [
                'required'    => 'O campo email é obrigatório.',
                'valid_email' => 'Por favor, insira um email válido.'
            ],
            'password' => [
                'required'   => 'O campo senha é obrigatório.',
                'min_length' => 'A senha deve ter pelo menos {param} caracteres.'
            ]
        ];

        // Valida os dados do formulário
        if (!$this->validate($rules, $messages)) {
            // Se a validação falhar, retorna para a view de login com os erros
            return view('login_view', ['validation' => $this->validator]);
        }

        // Se a validação passar, tenta autenticar
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Busca o administrador pelo email no banco de dados
        $admin = $this->adminModel->getAdminByEmail($email);

        // Verifica se o administrador existe e se a senha está correta
        if ($admin && password_verify($password, $admin['password'])) {
            // Senha correta, cria a sessão
            $sessionData = [
                'admin_id'    => $admin['id'],
                'admin_name'  => $admin['name'],
                'admin_email' => $admin['email'],
                'is_admin_logged_in' => TRUE
            ];
            $this->session->set($sessionData);

            // Redireciona para o dashboard do administrador
            return redirect()->to(site_url('admin/dashboard'))->with('success', 'Login realizado com sucesso!');
        } else {
            // Credenciais inválidas, retorna para a view de login com mensagem de erro
            return redirect()->back()->withInput()->with('error', 'Email ou senha inválidos.');
        }
    }

    /**
     * Exibe uma página de dashboard simples para o administrador.
     */
    public function dashboard()
    {
        if (!$this->session->get('is_admin_logged_in')) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Você precisa estar logado para acessar esta página.');
        }

        // Buscar dados de vendas para o gráfico
        $dailySalesData = $this->saleModel->getDailySalesSummary(30); // Últimos 30 dias

        // Formatar dados para o Google Charts (array de arrays)
        // A primeira linha são os cabeçalhos das colunas
        $chartData = [['Dia', 'Total de Vendas']];
        foreach ($dailySalesData as $row) {
            // Formatar a data para melhor exibição no gráfico (opcional, mas recomendado)
            // $formattedDate = date('d/m', strtotime($row['sale_day'])); // Ex: 25/05
            // Ou manter YYYY-MM-DD se preferir para ordenação e clareza
            $chartData[] = [$row['sale_day'], (float)$row['daily_total']];
        }

        $data = [
            'admin_name' => $this->session->get('admin_name'),
            'salesChartData' => json_encode($chartData) // Converte para JSON para usar no JavaScript
        ];
        return view('dashboard_view', $data);
    }

    /**
     * Realiza o logout do administrador.
     */
    public function logout()
    {
        // Destroi os dados da sessão específicos do admin ou todos
        $this->session->remove(['admin_id', 'admin_name', 'admin_email', 'is_admin_logged_in']);
        // $this->session->destroy(); // Para destruir toda a sessão

        // Redireciona para a página de login com mensagem de sucesso
        return redirect()->to(site_url('admin/login'))->with('success', 'Logout realizado com sucesso!');
    }
}