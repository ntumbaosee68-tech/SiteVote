PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS etudiant (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    matricule TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS election (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titre TEXT NOT NULL,
    date_debut TEXT,
    date_fin TEXT,
    statut TEXT NOT NULL DEFAULT 'ouvert'
);

CREATE TABLE IF NOT EXISTS candidat (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    promotion TEXT NOT NULL,
    faculte TEXT NOT NULL,
    photo TEXT,
    id_election INTEGER NOT NULL,
    FOREIGN KEY(id_election) REFERENCES election(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS vote (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_etudiant INTEGER NOT NULL,
    id_candidat INTEGER NOT NULL,
    id_election INTEGER NOT NULL,
    FOREIGN KEY(id_etudiant) REFERENCES etudiant(id) ON DELETE CASCADE,
    FOREIGN KEY(id_candidat) REFERENCES candidat(id) ON DELETE CASCADE,
    FOREIGN KEY(id_election) REFERENCES election(id) ON DELETE CASCADE
);
