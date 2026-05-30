import os
import sqlite3
from pathlib import Path
from flask import g

BASE_DIR = Path(__file__).resolve().parent

# Support robust storage path fallback
def get_storage_paths():
    persistent_dir = os.environ.get("PERSISTENT_DIR")
    if persistent_dir:
        try:
            path = Path(persistent_dir).resolve()
            # test directory creation/write permissions
            path.mkdir(parents=True, exist_ok=True)
            # test creating a dummy file to ensure write permission
            test_file = path / ".write_test"
            test_file.touch()
            test_file.unlink()
            return path / "vote.db", path / "uploads"
        except Exception as e:
            print(f"Warning: Persistent directory '{persistent_dir}' not writable: {e}. Falling back to local directory.", flush=True)
    return BASE_DIR / "vote.db", BASE_DIR / "uploads"

DATABASE, UPLOAD_FOLDER = get_storage_paths()
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
