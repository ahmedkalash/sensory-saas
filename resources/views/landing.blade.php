<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRPA Clinical - المقياس الحسي</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Noto Kufi Arabic', sans-serif;
            background-color: #f8fafc; /* Very light slate/blueish background for that clean minimal look */
            color: #334155;
        }
        .font-en {
            font-family: 'Outfit', sans-serif;
        }

        /* Soft Drop Shadows (Option 7 Aesthetic) */
        .shadow-soft {
            box-shadow: 0 10px 40px -10px rgba(14, 116, 144, 0.08);
        }
        .shadow-card {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .shadow-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px -5px rgba(14, 116, 144, 0.1);
        }
    </style>
</head>
<body class="antialiased selection:bg-cyan-200 selection:text-cyan-900">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="/icon.png" alt="SRPA Logo" class="h-10 w-10 rounded-xl shadow-sm">
                    <div class="flex flex-col">
                        <span class="font-en font-bold text-xl text-slate-800 tracking-tight leading-none">SRPA Clinical</span>
                        <span class="text-xs text-cyan-600 font-medium mt-1">مِقْيَاسٌ حِسّيٌ</span>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 space-x-reverse items-center">
                    <a href="#about" class="text-slate-500 hover:text-cyan-600 font-medium transition-colors">عن المقياس</a>
                    <a href="#features" class="text-slate-500 hover:text-cyan-600 font-medium transition-colors">المميزات</a>
                    <a href="#screenshots" class="text-slate-500 hover:text-cyan-600 font-medium transition-colors">واجهة التطبيق</a>
                    <a href="#creator" class="text-slate-500 hover:text-cyan-600 font-medium transition-colors">عن المبتكر</a>
                </div>

                <!-- Authentication CTAs -->
                <div class="flex items-center gap-4">
                    <a href="/app/login" class="hidden sm:inline-flex text-slate-600 hover:text-cyan-700 font-medium px-4 py-2 transition-colors">
                        تسجيل الدخول
                    </a>
                    <a href="/app/register" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2.5 rounded-full font-medium shadow-md shadow-cyan-600/20 transition-all active:scale-95">
                        إنشاء حساب
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-cyan-100/50 blur-3xl opacity-60 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 w-80 h-80 rounded-full bg-emerald-100/50 blur-3xl opacity-60 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <!-- Text Content -->
                <div class="text-center lg:text-right">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 text-cyan-700 text-sm font-semibold mb-6 border border-cyan-100">
                        <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                        الإصدار السحابي الجديد
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-slate-800 leading-tight mb-6">
                        مقياس أنماط <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-emerald-500">الاستجابة الحسية</span>
                    </h1>
                    <p class="text-lg text-slate-500 mb-8 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                       <b>أول مقياس إلكتروني عربي مقنن لقياس أنماط الاستجابة الحسية</b>
                        <br>
                        أداة احترافية مصممة لمساعدة الأخصائيين والأطباء في تقييم ومعالجة الاضطرابات الحسية لدى الأطفال عبر 7 مقاييس دقيقة وتقارير آلية شاملة.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="/app/register" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-900 text-white px-8 py-3.5 rounded-xl font-medium shadow-soft transition-all text-center flex justify-center items-center gap-2">
                            ابدأ الاستخدام الآن
                            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="/app/login" class="w-full sm:w-auto bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-8 py-3.5 rounded-xl font-medium shadow-sm transition-all text-center">
                            تسجيل الدخول
                        </a>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="relative mx-auto w-full max-w-lg lg:max-w-none">
                    <div class="relative bg-white rounded-3xl p-2 shadow-soft border border-slate-100/50">
                        <img src="/icon.png" alt="SRPA Dashboard Mockup" class="rounded-2xl w-full object-cover">

                        <!-- Floating Badges for aesthetics -->
                        <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-2xl shadow-card border border-slate-50 flex items-center gap-4 hidden sm:flex">
                            <div class="bg-emerald-100 text-emerald-600 p-2.5 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">تقارير دقيقة</p>
                                <p class="text-xs text-slate-500">جاهزة للطباعة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">تقييم شامل لجميع الحواس</h2>
                <div class="h-1 w-20 bg-cyan-500 mx-auto rounded-full mb-6"></div>
                <p class="text-slate-500 leading-relaxed text-lg">
                    يغطي المقياس الحسي 7 مجالات رئيسية للتطور الحسي، ويحلل البيانات بدقة لاستخراج نقاط الضعف وتقديم التوصيات والأنشطة المناسبة لكل حالة.
                </p>
            </div>

            <!-- The 7 Scales Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Scale 1 -->
                <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100 shadow-card hover:border-cyan-100 transition-colors">
                    <div class="w-14 h-14 mx-auto bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">المقياس البصري</h3>
                </div>
                <!-- Scale 2 -->
                <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100 shadow-card hover:border-cyan-100 transition-colors">
                    <div class="w-14 h-14 mx-auto bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">المقياس السمعي</h3>
                </div>
                <!-- Scale 3 -->
                <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100 shadow-card hover:border-cyan-100 transition-colors">
                    <div class="w-14 h-14 mx-auto bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">المقياس اللمسي</h3>
                </div>
                <!-- Scale 4 -->
                <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100 shadow-card hover:border-cyan-100 transition-colors">
                    <div class="w-14 h-14 mx-auto bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">المقياس الدهليزي</h3>
                </div>
                <!-- Scale 5 -->
                <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100 shadow-card hover:border-cyan-100 transition-colors">
                    <div class="w-14 h-14 mx-auto bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v8l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">الاستقبال العميق</h3>
                </div>
                <!-- Scale 6 -->
                <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100 shadow-card hover:border-cyan-100 transition-colors">
                    <div class="w-14 h-14 mx-auto bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">المقياس الشمي</h3>
                </div>
                <!-- Scale 7 -->
                <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100 shadow-card hover:border-cyan-100 transition-colors lg:col-span-2">
                    <div class="w-14 h-14 mx-auto bg-cyan-100 text-cyan-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">المقياس التذوقي</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">أدوات متطورة لتقييم أدق</h2>
                <div class="h-1 w-20 bg-emerald-500 mx-auto rounded-full mb-6"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-3xl p-8 shadow-card border border-slate-100">
                    <div class="w-12 h-12 bg-cyan-50 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">تقارير PDF آلية</h3>
                    <p class="text-slate-500 leading-relaxed">
                        استخراج تقارير احترافية بنقرة واحدة، تتضمن النتائج الإحصائية، نقاط الضعف، والتوصيات والأنشطة العلاجية الجاهزة للطباعة والمشاركة مع الوالدين.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white rounded-3xl p-8 shadow-card border border-slate-100">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">أمان البيانات والخصوصية</h3>
                    <p class="text-slate-500 leading-relaxed">
                        تشفير عالي المستوى للبيانات الشخصية للمرضى (PII) مثل الأسماء وتواريخ الميلاد لضمان الامتثال لمعايير السرية الطبية بشكل كامل.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white rounded-3xl p-8 shadow-card border border-slate-100">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">تحليل الأبعاد الأربعة</h3>
                    <p class="text-slate-500 leading-relaxed">
                        نظام ذكي يحلل الإجابات بناءً على 4 أبعاد سلوكية (القصور، الفرط، التجنب، والسعي الحسي) لحساب شدة الاضطراب بشكل دقيق وعلمي.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Screenshots Section -->
    <section id="screenshots" class="py-20 bg-white overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">واجهة مستخدم احترافية وبسيطة</h2>
                <div class="h-1 w-20 bg-cyan-500 mx-auto rounded-full mb-6"></div>
                <p class="text-slate-500 leading-relaxed text-lg">
                    تصميم عصري يسهل عليك إدارة مرضاك، إجراء التقييمات عبر معالج خطوات سهل (Wizard)، والوصول السريع لجميع المعلومات بمرونة.
                </p>
            </div>

            <!-- Conceptual App Window -->
            <div class="max-w-5xl mx-auto">
                <div class="bg-white rounded-2xl shadow-soft border border-slate-200/60 overflow-hidden">
                    <!-- Fake Window Header -->
                    <div class="bg-slate-50 border-b border-slate-100 px-4 py-3 flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    </div>
                    <!-- Fake App Content (We use styling to make it look like an app dashboard) -->
                    <div class="bg-slate-50/50 p-6 sm:p-10 flex flex-col md:flex-row gap-8">
                        <!-- Sidebar mockup -->
                        <div class="w-full md:w-64 bg-white p-4 rounded-xl border border-slate-100 shadow-sm hidden md:block">
                            <div class="h-8 bg-slate-100 rounded mb-8 w-3/4"></div>
                            <div class="space-y-4">
                                <div class="h-10 bg-cyan-50 rounded-lg border-r-4 border-cyan-500"></div>
                                <div class="h-10 bg-slate-50 rounded-lg"></div>
                                <div class="h-10 bg-slate-50 rounded-lg"></div>
                            </div>
                        </div>
                        <!-- Main content mockup -->
                        <div class="flex-1 space-y-6">
                            <div class="h-12 bg-white border border-slate-100 rounded-xl shadow-sm flex items-center px-4">
                                <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="h-24 bg-white border border-slate-100 rounded-xl shadow-sm"></div>
                                <div class="h-24 bg-white border border-slate-100 rounded-xl shadow-sm"></div>
                                <div class="h-24 bg-white border border-slate-100 rounded-xl shadow-sm"></div>
                            </div>
                            <div class="h-64 bg-white border border-slate-100 rounded-xl shadow-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Creator Profile Section -->
    <section id="creator" class="py-20 bg-slate-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_bottom_right,_var(--tw-gradient-stops))] from-cyan-400 via-transparent to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="bg-slate-700/40 border border-slate-600/50 rounded-3xl p-8 sm:p-12 backdrop-blur-md max-w-4xl mx-auto">
                <div class="flex flex-col md:flex-row items-center gap-10">
                    <div class="w-32 h-32 md:w-40 md:h-40 shrink-0 bg-slate-600 rounded-full border-4 border-slate-500 flex items-center justify-center overflow-hidden shadow-xl">
                        <img src="/dr-ahmed.jpg" alt="د. أحمد خليف" class="w-full h-full object-cover">
                    </div>
                    <div class="text-center md:text-right flex-1">
                        <div class="inline-block px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 text-sm font-medium mb-4">
                            معد القائمة
                        </div>
                        <h2 class="text-3xl font-bold mb-2">د.أَحْمَدٌ خُلِيفٌ</h2>
                        <p class="text-cyan-400 font-medium mb-6">دكتوراه علم النفس جامعة عين شمس واختصاصي التكامل الحسي</p>
                        <p class="text-slate-300 leading-relaxed text-lg">
                            تم تصميم هذا المقياس حصيلة سنوات من الخبرة السريرية والبحث العلمي في مجال التكامل الحسي. نهدف إلى توفير أداة تقييم دقيقة وعملية تساعد الزملاء الأخصائيين على فهم التحديات التي يواجهها الأطفال ووضع خطط علاجية مبنية على أدلة وتقارير واضحة.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <img src="/icon.png" alt="SRPA Logo" class="h-8 w-8 rounded-lg opacity-80">
                <span class="font-en font-bold text-slate-700">SRPA Clinical</span>
            </div>

            <p class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} SRPA Clinical. جميع الحقوق محفوظة.
            </p>

            <div class="flex gap-4">
                <a href="/app/login" class="text-slate-400 hover:text-cyan-600 transition-colors">تسجيل الدخول</a>
                <span class="text-slate-300">|</span>
                <a href="/app/register" class="text-slate-400 hover:text-cyan-600 transition-colors">إنشاء حساب</a>
            </div>
        </div>
    </footer>

</body>
</html>
