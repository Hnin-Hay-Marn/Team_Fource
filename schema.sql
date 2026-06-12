BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS categories (
    id TEXT PRIMARY KEY,
    label TEXT NOT NULL,
    icon TEXT,
    category_type TEXT NOT NULL,
    budget INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS expenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT NOT NULL,
    date TEXT NOT NULL,
    amount INTEGER NOT NULL,
    notes TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (category) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS budgets (
    category TEXT PRIMARY KEY,
    amount INTEGER NOT NULL,
    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS incomes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    amount INTEGER NOT NULL,
    month TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS settings (
    name TEXT PRIMARY KEY,
    value TEXT NOT NULL
);

DELETE FROM categories;
DELETE FROM expenses;
DELETE FROM budgets;
DELETE FROM incomes;
DELETE FROM settings;

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
    ('Gas fill-up', 'car', '2026-05-27', 55),
    ('Takeout dinner', 'food', '2026-05-25', 32),
    ('Car wash', 'car', '2026-05-20', 15),
    ('New shoes', 'shopping', '2026-05-15', 89),
    ('Dentist visit', 'health', '2026-05-10', 90),
    ('Cinema ticket', 'movies', '2026-06-01', 15),
    ('Steam game', 'games', '2026-05-29', 60),
    ('Spotify', 'music', '2026-05-28', 10),
    ('Dinner date', 'dating', '2026-05-25', 45),
    ('Hospital visit', 'medical', '2026-06-01', 70),
    ('Bike repair', 'repair', '2026-05-28', 30),
    ('Family support', 'family', '2026-05-20', 50);

INSERT INTO budgets (category, amount) VALUES
    ('basic', 100000),
    ('entertainment', 50000),
    ('emergency', 50000);

INSERT INTO incomes (amount, month) VALUES
    (200000, '2026-06');

INSERT INTO settings (name, value) VALUES
    ('streak', '0');

COMMIT;
