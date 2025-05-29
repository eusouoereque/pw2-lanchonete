<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admins'; // Nome da tabela no banco de dados
    protected $primaryKey       = 'id';     // Chave primária da tabela
    protected $allowedFields    = ['name', 'email', 'password']; // Campos que podem ser inseridos/atualizados

    // Campos de data e hora automáticos (opcional, mas bom para rastreamento)
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Busca um administrador pelo email.
     *
     * @param string $email O email do administrador.
     * @return object|null Retorna o objeto do administrador se encontrado, ou null caso contrário.
     */
    public function getAdminByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}