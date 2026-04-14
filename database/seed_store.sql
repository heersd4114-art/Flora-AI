USE plant_app_db;

-- 1. Insert More Comprehensive Products (Tools, Fertilizers, Cures)
INSERT INTO products (name, type, description, price, stock_quantity, image_url) VALUES 
('Copper Fungicide', 'Pesticide', 'Effective against blight, mildew, and leaf spots.', 450.00, 50, 'uploads/product_copper_fungicide.jpg'),
('All-Purpose Liquid Fertilizer', 'Fertilizer', 'Balanced nutrients for all house plants.', 300.00, 100, 'uploads/product_liquid_fert.jpg'),
('Pruning Shears', 'Tool', 'Sharp shears for removing diseased branches.', 850.00, 30, 'uploads/product_shears.jpg'),
('Soil pH Meter', 'Tool', 'Test your soil acidity instantly.', 1200.00, 20, 'uploads/product_ph_meter.jpg'),
('Yellow Sticky Traps', 'Other', 'Traps flying insects like whiteflies.', 150.00, 200, 'uploads/product_traps.jpg');

-- 2. Insert More Diseases to match AI output potential
INSERT INTO diseases (name, symptoms, care_tips) VALUES 
('Leaf Spot', 'Brown or black spots on foliage.', 'Apply fungicide and avoid overhead watering.'),
('Powdery Mildew', 'White powdery substance on leaves.', 'Improve air circulation and use sulfur-based fungicide.'),
('Nutrient Deficiency', 'Yellowing leaves (chlorosis).', 'Apply balanced fertilizer and check soil pH.');

-- 3. Link Diseases to Treatments
-- Leaf Blight (ID 1) -> Copper Fungicide (ID 4 - estimated)
INSERT INTO disease_treatments (disease_id, product_id) VALUES 
(1, (SELECT product_id FROM products WHERE name='Copper Fungicide')),
((SELECT disease_id FROM diseases WHERE name='Leaf Spot'), (SELECT product_id FROM products WHERE name='Copper Fungicide')),
((SELECT disease_id FROM diseases WHERE name='Powdery Mildew'), (SELECT product_id FROM products WHERE name='Organic Neem Oil')),
((SELECT disease_id FROM diseases WHERE name='Nutrient Deficiency'), (SELECT product_id FROM products WHERE name='All-Purpose Liquid Fertilizer'));
