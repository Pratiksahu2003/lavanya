-- Allow the same product_code in different category/subcategory combinations.
-- Run once on the existing NOVAHOMZ database; this does not delete data.

USE `novahomz`;

DROP INDEX IF EXISTS `product_code` ON `products`;
DROP INDEX IF EXISTS `idx_code` ON `products`;
CREATE INDEX IF NOT EXISTS `idx_code_scope` ON `products` (`product_code`, `category_id`, `subcategory_id`);
