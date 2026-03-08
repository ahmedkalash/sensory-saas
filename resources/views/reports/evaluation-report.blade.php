@php use App\Enums\Severity; @endphp
<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>تقرير التقييم الشامل للمعالجة الحسية</title>
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1f2937;
            background: #f9fafb;
            direction: rtl;
            padding: 20px;
        }

        /* Container */
        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        /* Card Base */
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
        }

        /* Header Card */
        .header-card {
            border-top: 4px solid #4f46e5;
        }

        .header-title {
            font-size: 18pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }

        .header-subtitle {
            color: #6b7280;
            font-size: 10pt;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f3f4f6;
        }

        /* Patient Info Grid */
        .info-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }

        .info-cell {
            background: #f9fafb;
            padding: 12px 15px;
            border-radius: 8px;
            width: 50%;
            vertical-align: top;
        }

        .info-label {
            display: block;
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 11pt;
            font-weight: bold;
            color: #111827;
        }

        /* Section Heading */
        .section-heading {
            font-size: 14pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 18px;
        }

        /* Summary Table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .summary-table th {
            background: #eef2ff;
            color: #312e81;
            padding: 10px 8px;
            font-weight: bold;
            text-align: center;
            border-bottom: 2px solid #c7d2fe;
        }

        .summary-table td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .summary-table .scale-name {
            text-align: right;
            font-weight: bold;
            color: #1f2937;
            font-size: 10pt;
        }

        /* Severity Badges */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 8pt;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-yellow {
            background: #fef9c3;
            color: #a16207;
        }

        .badge-orange {
            background: #ffedd5;
            color: #c2410c;
        }

        .badge-red {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* Legend */
        .legend {
            margin-top: 15px;
            background: #f9fafb;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #f3f4f6;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }

        .legend-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-left: 4px;
            vertical-align: middle;
        }

        .legend-item {
            margin: 0 8px;
        }

        /* Scale Card */
        .scale-card {
            background: #ffffff;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-right: 4px solid #4f46e5;
            overflow: hidden;
        }

        .scale-card-header {
            background: #f9fafb;
            padding: 12px 20px;
            border-bottom: 1px solid #f3f4f6;
        }

        .scale-card-header h3 {
            font-size: 13pt;
            font-weight: bold;
            color: #1f2937;
        }

        .scale-card-body {
            padding: 20px;
        }

        /* Dimension Block */
        .dimension-block {
            margin-bottom: 18px;
        }

        .dimension-header {
            margin-bottom: 10px;
        }

        .dimension-title {
            font-size: 11pt;
            font-weight: bold;
            color: #374151;
            display: inline;
        }

        .dimension-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
            margin-left: 8px;
        }

        /* Weakness Item */
        .weakness-list {
            padding-right: 10px;
        }

        .weakness-item {
            padding: 4px 0;
            border-bottom: 1px solid #f9fafb;
            font-size: 10pt;
            line-height: 1.4;
        }

        .weakness-text {
            font-size: 11pt;
            color: #1f2937;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        .weakness-recommendation {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 3px;
        }

        .weakness-marker {
            color: #ef4444;
            font-weight: bold;
            margin-left: 5px;
        }

        /* No Weaknesses */
        .no-weakness {
            color: #16a34a;
            font-size: 10pt;
            padding: 10px;
            text-align: center;
            background: #f0fdf4;
            border-radius: 8px;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #9ca3af;
            font-size: 8pt;
            padding: 20px 0;
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
        }

        /* جدول نقاط الضعف الجديد بخطوط الشبكة */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #ffffff;
        }

        .grid-table th,
        .grid-table td {
            border: 1px solid #e5e7eb;
            /* خط رفيع للجدول العادي */
            padding: 8px 6px;
            /* تم تصغير هذا الرقم لتقليل الفراغ */
            vertical-align: middle;
            /* لضبط النص في المنتصف عمودياً وعدم تركيزه في الأعلى */
            text-align: center;
            /* لتوسيط الأرقام والشارات أفقياً */
        }

        .thick-grid th,
        .thick-grid td {
            border: 3px solid #6b7280;
            /* خصيصاً لجدول الملخص */
        }

        .grid-table th {
            background: #eef2ff;
            color: #312e81;
            font-size: 11pt;
        }

        .scale-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 12px;
            border-right: 4px solid #4f46e5;
            padding-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- ======================================== --}}
        {{-- HEADER: Patient Info --}}
        {{-- ======================================== --}}
        <div class="card header-card">
            <div class="header-title">
                {{ $evaluation->title ?: 'تقرير تقييم قائمة أنماط الأستجابة الحسية' }}
            </div>
            <div class="header-subtitle">
                @if(count($measurements) === 1)
                    {{ $measurements[0]['name'] }} - تقرير مفصل
                @else
                    تقرير طبي وتحليلي مفصل لحالة الطفل (شامل)
                @endif
            </div>

            <table class="info-grid">
                <tr>
                    <td class="info-cell">
                        <span class="info-label">اسم الطفل</span>
                        <span class="info-value">{{ $patient->name }}</span>
                    </td>
                    <td class="info-cell">
                        <span class="info-label">النوع - العمر</span>
                        <span class="info-value">{{ $patient->gender }} - {{ $evaluation->child_age }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="info-cell">
                        <span class="info-label">المدرسة - الصف</span>
                        <span class="info-value">{{ $patient->school }} - {{ $patient->grade }}</span>
                    </td>
                    <td class="info-cell">
                        <span class="info-label">تاريخ التطبيق - الأخصائي</span>
                        <span class="info-value">{{ $evaluation->evaluation_date?->format('Y-m-d') }} -
                            {{ $evaluation->specialist_name }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ======================================== --}}
        {{-- SECTION 1: Summary Matrix --}}
        {{-- ======================================== --}}
        <div class="card">
            <div class="section-heading">أولاً: ملخص الدرجات ومستوى الشدة</div>
            <table class="grid-table thick-grid">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align: right;">المقياس الحسّي</th>
                        <th colspan="2">ضعف الاستجابة</th>
                        <th colspan="2">فرط الاستجابة</th>
                        <th colspan="2">المتجنب الحسي</th>
                        <th colspan="2">الساعي وراء المثير</th>
                    </tr>
                    <tr>
                        <th>الدرجة</th>
                        <th>الشدة</th>
                        <th>الدرجة</th>
                        <th>الشدة</th>
                        <th>الدرجة</th>
                        <th>الشدة</th>
                        <th>الدرجة</th>
                        <th>الشدة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($measurements as $measurement)
                        <tr>
                            <td class="scale-name">{{ $measurement['name'] }}</td>
                            @foreach ($measurement['dimensions'] as $dim)
                                <td style="font-weight: bold;">{{ $dim['total_score'] }}</td>
                                <td>
                                    @if ($dim['severity'] === Severity::OK)
                                        <span class="badge badge-green">طبيعي</span>
                                    @elseif ($dim['severity'] === Severity::LOW)
                                        <span class="badge badge-yellow">بسيط</span>
                                    @elseif ($dim['severity'] === Severity::MID)
                                        <span class="badge badge-orange">متوسط</span>
                                    @elseif ($dim['severity'] === Severity::HIGH)
                                        <span class="badge badge-red">شديد</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="page-break"></div>

        {{-- ======================================== --}}
        {{-- SECTION 2: Detailed Weaknesses --}}
        {{-- ======================================== --}}
        <div class="section-heading">ثانياً: التحليل المفصل لنقاط الضعف</div>

        @foreach ($measurements as $index => $measurement)
            @php
                $hasWeaknesses = collect($measurement['dimensions'])->flatMap(fn($d) => $d['weaknesses'])->isNotEmpty();
                $hasObservations = collect($measurement['dimensions'])->flatMap(fn($d) => $d['observations'] ?? [])->isNotEmpty();
            @endphp

            <div class="scale-card">
                <div class="scale-card-header">
                    <h3>{{ $index + 1 }}. {{ $measurement['name'] }}</h3>
                </div>

                <table class="grid-table">
                    <thead>
                        <tr>
                            <th style="width: 30%; text-align: right;">البعد</th>
                            <th style="width: 70%; text-align: right; color: #b91c1c; font-weight: bold;">نقاط الضعف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($measurement['dimensions'] as $dim)
                            <tr>
                                <td style="text-align: right; vertical-align: top; font-weight: bold; width: 30%;">
                                    {{ $dim['name'] }}
                                </td>
                                <td style="text-align: right; vertical-align: top;">
                                    @empty($dim['weaknesses'])
                                        <span class="badge badge-green">لا توجد نقاط ضعف.</span>
                                    @else
                                        @foreach ($dim['weaknesses'] as $weakness)
                                            <div style="margin-bottom: 6px;">- {{ $weakness['question_text'] }}</div>
                                        @endforeach
                                    @endempty
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="scale-card-body">
                    @if($hasObservations)
                        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #cbd5e1;">
                            <div class="weakness-text" style="color: #4f46e5; margin-bottom: 15px; font-size: 13pt;">
                                الملاحظات:
                            </div>
                            @foreach ($measurement['dimensions'] as $dim)
                                @if(!empty($dim['observations']))
                                    <div class="dimension-title"
                                         style="margin-top: 10px; margin-bottom: 8px; display: block; color: #374151;">
                                        {{ $dim['name'] }}:
                                    </div>
                                    @foreach ($dim['observations'] as $obs)
                                        <div class="weakness-item"
                                             style="margin-bottom: 12px; font-size: 10pt; line-height: 1.6; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <div style="color: #1e293b; font-weight: bold; margin-bottom: 6px;">السؤال:
                                                {{ $obs['question_text'] }}</div>
                                            <div style="color: #334155; margin-right: 10px;"><strong
                                                        style="color: #475569;">الملاحظة:</strong> {!! nl2br(e($obs['notes'])) !!}</div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if (!$hasWeaknesses)
                        <div class="no-weakness">لا توجد نقاط ضعف في هذا المقياس.</div>
                    @else
                        <div class="dimension-block">

                            <div class="weakness-list">
                                <h2 style="color: #4f46e5; margin-bottom: 15px;">توصيات لولي الامر:</h2>
                                @foreach ($measurement['dimensions'] as $dim)
                                    <h3 style="color: #4f46e5; margin-bottom: 15px;">{{ $dim['name'] }}</h3>
                                    @foreach ($dim['weaknesses'] as $w)
                                        <div style="margin-bottom: 6px;">
                                            @foreach($w['recommendations'] as $recommendation)
                                                - {{ $recommendation }}
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endforeach

                                <h2 style="color: #4f46e5; margin-bottom: 15px;">أهداف:</h2>
                                @foreach ($measurement['dimensions'] as $dim)
                                    <h3 style="color: #4f46e5; margin-bottom: 15px;">{{ $dim['name'] }}</h3>
                                    @foreach ($dim['weaknesses'] as $w)
                                        <div style="margin-bottom: 6px;">
                                            @foreach($w['goals'] as $goal)
                                                - {{ $goal }}
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endforeach

                                <h2 style="color: #4f46e5; margin-bottom: 15px;">أنشطة:</h2>
                                @foreach ($measurement['dimensions'] as $dim)
                                    <h3 style="color: #4f46e5; margin-bottom: 15px;">{{ $dim['name'] }}</h3>
                                    @foreach ($dim['weaknesses'] as $w)
                                        <div style="margin-bottom: 6px;">
                                            @foreach($w['activities'] as $activity)
                                                - {{ $activity }}
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                    @endif

                </div>


            </div>
        @endforeach

        {{-- Footer --}}
        <div class="footer">
            تم استخراج هذا التقرير آلياً بواسطة نظام التقييم الحسي الإلكتروني © {{ date('Y') }}
        </div>

    </div>
</body>

</html>