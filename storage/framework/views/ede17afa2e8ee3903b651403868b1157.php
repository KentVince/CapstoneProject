<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CofSys – Coffee Farm Management System with Smart Disease Detection and GeoAnalytics</title>
    <meta name="description" content="CofSys: Coffee Farm Management System with Smart Disease Detection and GeoAnalytics — serving the coffee farming communities of Davao de Oro, Philippines.">
    <link rel="icon" href="/images/favicon.png" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --coffee-dark:   #003432;
            --coffee:        #003D34;
            --coffee-mid:    #14532d;
            --coffee-light:  #107737;
            --caramel:       #107737;
            --gold:          #3EC87A;
            --cream:         #F3FBF6;
            --cream-dark:    #E0F0E8;
            --forest-dark:   #001E1D;
            --forest:        #003432;
            --forest-mid:    #107737;
            --forest-light:  #2DAA65;
            --white:         #FFFFFF;
            --text-dark:     #001818;
            --text-mid:      #14532d;
            --text-light:    #4A7A5E;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--cream);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        h1, h2, h3 { font-family: 'Playfair Display', serif; }

        /* ── NAVBAR ─────────────────────────────── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.4s ease;
        }

        .navbar.scrolled {
            background: rgba(0, 52, 50, 0.97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 0.75rem 2rem;
            box-shadow: 0 4px 30px rgba(0,0,0,0.3);
        }

        .navbar-logo img {
            height: 48px;
            transition: height 0.3s ease;
        }

        .navbar.scrolled .navbar-logo img { height: 40px; }

        .navbar-nav {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .navbar-nav a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            transition: color 0.2s;
        }

        .navbar-nav a:hover { color: var(--gold); }

        .btn-nav {
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            color: var(--coffee-dark) !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.4rem;
            border-radius: 50px;
            transition: transform 0.2s, box-shadow 0.2s !important;
        }

        .btn-nav:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 119, 55, 0.45);
        }

        /* ── HERO ───────────────────────────────── */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: var(--coffee-dark);
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(
                    to bottom right,
                    rgba(0,52,50,0.93) 0%,
                    rgba(20,83,45,0.82) 50%,
                    rgba(0,30,29,0.88) 100%
                );
            z-index: 2;
        }

        .hero-bg-img {
            position: absolute;
            inset: 0;
            background-image: url('/images/coffee farm area.jpg');
            background-size: cover;
            background-position: center;
            z-index: 1;
            animation: slowZoom 20s ease-in-out infinite alternate;
        }

        @keyframes slowZoom {
            0%   { transform: scale(1); }
            100% { transform: scale(1.08); }
        }

        /* Coffee bean decorative circles */
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 2;
            pointer-events: none;
        }

        .hero-orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(16,119,55,0.25), transparent 70%);
            top: -150px; right: -100px;
        }

        .hero-orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(0,52,50,0.35), transparent 70%);
            bottom: -100px; left: -80px;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            max-width: 1200px;
            margin: 0 auto;
            padding: 8rem 2rem 4rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(62,200,122,0.15);
            border: 1px solid rgba(62,200,122,0.35);
            color: var(--gold);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            margin-bottom: 1.5rem;
        }

        .hero-badge-dot {
            width: 6px; height: 6px;
            background: var(--gold);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .hero-title {
            font-size: clamp(2.8rem, 5vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--white);
            margin-bottom: 1.5rem;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-desc {
            font-size: 1.1rem;
            line-height: 1.75;
            color: rgba(255,255,255,0.72);
            margin-bottom: 2.5rem;
            max-width: 520px;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            color: var(--coffee-dark);
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.85rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(16,119,55,0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(16,119,55,0.55);
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.3);
            color: var(--white);
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.85rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(8px);
        }

        .btn-outline:hover {
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.5);
        }

        .hero-stats {
            display: flex;
            gap: 2.5rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.12);
        }

        .hero-stat-val {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--gold);
        }

        .hero-stat-lbl {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 0.2rem;
        }

        /* Hero right – floating card */
        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            animation: floatY 6s ease-in-out infinite;
        }

        @keyframes floatY {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        .hero-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .hero-card-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .hero-card-title {
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--white);
        }

        .hero-card-sub {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.5);
        }

        .hero-mini-list { list-style: none; }

        .hero-mini-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            font-size: 0.82rem;
            color: rgba(255,255,255,0.75);
        }

        .hero-mini-list li:last-child { border-bottom: none; }

        .mini-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .mini-dot.green  { background: #5FC877; }
        .mini-dot.gold   { background: var(--gold); }
        .mini-dot.orange { background: #E07830; }
        .mini-dot.blue   { background: #5B9BD5; }

        .hero-mini-badge {
            margin-left: auto;
            font-size: 0.68rem;
            font-weight: 600;
            padding: 0.2rem 0.55rem;
            border-radius: 50px;
        }

        .badge-success { background: rgba(95,200,119,0.2); color: #5FC877; }
        .badge-warn    { background: rgba(224,120,48,0.2); color: #E07830; }
        .badge-info    { background: rgba(91,155,213,0.2); color: #5B9BD5; }

        /* Floating side cards */
        .hero-float-card {
            position: absolute;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 0.9rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .hero-float-card-1 {
            top: 10%; right: -30px;
            animation: floatY 5s ease-in-out infinite;
            animation-delay: 1s;
        }

        .hero-float-card-2 {
            bottom: 15%; left: -40px;
            animation: floatY 7s ease-in-out infinite;
            animation-delay: 2.5s;
        }

        .float-icon {
            width: 36px; height: 36px;
            background: rgba(16,119,55,0.18);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .float-text-main {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--white);
        }

        .float-text-sub {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.5);
        }

        /* ── MARQUEE BAND ───────────────────────── */
        .marquee-band {
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            padding: 0.85rem 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-track {
            display: inline-flex;
            animation: marquee 24s linear infinite;
        }

        .marquee-track span {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--coffee-dark);
            padding: 0 2rem;
        }

        .marquee-track span::before {
            content: "✦";
            margin-right: 2rem;
            opacity: 0.6;
        }

        @keyframes marquee {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* ── SECTION SHARED ─────────────────────── */
        section { padding: 6rem 2rem; }

        .section-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--coffee-mid);
            margin-bottom: 1rem;
        }

        .section-label::before {
            content: "";
            display: block;
            width: 24px; height: 2px;
            background: var(--caramel);
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            line-height: 1.2;
            color: var(--coffee-dark);
            margin-bottom: 1rem;
        }

        .section-sub {
            font-size: 1.05rem;
            color: var(--text-light);
            line-height: 1.7;
            max-width: 560px;
        }

        /* ── FEATURES ───────────────────────────── */
        .features { background: var(--cream); }

        .features-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .features-header .section-label { justify-content: center; }
        .features-header .section-sub { margin: 0 auto; }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        @media (max-width: 1024px) {
            .features-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 640px) {
            .features-grid { grid-template-columns: 1fr; }
        }

        .feature-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(16,119,55,0.15);
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--cream) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.35s;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(0,52,50,0.12);
            border-color: rgba(16,119,55,0.25);
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-icon-wrap {
            width: 60px; height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            position: relative;
            z-index: 1;
        }

        .feature-icon-wrap img {
            width: 32px; height: 32px;
            object-fit: contain;
        }

        .feature-icon-wrap.brown  { background: rgba(16,119,55,0.12); }
        .feature-icon-wrap.green  { background: rgba(16,119,55,0.15); }
        .feature-icon-wrap.gold   { background: rgba(16,119,55,0.12); }
        .feature-icon-wrap.forest { background: rgba(0,52,50,0.1); }

        .feature-tag {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--caramel);
            margin-bottom: 0.5rem;
            position: relative; z-index: 1;
        }

        .feature-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 0.75rem;
            position: relative; z-index: 1;
        }

        .feature-desc {
            font-size: 0.9rem;
            color: var(--text-light);
            line-height: 1.65;
            position: relative; z-index: 1;
        }

        .feature-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 1.25rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--coffee-mid);
            text-decoration: none;
            transition: gap 0.2s, color 0.2s;
            position: relative; z-index: 1;
        }

        .feature-link:hover { gap: 0.65rem; color: var(--caramel); }


        /* ── MOBILE APP BANNER ──────────────────── */
        .mobile-app-banner {
            background: linear-gradient(135deg, var(--forest-dark) 0%, var(--coffee-dark) 50%, var(--coffee-mid) 100%);
            padding: 5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .mobile-app-banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 0% 50%, rgba(16,119,55,0.2) 0%, transparent 55%),
                radial-gradient(ellipse at 100% 50%, rgba(62,200,122,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        /* subtle dot pattern */
        .mobile-app-banner::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        .mab-inner {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .mab-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(62,200,122,0.15);
            border: 1px solid rgba(62,200,122,0.3);
            color: var(--gold);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            margin-bottom: 1.5rem;
        }

        .mab-title {
            font-size: clamp(2.2rem, 4vw, 3.4rem);
            font-weight: 800;
            color: var(--white);
            line-height: 1.1;
            margin-bottom: 1.25rem;
        }

        .mab-highlight {
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .mab-desc {
            font-size: 1rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.75;
            margin-bottom: 2rem;
            max-width: 480px;
        }

        .mab-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 2.5rem;
        }

        .mab-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
        }

        .mab-feat-icon {
            width: 30px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* Store buttons */
        .mab-store-btns {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .store-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            min-width: 170px;
        }

        .store-btn--android {
            background: var(--white);
            color: var(--coffee-dark);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .store-btn--android:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 35px rgba(0,0,0,0.28);
        }

        .store-btn--ios {
            background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.25);
            color: var(--white);
            backdrop-filter: blur(8px);
        }

        .store-btn--ios:hover {
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.4);
            transform: translateY(-3px);
        }

        .store-icon {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
        }

        .store-text {
            display: flex;
            flex-direction: column;
        }

        .store-sub {
            font-size: 0.65rem;
            opacity: 0.7;
            letter-spacing: 0.04em;
        }

        .store-name {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        /* Phone mockup */
        .mab-mockup {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .phone-wrap {
            position: relative;
            width: 100%;
            max-width: 260px;
        }

        .phone-glow {
            position: absolute;
            inset: -50px;
            background: radial-gradient(ellipse, rgba(16,119,55,0.35) 0%, transparent 65%);
            filter: blur(30px);
            z-index: 0;
            border-radius: 50%;
        }

        .app-screenshot {
            position: relative;
            z-index: 1;
            width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow:
                0 30px 80px rgba(0,0,0,0.45),
                0 0 0 1px rgba(255,255,255,0.08);
            animation: floatY 6s ease-in-out infinite;
            display: block;
        }

        /* Floating pill badges around image */
        .phone-pill {
            position: absolute;
            background: var(--white);
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.1rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--coffee-dark);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
            z-index: 2;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        .phone-pill-1 {
            top: 12%;
            right: -30px;
            animation: floatY 5s ease-in-out infinite;
        }

        .phone-pill-2 {
            bottom: 15%;
            left: -30px;
            animation: floatY 7s ease-in-out infinite;
            animation-delay: 1.5s;
        }

        @media (max-width: 1024px) {
            .mab-inner { grid-template-columns: 1fr; }
            .mab-mockup { margin-top: 3rem; }
            .phone-pill { display: none; }
            .phone-wrap { max-width: 360px; margin: 0 auto; }
        }

        /* ── HOW IT WORKS ───────────────────────── */
        .how {
            background: var(--coffee-dark);
            position: relative;
            overflow: hidden;
        }

        .how::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 80% 20%, rgba(16,119,55,0.12) 0%, transparent 60%),
                radial-gradient(ellipse at 10% 80%, rgba(0,52,50,0.2) 0%, transparent 50%);
        }

        .how-inner {
            position: relative;
            z-index: 1;
        }

        .how-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
            margin-top: 4rem;
        }

        .how-steps { display: flex; flex-direction: column; gap: 2rem; }

        .how-step {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
            opacity: 0.65;
            transition: opacity 0.3s;
        }

        .how-step:hover { opacity: 1; }

        .step-num {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: rgba(16,119,55,0.14);
            border: 1.5px solid rgba(16,119,55,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--gold);
            flex-shrink: 0;
        }

        .step-body-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 0.4rem;
        }

        .step-body-desc {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.55);
            line-height: 1.65;
        }

        /* Dashboard mockup */
        .how-mockup {
            position: relative;
        }

        .mockup-frame {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            overflow: hidden;
            padding: 1.5rem;
        }

        .mockup-bar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-dot.r { background: #FF5F57; }
        .mockup-dot.y { background: #FEBC2E; }
        .mockup-dot.g { background: #28C840; }

        .mockup-title-bar {
            flex: 1;
            background: rgba(255,255,255,0.08);
            border-radius: 6px;
            height: 20px;
            margin-left: 0.5rem;
        }

        .mockup-stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .mockup-stat {
            background: rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 0.9rem;
            text-align: center;
        }

        .ms-val {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gold);
        }

        .ms-lbl {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 0.2rem;
        }

        .mockup-chart-bar {
            display: flex;
            align-items: flex-end;
            gap: 0.4rem;
            height: 80px;
            margin: 1rem 0;
        }

        .chart-bar-col {
            flex: 1;
            background: rgba(16,119,55,0.18);
            border-radius: 4px 4px 0 0;
            position: relative;
        }

        .chart-bar-fill {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, var(--caramel), var(--gold));
            border-radius: 4px 4px 0 0;
            transition: height 0.3s;
        }

        .mockup-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem;
            background: rgba(255,255,255,0.04);
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }

        .mockup-row-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .mockup-row-line {
            flex: 1;
            height: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .mockup-row-badge {
            font-size: 0.62rem;
            padding: 0.18rem 0.5rem;
            border-radius: 50px;
            font-weight: 600;
        }

        /* ── COVERAGE SECTION ───────────────────── */
        .coverage { background: var(--cream-dark); }

        .coverage-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
        }

        .map-placeholder {
            position: relative;
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            aspect-ratio: 1 / 1.1;
            box-shadow: 0 20px 60px rgba(0,52,50,0.12);
        }

        .map-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .map-overlay-badge {
            position: absolute;
            bottom: 1.5rem; left: 1.5rem;
            background: rgba(0,52,50,0.9);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            color: var(--white);
        }

        .mob-val {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--gold);
        }

        .mob-lbl {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .coverage-points {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        .coverage-point {
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
        }

        .cp-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: 0 6px 18px rgba(16,119,55,0.25);
        }

        .cp-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 0.35rem;
        }

        .cp-desc {
            font-size: 0.88rem;
            color: var(--text-light);
            line-height: 1.6;
        }

        /* ── TESTIMONIALS / MISSION ─────────────── */
        .mission { background: var(--white); }

        .mission-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .mission-quote {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 700;
            color: var(--coffee-dark);
            line-height: 1.35;
            margin-bottom: 2rem;
        }

        .mission-quote .em { color: var(--caramel); }

        .mission-desc {
            font-size: 0.95rem;
            color: var(--text-light);
            line-height: 1.75;
            margin-bottom: 2rem;
        }

        .mission-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .mission-tag {
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            background: var(--cream);
            color: var(--coffee-mid);
            border: 1px solid var(--cream-dark);
        }

        .mission-image {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            aspect-ratio: 4/5;
            box-shadow: 0 24px 60px rgba(0,52,50,0.15);
        }

        .mission-image img {
            width: 100%; height: 100%;
            object-fit: cover;
        }

        .mission-img-badge {
            position: absolute;
            top: 1.5rem; right: 1.5rem;
            background: rgba(0,52,50,0.9);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: 0.9rem 1.1rem;
            text-align: center;
            color: var(--white);
        }

        /* ── CTA ────────────────────────────────── */
        .cta-section {
            background: linear-gradient(135deg, var(--forest-dark) 0%, var(--coffee-dark) 60%, var(--coffee) 100%);
            position: relative;
            overflow: hidden;
            padding: 5rem 2rem;
        }

        .cta-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(16,119,55,0.16) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(0,52,50,0.2) 0%, transparent 50%);
        }

        .cta-inner {
            position: relative;
            z-index: 1;
            max-width: 760px;
            margin: 0 auto;
            text-align: center;
        }

        .cta-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            color: var(--white);
            margin-bottom: 1rem;
        }

        .cta-desc {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        .cta-btns {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ── FOOTER ─────────────────────────────── */
        footer {
            background: var(--coffee-dark);
            color: rgba(255,255,255,0.7);
            padding: 4rem 2rem 2rem;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .footer-brand img {
            height: 44px;
            margin-bottom: 1.25rem;
        }

        .footer-brand p {
            font-size: 0.88rem;
            line-height: 1.65;
            color: rgba(255,255,255,0.5);
        }

        .footer-col-title {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1.25rem;
        }

        .footer-links { list-style: none; }

        .footer-links li { margin-bottom: 0.65rem; }

        .footer-links a {
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 0.88rem;
            transition: color 0.2s;
        }

        .footer-links a:hover { color: var(--gold); }

        .footer-bottom {
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.35);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .footer-bottom a {
            color: var(--gold);
            text-decoration: none;
        }

        /* ── MOBILE NAV TOGGLE ──────────────────── */
        .nav-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 4px;
        }

        .nav-toggle span {
            display: block;
            width: 24px; height: 2px;
            background: var(--white);
            border-radius: 2px;
            transition: all 0.3s;
        }

        /* ── RESPONSIVE ─────────────────────────── */
        @media (max-width: 1024px) {
            .hero-content { grid-template-columns: 1fr; text-align: center; }
            .hero-desc { margin: 0 auto 2.5rem; }
            .hero-cta { justify-content: center; }
            .hero-stats { justify-content: center; }
            .hero-visual { display: none; }
            .how-grid { grid-template-columns: 1fr; }
            .coverage-grid { grid-template-columns: 1fr; }
            .mission-inner { grid-template-columns: 1fr; }
            .mission-image { max-height: 340px; }
            .footer-top { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .navbar-nav { display: none; }
            .nav-toggle { display: flex; }
            section { padding: 4rem 1.5rem; }
            .footer-top { grid-template-columns: 1fr; gap: 2rem; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }

        /* ── SCROLL REVEAL ──────────────────────── */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.65s ease, transform 0.65s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
    </style>
</head>

<body>

    <!-- ── NAVBAR ──────────────────────────────────── -->
    <nav class="navbar" id="navbar">
        <a class="navbar-logo" href="#">
            <img src="/images/cafarm_wg.png" alt="CofSys Logo">
        </a>

        <ul class="navbar-nav">
            <li><a href="#features">Features</a></li>
            <li><a href="#mobile-app">Mobile App</a></li>
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="#coverage">Coverage</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="/admin/login" class="btn-nav">Login to Dashboard</a></li>
        </ul>

        <div class="nav-toggle" id="navToggle">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <!-- ── HERO ────────────────────────────────────── -->
    <section class="hero" id="home">
        <div class="hero-bg-img"></div>
        <div class="hero-bg"></div>
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>

        <div class="hero-content">
            <!-- Left -->
            <div class="hero-left">
                <div class="hero-badge">
                    <span class="hero-badge-dot"></span>
                    Davao de Oro, Philippines
                </div>

                <h1 class="hero-title">
                    Coffee Farm Management<br>
                    <span class="highlight">Smart Disease Detection<br>&amp; GeoAnalytics</span>
                </h1>

                <p class="hero-desc">
                    CofSys empowers coffee farmers and agricultural professionals with AI-driven disease detection,
                    smart geoanalytics, soil analysis, and real-time farm insights — all in one platform.
                </p>

                <div class="hero-cta">
                    <a href="/admin/login" class="btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        Access Dashboard
                    </a>
                    <a href="#features" class="btn-outline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
                        Explore Features
                    </a>
                </div>

                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-val">500+</div>
                        <div class="hero-stat-lbl">Farms Monitored</div>
                    </div>
                    <div>
                        <div class="hero-stat-val">12</div>
                        <div class="hero-stat-lbl">Municipalities</div>
                    </div>
                    <div>
                        <div class="hero-stat-val">AI</div>
                        <div class="hero-stat-lbl">Powered Detection</div>
                    </div>
                </div>
            </div>

            <!-- Right – floating dashboard card -->
            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card-header">
                        <div class="hero-card-icon">☕</div>
                        <div>
                            <div class="hero-card-title">Farm Overview</div>
                            <div class="hero-card-sub">Real-time monitoring</div>
                        </div>
                    </div>
                    <ul class="hero-mini-list">
                        <li>
                            <span class="mini-dot green"></span>
                            Pest & Disease Detection
                            <span class="hero-mini-badge badge-success">Active</span>
                        </li>
                        <li>
                            <span class="mini-dot gold"></span>
                            Soil Analysis
                            <span class="hero-mini-badge badge-info">Updated</span>
                        </li>
                        <li>
                            <span class="mini-dot orange"></span>
                            Pending Validations
                            <span class="hero-mini-badge badge-warn">3 New</span>
                        </li>
                        <li>
                            <span class="mini-dot blue"></span>
                            GIS Farm Mapping
                            <span class="hero-mini-badge badge-info">Live</span>
                        </li>
                    </ul>
                </div>

                <!-- Floating side tags -->
                <div class="hero-float-card hero-float-card-1">
                    <div class="float-icon">🌱</div>
                    <div>
                        <div class="float-text-main">Soil pH Normal</div>
                        <div class="float-text-sub">6.2 – Optimal range</div>
                    </div>
                </div>
                <div class="hero-float-card hero-float-card-2">
                    <div class="float-icon">📍</div>
                    <div>
                        <div class="float-text-main">Farm Mapped</div>
                        <div class="float-text-sub">Davao de Oro</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── MARQUEE BAND ─────────────────────────────── -->
    <div class="marquee-band" aria-hidden="true">
        <div class="marquee-track">
            <span>Coffee Farm Management</span>
            <span>Pest & Disease Detection</span>
            <span>Soil Analysis</span>
            <span>GIS Farm Mapping</span>
            <span>Agricultural Bulletins</span>
            <span>AI-Powered Insights</span>
            <span>QR Farmer ID</span>
            <span>Davao de Oro</span>
            <span>Coffee Farm Management</span>
            <span>Pest & Disease Detection</span>
            <span>Soil Analysis</span>
            <span>GIS Farm Mapping</span>
            <span>Agricultural Bulletins</span>
            <span>AI-Powered Insights</span>
            <span>QR Farmer ID</span>
            <span>Davao de Oro</span>
        </div>
    </div>

    <!-- ── FEATURES ─────────────────────────────────── -->
    <section class="features" id="features">
        <div class="section-inner">
            <div class="features-header">
                <div class="section-label">Core Features</div>
                <h2 class="section-title">Everything a Coffee Farm Needs</h2>
                <p class="section-sub">
                    From AI-assisted pest detection to detailed soil reports — CofSys gives farmers and professionals
                    the tools to manage smarter, not harder.
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon-wrap brown">
                        <img src="/images/pest-icon.png" alt="Pest" onerror="this.style.display='none';this.parentNode.innerHTML='🐛'">
                    </div>
                    <div class="feature-tag">AI Powered</div>
                    <div class="feature-title">Pest & Disease Detection</div>
                    <p class="feature-desc">
                        Upload photos from the field and let our AI model identify coffee plant pests and diseases instantly,
                        with expert-level recommendations for treatment and prevention.
                    </p>
                    <a href="/admin/login" class="feature-link">
                        View detections
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon-wrap green">
                        <img src="/images/soil-icon.png" alt="Soil" onerror="this.style.display='none';this.parentNode.innerHTML='🌿'">
                    </div>
                    <div class="feature-tag">Lab & Field</div>
                    <div class="feature-title">Soil Analysis</div>
                    <p class="feature-desc">
                        Track pH levels, nutrient content, and soil health over time. Get actionable,
                        AI-backed recommendations tailored to your specific coffee variety and terrain.
                    </p>
                    <a href="/admin/login" class="feature-link">
                        View analyses
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon-wrap gold">
                        <img src="/images/farm-icon.png" alt="Farm" onerror="this.style.display='none';this.parentNode.innerHTML='🗺️'">
                    </div>
                    <div class="feature-tag">GIS Mapping</div>
                    <div class="feature-title">Interactive Farm Map</div>
                    <p class="feature-desc">
                        Visualize all registered farms on an interactive GIS map with barangay and municipal boundary overlays.
                        Identify hotspots and track geographic trends across Davao de Oro.
                    </p>
                    <a href="/admin/login" class="feature-link">
                        Open map
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="feature-card reveal reveal-delay-3">
                    <div class="feature-icon-wrap forest">
                        <img src="/images/disease-icon.png" alt="Disease" onerror="this.style.display='none';this.parentNode.innerHTML='📋'">
                    </div>
                    <div class="feature-tag">Farmer Registry</div>
                    <div class="feature-title">Digital Farmer Profiles</div>
                    <p class="feature-desc">
                        Maintain comprehensive digital records for every registered farmer — including farm details,
                        unique QR code IDs, and a full history of pest reports and soil assessments.
                    </p>
                    <a href="/admin/login" class="feature-link">
                        View registry
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon-wrap brown">
                        <span style="font-size:1.5rem">📢</span>
                    </div>
                    <div class="feature-tag">Communication</div>
                    <div class="feature-title">Agricultural Bulletins</div>
                    <p class="feature-desc">
                        Agricultural professionals can publish and distribute timely bulletins — advisories,
                        best practices, and seasonal guides — directly to farmers through the mobile app.
                    </p>
                    <a href="/admin/login" class="feature-link">
                        Read bulletins
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon-wrap green">
                        <span style="font-size:1.5rem">📊</span>
                    </div>
                    <div class="feature-tag">Analytics</div>
                    <div class="feature-title">Dashboard & Reports</div>
                    <p class="feature-desc">
                        Monitor key statistics through rich visual dashboards. Export professional PDF reports
                        covering farm inventories, pest trends, and soil health summaries for local government planning.
                    </p>
                    <a href="/admin/login" class="feature-link">
                        View reports
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- ── MOBILE APP BANNER ───────────────────────────── -->
    <section class="mobile-app-banner" id="mobile-app">
        <div class="mab-inner">

            <!-- Left: text + download buttons -->
            <div class="mab-content reveal">
                <div class="mab-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    Now Available
                </div>

                <h2 class="mab-title">
                    Take CofSys<br>
                    <span class="mab-highlight">To The Field</span>
                </h2>

                <p class="mab-desc">
                    The CofSys mobile app puts the full power of smart farm management right in farmers' hands.
                    Capture pest photos, submit reports with GPS tagging, scan QR farmer IDs, and receive
                    real-time bulletins — even in areas with limited connectivity.
                </p>

                <ul class="mab-features">
                    <li>
                        <span class="mab-feat-icon">📸</span>
                        <span>AI-powered photo pest &amp; disease detection</span>
                    </li>
                    <li>
                        <span class="mab-feat-icon">📍</span>
                        <span>GPS-tagged field reports &amp; farm location</span>
                    </li>
                    <li>
                        <span class="mab-feat-icon">🔍</span>
                        <span>QR code scanner for instant farmer ID lookup</span>
                    </li>
                    <li>
                        <span class="mab-feat-icon">🔔</span>
                        <span>Push notifications for bulletins &amp; alerts</span>
                    </li>
                    <li>
                        <span class="mab-feat-icon">📶</span>
                        <span>Offline-ready for remote farm areas</span>
                    </li>
                </ul>

                <div class="mab-store-btns">
                    <!-- Google Play -->
                    <a href="#" class="store-btn store-btn--android">
                        <svg class="store-icon" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.18 23.76c.3.17.65.2.98.09l11.07-6.39-2.38-2.38-9.67 8.68zm-1.06-20.3C2.04 3.73 2 4.02 2 4.34v15.32c0 .32.04.61.12.88l.06.06 8.59-8.59v-.2L2.18 3.4l-.06.06zM20.1 10.3l-2.35-1.36-2.65 2.65 2.65 2.65 2.37-1.37c.68-.39.68-1.18-.02-1.57zm-17.74 12c.3.3.75.38 1.16.15l12.55-7.25-2.56-2.56-11.15 9.66z"/>
                        </svg>
                        <div class="store-text">
                            <span class="store-sub">Get it on</span>
                            <span class="store-name">Google Play</span>
                        </div>
                    </a>

                    <!-- App Store -->
                    <a href="#" class="store-btn store-btn--ios">
                        <svg class="store-icon" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                        </svg>
                        <div class="store-text">
                            <span class="store-sub">Download on the</span>
                            <span class="store-name">App Store</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Right: app screenshot image -->
            <div class="mab-mockup reveal reveal-delay-2">
                <div class="phone-wrap">
                    <div class="phone-glow"></div>
                    <img src="/images/CofSys.jpg"
                         alt="CofSys Mobile App"
                         class="app-screenshot">
                    <div class="phone-pill phone-pill-1">
                        <span>📸</span> AI Detection
                    </div>
                    <div class="phone-pill phone-pill-2">
                        <span>📍</span> GPS Tagged
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ── HOW IT WORKS ─────────────────────────────── -->
    <section class="how" id="how-it-works">
        <div class="section-inner how-inner">
            <div class="section-label" style="color: rgba(62,200,122,0.9);">Simple Process</div>
            <h2 class="section-title" style="color: var(--white);">How <em style="font-style:normal;color:var(--gold)">CofSys</em> Works</h2>
            <p class="section-sub" style="color: rgba(255,255,255,0.55);">
                From registration to actionable insights — the workflow is designed to be intuitive
                for field workers and administrators alike.
            </p>

            <div class="how-grid">
                <!-- Steps -->
                <div class="how-steps">
                    <div class="how-step reveal">
                        <div class="step-num">1</div>
                        <div>
                            <div class="step-body-title">Register Farmers & Farms</div>
                            <p class="step-body-desc">
                                Agricultural professionals register farmers and their farms with geo-location data.
                                Each farmer receives a unique QR code for fast field identification.
                            </p>
                        </div>
                    </div>

                    <div class="how-step reveal reveal-delay-1">
                        <div class="step-num">2</div>
                        <div>
                            <div class="step-body-title">Submit Reports via Mobile</div>
                            <p class="step-body-desc">
                                Farmers use the CofSys mobile app to photograph and report pests, diseases,
                                and soil concerns directly from the field with GPS tagging.
                            </p>
                        </div>
                    </div>

                    <div class="how-step reveal reveal-delay-2">
                        <div class="step-num">3</div>
                        <div>
                            <div class="step-body-title">AI Analysis & Validation</div>
                            <p class="step-body-desc">
                                Submissions are analyzed by our AI engine and reviewed by agricultural
                                professionals who validate findings and provide tailored recommendations.
                            </p>
                        </div>
                    </div>

                    <div class="how-step reveal reveal-delay-3">
                        <div class="step-num">4</div>
                        <div>
                            <div class="step-body-title">Insights & Action Plans</div>
                            <p class="step-body-desc">
                                Approved findings appear on the GIS dashboard with trend analytics, helping
                                local agricultural offices plan targeted interventions and resource allocation.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mockup -->
                <div class="how-mockup reveal">
                    <div class="mockup-frame">
                        <div class="mockup-bar">
                            <span class="mockup-dot r"></span>
                            <span class="mockup-dot y"></span>
                            <span class="mockup-dot g"></span>
                            <div class="mockup-title-bar"></div>
                        </div>

                        <div class="mockup-stats-row">
                            <div class="mockup-stat">
                                <div class="ms-val">247</div>
                                <div class="ms-lbl">Farmers</div>
                            </div>
                            <div class="mockup-stat">
                                <div class="ms-val">89</div>
                                <div class="ms-lbl">Reports</div>
                            </div>
                            <div class="mockup-stat">
                                <div class="ms-val">12</div>
                                <div class="ms-lbl">Municipalities</div>
                            </div>
                        </div>

                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.4);letter-spacing:0.08em;text-transform:uppercase;margin-bottom:0.5rem;">Pest Reports – Past 6 Months</div>
                        <div class="mockup-chart-bar">
                            <div class="chart-bar-col"><div class="chart-bar-fill" style="height:45%"></div></div>
                            <div class="chart-bar-col"><div class="chart-bar-fill" style="height:65%"></div></div>
                            <div class="chart-bar-col"><div class="chart-bar-fill" style="height:35%"></div></div>
                            <div class="chart-bar-col"><div class="chart-bar-fill" style="height:80%"></div></div>
                            <div class="chart-bar-col"><div class="chart-bar-fill" style="height:55%"></div></div>
                            <div class="chart-bar-col"><div class="chart-bar-fill" style="height:70%"></div></div>
                        </div>

                        <div style="margin-top:1rem;">
                            <div class="mockup-row">
                                <span class="mockup-row-dot" style="background:#E07830"></span>
                                <div class="mockup-row-line"></div>
                                <span class="mockup-row-badge" style="background:rgba(224,120,48,0.2);color:#E07830">Pending</span>
                            </div>
                            <div class="mockup-row">
                                <span class="mockup-row-dot" style="background:#5FC877"></span>
                                <div class="mockup-row-line"></div>
                                <span class="mockup-row-badge" style="background:rgba(95,200,119,0.2);color:#5FC877">Approved</span>
                            </div>
                            <div class="mockup-row">
                                <span class="mockup-row-dot" style="background:#5B9BD5"></span>
                                <div class="mockup-row-line"></div>
                                <span class="mockup-row-badge" style="background:rgba(91,155,213,0.2);color:#5B9BD5">Reviewed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── COVERAGE ─────────────────────────────────── -->
    <section class="coverage" id="coverage">
        <div class="section-inner">
            <div class="coverage-grid">
                <!-- Map visual -->
                <div class="map-placeholder reveal">
                    <img src="/images/coffee_farm1.jpg" alt="Coffee farm aerial view"
                         onerror="this.style.background='linear-gradient(135deg, #107737, #003432)';this.style.display='block'">
                    <div class="map-overlay-badge">
                        <div class="mob-val">12+</div>
                        <div class="mob-lbl">Municipalities<br>Covered</div>
                    </div>
                </div>

                <!-- Points -->
                <div>
                    <div class="section-label">Geographic Reach</div>
                    <h2 class="section-title">Serving Coffee Farms<br>Across Davao de Oro</h2>
                    <p class="section-sub" style="margin-bottom:3rem;">
                        CofSys provides detailed geographic coverage across Davao de Oro through GeoAnalytics,
                        helping local agricultural offices monitor every registered farm.
                    </p>

                    <div class="coverage-points">
                        <div class="coverage-point reveal">
                            <div class="cp-icon">📍</div>
                            <div>
                                <div class="cp-title">Barangay-Level Precision</div>
                                <p class="cp-desc">Farm locations are mapped down to individual barangay boundaries, giving agricultural officers granular visibility over any geographic area.</p>
                            </div>
                        </div>

                        <div class="coverage-point reveal reveal-delay-1">
                            <div class="cp-icon">🗺️</div>
                            <div>
                                <div class="cp-title">Interactive Map Layers</div>
                                <p class="cp-desc">Toggle between pest distribution, soil analysis hotspots, and farmer density to identify regions that need the most support.</p>
                            </div>
                        </div>

                        <div class="coverage-point reveal reveal-delay-2">
                            <div class="cp-icon">📡</div>
                            <div>
                                <div class="cp-title">Real-Time Updates</div>
                                <p class="cp-desc">New farm registrations and field reports appear on the map immediately, ensuring decision-makers always have the latest data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── MISSION ──────────────────────────────────── -->
    <section class="mission" id="about">
        <div class="section-inner">
            <div class="mission-inner">
                <div class="reveal">
                    <div class="section-label">Our Mission</div>
                    <h2 class="mission-quote">
                        "Empowering every coffee farmer with the <span class="em">knowledge</span> and <span class="em">tools</span> to thrive."
                    </h2>
                    <p class="mission-desc">
                        CofSys was built for the coffee farming communities of Davao de Oro — bridging the gap between
                        farmers in the field and agricultural professionals through smart disease detection and geoanalytics.
                        Our platform turns raw field observations into actionable intelligence, helping sustain and grow the local coffee industry.
                    </p>
                    <p class="mission-desc">
                        Developed as a capstone project dedicated to improving the welfare of local coffee farmers through
                        accessible, intuitive digital tools that work even in low-connectivity environments.
                    </p>
                    <div class="mission-tags">
                        <span class="mission-tag">☕ Coffee Agriculture</span>
                        <span class="mission-tag">🌿 Sustainable Farming</span>
                        <span class="mission-tag">🤖 AI Technology</span>
                        <span class="mission-tag">📱 Mobile First</span>
                        <span class="mission-tag">🇵🇭 Davao de Oro, Philippines</span>
                    </div>
                </div>

                <div class="mission-image reveal reveal-delay-2">
                    <img src="/images/coffee_farm2.jpg" alt="Coffee farm"
                         onerror="this.style.background='linear-gradient(160deg, #14532d, #003432)';this.style.display='block'">
                    <div class="mission-img-badge">
                        <div style="font-size:1.4rem;font-weight:700;font-family:'Playfair Display',serif;color:var(--gold)">100%</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.08em">Local Focus</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CTA ──────────────────────────────────────── -->
    <section class="cta-section">
        <div class="cta-inner reveal">
            <div class="section-label" style="color:rgba(62,200,122,0.9);justify-content:center;display:flex">Get Started</div>
            <h2 class="cta-title">Ready to Manage Your<br>Coffee Farm Smarter?</h2>
            <p class="cta-desc">
                Join agricultural professionals already using CofSys to monitor farms, detect diseases early,
                and leverage geoanalytics for smarter decisions across Davao de Oro.
            </p>
            <div class="cta-btns">
                <a href="/admin/login" class="btn-primary" style="font-size:1rem;padding:1rem 2.5rem">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Login to Dashboard
                </a>
                <a href="#features" class="btn-outline" style="font-size:1rem;padding:1rem 2.5rem">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- ── FOOTER ───────────────────────────────────── -->
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div class="footer-brand">
                    <img src="/images/cafarm_wg.png" alt="CofSys Logo"
                         onerror="this.alt='CofSys';this.style.display='none'">
                    <p>
                        Coffee Farm Management System with Smart Disease Detection and GeoAnalytics,
                        serving the coffee farming communities of Davao de Oro.
                    </p>
                </div>

                <div>
                    <div class="footer-col-title">Platform</div>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#coverage">Coverage Map</a></li>
                        <li><a href="/admin/login">Dashboard Login</a></li>
                    </ul>
                </div>

                <div>
                    <div class="footer-col-title">Resources</div>
                    <ul class="footer-links">
                        <li><a href="/admin/login">Pest & Disease Guide</a></li>
                        <li><a href="/admin/login">Soil Analysis Guide</a></li>
                        <li><a href="/admin/login">Farm Registry</a></li>
                        <li><a href="/admin/login">Bulletins</a></li>
                    </ul>
                </div>

                <div>
                    <div class="footer-col-title">Contact</div>
                    <ul class="footer-links">
                        <li><a href="#">Davao de Oro, Philippines</a></li>
                        <li><a href="#">Provincial Agriculture Office – Davao de Oro</a></li>
                        <li><a href="#">Support & Help</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© 2026 CofSys. Coffee Farm Management System with Smart Disease Detection and GeoAnalytics. All rights reserved.</span>
                <span>Built with ❤️ for the coffee farmers of <a href="#">Davao de Oro</a></span>
            </div>
        </div>
    </footer>

    <script>
        // ── Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 60);
        });

        // ── Scroll reveal
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });
        reveals.forEach(el => observer.observe(el));

        // ── Mobile nav (simple toggle)
        document.getElementById('navToggle').addEventListener('click', () => {
            const nav = document.querySelector('.navbar-nav');
            if (nav.style.display === 'flex') {
                nav.style.display = '';
            } else {
                nav.style.display = 'flex';
                nav.style.flexDirection = 'column';
                nav.style.position = 'fixed';
                nav.style.top = '70px';
                nav.style.left = '0';
                nav.style.right = '0';
                nav.style.background = 'rgba(0,52,50,0.98)';
                nav.style.padding = '1.5rem 2rem';
                nav.style.gap = '1.25rem';
                nav.style.zIndex = '99';
            }
        });

        // ── Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
<?php /**PATH /var/www/html/CapstoneProject/resources/views/landing.blade.php ENDPATH**/ ?>