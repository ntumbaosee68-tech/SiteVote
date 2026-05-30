import os
import time
import sqlite3
from datetime import datetime
from pathlib import Path
from flask import (
    Flask,
    render_template,
    request,
    redirect,
    url_for,
    session,
    flash,
    send_from_directory,
    g,
    Response,
)
import logging
import traceback
from werkzeug.security import generate_password_hash, check_password_hash
from werkzeug.utils import secure_filename
from db import get_db, close_db, init_db, UPLOAD_FOLDER

BASE_DIR = Path(__file__).resolve().parent
UPLOAD_FOLDER.mkdir(parents=True, exist_ok=True)

ALLOWED_EXTENSIONS = {"png", "jpg", "jpeg", "gif"}
ADMIN_CODE = os.environ.get("ADMIN_CODE", "ADMIN123")

app = Flask(__name__, static_folder="assets", static_url_path="/assets")
app.secret_key = os.environ.get("FLASK_SECRET_KEY", "change-me-for-production")
app.config["UPLOAD_FOLDER"] = UPLOAD_FOLDER

# Initialisation automatique de la base de données (Gunicorn et local)
init_db()

# Setup error logging
LOG_DIR = BASE_DIR / "logs"
LOG_DIR.mkdir(parents=True, exist_ok=True)
logging.basicConfig(
    filename=str(LOG_DIR / "error.log"),
    level=logging.ERROR,
    format="%(asctime)s %(levelname)s: %(message)s",
)


def query_db(query, args=(), one=False):
    cur = get_db().execute(query, args)
    rv = cur.fetchall()
    cur.close()
    return (rv[0] if rv else None) if one else rv


def execute_db(query, args=()):
    db = get_db()
    cur = db.execute(query, args)
    db.commit()
    return cur.lastrowid


def allowed_file(filename):
    return "." in filename and filename.rsplit(".", 1)[1].lower() in ALLOWED_EXTENSIONS


def login_required(f):
    def wrapper(*args, **kwargs):
        if not session.get("etudiant_id"):
            return redirect(url_for("login"))
        return f(*args, **kwargs)
    wrapper.__name__ = f.__name__
    return wrapper


def admin_required(f):
    def wrapper(*args, **kwargs):
        if not session.get("admin"):
            return redirect(url_for("login"))
        return f(*args, **kwargs)
    wrapper.__name__ = f.__name__
    return wrapper


@app.before_request
def before_request():
    get_db()


@app.teardown_appcontext
def teardown_db(exception):
    close_db(exception)


@app.route("/")
def home():
    return render_template("home.html", title="Plateforme de Vote Étudiant")


@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        matricule = request.form.get("matricule", "").strip()
        password = request.form.get("password", "").strip()
        admin_code = request.form.get("admin_code", "").strip()

        if not matricule or not password:
            flash("Veuillez remplir tous les champs", "error")
            return redirect(url_for("login"))

        user = query_db("SELECT * FROM etudiant WHERE matricule = ?", (matricule,), one=True)
        if not user or not check_password_hash(user["password"], password):
            flash("Matricule ou mot de passe incorrect", "error")
            return redirect(url_for("login"))

        session["etudiant_id"] = user["id"]
        session["nom"] = user["nom"]

        if admin_code and admin_code == ADMIN_CODE:
            session["admin"] = True
            return redirect(url_for("admin_dashboard"))

        return redirect(url_for("student_dashboard"))

    return render_template("login.html", title="Connexion Étudiant")


@app.route("/register", methods=["GET", "POST"])
def register():
    if request.method == "POST":
        nom = request.form.get("nom", "").strip()
        matricule = request.form.get("matricule", "").strip()
        password = request.form.get("password", "").strip()

        if not nom or not matricule or not password:
            flash("Veuillez remplir tous les champs", "error")
            return redirect(url_for("register"))

        existing = query_db("SELECT * FROM etudiant WHERE matricule = ?", (matricule,), one=True)
        if existing:
            flash("Ce matricule est déjà utilisé", "error")
            return redirect(url_for("register"))

        hashed = generate_password_hash(password)
        execute_db(
            "INSERT INTO etudiant (nom, matricule, password) VALUES (?, ?, ?)",
            (nom, matricule, hashed),
        )
        flash("Inscription réussie, vous pouvez vous connecter", "success")
        return redirect(url_for("login"))

    return render_template("register.html", title="Inscription Étudiant")


@app.route("/logout")
def logout():
    session.clear()
    return redirect(url_for("login"))


@app.route("/student/dashboard")
@login_required
def student_dashboard():
    etudiant_id = session.get("etudiant_id")
    elections = query_db(
        "SELECT * FROM election WHERE statut = 'ouvert' ORDER BY id DESC"
    )
    open_elections = []
    for election in elections:
        voted = query_db(
            "SELECT 1 FROM vote WHERE id_etudiant = ? AND id_election = ?",
            (etudiant_id, election["id"]),
            one=True,
        )
        open_elections.append({
            "id": election["id"],
            "titre": election["titre"],
            "voted": bool(voted),
        })

    return render_template(
        "student_dashboard.html",
        title="Dashboard Étudiant",
        elections=open_elections,
        nom=session.get("nom"),
    )


@app.route("/student/vote/<int:election_id>", methods=["GET", "POST"])
@login_required
def student_vote(election_id):
    etudiant_id = session.get("etudiant_id")
    election = query_db(
        "SELECT * FROM election WHERE id = ? AND statut = 'ouvert'",
        (election_id,),
        one=True,
    )
    if not election:
        flash("Élection introuvable ou fermée", "error")
        return redirect(url_for("student_dashboard"))

    if request.method == "POST":
        candidat_id = request.form.get("candidat")
        if not candidat_id:
            flash("Veuillez sélectionner un candidat", "error")
            return redirect(url_for("student_vote", election_id=election_id))

        vote_exists = query_db(
            "SELECT 1 FROM vote WHERE id_etudiant = ? AND id_election = ?",
            (etudiant_id, election_id),
            one=True,
        )
        if vote_exists:
            flash("Vous avez déjà voté pour cette élection", "error")
            return redirect(url_for("student_dashboard"))

        execute_db(
            "INSERT INTO vote (id_etudiant, id_candidat, id_election) VALUES (?, ?, ?)",
            (etudiant_id, candidat_id, election_id),
        )
        flash("Vote enregistré avec succès", "success")
        return redirect(url_for("student_dashboard"))

    candidats = query_db(
        "SELECT * FROM candidat WHERE id_election = ?", (election_id,)
    )
    return render_template(
        "vote.html",
        title="Vote",
        election=election,
        candidats=candidats,
    )


@app.route("/results")
def results():
    election = query_db(
        "SELECT * FROM election WHERE statut = 'ouvert' ORDER BY id DESC LIMIT 1", (), one=True
    )
    if not election:
        return render_template("results.html", title="Résultats officiels", election=None, data=[], labels=[], votes=[])

    rows = query_db(
        """
        SELECT c.*, COUNT(v.id) AS total
        FROM candidat c
        LEFT JOIN vote v ON c.id = v.id_candidat
        WHERE c.id_election = ?
        GROUP BY c.id
        ORDER BY total DESC
        """,
        (election["id"],),
    )
    labels = [row["nom"] for row in rows]
    votes = [row["total"] for row in rows]
    return render_template(
        "results.html",
        title="Résultats officiels",
        election=election,
        data=rows,
        labels=labels,
        votes=votes,
    )


@app.route("/historique_results")
def historique_results():
    elections = query_db(
        "SELECT * FROM election WHERE statut = 'ferme' ORDER BY id DESC"
    )
    results = []
    for election in elections:
        rows = query_db(
            """
            SELECT c.*, COUNT(v.id) AS total
            FROM candidat c
            LEFT JOIN vote v ON c.id = v.id_candidat
            WHERE c.id_election = ?
            GROUP BY c.id
            ORDER BY total DESC
            """,
            (election["id"],),
        )
        results.append({
            "election": election,
            "candidats": rows,
        })
    return render_template(
        "historique_results.html",
        title="Historique des résultats",
        results=results,
    )


@app.route("/admin/dashboard")
@admin_required
def admin_dashboard():
    total_etudiants = query_db("SELECT COUNT(*) AS total FROM etudiant", (), one=True)["total"]
    total_votes = query_db("SELECT COUNT(*) AS total FROM vote", (), one=True)["total"]
    total_candidats = query_db("SELECT COUNT(*) AS total FROM candidat", (), one=True)["total"]
    total_elections = query_db("SELECT COUNT(*) AS total FROM election", (), one=True)["total"]
    taux = 0
    if total_etudiants:
        taux = round(total_votes * 100 / total_etudiants, 2)

    return render_template(
        "admin_dashboard.html",
        title="Dashboard Admin",
        stats={
            "etudiants": total_etudiants,
            "votes": total_votes,
            "candidats": total_candidats,
            "elections": total_elections,
            "taux": taux,
        },
    )


@app.route("/admin/create_election", methods=["GET", "POST"])
@admin_required
def create_election():
    if request.method == "POST":
        titre = request.form.get("titre", "").strip()
        debut = request.form.get("debut", "").strip()
        fin = request.form.get("fin", "").strip()
        if not titre:
            flash("Le titre est requis", "error")
            return redirect(url_for("create_election"))
        execute_db(
            "INSERT INTO election (titre, date_debut, date_fin, statut) VALUES (?, ?, ?, 'ouvert')",
            (titre, debut, fin),
        )
        flash("Élection créée", "success")
        return redirect(url_for("admin_dashboard"))
    return render_template("create_election.html", title="Créer une élection")


@app.route("/admin/close_election", methods=["GET", "POST"])
@admin_required
def close_election():
    elections = query_db("SELECT * FROM election WHERE statut = 'ouvert'")
    if request.method == "POST":
        election_id = request.form.get("election")
        if election_id:
            execute_db(
                "UPDATE election SET statut = 'ferme' WHERE id = ?",
                (election_id,),
            )
            flash("Élection clôturée avec succès", "success")
            return redirect(url_for("admin_dashboard"))
        flash("Veuillez sélectionner une élection", "error")
    return render_template(
        "close_election.html",
        title="Clôturer une élection",
        elections=elections,
    )


@app.route("/admin/add_candidate", methods=["GET", "POST"])
@admin_required
def add_candidate():
    elections = query_db("SELECT * FROM election ORDER BY id DESC")
    if request.method == "POST":
        nom = request.form.get("nom", "").strip()
        promotion = request.form.get("promotion", "").strip()
        faculte = request.form.get("faculte", "").strip()
        election_id = request.form.get("election")
        photo = request.files.get("photo")

        if not nom or not promotion or not faculte or not election_id or not photo:
            flash("Tous les champs sont requis", "error")
            return redirect(url_for("add_candidate"))

        if not allowed_file(photo.filename):
            flash("Type de fichier non autorisé", "error")
            return redirect(url_for("add_candidate"))

        filename = secure_filename(photo.filename)
        filename = f"{int(time.time())}_{filename}"
        destination = app.config["UPLOAD_FOLDER"] / filename
        photo.save(destination)

        execute_db(
            "INSERT INTO candidat (nom, promotion, faculte, photo, id_election) VALUES (?, ?, ?, ?, ?)",
            (nom, promotion, faculte, filename, election_id),
        )
        flash("Candidat ajouté avec succès", "success")
        return redirect(url_for("admin_dashboard"))

    return render_template(
        "add_candidate.html",
        title="Ajouter un candidat",
        elections=elections,
    )


@app.route("/admin/proces_verbal")
@admin_required
def proces_verbal():
    election = query_db(
        "SELECT * FROM election WHERE statut = 'ouvert' ORDER BY id DESC LIMIT 1", (), one=True
    )
    if not election:
        flash("Aucun procès-verbal actif disponible", "error")
        return redirect(url_for("admin_dashboard"))

    candidats = query_db(
        """
        SELECT c.*, COUNT(v.id) AS total
        FROM candidat c
        LEFT JOIN vote v ON c.id = v.id_candidat
        WHERE c.id_election = ?
        GROUP BY c.id
        ORDER BY total DESC
        """,
        (election["id"],),
    )
    total_votes = sum(row["total"] for row in candidats)
    return render_template(
        "proces_verbal.html",
        title="Procès-Verbal",
        election=election,
        candidats=candidats,
        total_votes=total_votes,
        now=datetime.now(),
    )


@app.route("/uploads/<path:filename>")
def uploaded_file(filename):
    return send_from_directory(app.config["UPLOAD_FOLDER"], filename)


@app.errorhandler(Exception)
def handle_exception(e):
    # write full traceback to logs/error.txt for inspection
    tb = traceback.format_exc()
    log_path = BASE_DIR / "logs" / "error.txt"
    log_path.parent.mkdir(parents=True, exist_ok=True)
    log_path.write_text(tb, encoding="utf-8")
    logging.error(tb)
    message = str(e)
    if not message:
        message = "Une erreur inconnue est survenue."
    return (
        render_template(
            "error.html",
            error_message=message,
            error_text=tb,
        ),
        500,
    )


@app.route("/error-log")
def error_log():
    p = BASE_DIR / "logs" / "error.txt"
    if p.exists():
        return Response(p.read_text(encoding="utf-8"), mimetype="text/plain")
    return Response("No error log found.", mimetype="text/plain", status=404)


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=int(os.environ.get("PORT", 5000)), debug=False)
