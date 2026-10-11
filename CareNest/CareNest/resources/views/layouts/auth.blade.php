<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CareNest - Maternal & Pediatric Care System')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --primary-emerald: #00e676;
            --primary-dark-emerald: #00c853;
            --forest-green: #1b4d3e;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --accent-pink: #f8bbd0;
            --accent-pink-dark: #880e4f;
            --bg-gradient: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 35%, #e3f2fd 70%, #e8eaf6 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            min-height: 100vh;
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Top Navbar */
        .auth-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 60px;
            z-index: 10;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-heading);
            font-size: 24px;
            font-weight: 800;
            color: var(--forest-green);
            text-decoration: none;
        }

        .brand-logo i {
            color: #00c853;
            font-size: 22px;
        }

        .header-link {
            font-size: 15px;
            font-weight: 600;
            color: var(--forest-green);
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .header-link:hover {
            opacity: 0.8;
        }

        /* Layout Container */
        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 40px 60px;
            max-width: 1350px;
            margin: 0 auto;
            width: 100%;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 32px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.05);
        }

        /* Custom Input Groups */
        .input-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .input-field {
            width: 100%;
            padding: 16px 20px 16px 20px;
            padding-right: 45px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            font-family: var(--font-main);
            font-size: 14px;
            color: var(--text-dark);
            transition: all 0.25s ease;
        }

        .input-field:focus {
            outline: none;
            background: #ffffff;
            border-color: var(--primary-emerald);
            box-shadow: 0 0 0 4px rgba(0, 230, 118, 0.15);
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }

        /* Primary Button */
        .btn-emerald {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #00e676 0%, #00c853 100%);
            border: none;
            border-radius: 20px;
            color: #ffffff;
            font-family: var(--font-heading);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(0, 200, 83, 0.3);
            transition: all 0.3s ease;
        }

        .btn-emerald:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(0, 200, 83, 0.4);
            background: linear-gradient(135deg, #00f07c 0%, #00d659 100%);
        }

        .btn-emerald:active {
            transform: translateY(0);
        }

        /* Soft Pink Button */
        .btn-pink {
            width: 100%;
            padding: 16px;
            background: #f8bbd0;
            border: none;
            border-radius: 20px;
            color: #880e4f;
            font-family: var(--font-heading);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            box-shadow: 0 8px 20px rgba(248, 187, 208, 0.5);
            transition: all 0.3s ease;
        }

        .btn-pink:hover {
            background: #f48fb1;
            transform: translateY(-2px);
        }

        /* Footer */
        .auth-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.5px;
            z-index: 10;
        }

        .footer-links {
            display: flex;
            gap: 24px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: var(--forest-green);
        }

        @media (max-width: 992px) {
            .auth-header, .auth-footer {
                padding: 20px 24px;
            }
            .auth-container {
                padding: 20px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <header class="auth-header">
        <a href="{{ url('/') }}" class="brand-logo">
            <i class="fa-solid fa-leaf"></i> CareNest
        </a>
        @yield('header_action')
    </header>

    <main class="auth-container">
        @yield('content')
    </main>

    <footer class="auth-footer">
        <div>© 2026 CARENEST MATERNAL AND PEDIATRIC MANAGEMENT SYSTEM. ALL RIGHTS RESERVED.</div>
        <div class="footer-links">
            <a href="#">PRIVACY POLICY</a>
            <a href="#">TERMS OF SERVICE</a>
            <a href="#">SUPPORT</a>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
