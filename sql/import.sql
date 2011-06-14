# IMPORT SCRIPT - db_gl -> glcorporate

# 1: db_gl.tbl_products -> glcorporate.products

TRUNCATE `glcorporate`.`products`;
INSERT INTO `glcorporate`.`products`
SELECT
	`db_gl`.`tbl_products`.`product_id` as `id`,
	`db_gl`.`tbl_products`.`product_number` as `sku`,
	`db_gl`.`tbl_products`.`product_image` as `image`,
	`db_gl`.`tbl_products`.`product_description` as `description`,
	`db_gl`.`tbl_product_category`.`category_id` as `category_id`,
	`db_gl`.`tbl_product_subcategory`.`subcategory_id` as `category_id`
FROM `db_gl`.`tbl_products` 
LEFT JOIN `db_gl`.`tbl_product_category`	
	ON (`db_gl`.`tbl_products`.`product_id`=`db_gl`.`tbl_product_category`.`product_id`) 
LEFT JOIN `db_gl`.`tbl_product_subcategory`
	ON (`db_gl`.`tbl_products`.`product_id`=`db_gl`.`tbl_product_subcategory`.`product_id`)
GROUP BY `db_gl`.`tbl_products`.`product_id`;

# end 1:

# 2: db_gl.tbl_category -> glcorporate.categories

TRUNCATE `glcorporate`.`categories`
INSERT INTO `glcorporate`.`categories`
SELECT 
	`db_gl`.`tbl_category`.`category_id` as id,
	`db_gl`.`tbl_category`.`category_name` as name
FROM `db_gl`.`tbl_category`;

# end 2:

# 3: db_gl.tbl_category -> glcorporate.categories

TRUNCATE `glcorporate`.`subcategories`
INSERT INTO `glcorporate`.`subcategories`
SELECT 
	`db_gl`.`tbl_subcategory`.`subcategory_id` as `id`,
	`db_gl`.`tbl_subcategory`.`subcategory_name` as `name`,
	`db_gl`.`tbl_subcategory`.`extra_info` as `description`,
	`db_gl`.`tbl_subcategory`.`category_id` as `category_id`
FROM `db_gl`.`tbl_subcategory`;

# end 3:

# 4: db_gl.tbl_products -> glcorporate.featured

INSERT INTO `glcorporate`.`featured`
(`product_id`, `location`)
SELECT 
	`product_id` as `product_id`,
	'category' as `location`
FROM tbl_products
	WHERE `featured` = 1;
	
INSERT INTO `glcorporate`.`featured`
(`product_id`, `location`)
SELECT 
	`product_id` as `product_id`,
	'homepage' as `location`
FROM tbl_products
	WHERE `featured_home` = 1;
	
# end 4:
