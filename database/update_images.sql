USE plant_app_db;

-- Update PAST products images only
UPDATE products SET image_url = 'uploads/product_neem_oil.jpg' WHERE name = 'Organic Neem Oil';
UPDATE products SET image_url = 'uploads/product_npk.jpg' WHERE name = 'NPK Fertilizer';
UPDATE products SET image_url = 'uploads/product_trowel.jpg' WHERE name = 'Garden Trowel';
UPDATE products SET image_url = 'uploads/product_shears.jpg' WHERE name = 'Pruning Shears'; -- Ensure this is linked
