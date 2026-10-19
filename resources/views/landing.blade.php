<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $konfigurasi->hero_subtitle ?: 'Salon & Barber profesional ' . $konfigurasi->nama_perusahaan }}">
    <title>{{ $konfigurasi->nama_perusahaan ?: 'LINY Salon' }} — Hair and Beauty</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('assets/images/logo.jpeg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --liny-black: #0f0f0f;
            --liny-dark: #1a1a1a;
            --liny-soft: #2c2c2c;
            --liny-gold: #c9a961;
            --liny-gold-dark: #b08d4a;
            --liny-gold-soft: #e2c98a;
            --liny-cream: #f5efe6;
            --liny-text: #1a1a1a;
            --liny-muted: #8a7f6c;
            --liny-border: #e8e2d5;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--liny-text);
            background: #fff;
            overflow-x: hidden;
        }
        h1, h2, h3, h4, .serif { font-family: 'Cormorant Garamond', serif; }

        a { text-decoration: none; }

        /* === NAVBAR === */
        .navbar-liny {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            padding: 1rem 0;
            background: transparent;
            transition: all 0.3s ease;
        }
        .navbar-liny.scrolled {
            background: rgba(15, 15, 15, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            padding: 0.6rem 0;
        }
        .navbar-brand img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--liny-gold);
            background: #fff;
            padding: 2px;
        }
        .navbar-brand .brand-text {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            font-size: 1.4rem;
            letter-spacing: 3px;
            color: var(--liny-gold-soft);
            margin-left: 0.5rem;
        }
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0.5rem 1rem !important;
            transition: color 0.2s;
        }
        .navbar-nav .nav-link:hover { color: var(--liny-gold) !important; }
        .navbar-toggler {
            border: 1px solid var(--liny-gold);
            color: var(--liny-gold);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23c9a961' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .btn-reservasi-nav {
            background: linear-gradient(180deg, var(--liny-gold) 0%, var(--liny-gold-dark) 100%);
            color: var(--liny-black);
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 4px 12px rgba(201, 169, 97, 0.4);
            transition: all 0.25s;
        }
        .btn-reservasi-nav:hover {
            background: linear-gradient(180deg, var(--liny-gold-dark) 0%, var(--liny-gold) 100%);
            color: var(--liny-black);
            transform: translateY(-1px);
        }

        /* === HERO === */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--liny-black) 0%, var(--liny-soft) 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 6rem 0 4rem;
        }
        .hero.has-bg-image {
            background-size: cover;
            background-position: center;
        }
        .hero.has-bg-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 15, 15, 0.7) 0%, rgba(15, 15, 15, 0.5) 100%);
        }
        .hero::after {
            content: '';
            position: absolute;
            top: -10%;
            right: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.15), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-content { position: relative; z-index: 1; text-align: center; }
        .hero-eyebrow {
            display: inline-block;
            font-size: 0.75rem;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: var(--liny-gold);
            border: 1px solid var(--liny-gold);
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 7vw, 5rem);
            font-weight: 600;
            color: #fff;
            line-height: 1.1;
            margin-bottom: 1.25rem;
            letter-spacing: 1px;
        }
        .hero h1 .gold { color: var(--liny-gold-soft); font-style: italic; }
        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.15rem);
            color: rgba(255, 255, 255, 0.8);
            max-width: 640px;
            margin: 0 auto 2.5rem;
            line-height: 1.6;
        }
        .hero-buttons { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn-gold {
            background: linear-gradient(180deg, var(--liny-gold) 0%, var(--liny-gold-dark) 100%);
            color: var(--liny-black);
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 6px 20px rgba(176, 141, 74, 0.4);
            transition: all 0.25s;
        }
        .btn-gold:hover {
            background: linear-gradient(180deg, var(--liny-gold-dark) 0%, var(--liny-gold) 100%);
            color: var(--liny-black);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(176, 141, 74, 0.5);
        }
        .btn-outline-gold {
            background: transparent;
            color: var(--liny-gold-soft);
            font-weight: 500;
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            border: 1.5px solid var(--liny-gold);
            transition: all 0.25s;
        }
        .btn-outline-gold:hover {
            background: var(--liny-gold);
            color: var(--liny-black);
        }
        .hero-scroll {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            color: var(--liny-gold);
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            text-align: center;
            z-index: 1;
        }
        .hero-scroll .mouse {
            display: block;
            width: 24px;
            height: 38px;
            border: 1.5px solid var(--liny-gold);
            border-radius: 12px;
            margin: 0.5rem auto 0;
            position: relative;
        }
        .hero-scroll .mouse::before {
            content: '';
            position: absolute;
            top: 6px;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 6px;
            background: var(--liny-gold);
            border-radius: 2px;
            animation: scroll-anim 1.5s infinite;
        }
        @keyframes scroll-anim {
            0% { opacity: 1; top: 6px; }
            100% { opacity: 0; top: 20px; }
        }

        /* === SECTIONS === */
        section { padding: 5rem 0; }
        .section-eyebrow {
            font-size: 0.75rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--liny-gold-dark);
            margin-bottom: 0.5rem;
        }
        .section-title {
            font-size: clamp(2rem, 5vw, 3.25rem);
            font-weight: 600;
            color: var(--liny-text);
            margin-bottom: 1rem;
            line-height: 1.15;
        }
        .section-subtitle {
            color: var(--liny-muted);
            font-size: 1rem;
            max-width: 600px;
            margin: 0 auto 3rem;
        }
        .section-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: var(--liny-gold);
        }
        .section-divider::before,
        .section-divider::after {
            content: '';
            width: 50px;
            height: 1px;
            background: var(--liny-gold);
        }

        /* === ABOUT === */
        #tentang { background: var(--liny-cream); }
        .about-image {
            width: 100%;
            height: 480px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        .about-image-placeholder {
            width: 100%;
            height: 480px;
            background: linear-gradient(135deg, var(--liny-dark), var(--liny-soft));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--liny-gold);
            font-size: 4rem;
        }
        .about-text { font-size: 1.05rem; line-height: 1.8; color: #444; }
        .about-text p { margin-bottom: 1rem; }

        /* === SERVICES === */
        #layanan { background: #fff; }
        .service-category-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            color: var(--liny-gold-dark);
            margin: 2.5rem 0 1rem;
            text-align: center;
            letter-spacing: 2px;
        }
        .service-category-title:first-of-type { margin-top: 1rem; }
        .service-card {
            background: #fff;
            border: 1.5px solid var(--liny-border);
            border-radius: 14px;
            padding: 1.75rem 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .service-card:hover {
            border-color: var(--liny-gold);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(201, 169, 97, 0.18);
        }
        .service-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--liny-cream);
            color: var(--liny-gold-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        .service-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--liny-text);
            margin-bottom: 0.5rem;
        }
        .service-desc {
            font-size: 0.88rem;
            color: var(--liny-muted);
            line-height: 1.5;
            margin-bottom: 1rem;
            flex: 1;
        }
        .service-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--liny-gold-dark);
            margin-bottom: 1rem;
        }
        .btn-service {
            background: transparent;
            border: 1.5px solid var(--liny-gold);
            color: var(--liny-gold-dark);
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0.55rem 1rem;
            border-radius: 50px;
            transition: all 0.2s;
        }
        .btn-service:hover {
            background: var(--liny-gold);
            color: var(--liny-black);
        }

        /* === GALLERY === */
        #galeri { background: var(--liny-cream); }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
        }
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            cursor: pointer;
            aspect-ratio: 1 / 1;
            background: var(--liny-dark);
        }
        .gallery-item.tall { aspect-ratio: 1 / 1.25; grid-row: span 1; }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .gallery-item:hover img { transform: scale(1.08); }
        .gallery-item-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 15, 15, 0.85), transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            align-items: flex-end;
            padding: 1rem;
            color: #fff;
        }
        .gallery-item:hover .gallery-item-overlay { opacity: 1; }
        .gallery-item-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            margin: 0;
        }
        .gallery-item-desc {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0.25rem 0 0;
        }

        /* === VIDEOS === */
        #video { background: #fff; }
        .video-card {
            background: var(--liny-dark);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            height: 100%;
        }
        .video-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #000;
        }
        .video-wrapper iframe,
        .video-wrapper video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .video-info { padding: 1.25rem; color: #fff; }
        .video-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            color: var(--liny-gold-soft);
            margin-bottom: 0.25rem;
        }
        .video-desc { font-size: 0.85rem; color: rgba(255, 255, 255, 0.7); margin: 0; }

        /* === TESTIMONI === */
        #testimoni { background: linear-gradient(135deg, var(--liny-black) 0%, var(--liny-soft) 100%); color: #fff; }
        #testimoni .section-title { color: #fff; }
        #testimoni .section-subtitle { color: rgba(255, 255, 255, 0.7); }
        #testimoni .section-eyebrow { color: var(--liny-gold); }
        .testimoni-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(201, 169, 97, 0.2);
            border-radius: 16px;
            padding: 2rem 1.75rem;
            height: 100%;
            text-align: center;
        }
        .testimoni-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--liny-gold);
            margin: 0 auto 1rem;
            background: var(--liny-cream);
        }
        .testimoni-photo-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--liny-gold), var(--liny-gold-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0 auto 1rem;
        }
        .testimoni-stars { color: var(--liny-gold); font-size: 1rem; margin-bottom: 0.75rem; }
        .testimoni-text {
            font-style: italic;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.25rem;
        }
        .testimoni-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            color: var(--liny-gold-soft);
            margin-bottom: 0.15rem;
        }
        .testimoni-role { font-size: 0.8rem; color: rgba(255, 255, 255, 0.6); }

        /* === KONTAK === */
        #kontak { background: var(--liny-cream); }
        .contact-card {
            background: #fff;
            border-radius: 14px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            height: 100%;
        }
        .contact-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--liny-cream);
            color: var(--liny-gold-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        .contact-label {
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--liny-muted);
            margin-bottom: 0.5rem;
        }
        .contact-value {
            font-size: 1rem;
            color: var(--liny-text);
            line-height: 1.6;
        }
        .contact-value a { color: var(--liny-gold-dark); font-weight: 500; }
        .contact-value a:hover { color: var(--liny-gold); }
        .map-wrapper {
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 100%;
            min-height: 360px;
        }
        .map-wrapper iframe { width: 100%; height: 100%; border: 0; min-height: 360px; }

        .social-icons { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1rem; }
        .social-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--liny-cream);
            color: var(--liny-gold-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.25s;
        }
        .social-icon:hover {
            background: var(--liny-gold);
            color: var(--liny-black);
            transform: translateY(-3px);
        }
        .social-icon.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

        /* === FOOTER === */
        .site-footer {
            background: var(--liny-black);
            color: rgba(255, 255, 255, 0.7);
            padding: 3rem 0 1.5rem;
        }
        .footer-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            letter-spacing: 4px;
            color: var(--liny-gold-soft);
            margin-bottom: 0.5rem;
        }
        .footer-tagline { font-size: 0.85rem; letter-spacing: 2px; color: rgba(255, 255, 255, 0.5); }
        .footer-divider { width: 60px; height: 1px; background: var(--liny-gold); margin: 1.5rem auto; }
        .footer-copy { font-size: 0.8rem; color: rgba(255, 255, 255, 0.4); margin: 0; }

        /* === BACK TO TOP === */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--liny-gold);
            color: var(--liny-black);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 6px 18px rgba(201, 169, 97, 0.5);
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 1020;
        }
        .back-to-top.show { opacity: 1; visibility: visible; }
        .back-to-top:hover { background: var(--liny-gold-dark); }

        /* === FLOATING WA BUTTON === */
        .wa-float {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #25D366;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 6px 18px rgba(37, 211, 102, 0.5);
            z-index: 1020;
            transition: all 0.3s;
        }
        .wa-float:hover { background: #1ebd5a; color: #fff; transform: scale(1.08); }
        .wa-float.disabled { opacity: 0.5; pointer-events: none; }

        /* === MODAL === */
        .modal-content {
            background: var(--liny-dark);
            border: 1px solid rgba(201, 169, 97, 0.3);
        }
        .modal-body { padding: 0; }
        .modal-body img { width: 100%; height: auto; display: block; }
        .modal-info { padding: 1.5rem; color: #fff; }
        .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }

        /* === RESPONSIVE === */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(15, 15, 15, 0.98);
                padding: 1rem;
                border-radius: 10px;
                margin-top: 0.5rem;
            }
            .navbar-nav { padding: 0.5rem 0; }
            .btn-reservasi-nav { margin-top: 0.5rem; }
        }
        @media (max-width: 768px) {
            section { padding: 3.5rem 0; }
            .hero { min-height: 90vh; padding: 7rem 0 4rem; }
            .about-image, .about-image-placeholder { height: 320px; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .wa-float { width: 50px; height: 50px; bottom: 20px; left: 20px; font-size: 1.4rem; }
            .back-to-top { width: 42px; height: 42px; bottom: 20px; right: 20px; }
        }
        @media (max-width: 480px) {
            .gallery-grid { grid-template-columns: 1fr; }
            .hero-buttons .btn { width: 100%; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-liny" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#home">
                <img src="{{ asset('assets/images/logo.jpeg') }}" alt="LINY Salon">
                <span class="brand-text d-none d-sm-inline">LINY</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                    @if($galleries->count() > 0)
                    <li class="nav-item"><a class="nav-link" href="#galeri">Galeri</a></li>
                    @endif
                    @if($videos->count() > 0)
                    <li class="nav-item"><a class="nav-link" href="#video">Video</a></li>
                    @endif
                    @if($testimonis->count() > 0)
                    <li class="nav-item"><a class="nav-link" href="#testimoni">Testimoni</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                </ul>
                @if($reservasiUrl)
                <a href="{{ $reservasiUrl }}" target="_blank" class="btn-reservasi-nav">
                    <i class="bi bi-whatsapp me-1"></i> Reservasi
                </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero {{ $konfigurasi->hero_image_url ? 'has-bg-image' : '' }}" id="home"
        @if($konfigurasi->hero_image_url) style="background-image: url('{{ $konfigurasi->hero_image_url }}');" @endif>
        <div class="container">
            <div class="hero-content">
                <span class="hero-eyebrow">Hair & Beauty</span>
                @php
                    $rawTitle = $konfigurasi->hero_title ?: ($konfigurasi->nama_perusahaan ?: 'LINY Salon');
                    // Split title for styling: first word normal, rest as gold italic
                    $words = explode(' ', $rawTitle, 2);
                    $titleFirst = $words[0];
                    $titleRest = $words[1] ?? '';
                @endphp
                <h1>
                    {{ $titleFirst }}
                    @if($titleRest) <span class="gold">{{ $titleRest }}</span> @endif
                </h1>
                <p class="hero-subtitle">
                    {{ $konfigurasi->hero_subtitle ?: 'Tempat di mana keanggunan bertemu setiap helai rambut Anda — kecantikan Anda, keahlian kami.' }}
                </p>
                <div class="hero-buttons">
                    @if($reservasiUrl)
                    <a href="{{ $reservasiUrl }}" target="_blank" class="btn-gold">
                        <i class="bi bi-calendar-check me-2"></i>Reservasi Sekarang
                    </a>
                    @endif
                    <a href="#layanan" class="btn-outline-gold">Lihat Layanan</a>
                </div>
            </div>
        </div>
        <a href="#tentang" class="hero-scroll">
            Scroll
            <span class="mouse"></span>
        </a>
    </section>

    <!-- TENTANG -->
    <section id="tentang">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-divider"><i class="bi bi-scissors"></i></div>
                <p class="section-eyebrow">Tentang Kami</p>
                <h2 class="section-title">{{ $konfigurasi->about_title ?: 'Cerita di Balik LINY' }}</h2>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    @if($konfigurasi->about_image_url)
                        <img src="{{ $konfigurasi->about_image_url }}" alt="Tentang {{ $konfigurasi->nama_perusahaan }}" class="about-image">
                    @else
                        <div class="about-image-placeholder">
                            <i class="bi bi-scissors"></i>
                        </div>
                    @endif
                </div>
                <div class="col-lg-6">
                    <div class="about-text">
                        @if($konfigurasi->about_text)
                            {!! nl2br(e($konfigurasi->about_text)) !!}
                        @else
                            <p>
                                <strong>{{ $konfigurasi->nama_perusahaan ?: 'LINY Salon' }}</strong> adalah salon & barber profesional yang berdedikasi untuk memberikan pengalaman kecantikan terbaik. Kami memadukan teknik modern dengan sentuhan personal untuk hasil yang sesuai dengan gaya dan karakter setiap pelanggan.
                            </p>
                            <p>
                                Tim stylist kami yang berpengalaman siap membantu Anda — dari potong rambut, coloring, hingga perawatan premium. Setiap kunjungan adalah momen relaksasi dan transformasi.
                            </p>
                            <p>
                                <em>"Where elegance meets every strand — your beauty, our craft."</em>
                            </p>
                        @endif
                    </div>
                    @if($konfigurasi->jam_buka || $konfigurasi->jam_tutup)
                    <div class="mt-3 p-3" style="background: var(--liny-cream); border-radius: 10px; border-left: 3px solid var(--liny-gold);">
                        <strong style="color: var(--liny-gold-dark);"><i class="bi bi-clock me-2"></i>Jam Operasional</strong>
                        <div class="mt-1" style="color: #555;">
                            @if($konfigurasi->jam_buka) {{ $konfigurasi->jam_buka }} @endif
                            @if($konfigurasi->jam_buka && $konfigurasi->jam_tutup) &mdash; @endif
                            @if($konfigurasi->jam_tutup) {{ $konfigurasi->jam_tutup }} @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- LAYANAN -->
    <section id="layanan">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-divider"><i class="bi bi-stars"></i></div>
                <p class="section-eyebrow">Layanan & Harga</p>
                <h2 class="section-title">Solusi Kecantikan Anda</h2>
                <p class="section-subtitle">Pilih layanan terbaik dari stylist profesional kami. Klik untuk reservasi via WhatsApp.</p>
            </div>

            @if($layanans->count() > 0)
                @foreach($layanans as $kategori => $items)
                    @if($kategori)
                        <h3 class="service-category-title">{{ ucfirst($kategori) }}</h3>
                    @endif
                    <div class="row g-4 mb-4">
                        @foreach($items as $layanan)
                            @php
                                $serviceWaUrl = app(\App\Http\Controllers\LandingController::class)->serviceWaUrl($layanan->nama, $layanan->harga_default);
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="service-card">
                                    <div class="service-icon"><i class="bi bi-scissors"></i></div>
                                    <h4 class="service-name">{{ $layanan->nama }}</h4>
                                    @if($layanan->deskripsi)
                                        <p class="service-desc">{{ Str::limit($layanan->deskripsi, 90) }}</p>
                                    @endif
                                    <div class="service-price">{{ rupiah($layanan->harga_default) }}</div>
                                    @if($serviceWaUrl)
                                    <a href="{{ $serviceWaUrl }}" target="_blank" class="btn btn-service">
                                        <i class="bi bi-whatsapp me-1"></i> Pesan
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-3">Belum ada layanan yang ditampilkan.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- GALERI -->
    @if($galleries->count() > 0)
    <section id="galeri">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-divider"><i class="bi bi-images"></i></div>
                <p class="section-eyebrow">Galeri</p>
                <h2 class="section-title">Hasil Karya Kami</h2>
                <p class="section-subtitle">Karya terbaik dari tim profesional kami untuk pelanggan-pelanggan kami.</p>
            </div>
            <div class="gallery-grid">
                @foreach($galleries as $gallery)
                <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#galleryModal{{ $gallery->id }}">
                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->judul ?: 'Galeri' }}" loading="lazy">
                    @if($gallery->judul || $gallery->deskripsi)
                    <div class="gallery-item-overlay">
                        <div>
                            @if($gallery->judul)<h5 class="gallery-item-title">{{ $gallery->judul }}</h5>@endif
                            @if($gallery->deskripsi)<p class="gallery-item-desc">{{ Str::limit($gallery->deskripsi, 80) }}</p>@endif
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Gallery Modals -->
    @foreach($galleries as $gallery)
    <div class="modal fade" id="galleryModal{{ $gallery->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->judul ?: 'Galeri' }}">
                    @if($gallery->judul || $gallery->deskripsi)
                    <div class="modal-info">
                        @if($gallery->judul)<h4 class="serif" style="color: var(--liny-gold-soft);">{{ $gallery->judul }}</h4>@endif
                        @if($gallery->deskripsi)<p style="color: rgba(255,255,255,0.8); margin-bottom: 0;">{{ $gallery->deskripsi }}</p>@endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif

    <!-- VIDEO -->
    @if($videos->count() > 0)
    <section id="video">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-divider"><i class="bi bi-play-circle"></i></div>
                <p class="section-eyebrow">Video</p>
                <h2 class="section-title">Lihat Kami Beraksi</h2>
                <p class="section-subtitle">Cuplikan proses dan hasil karya tim kami.</p>
            </div>
            <div class="row g-4">
                @foreach($videos as $video)
                <div class="col-md-6">
                    <div class="video-card">
                        <div class="video-wrapper">
                            @if($video->tipe === 'youtube' && $video->embed_url)
                                <iframe src="{{ $video->embed_url }}" title="{{ $video->judul }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                            @elseif($video->tipe === 'upload' && $video->video_file_url)
                                <video controls preload="metadata" poster="{{ $video->thumbnail_url }}">
                                    <source src="{{ $video->video_file_url }}" type="video/mp4">
                                    Browser Anda tidak mendukung video.
                                </video>
                            @endif
                        </div>
                        <div class="video-info">
                            <h4 class="video-title">{{ $video->judul }}</h4>
                            @if($video->deskripsi)<p class="video-desc">{{ $video->deskripsi }}</p>@endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- TESTIMONI -->
    @if($testimonis->count() > 0)
    <section id="testimoni">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-divider"><i class="bi bi-chat-quote"></i></div>
                <p class="section-eyebrow">Testimoni</p>
                <h2 class="section-title">Apa Kata Mereka</h2>
                <p class="section-subtitle">Cerita pengalaman pelanggan kami.</p>
            </div>

            @if($testimonis->count() === 1)
                <div class="row justify-content-center"><div class="col-md-8 col-lg-6">
                    @php $t = $testimonis->first(); @endphp
                    <div class="testimoni-card">
                        @if($t->foto_url)
                            <img src="{{ $t->foto_url }}" alt="{{ $t->nama }}" class="testimoni-photo">
                        @else
                            <div class="testimoni-photo-placeholder">{{ mb_substr($t->nama, 0, 1) }}</div>
                        @endif
                        <div class="testimoni-stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $t->rating)<i class="bi bi-star-fill"></i>@else<i class="bi bi-star"></i>@endif
                            @endfor
                        </div>
                        <p class="testimoni-text">"{{ $t->pesan }}"</p>
                        <h5 class="testimoni-name">{{ $t->nama }}</h5>
                        @if($t->jabatan)<small class="testimoni-role">{{ $t->jabatan }}</small>@endif
                    </div>
                </div></div>
            @else
            <div id="testimoniCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner">
                    @foreach($testimonis as $i => $t)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        <div class="row justify-content-center"><div class="col-md-8 col-lg-6">
                            <div class="testimoni-card">
                                @if($t->foto_url)
                                    <img src="{{ $t->foto_url }}" alt="{{ $t->nama }}" class="testimoni-photo">
                                @else
                                    <div class="testimoni-photo-placeholder">{{ mb_substr($t->nama, 0, 1) }}</div>
                                @endif
                                <div class="testimoni-stars">
                                    @for($s = 1; $s <= 5; $s++)
                                        @if($s <= $t->rating)<i class="bi bi-star-fill"></i>@else<i class="bi bi-star"></i>@endif
                                    @endfor
                                </div>
                                <p class="testimoni-text">"{{ $t->pesan }}"</p>
                                <h5 class="testimoni-name">{{ $t->nama }}</h5>
                                @if($t->jabatan)<small class="testimoni-role">{{ $t->jabatan }}</small>@endif
                            </div>
                        </div></div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button class="btn btn-sm" type="button" data-bs-target="#testimoniCarousel" data-bs-slide="prev" style="color: var(--liny-gold); border: 1px solid var(--liny-gold); border-radius: 50px;">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm" type="button" data-bs-target="#testimoniCarousel" data-bs-slide="next" style="color: var(--liny-gold); border: 1px solid var(--liny-gold); border-radius: 50px;">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    <!-- KONTAK -->
    <section id="kontak">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-divider"><i class="bi bi-geo-alt"></i></div>
                <p class="section-eyebrow">Kontak & Lokasi</p>
                <h2 class="section-title">Hubungi Kami</h2>
                <p class="section-subtitle">Kunjungi salon kami atau hubungi untuk reservasi.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="contact-card">
                        @if($konfigurasi->alamat)
                        <div class="mb-4">
                            <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <p class="contact-label">Alamat</p>
                            <div class="contact-value">{{ $konfigurasi->alamat }}</div>
                        </div>
                        @endif

                        @if($konfigurasi->no_telepon)
                        <div class="mb-4">
                            <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                            <p class="contact-label">Telepon</p>
                            <div class="contact-value"><a href="tel:{{ $konfigurasi->no_telepon }}">{{ $konfigurasi->no_telepon }}</a></div>
                        </div>
                        @endif

                        @if($konfigurasi->jam_buka || $konfigurasi->jam_tutup)
                        <div class="mb-4">
                            <div class="contact-icon"><i class="bi bi-clock-fill"></i></div>
                            <p class="contact-label">Jam Buka</p>
                            <div class="contact-value">
                                @if($konfigurasi->jam_buka) {{ $konfigurasi->jam_buka }} @endif
                                @if($konfigurasi->jam_buka && $konfigurasi->jam_tutup) &mdash; @endif
                                @if($konfigurasi->jam_tutup) {{ $konfigurasi->jam_tutup }} @endif
                            </div>
                        </div>
                        @endif

                        @if($waPhone)
                        <div class="mb-4">
                            <div class="contact-icon" style="background: #25D366; color: #fff;"><i class="bi bi-whatsapp"></i></div>
                            <p class="contact-label">WhatsApp</p>
                            @if($reservasiUrl)
                            <a href="{{ $reservasiUrl }}" target="_blank" class="btn-gold d-inline-flex align-items-center" style="padding: 0.75rem 1.5rem; font-size: 0.85rem;">
                                <i class="bi bi-whatsapp me-2"></i>Chat Sekarang
                            </a>
                            @endif
                        </div>
                        @endif

                        <div>
                            <p class="contact-label">Ikuti Kami</p>
                            <div class="social-icons">
                                @if($konfigurasi->instagram_url)
                                <a href="{{ $konfigurasi->instagram_url }}" target="_blank" class="social-icon" title="Instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                                @else
                                <span class="social-icon disabled" title="Belum diatur"><i class="bi bi-instagram"></i></span>
                                @endif
                                @if($konfigurasi->tiktok_url)
                                <a href="{{ $konfigurasi->tiktok_url }}" target="_blank" class="social-icon" title="TikTok" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                                @else
                                <span class="social-icon disabled" title="Belum diatur"><i class="bi bi-tiktok"></i></span>
                                @endif
                                @if($konfigurasi->facebook_url)
                                <a href="{{ $konfigurasi->facebook_url }}" target="_blank" class="social-icon" title="Facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                                @else
                                <span class="social-icon disabled" title="Belum diatur"><i class="bi bi-facebook"></i></span>
                                @endif
                                @if($konfigurasi->youtube_url)
                                <a href="{{ $konfigurasi->youtube_url }}" target="_blank" class="social-icon" title="YouTube" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                                @else
                                <span class="social-icon disabled" title="Belum diatur"><i class="bi bi-youtube"></i></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    @if($konfigurasi->maps_embed_url)
                    <div class="map-wrapper">
                        <iframe src="{{ $konfigurasi->maps_embed_url }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi {{ $konfigurasi->nama_perusahaan }}"></iframe>
                    </div>
                    @else
                    <div class="contact-card d-flex align-items-center justify-content-center text-center" style="min-height: 360px; background: linear-gradient(135deg, var(--liny-dark), var(--liny-soft)); color: #fff;">
                        <div>
                            <i class="bi bi-map" style="font-size: 3rem; color: var(--liny-gold);"></i>
                            <p class="mt-3 mb-0" style="color: rgba(255,255,255,0.7);">Maps belum diatur di panel admin.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <div class="text-center">
                <img src="{{ asset('assets/images/logo.jpeg') }}" alt="LINY" style="width: 60px; height: 60px; border-radius: 50%; border: 2px solid var(--liny-gold); background: #fff; padding: 4px; margin-bottom: 1rem;">
                <h5 class="footer-brand">{{ strtoupper($konfigurasi->nama_perusahaan ?: 'LINY SALON') }}</h5>
                <p class="footer-tagline">hair and beauty</p>
                <div class="footer-divider"></div>
                <p class="footer-copy">&copy; {{ date('Y') }} {{ $konfigurasi->nama_perusahaan ?: 'LINY Salon' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- FLOATING WA -->
    @if($reservasiUrl)
    <a href="{{ $reservasiUrl }}" target="_blank" class="wa-float" aria-label="Chat WhatsApp" title="Chat WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
    @endif

    <!-- BACK TO TOP -->
    <button class="back-to-top" id="backToTop" aria-label="Kembali ke atas" title="Kembali ke atas">
        <i class="bi bi-chevron-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        const nav = document.getElementById('mainNav');
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 80) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
            if (window.scrollY > 400) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        // Back to top
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Close mobile nav on link click
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const collapse = document.querySelector('.navbar-collapse');
                if (collapse.classList.contains('show')) {
                    new bootstrap.Collapse(collapse).hide();
                }
            });
        });
    </script>
</body>
</html>
