--
-- Users
--
CREATE TABLE IF NOT EXISTS users (
    id                  INTEGER         PRIMARY KEY AUTOINCREMENT,
    creationDate        INTEGER,
    modificationDate    INTEGER,
    enabled             INTEGER,
    email               TEXT,
    nickname            TEXT
);


--
-- Projects
--
CREATE TABLE IF NOT EXISTS projects (
    id                  INTEGER         PRIMARY KEY AUTOINCREMENT,
    creationDate        INTEGER,
    modificationDate    INTEGER,
    enabled             INTEGER,
    codeName            TEXT            UNIQUE,
    name                TEXT,
    description         TEXT
);


--
-- Milestones
--
CREATE TABLE IF NOT EXISTS milestones (
    id                  INTEGER         PRIMARY KEY AUTOINCREMENT,
    creationDate        INTEGER,
    modificationDate    INTEGER,
    name                TEXT,
    description         TEXT
);


--
-- Milestones
--
CREATE TABLE IF NOT EXISTS issues (
    id                  INTEGER         PRIMARY KEY AUTOINCREMENT,
    creationDate        INTEGER,
    modificationDate    INTEGER,
    name                TEXT,
    description         TEXT
);
