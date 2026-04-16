<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CofSys - Coffee Farm Management System</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: #1a1a1a; line-height: 1.6; }

        /* Navbar */
        .navbar {
            position: fixed; top: 0; width: 100%; z-index: 50;
            background: rgba(255,255,255,0.95); backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding: 0.75rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .navbar-brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .navbar-brand img { height: 44px; }
        .navbar-brand span { font-size: 1.25rem; font-weight: 700; color: #166534; }
        .navbar-links { display: flex; gap: 1.5rem; align-items: center; }
        .navbar-links a {
            text-decoration: none; font-size: 0.875rem; font-weight: 500;
            color: #4b5563; transition: color 0.2s;
        }
        .navbar-links a:hover { color: #166534; }
        .btn-login {
            background: #166534; color: #fff !important; padding: 0.5rem 1.25rem;
            border-radius: 8px; font-weight: 600; transition: background 0.2s;
        }
        .btn-login:hover { background: #14532d; }

        /* Hero */
        .hero {
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 30%, #bbf7d0 60%, #f0fdf4 100%);
            padding: 6rem 2rem 4rem;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -50%; right: -30%; width: 80%; height: 200%;
            background: radial-gradient(circle, rgba(22,101,52,0.05) 0%, transparent 70%);
        }
        .hero-content { max-width: 1200px; width: 100%; display: flex; align-items: center; gap: 4rem; position: relative; z-index: 1; }
        .hero-text { flex: 1; }
        .hero-badge {
            display: inline-block; background: #dcfce7; color: #166534;
            padding: 0.375rem 1rem; border-radius: 9999px; font-size: 0.75rem;
            font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 1.25rem;
        }
        .hero-text h1 { font-size: 3.25rem; font-weight: 800; line-height: 1.1; color: #0f172a; margin-bottom: 0.5rem; }
        .hero-text h1 span { color: #166534; }
        .hero-text .subtitle { font-size: 1.125rem; color: #166534; font-weight: 600; margin-bottom: 1.25rem; }
        .hero-text p { font-size: 1.05rem; color: #4b5563; line-height: 1.75; margin-bottom: 2rem; max-width: 540px; }
        .hero-buttons { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-primary {
            background: #166534; color: #fff; padding: 0.875rem 2rem; border-radius: 10px;
            font-weight: 600; font-size: 0.95rem; text-decoration: none; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-primary:hover { background: #14532d; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(22,101,52,0.3); }
        .btn-outline {
            background: transparent; color: #166534; padding: 0.875rem 2rem; border-radius: 10px;
            font-weight: 600; font-size: 0.95rem; text-decoration: none; transition: all 0.2s;
            border: 2px solid #166534; display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-outline:hover { background: #f0fdf4; }
        .hero-image { flex: 1; display: flex; justify-content: center; }
        .hero-image img {
            width: 100%; max-width: 480px; border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            object-fit: cover; height: 380px;
        }

        /* Stats */
        .stats-bar {
            background: #fff; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;
            padding: 2.5rem 2rem;
        }
        .stats-grid {
            max-width: 1000px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; text-align: center;
        }
        .stat-item h3 { font-size: 2rem; font-weight: 800; color: #166534; }
        .stat-item p { font-size: 0.8rem; color: #6b7280; font-weight: 500; margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em; }

        /* Sections */
        .section { padding: 5rem 2rem; }
        .section-alt { background: #f9fafb; }
        .section-header { text-align: center; margin-bottom: 3.5rem; }
        .section-header h2 { font-size: 2.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem; }
        .section-header p { font-size: 1.05rem; color: #6b7280; max-width: 600px; margin: 0 auto; }

        /* Features Grid */
        .features-grid {
            max-width: 1100px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.75rem;
        }
        .feature-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
            padding: 2rem; transition: all 0.25s;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.08); border-color: #bbf7d0; }
        .feature-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem; font-size: 1.5rem;
        }
        .feature-icon.green { background: #dcfce7; }
        .feature-icon.blue { background: #dbeafe; }
        .feature-icon.amber { background: #fef3c7; }
        .feature-icon.red { background: #fee2e2; }
        .feature-icon.purple { background: #ede9fe; }
        .feature-icon.teal { background: #ccfbf1; }
        .feature-card h3 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
        .feature-card p { font-size: 0.875rem; color: #6b7280; line-height: 1.65; }

        /* Objectives */
        .objectives-grid {
            max-width: 1000px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;
        }
        .objective-card {
            display: flex; gap: 1.25rem; padding: 1.75rem; background: #fff;
            border-radius: 14px; border: 1px solid #e5e7eb;
        }
        .objective-num {
            width: 40px; height: 40px; min-width: 40px; border-radius: 10px;
            background: #166534; color: #fff; display: flex; align-items: center;
            justify-content: center; font-weight: 700; font-size: 1.1rem;
        }
        .objective-card h4 { font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 0.375rem; }
        .objective-card p { font-size: 0.825rem; color: #6b7280; line-height: 1.6; }

        /* Scope */
        .scope-content {
            max-width: 800px; margin: 0 auto; background: #fff; border: 1px solid #e5e7eb;
            border-radius: 16px; padding: 2.5rem; text-align: center;
        }
        .scope-content p { font-size: 1rem; color: #4b5563; line-height: 1.8; }

        /* Footer */
        .footer {
            background: #0f172a; color: #94a3b8; padding: 3rem 2rem 2rem; text-align: center;
        }
        .footer-brand { display: flex; align-items: center; justify-content: center; gap: 0.75rem; margin-bottom: 1rem; }
        .footer-brand img { height: 36px; }
        .footer-brand span { font-size: 1.125rem; font-weight: 700; color: #fff; }
        .footer p { font-size: 0.8rem; max-width: 480px; margin: 0 auto 1.5rem; line-height: 1.7; }
        .footer-line { border-top: 1px solid #1e293b; padding-top: 1.5rem; font-size: 0.75rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content { flex-direction: column; text-align: center; gap: 2.5rem; }
            .hero-text h1 { font-size: 2.25rem; }
            .hero-text p { margin: 0 auto 2rem; }
            .hero-buttons { justify-content: center; }
            .hero-image img { max-width: 340px; height: 260px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: 1fr; }
            .objectives-grid { grid-template-columns: 1fr; }
            .navbar-links { gap: 0.75rem; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="/" class="navbar-brand">
            <img src="{{ asset('images/favicon.png') }}" alt="CofSys Logo">
            <span>CofSys</span>
        </a>
        <div class="navbar-links">
            <a href="#features">Features</a>
            <a href="#objectives">Objectives</a>
            <a href="#scope">Scope</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/admin') }}" class="btn-login">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Log in</a>
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <span class="hero-badge">Maragusan, Davao de Oro</span>
                <h1>Coffee Farm Management<br>with <span>Smart Detection</span></h1>
                <p class="subtitle">CofSys: Coffee Farm Management System with Smart Disease Detection and Geoanalytics</p>
                <p>A web and mobile-based system designed to support small to medium-scale coffee farmers by providing tools for image-based pest and disease detection, soil health analysis, GPS-based farm monitoring, and data-driven agricultural decision-making.</p>
                <div class="hero-buttons">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/admin') }}" class="btn-primary">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                                Open Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                Admin Login
                            </a>
                        @endauth
                    @endif
                    <a href="#features" class="btn-outline">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ asset('images/coffee farm area.jpg') }}" alt="Coffee Farm in Maragusan">
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stats-grid">
            <div class="stat-item">
                <h3>{{ \App\Models\Farmer::count() }}</h3>
                <p>Registered Farmers</p>
            </div>
            <div class="stat-item">
                <h3>{{ \App\Models\Farm::count() }}</h3>
                <p>Farm Plots</p>
            </div>
            <div class="stat-item">
                <h3>{{ \App\Models\PestAndDisease::where('validation_status', 'approved')->count() }}</h3>
                <p>Detections Validated</p>
            </div>
            <div class="stat-item">
                <h3>{{ \App\Models\SoilAnalysis::count() }}</h3>
                <p>Soil Analyses</p>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="section-header">
            <h2>System Features</h2>
            <p>CofSys integrates farm management, pest detection, soil health analysis, and geoanalytics into one unified platform.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon green">
                    <img src="{{ asset('images/pest-icon.png') }}" alt="" width="28" height="28">
                </div>
                <h3>Smart Pest & Disease Detection</h3>
                <p>Farmers submit pest and disease reports via the mobile app with images, GPS location, and detection confidence. Experts validate detections and provide guide-based recommendations through the web panel.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon blue">
                    <img src="{{ asset('images/soil-icon.png') }}" alt="" width="28" height="28">
                </div>
                <h3>Soil Health Analysis</h3>
                <p>Records soil properties including pH level, nitrogen, phosphorus, potassium, and organic matter. Experts review results and issue soil fertility advisories with guide-based or AI-generated recommendations.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon amber">
                    <img src="{{ asset('images/farm-icon.png') }}" alt="" width="28" height="28">
                </div>
                <h3>Farm & Farmer Management</h3>
                <p>Maintains a complete database of coffee farmers and their registered farm plots in Maragusan, including GPS coordinates, crop details, soil type, and cropping system. Supports bulk registration and QR-based ID printing.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon purple">
                    <svg width="28" height="28" fill="none" stroke="#7c3aed" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/></svg>
                </div>
                <h3>GIS Mapping & Geoanalytics</h3>
                <p>Interactive maps display farms, pest and disease cases, and soil analysis locations. Filter records to identify affected areas and analyze geographic patterns across different barangays within Maragusan.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon red">
                    <svg width="28" height="28" fill="none" stroke="#dc2626" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </div>
                <h3>Dashboard & Analytics</h3>
                <p>A web dashboard presents pest severity distribution, pest incidence rate trends, top affected barangays, soil nutrient levels per barangay, and a validation queue for prompt review of pending submissions.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon teal">
                    <svg width="28" height="28" fill="none" stroke="#0d9488" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                </div>
                <h3>Real-Time Notifications & Alerts</h3>
                <p>Push notifications via Firebase Cloud Messaging keep farmers informed of validated detections and soil results. Outbreak proximity alerts automatically notify nearby farms when a high-severity detection is approved.</p>
            </div>
        </div>
    </section>

    <!-- Objectives Section -->
    <section class="section section-alt" id="objectives">
        <div class="section-header">
            <h2>Specific Objectives</h2>
            <p>The core goals driving CofSys development</p>
        </div>
        <div class="objectives-grid">
            <div class="objective-card">
                <div class="objective-num">1</div>
                <div>
                    <h4>Mobile Pest & Disease Detection</h4>
                    <p>Develop a mobile application with image recognition capability to detect coffee plant pests and diseases, and provide farmers with real-time guide-based treatment recommendations for pest and disease control, as well as guide-based soil fertility recommendations.</p>
                </div>
            </div>
            <div class="objective-card">
                <div class="objective-num">2</div>
                <div>
                    <h4>Integrated Farm Monitoring</h4>
                    <p>Develop an integrated farm monitoring system that detects pests and diseases, analyzes soil health, calculates pest incidence rates, and automatically tags each detection to the farm's registered location to reveal patterns across different barangays within the municipality of Maragusan.</p>
                </div>
            </div>
            <div class="objective-card">
                <div class="objective-num">3</div>
                <div>
                    <h4>Web Administrative Dashboard</h4>
                    <p>Develop a web-based administrative dashboard that visualizes active pest and disease cases, validation statuses, soil fertility results, and geographic detection hotspots through interactive maps, analytics charts, and validation queue management tools.</p>
                </div>
            </div>
            <div class="objective-card">
                <div class="objective-num">4</div>
                <div>
                    <h4>Reporting & Advisory Tools</h4>
                    <p>Integrate reporting and advisory tools to enable agricultural personnel to validate detections using guide-based and AI-generated recommendations, issue soil fertility advisories, communicate with farmers, and send real-time notifications including automatic outbreak proximity alerts.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Scope Section -->
    <section class="section" id="scope">
        <div class="section-header">
            <h2>Scope & Coverage</h2>
            <p>Designed specifically for the coffee-producing communities of Maragusan</p>
        </div>
        <div class="scope-content">
            <p>CofSys covers the coffee-producing communities of Maragusan, Davao de Oro, with particular focus on registered Coffee Farmers Associations and local farming groups. The system includes a mobile application for pest and disease detection using image recognition, GPS-based geotagging for farm-level monitoring, and a soil test submission module that generates site-specific soil management recommendations. The web application enables agricultural personnel from MAGSO, PAGRO, and DDOSC to visualize pest and disease outbreak locations, manage validation requests, analyze farm data through interactive dashboards, and issue advisories and real-time notifications for timely interventions. The mobile application also supports bilingual language switching between English and Bisaya (Cebuano) to ensure accessibility for local farmers.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-brand">
            <img src="{{ asset('images/favicon.png') }}" alt="CofSys">
            <span>CofSys</span>
        </div>
        <p>Coffee Farm Management System with Smart Disease Detection and Geoanalytics &mdash; supporting sustainable coffee farming in Maragusan, Davao de Oro.</p>
        <div class="footer-line">
            &copy; {{ date('Y') }} CofSys &mdash; Developed by Kent Vincent B. Gonzales
        </div>
    </footer>

</body>
</html>
