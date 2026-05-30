import os

# Port injected by Render (defaulting to 5000 locally)
port = os.environ.get("PORT", "5000")
bind = f"0.0.0.0:{port}"
