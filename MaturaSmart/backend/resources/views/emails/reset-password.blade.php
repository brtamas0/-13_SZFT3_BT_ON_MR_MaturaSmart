<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelszó Visszaállítása - MaturaSmart</title>
    <style>
        /* Alap beállítások */
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #020617; color: #e2e8f0; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table { border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; -ms-interpolation-mode: bicubic; }

        /* Wrapper - Teljes szélesség */
        .wrapper { width: 100%; table-layout: fixed; background-color: #020617; padding-bottom: 40px; }

        /* Központi kártya */
        .main-table { background-color: #0f172a; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; font-family: sans-serif; color: #e2e8f0; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5); border: 1px solid #1e293b; }

        /* Fejléc */
        .header { 
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); 
            padding: 40px 20px; 
            text-align: center; 
            position: relative;
            overflow: hidden;
        }
        
        /* --- JAVÍTOTT IKON KONTÉNER --- */
        .icon-container {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            margin: 0 auto 15px auto; /* Középre igazítja a dobozt vízszintesen */
            
            /* A Flexbox helyett a "régi" módszert használjuk, ami stabilabb emailben: */
            text-align: center; /* Vízszintes középre igazítás */
            line-height: 80px;  /* Függőleges középre igazítás (megegyezik a magassággal) */
            font-size: 40px;
            display: block; /* Biztosítjuk, hogy blokk elem legyen */
            
            /* Design elemek maradnak: */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px); /* Safari támogatás */
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        .logo { font-size: 26px; font-weight: 800; color: white; text-decoration: none; letter-spacing: 1px; display: inline-block; margin-top: 10px;}
        .accent { color: #818cf8; }

        /* Tartalom */
        .content { padding: 40px 30px; text-align: center; }
        .title { font-size: 24px; font-weight: 700; margin: 0 0 16px 0; color: #f8fafc; letter-spacing: -0.5px; }
        .text { color: #94a3b8; line-height: 1.7; font-size: 16px; margin-bottom: 30px; }

        /* Gomb */
        .btn { 
            display: inline-block; 
            background: linear-gradient(90deg, #8b5cf6 0%, #d946ef 100%);
            color: white !important; 
            padding: 16px 40px; 
            border-radius: 50px;
            text-decoration: none; 
            font-weight: 700; 
            font-size: 16px; 
            margin: 10px 0 30px 0; 
            box-shadow: 0 10px 20px -5px rgba(217, 70, 239, 0.4);
            border: 1px solid rgba(255,255,255,0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn:hover { opacity: 0.9; }

        /* Lábléc */
        .footer { padding: 30px; text-align: center; color: #475569; font-size: 13px; border-top: 1px solid #1e293b; background-color: #0b1121; }
        .link-text { margin-top: 20px; font-size: 12px; color: #475569; word-break: break-all; line-height: 1.5; }
        .link-text a { color: #6366f1; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <br><br>
        <table class="main-table" align="center">
            <tr>
                <td class="header">
                    <div class="icon-container">🔐</div>
                    <div class="logo">
                        Matura<span class="accent">Smart</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <h1 class="title">Elfelejtetted a jelszavad?</h1>
                    <p class="text">
                        Semmi gond! 👋 </p><p class="text">
                        Kattints az alábbi gombra, és máris beállíthatsz egy új jelszót!
                    </p>

                    <a href="{{ $url }}" class="btn">Új Jelszó Beállítása</a>

                    <p class="text" style="font-size: 14px; margin-bottom: 0;">
                        Ez a link <strong>60 percig</strong> érvényes.<br>
                        Ha nem te kérted, nyugodtan töröld ezt az emailt.
                    </p>

                    <div class="link-text">
                        <p style="margin-bottom: 5px;">Probléma a gombbal? Másold be ezt a linket:</p>
                        <a href="{{ $url }}">{{ $url }}</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    &copy; {{ date('Y') }} MaturaSmart. Minden jog fenntartva.<br>
                    <span style="opacity: 0.7;">Ez egy automatikus üzenet, kérjük ne válaszolj rá.</span>
                </td>
            </tr>
        </table>
        <br><br>
    </div>
</body>
</html>