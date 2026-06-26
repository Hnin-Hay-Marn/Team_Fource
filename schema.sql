-- DROP TABLE IF EXISTS expenses;
-- DROP TABLE IF EXISTS categories;
-- DROP TABLE IF EXISTS budgets;
-- DROP TABLE IF EXISTS incomes;
-- DROP TABLE IF EXISTS settings;

CREATE TABLE categories (
id VARCHAR(50) PRIMARY KEY,
label VARCHAR(100) NOT NULL,
icon VARCHAR(20),
category_type VARCHAR(50) NOT NULL,
budget INT NOT NULL DEFAULT 0
);

CREATE TABLE expenses (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255) NOT NULL,
category VARCHAR(50) NOT NULL,
date DATE NOT NULL,
amount INT NOT NULL,
notes TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT fk_expense_category
FOREIGN KEY (category)
REFERENCES categories(id)
ON DELETE CASCADE
);

CREATE TABLE budgets (
category VARCHAR(50) PRIMARY KEY,
amount INT NOT NULL,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE incomes (
id INT AUTO_INCREMENT PRIMARY KEY,
amount INT NOT NULL,
month VARCHAR(7) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (
name VARCHAR(50) PRIMARY KEY,
value TEXT NOT NULL
);

INSERT INTO categories (id, label, icon, category_type, budget) VALUES
('food', 'Food', '🍔', 'basic', 117),
('car', 'Car', '🚗', 'basic', 70),
('electricity', 'Electricity', '💡', 'basic', 120),
('gas', 'Gas', '🔥', 'basic', 80),
('rent', 'Rent', '🏠', 'basic', 900),
('internet', 'Internet', '🌐', 'basic', 60),
('phone', 'Phone', '📱', 'basic', 50),
('water', 'Water', '💧', 'basic', 40),
('insurance', 'Insurance', '🛡️', 'basic', 150),
('shopping', 'Shopping', '🛍️', 'basic', 89),
('health', 'Health', '🏥', 'basic', 90),
('movies', 'Movies', '🎬', 'entertainment', 120),
('games', 'Games', '🎮', 'entertainment', 200),
('music', 'Music', '🎵', 'entertainment', 80),
('travel', 'Travel', '✈️', 'entertainment', 300),
('dating', 'Dating', '💖', 'entertainment', 150),
('sports', 'Sports', '⚽', 'entertainment', 90),
('medical', 'Medical', '🏥', 'emergency', 100),
('accident', 'Accident', '🚑', 'emergency', 80),
('repair', 'Repair', '🔧', 'emergency', 60),
('family', 'Family', '👨‍👩‍👧', 'emergency', 90),
('emergency', 'Other', '⚠️', 'emergency', 50);

INSERT INTO expenses (name, category, date, amount) VALUES
('June rent', 'rent', '2026-06-01', 900),
('Weekly groceries', 'food', '2026-05-28', 85),
('Gas fill-up', 'car', '2026-05-27', 55);

INSERT INTO budgets (category, amount) VALUES
('basic', 100000),
('entertainment', 50000),
('emergency', 50000);

INSERT INTO incomes (amount, month) VALUES
(200000, '2026-06');

INSERT INTO settings (name, value) VALUES
('streak', '0');
