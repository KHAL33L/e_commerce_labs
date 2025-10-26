-- Fix cart table foreign key
-- Drop the existing foreign key constraint
ALTER TABLE `cart` DROP FOREIGN KEY `cart_ibfk_1`;

-- Add the correct foreign key that references product.id
ALTER TABLE `cart` 
ADD CONSTRAINT `cart_ibfk_1` 
FOREIGN KEY (`p_id`) REFERENCES `product` (`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;
