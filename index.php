<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Plateforme de Vote Estudiantine
    </title>

    <base href="http://localhost/vote-platform/">

    <link rel="stylesheet" href="assets/css/style.css">

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- ICONES -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{

            background:
            linear-gradient(
                rgba(10,20,40,0.75),
                rgba(10,20,40,0.85)
            ),

            url('assets/images/bg.jpg');

            background-size:cover;
            background-position:center;
            background-attachment:fixed;

            min-height:100vh;

            overflow-x:hidden;

            color:white;
        }

        /* OVERLAY */
        .overlay{

            width:100%;
            min-height:100vh;

            backdrop-filter:blur(3px);
        }

        /*NAVBAR */
        .navbar{

            width:100%;

            padding:20px 8%;

            display:flex;

            justify-content:space-between;

            align-items:center;

            background:rgba(255,255,255,0.05);

            backdrop-filter:blur(10px);

            border-bottom:1px solid rgba(255,255,255,0.1);
        }

        .nav-left{

            display:flex;

            align-items:center;

            gap:15px;
        }

        .logo{

            width:70px;
            height:70px;

            border-radius:50%;

            background:white;

            padding:5px;

            box-shadow:0 5px 20px rgba(0,0,0,0.3);

            animation:float 3s ease-in-out infinite;
        }

        @keyframes float{

            0%{
                transform:translateY(0px);
            }

            50%{
                transform:translateY(-8px);
            }

            100%{
                transform:translateY(0px);
            }
        }

        .univ-name{

            font-size:24px;

            font-weight:700;
        }

        .nav-btn{

            background:white;

            color:#1e3c72;

            padding:12px 25px;

            border-radius:10px;

            text-decoration:none;

            font-weight:600;

            transition:0.3s;
        }

        .nav-btn:hover{

            transform:translateY(-3px);

            background:#dfe6f1;
        }

        /*  HERO */
        .hero{

            width:90%;

            max-width:1300px;

            margin:auto;

            min-height:85vh;

            display:flex;

            justify-content:space-between;

            align-items:center;

            gap:50px;
        }

        /* LEFT */
        .hero-left{

            flex:1;
        }

        .badge{

            display:inline-block;

            background:rgba(255,255,255,0.1);

            border:1px solid rgba(255,255,255,0.2);

            padding:10px 18px;

            border-radius:50px;

            margin-bottom:25px;

            font-size:14px;

            backdrop-filter:blur(5px);
        }

        .hero-title{

            font-size:58px;

            font-weight:800;

            line-height:1.2;

            margin-bottom:25px;
        }

        .hero-title span{

            color:#4da3ff;
        }

        .hero-text{

            font-size:18px;

            line-height:1.9;

            color:#dfe6f1;

            margin-bottom:35px;

            max-width:700px;
        }

        /* BUTTONS */
        .hero-buttons{

            display:flex;

            gap:20px;

            flex-wrap:wrap;
        }

        .btn-primary{

            background:#4da3ff;

            color:white;

            padding:15px 35px;

            border-radius:12px;

            text-decoration:none;

            font-weight:600;

            transition:0.3s;

            box-shadow:0 8px 20px rgba(0,0,0,0.3);
        }

        .btn-primary:hover{

            transform:translateY(-5px);
        }

        .btn-secondary{

            border:2px solid rgba(255,255,255,0.2);

            color:white;

            padding:15px 35px;

            border-radius:12px;

            text-decoration:none;

            font-weight:600;

            backdrop-filter:blur(5px);

            transition:0.3s;
        }

        .btn-secondary:hover{

            background:rgba(255,255,255,0.1);
        }

        /* RIGHT CARD */
        .hero-right{

            flex:1;

            display:flex;

            justify-content:center;
        }

        .glass-card{

            width:420px;

            background:rgba(255,255,255,0.08);

            border:1px solid rgba(255,255,255,0.15);

            backdrop-filter:blur(15px);

            border-radius:25px;

            padding:35px;

            box-shadow:0 15px 40px rgba(0,0,0,0.3);
        }

        .glass-card h2{

            text-align:center;

            margin-bottom:25px;

            font-size:28px;
        }

        .feature{

            display:flex;

            align-items:flex-start;

            gap:15px;

            margin-bottom:25px;
        }

        .feature i{

            font-size:24px;

            color:#4da3ff;

            margin-top:5px;
        }

        .feature h3{

            margin-bottom:5px;
        }

        .feature p{

            color:#dfe6f1;

            font-size:15px;

            line-height:1.7;
        }

        /* FOOTER */
        .footer{

            text-align:center;

            padding:25px;

            color:#dfe6f1;

            background:rgba(255,255,255,0.05);

            backdrop-filter:blur(5px);

            border-top:1px solid rgba(255,255,255,0.1);
        }

        .footer strong{

            color:#4da3ff;
        }

        /* RESPONSIVE */
        @media(max-width:1000px){

            .hero{

                flex-direction:column;

                text-align:center;

                padding:50px 0;
            }

            .hero-buttons{

                justify-content:center;
            }

            .hero-title{

                font-size:42px;
            }

            .glass-card{

                width:100%;
            }
        }

        @media(max-width:600px){

            .navbar{

                flex-direction:column;

                gap:15px;
            }

            .hero-title{

                font-size:32px;
            }

            .hero-text{

                font-size:16px;
            }

            .univ-name{

                font-size:18px;
            }
        }

    </style>

</head>

<body>

<div class="overlay">

    <!-- NAVBAR -->
    <div class="navbar">

        <div class="nav-left">

            <img
                src="assets/images/logo.png"
                class="logo"
            >

            <div class="univ-name">
                Université Protestante de Lubumbashi
            </div>

        </div>

        <a href="login.php" class="nav-btn">
            Se connecter
        </a>

    </div>

    <!--  HERO -->
    <section class="hero">

        <!-- LEFT -->
        <div class="hero-left">

            <div class="badge">
                🔐 Plateforme numérique sécurisée
            </div>

            <h1 class="hero-title">

                Votez en toute
                <span>transparence</span>
                et sécurité

            </h1>

            <p class="hero-text">

                La plateforme officielle de vote estudiantin
                de l'Université Protestante de Lubumbashi
                permet aux étudiants de participer
                aux élections universitaires
                de manière moderne, rapide,
                fiable et totalement sécurisée.

            </p>

            <div class="hero-buttons">

                <a href="login.php" class="btn-primary">
                    Accéder à la plateforme
                </a>

                <a href="register.php" class="btn-secondary">
                    Créer un compte
                </a>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="hero-right">

            <div class="glass-card">

                <h2>
                    Pourquoi utiliser cette plateforme ?
                </h2>

                <div class="feature">

                    <i class="fas fa-shield-alt"></i>

                    <div>

                        <h3>Sécurité maximale</h3>

                        <p>
                            Chaque vote est sécurisé
                            grâce à un système fiable
                            empêchant toute fraude.
                        </p>

                    </div>

                </div>

                <div class="feature">

                    <i class="fas fa-bolt"></i>

                    <div>

                        <h3>Résultats instantanés</h3>

                        <p>
                            Les résultats sont calculés
                            automatiquement après clôture
                            des élections.
                        </p>

                    </div>

                </div>

                <div class="feature">

                    <i class="fas fa-chart-pie"></i>

                    <div>

                        <h3>Transparence totale</h3>

                        <p>
                            Procès-verbaux, statistiques
                            et historiques accessibles
                            de manière transparente.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- FOOTER -->
    <div class="footer">

        Plateforme conçue et développée par
        <strong>Osée Ntumba kashindi nathanael</strong>

    </div>

</div>

</body>

</html>