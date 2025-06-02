# Sistema de Gerenciamento de Lanchonete

Este é um sistema de gerenciamento para lanchonetes desenvolvido como parte de um projeto acadêmico/prático, utilizando PHP com o framework CodeIgniter 4 e Bootstrap para o frontend. O sistema permite o gerenciamento de produtos e visualização de vendas através de um painel administrativo.

## Membros do Grupo

* **[Nome Completo do Membro 1]** - RA: [RA do Membro 1]
* **[Nome Completo do Membro 2]** - RA: [RA do Membro 2]
* **[Nome Completo do Membro 3]** - RA: [RA do Membro 3]
    * *(Adicione ou remova membros conforme necessário)*

## Sobre o Projeto

O sistema possui as seguintes funcionalidades principais:
* Autenticação de administrador.
* Painel administrativo (Dashboard) com gráfico de vendas diárias.
* CRUD (Criar, Ler, Atualizar, Deletar) de produtos, incluindo upload de imagens.

## Tecnologias Utilizadas

* PHP 8.x
* CodeIgniter 4.x
* MySQL (ou MariaDB)
* Bootstrap 5.x
* Google Charts API
* Apache (ou outro servidor web com suporte a PHP)

## Scripts utilizados

-- Cria o banco de dados se ele não existir, definindo o conjunto de caracteres e colação
CREATE DATABASE IF NOT EXISTS lanchonete_db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco de dados para uso
USE lanchonete_db;

-- Tabela de Usuários Administradores
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL, -- Armazenar hash da senha
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir um administrador padrão
-- Senha: password (hash bcrypt)
INSERT INTO `admins` (`name`, `email`, `password`) VALUES
('Admin Lanchonete', 'admin@example.com', '$2y$10$9.tA5sB9vP2xC8yZ0fE7nO0rX6uY3iW1aJ4sD7kF0gH2mN5oPqRsC');

-- Tabela de Produtos
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `quantity_stock` INT NOT NULL DEFAULT 0,
  `image_path` VARCHAR(255) NULL, -- Caminho para a imagem do produto
  `category` VARCHAR(100) NULL,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE, -- TRUE (1) para ativo, FALSE (0) para inativo
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir alguns produtos de exemplo
INSERT INTO `products` (`name`, `description`, `price`, `quantity_stock`, `category`, `image_path`) VALUES
('X-Burger Clássico', 'Pão, hambúrguer de carne bovina, queijo, alface, tomate e molho especial.', 18.50, 50, 'Sanduíches', 'xburger.jpg'),
('Batata Frita Média', 'Porção média de batatas fritas crocantes.', 9.00, 100, 'Acompanhamentos', 'batata_frita.jpg'),
('Refrigerante Lata', 'Refrigerante em lata 350ml (diversos sabores).', 5.00, 200, 'Bebidas', 'refrigerante.jpg'),
('Pizza Margherita Individual', 'Molho de tomate, mussarela fresca e manjericão.', 25.00, 30, 'Pizzas', 'pizza_margherita.jpg'),
('Suco Natural de Laranja', 'Suco de laranja feito na hora, 500ml.', 8.00, 70, 'Bebidas', 'suco_laranja.jpg');


-- Tabela de Vendas
CREATE TABLE IF NOT EXISTS `sales` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `sale_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `payment_method` VARCHAR(50) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Itens da Venda (para relacionar produtos a uma venda específica)
CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sale_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10, 2) NOT NULL, -- Preço do produto no momento da venda
  `total_price` DECIMAL(10, 2) NOT NULL, -- quantity * unit_price
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE, -- Se uma venda for deletada, seus itens também são.
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT -- Impede deletar um produto se ele estiver em um item de venda.
                                                                         -- Pode ser alterado para ON DELETE SET NULL se preferir manter o histórico
                                                                         -- mesmo que o produto seja removido do catálogo.
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir algumas vendas de exemplo para popular o gráfico
-- Venda 1
INSERT INTO `sales` (`total_amount`, `payment_method`, `sale_date`) VALUES 
(23.50, 'Cartão de Crédito', NOW() - INTERVAL 7 DAY); -- Venda de 7 dias atrás
SET @last_sale_id1 = LAST_INSERT_ID();
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(@last_sale_id1, 1, 1, 18.50, 18.50), -- X-Burger
(@last_sale_id1, 3, 1, 5.00, 5.00);   -- Refrigerante

-- Venda 2
INSERT INTO `sales` (`total_amount`, `payment_method`, `sale_date`) VALUES 
(27.50, 'PIX', NOW() - INTERVAL 6 DAY); -- Venda de 6 dias atrás
SET @last_sale_id2 = LAST_INSERT_ID();
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(@last_sale_id2, 1, 1, 18.50, 18.50), -- X-Burger
(@last_sale_id2, 2, 1, 9.00, 9.00);   -- Batata Frita

-- Venda 3
INSERT INTO `sales` (`total_amount`, `payment_method`, `sale_date`) VALUES 
(42.00, 'Dinheiro', NOW() - INTERVAL 6 DAY); -- Outra venda de 6 dias atrás
SET @last_sale_id3 = LAST_INSERT_ID();
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(@last_sale_id3, 4, 1, 25.00, 25.00), -- Pizza
(@last_sale_id3, 5, 1, 8.00, 8.00),   -- Suco
(@last_sale_id3, 3, 2, 5.00, 10.00);  -- 2 Refrigerantes (simulando quantity > 1)

-- Venda 4
INSERT INTO `sales` (`total_amount`, `payment_method`, `sale_date`) VALUES 
(103.00, 'Cartão de Débito', NOW() - INTERVAL 3 DAY); -- Venda de 3 dias atrás
SET @last_sale_id4 = LAST_INSERT_ID();
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(@last_sale_id4, 1, 2, 18.50, 37.00), -- 2 X-Burger
(@last_sale_id4, 2, 1, 9.00, 9.00),   -- Batata Frita
(@last_sale_id4, 4, 1, 25.00, 25.00), -- Pizza
(@last_sale_id4, 5, 2, 8.00, 16.00),  -- 2 Sucos
(@last_sale_id4, 3, 3, 5.00, 15.00);  -- 3 Refrigerantes

-- Venda 5
INSERT INTO `sales` (`total_amount`, `payment_method`, `sale_date`) VALUES 
(55.50, 'PIX', NOW() - INTERVAL 1 DAY); -- Venda de ontem
SET @last_sale_id5 = LAST_INSERT_ID();
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(@last_sale_id5, 1, 3, 18.50, 55.50); -- 3 X-Burger

-- Venda 6 (Hoje)
INSERT INTO `sales` (`total_amount`, `payment_method`, `sale_date`) VALUES 
(33.00, 'Dinheiro', NOW());
SET @last_sale_id6 = LAST_INSERT_ID();
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(@last_sale_id6, 2, 2, 9.00, 18.00), -- 2 Batatas
(@last_sale_id6, 3, 3, 5.00, 15.00);  -- 3 Refrigerantes
