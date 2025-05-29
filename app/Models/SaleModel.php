<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['total_amount', 'sale_date', 'payment_method'];
    protected $useTimestamps    = false; // A tabela 'sales' já tem 'sale_date'

    /**
     * Obtém um resumo das vendas diárias para um número específico de dias anteriores.
     *
     * @param int $days Número de dias para buscar (ex: 30 para os últimos 30 dias).
     * @return array Retorna um array com ['sale_day' => 'YYYY-MM-DD', 'daily_total' => valor]
     */
    public function getDailySalesSummary(int $days = 30): array
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->select("DATE(sale_date) as sale_day, SUM(total_amount) as daily_total")
                    ->where('sale_date >=', $startDate)
                    ->groupBy('DATE(sale_date)')
                    ->orderBy('DATE(sale_date)', 'ASC')
                    ->findAll();
    }

    /**
     * Obtém um resumo das vendas por método de pagamento.
     * (Este é um exemplo para um futuro gráfico de pizza, se desejar)
     *
     * @param int $days Número de dias para buscar (ex: 30 para os últimos 30 dias).
     * @return array
     */
    public function getSalesByPaymentMethod(int $days = 30): array
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->select("payment_method, SUM(total_amount) as total_by_method")
                    ->where('sale_date >=', $startDate)
                    ->where('payment_method IS NOT NULL') // Ignora se não houver método de pagamento
                    ->groupBy('payment_method')
                    ->orderBy('total_by_method', 'DESC')
                    ->findAll();
    }
}