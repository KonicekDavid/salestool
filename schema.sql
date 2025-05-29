CREATE TABLE IF NOT EXISTS calculation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_name TEXT NOT NULL,
    tariff_name TEXT NOT NULL,
    price REAL NOT NULL,
    currency TEXT NOT NULL,
    status TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_update DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_calculation_created_at ON calculation(created_at);

CREATE TRIGGER IF NOT EXISTS set_last_update
AFTER UPDATE ON calculation
FOR EACH ROW
    WHEN NEW.last_update = OLD.last_update
BEGIN
    UPDATE calculation SET last_update = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;
