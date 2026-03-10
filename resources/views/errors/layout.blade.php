<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SRPS Clinical</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800;900&family=Noto+Kufi+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f9ff 0%, #ede9fe 50%, #f0fdfa 100%);
            font-family: 'Noto Kufi Arabic', 'Outfit', sans-serif;
            direction: rtl;
            overflow: hidden;
        }

        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 520px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-code {
            font-family: 'Outfit', sans-serif;
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.04em;
            background: @yield('gradient');
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.25rem;
            position: relative;
        }

        .error-code::after {
            content: '';
            position: absolute;
            bottom: 0.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            border-radius: 2px;
            background: @yield('gradient');
            opacity: 0.4;
        }

        .error-icon {
            margin: 1.5rem auto;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: @yield('icon-bg');
        }

        .error-icon svg {
            width: 40px;
            height: 40px;
            color: @yield('icon-color');
        }

        .error-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .error-subtitle {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.7rem 1.5rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Noto Kufi Arabic', sans-serif;
            text-decoration: none;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: @yield('btn-bg');
            color: #fff;
            border-color: @yield('btn-bg');
        }

        .btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px @yield('btn-shadow');
        }

        .btn-outline {
            background: transparent;
            color: #475569;
            border-color: #cbd5e1;
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }

        .decorative-dots {
            position: fixed;
            opacity: 0.04;
            z-index: 0;
        }

        .decorative-dots.top-right { top: -40px; right: -40px; }
        .decorative-dots.bottom-left { bottom: -40px; left: -40px; }

        .error-container { position: relative; z-index: 1; }
    </style>
</head>
<body>
    <svg class="decorative-dots top-right" width="300" height="300" viewBox="0 0 300 300" fill="currentColor">
        <circle cx="50" cy="50" r="4"/><circle cx="100" cy="50" r="4"/><circle cx="150" cy="50" r="4"/><circle cx="200" cy="50" r="4"/><circle cx="250" cy="50" r="4"/>
        <circle cx="50" cy="100" r="4"/><circle cx="100" cy="100" r="4"/><circle cx="150" cy="100" r="4"/><circle cx="200" cy="100" r="4"/><circle cx="250" cy="100" r="4"/>
        <circle cx="50" cy="150" r="4"/><circle cx="100" cy="150" r="4"/><circle cx="150" cy="150" r="4"/><circle cx="200" cy="150" r="4"/><circle cx="250" cy="150" r="4"/>
        <circle cx="50" cy="200" r="4"/><circle cx="100" cy="200" r="4"/><circle cx="150" cy="200" r="4"/><circle cx="200" cy="200" r="4"/><circle cx="250" cy="200" r="4"/>
        <circle cx="50" cy="250" r="4"/><circle cx="100" cy="250" r="4"/><circle cx="150" cy="250" r="4"/><circle cx="200" cy="250" r="4"/><circle cx="250" cy="250" r="4"/>
    </svg>
    <svg class="decorative-dots bottom-left" width="300" height="300" viewBox="0 0 300 300" fill="currentColor">
        <circle cx="50" cy="50" r="4"/><circle cx="100" cy="50" r="4"/><circle cx="150" cy="50" r="4"/><circle cx="200" cy="50" r="4"/><circle cx="250" cy="50" r="4"/>
        <circle cx="50" cy="100" r="4"/><circle cx="100" cy="100" r="4"/><circle cx="150" cy="100" r="4"/><circle cx="200" cy="100" r="4"/><circle cx="250" cy="100" r="4"/>
        <circle cx="50" cy="150" r="4"/><circle cx="100" cy="150" r="4"/><circle cx="150" cy="150" r="4"/><circle cx="200" cy="150" r="4"/><circle cx="250" cy="150" r="4"/>
    </svg>

    <div class="error-container">
        <div class="error-code">@yield('code')</div>

        <div class="error-icon">
            @yield('icon')
        </div>

        <h1 class="error-title">@yield('title')</h1>
        <p class="error-subtitle">@yield('message')</p>

        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                العودة للرئيسية
            </a>
            <a href="javascript:history.back()" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                الرجوع
            </a>
        </div>
    </div>
</body>
</html>
