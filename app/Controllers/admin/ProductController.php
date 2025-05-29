<?php

namespace App\Controllers\Admin; // Namespace para controllers dentro da pasta Admin

use App\Controllers\BaseController;
use App\Models\ProductModel; // Importa o ProductModel

class ProductController extends BaseController
{
    protected $productModel;
    protected $session;
    protected $helpers = ['form', 'url']; // Carrega helpers aqui ou no __construct

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->session = \Config\Services::session();

        // Verificação de login (redundante se o filtro de rota estiver funcionando, mas bom como dupla checagem)
        // Se não quiser essa redundância, pode remover, confiando no filtro.
        if (!$this->session->get('is_admin_logged_in')) {
            // Este redirecionamento pode causar loop se o filtro já redirecionou.
            // É melhor confiar no filtro ou usar uma abordagem diferente para verificação no construtor.
            // Para agora, vamos comentar e confiar no filtro de rotas.
            // return redirect()->to(site_url('admin/login'))->with('error', 'Você precisa estar logado.');
        }
    }

    /**
     * Lista todos os produtos.
     * Rota: GET /admin/products
     */
    public function index()
    {
        $data = [
            'products' => $this->productModel->orderBy('name', 'ASC')->findAll(),
            'title'    => 'Gerenciar Produtos',
            'session'  => $this->session // Passa a sessão para a view, se necessário
        ];
        return view('admin/products/index', $data);
    }

    /**
     * Mostra o formulário para criar um novo produto.
     * Rota: GET /admin/products/new
     */
    public function create()
    {
        $data = [
            'title'      => 'Adicionar Novo Produto',
            'validation' => \Config\Services::validation(), // Para exibir erros de validação
            'session'    => $this->session
        ];
        return view('admin/products/create', $data);
    }

    /**
     * Salva um novo produto no banco de dados.
     * Rota: POST /admin/products/store
     */
    public function store()
    {
        // 1. Definir Regras de Validação (com upload de imagem)
        $rules = [
            'name'  => 'required|min_length[3]|max_length[255]',
            'price' => 'required|decimal',
            'quantity_stock' => 'required|integer',
            'category' => 'permit_empty|max_length[100]',
            'description' => 'permit_empty|max_length[1000]',
            'image_file' => [ // Regras para o arquivo de imagem
                'label' => 'Imagem do Produto',
                'rules' => 'is_image[image_file]' // Garante que é uma imagem
                    . '|mime_in[image_file,image/jpg,image/jpeg,image/png,image/gif]' // Tipos permitidos
                    . '|max_size[image_file,2048]', // Tamanho máximo em KB (2MB)
                // 'uploaded[image_file]' // Removido para tornar opcional. Se obrigatório, adicione.
                'errors' => [ // Mensagens de erro personalizadas para a imagem
                    // 'uploaded' => 'Por favor, selecione uma imagem para o produto.',
                    'is_image' => 'O arquivo enviado não parece ser uma imagem válida.',
                    'mime_in'  => 'A imagem deve ser do tipo JPG, JPEG, PNG ou GIF.',
                    'max_size' => 'A imagem é muito grande. O tamanho máximo permitido é de 2MB.'
                ]
            ]
        ];

        // Mensagens personalizadas (opcional)
        $messages = [
            'name' => [
                'required'   => 'O nome do produto é obrigatório.',
                'min_length' => 'O nome deve ter pelo menos {param} caracteres.'
            ],
            'price' => [
                'required' => 'O preço é obrigatório.',
                'decimal'  => 'O preço deve ser um número decimal válido.'
            ],
            'quantity_stock' => [
                'required' => 'A quantidade em estoque é obrigatória.',
                'integer'  => 'A quantidade deve ser um número inteiro.'
            ]
        ];

        // 2. Validar os Dados
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. Lidar com o Upload da Imagem
        $imageFile = $this->request->getFile('image_file');
        $imageName = null; // Inicializa como null

        // Verifica se um arquivo foi realmente enviado e é válido
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Gera um nome aleatório para o arquivo para evitar conflitos
            $imageName = $imageFile->getRandomName();

            // Define o caminho para o diretório de uploads
            // FCPATH aponta para a pasta 'public'.
            $uploadPath = FCPATH . 'uploads/products/';

            // Tenta mover o arquivo para o diretório de uploads
            // O CodeIgniter automaticamente cria subdiretórios baseados na data se não especificado de outra forma.
            // Para um caminho simples:
            if ($imageFile->move($uploadPath, $imageName)) {
                // Arquivo movido com sucesso, $imageName contém o nome do arquivo.
            } else {
                // Falha ao mover o arquivo
                log_message('error', $imageFile->getErrorString().' ('.$imageFile->getError().')');
                return redirect()->back()->withInput()->with('error', 'Ocorreu um erro ao fazer upload da imagem: ' . $imageFile->getErrorString());
            }
        } elseif ($imageFile && $imageFile->getError() !== UPLOAD_ERR_NO_FILE) {
            // Se houve um erro de upload diferente de "nenhum arquivo enviado"
            return redirect()->back()->withInput()->with('error', 'Erro no upload da imagem: ' . $imageFile->getErrorString());
        }


        // 4. Preparar os Dados para Inserção
        $dataToSave = [
            'name'            => $this->request->getPost('name'),
            'description'     => $this->request->getPost('description'),
            'price'           => $this->request->getPost('price'),
            'quantity_stock'  => $this->request->getPost('quantity_stock'),
            'category'        => $this->request->getPost('category'),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
            'image_path'      => $imageName // Salva o nome da imagem (ou null se não houver upload)
        ];

        // 5. Tentar Salvar no Banco
        if ($this->productModel->save($dataToSave)) {
            return redirect()->to(route_to('admin.products.index'))->with('success', 'Produto adicionado com sucesso!');
        } else {
            // Em caso de falha ao salvar, se uma imagem foi carregada, podemos querer removê-la.
            if ($imageName && file_exists($uploadPath . $imageName)) {
                unlink($uploadPath . $imageName);
            }
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro ao salvar o produto. Tente novamente.')->with('errors', $this->productModel->errors());
        }
    }

    public function edit($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to(route_to('admin.products.index'))
                             ->with('error', 'Produto não encontrado.');
        }

        $data = [
            'title'      => 'Editar Produto: ' . esc($product['name']),
            'product'    => $product,
            'validation' => \Config\Services::validation(), // Para exibir erros se vier do update
            'session'    => $this->session
        ];
        return view('admin/products/edit', $data);
    }

    public function update($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to(route_to('admin.products.index'))
                             ->with('error', 'Produto não encontrado para atualização.');
        }

        // 1. Definir Regras de Validação
        $rules = [
            'name'  => "required|min_length[3]|max_length[255]|is_unique[products.name,id,{$id}]", // Ignora o próprio ID na verificação de unicidade
            'price' => 'required|decimal',
            'quantity_stock' => 'required|integer',
            'category' => 'permit_empty|max_length[100]',
            'description' => 'permit_empty|max_length[1000]',
            'image_file' => [
                'label' => 'Imagem do Produto',
                'rules' => 'is_image[image_file]'
                    . '|mime_in[image_file,image/jpg,image/jpeg,image/png,image/gif]'
                    . '|max_size[image_file,2048]',
                'errors' => [
                    'is_image' => 'O arquivo enviado não parece ser uma imagem válida.',
                    'mime_in'  => 'A imagem deve ser do tipo JPG, JPEG, PNG ou GIF.',
                    'max_size' => 'A imagem é muito grande. O tamanho máximo permitido é de 2MB.'
                ]
            ]
        ];
        // Mensagens não precisam ser repetidas se forem as mesmas do store

        // 2. Validar os Dados
        if (!$this->validate($rules)) { // Não precisa passar $messages se não mudaram
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. Lidar com o Upload da Nova Imagem (se houver)
        $imageFile = $this->request->getFile('image_file');
        $newImageName = $product['image_path']; // Assume imagem antiga por padrão

        $uploadPath = FCPATH . 'uploads/products/';

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = $imageFile->getRandomName();
            if ($imageFile->move($uploadPath, $newImageName)) {
                // Nova imagem carregada com sucesso, excluir a antiga se existir
                if (!empty($product['image_path']) && file_exists($uploadPath . $product['image_path'])) {
                    try {
                        unlink($uploadPath . $product['image_path']);
                    } catch (\Exception $e) {
                        log_message('error', 'Erro ao excluir imagem antiga: ' . $e->getMessage());
                        // Não interrompa o fluxo principal, apenas logue o erro.
                    }
                }
            } else {
                log_message('error', 'Falha ao mover nova imagem: ' . $imageFile->getErrorString());
                return redirect()->back()->withInput()->with('error', 'Ocorreu um erro ao fazer upload da nova imagem: ' . $imageFile->getErrorString());
            }
        } elseif ($imageFile && $imageFile->getError() !== UPLOAD_ERR_NO_FILE) {
             return redirect()->back()->withInput()->with('error', 'Erro no upload da imagem: ' . $imageFile->getErrorString());
        }


        // 4. Preparar os Dados para Atualização
        $dataToUpdate = [
            'name'            => $this->request->getPost('name'),
            'description'     => $this->request->getPost('description'),
            'price'           => $this->request->getPost('price'),
            'quantity_stock'  => $this->request->getPost('quantity_stock'),
            'category'        => $this->request->getPost('category'),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
            'image_path'      => $newImageName // Nome da nova imagem ou o nome da imagem antiga
        ];

        // 5. Tentar Atualizar no Banco
        if ($this->productModel->update($id, $dataToUpdate)) {
            return redirect()->to(route_to('admin.products.index'))->with('success', 'Produto atualizado com sucesso!');
        } else {
            // Se a atualização falhar e uma nova imagem foi carregada, remover a nova imagem
            if ($newImageName !== $product['image_path'] && !empty($newImageName) && file_exists($uploadPath . $newImageName)) {
                 try {
                    unlink($uploadPath . $newImageName);
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao excluir nova imagem após falha no update do DB: ' . $e->getMessage());
                }
            }
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro ao atualizar o produto.')->with('errors', $this->productModel->errors());
        }
    }

    public function delete($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to(route_to('admin.products.index'))
                             ->with('error', 'Produto não encontrado para exclusão.');
        }

        // Tentar excluir o produto do banco de dados
        if ($this->productModel->delete($id)) {
            // Se a exclusão do banco foi bem-sucedida, excluir a imagem do servidor
            if (!empty($product['image_path'])) {
                $imagePath = FCPATH . 'uploads/products/' . $product['image_path'];
                if (file_exists($imagePath)) {
                    try {
                        unlink($imagePath);
                    } catch (\Exception $e) {
                         log_message('error', 'Erro ao excluir imagem do produto: ' . $e->getMessage());
                        // Não impedir o sucesso da mensagem de exclusão do produto, apenas logar.
                    }
                }
            }
            return redirect()->to(route_to('admin.products.index'))
                             ->with('success', 'Produto excluído com sucesso!');
        } else {
            return redirect()->to(route_to('admin.products.index'))
                             ->with('error', 'Ocorreu um erro ao excluir o produto.')
                             ->with('errors', $this->productModel->errors());
        }
    }

    // Métodos edit(), update(), delete() virão nos próximos passos.
}