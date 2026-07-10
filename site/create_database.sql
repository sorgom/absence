PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS persons (
    id TEXT PRIMARY KEY,
    password_hash TEXT NOT NULL,
    is_staff INTEGER NOT NULL DEFAULT 0,
    first_login INTEGER NOT NULL DEFAULT 1,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reasons (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    deleted INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS absences (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    person_id TEXT NOT NULL,
    reason_id INTEGER NOT NULL,
    departure_time TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    return_time TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    FOREIGN KEY (reason_id) REFERENCES reasons(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS settings (
    name TEXT PRIMARY KEY,
    value TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_persons_is_staff ON persons(is_staff);
CREATE INDEX IF NOT EXISTS idx_reasons_deleted ON reasons(deleted);
CREATE INDEX IF NOT EXISTS idx_absences_person_id ON absences(person_id);
CREATE INDEX IF NOT EXISTS idx_absences_departure_time ON absences(departure_time);
CREATE INDEX IF NOT EXISTS idx_absences_return_time ON absences(return_time);
