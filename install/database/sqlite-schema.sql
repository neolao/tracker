CREATE TABLE IF NOT EXISTS projects (
    id                  INTEGER         PRIMARY KEY AUTOINCREMENT,
    creationDate        INTEGER,
    modificationDate    INTEGER,
    enabled             INTEGER,
    codeName            TEXT            UNIQUE,
    name                TEXT,
    description         TEXT
);
