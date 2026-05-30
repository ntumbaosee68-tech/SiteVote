import os
import sqlite3
from pathlib import Path
from flask import g

BASE_DIR = Path(__file__).resolve().parent

# Support persistent disk on Render or use local directory
PERSISTENT_DIR = os.environ.get("PERSISTENT_DIR")
if PERSISTENT_DIR:
    persistent_path = Path(PERSISTENT_DIR).resolve()
    DATABASE = persistent_path / "vote.db"
    UPLOAD_FOLDER = persistent_path / "uploads"
else:
    DATABASE = BASE_DIR / "vote.db"
    UPLOAD_FOLDER = BASE_DIR / "uploads"

SCHEMA_FILE = BASE_DIR / "schema.sql"


def get_db():
    if "db" not in g:
        g.db = sqlite3.connect(
            DATABASE,
            detect_types=sqlite3.PARSE_DECLTYPES,
            check_same_thread=False,
        )
        g.db.row_factory = sqlite3.Row
    return g.db


def close_db(e=None):
    db = g.pop("db", None)
    if db is not None:
        db.close()


def init_db():
    if not UPLOAD_FOLDER.exists():
        UPLOAD_FOLDER.mkdir(parents=True, exist_ok=True)

    if DATABASE.exists():
        return

    with sqlite3.connect(DATABASE) as connection:
        schema = SCHEMA_FILE.read_text(encoding="utf-8")
        connection.executescript(schema)
        connection.commit()
