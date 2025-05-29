<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products'; // Nome da tabela no banco de dados
    protected $primaryKey       = 'id';     // Chave primária da tabela
    protected $returnType       = 'array';  // Pode ser 'array' ou 'object'
    protected $useSoftDeletes   = false;    // Se true, usa 'deleted_at' para soft deletes

    // Campos que podem ser inseridos/atualizados através do model
    protected $allowedFields    = [
        'name',
        'description',
        'price',
        'quantity_stock',
        'image_path',
        'category',
        'is_active'
    ];

    // Habilita o uso de timestamps (created_at, updated_at)
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Regras de Validação (opcional, mas recomendado definir aqui ou no controller)
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;

    // Você pode adicionar métodos personalizados aqui, se necessário
    // Ex: para buscar produtos por categoria, etc.
}