# Plateforme de vote étudiant

Application Python Flask dérivée du projet PHP original.

## Installation locale

1. Créez un environnement virtuel :
   - Windows : `python -m venv venv`
   - macOS / Linux : `python3 -m venv venv`
2. Activez-le :
   - Windows : `venv\Scripts\activate`
   - macOS / Linux : `source venv/bin/activate`
3. Installez les dépendances :
   - `pip install -r requirements.txt`
4. Lancez l'application :
   - `python app.py`
5. Ouvrez `http://127.0.0.1:5000`.

## Déploiement sur Render

Le fichier `.render.yaml` configure le service Python.

- Build command : `pip install -r requirements.txt`
- Start command : `gunicorn app:app`

Vous pouvez créer un service Web dans Render et connecter votre dépôt GitHub.

## GitHub Actions

Le workflow `.github/workflows/python-app.yml` installe les dépendances et vérifie que `app.py` se compile.

## Notes

- Le mot de passe administrateur par défaut est `ADMIN123`.
- Le fichier de base de données SQLite est `vote.db`.
- Les photos des candidats sont stockées dans le dossier `uploads/`.
