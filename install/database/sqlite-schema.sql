CREATE TABLE IF NOT EXISTS projects (
    id          INTEGER         PRIMARY KEY AUTOINCREMENT,
    codeName    TEXT            UNIQUE,
    name        TEXT,
    description TEXT
);
