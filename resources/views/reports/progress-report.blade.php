@php use App\Enums\Severity; @endphp
<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>تقرير مقارنة التقييمات الشاملة</title>
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

        /* Table Grids */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background: #ffffff;
        }

        .grid-table th,
        .grid-table td {
            border: 1px solid #d1d5db;
            /* Grey border for normal cells */
            padding: 10px 8px;
            vertical-align: middle;
            text-align: center;
        }

        /* Summary Header Styling */
        .grid-table thead th {
            background: #eef2ff;
            color: #312e81;
            font-size: 10pt;
            font-weight: bold;
            border: 2px solid #6b7280;
            /* Thicker border for header */
        }

        .thick-grid {
            border: 2px solid #6b7280;
        }

        .thick-grid td,
        .thick-grid th {
            border: 2px solid #6b7280;
        }

        .scale-name {
            background-color: #f8fafc;
            font-weight: bold;
            color: #1e293b;
            text-align: right !important;
            padding-right: 12px !important;
            font-size: 11pt;
        }

        /* Severity Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
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
            color: #854d0e;
        }

        .badge-orange {
            background: #ffedd5;
            color: #c2410c;
        }

        .badge-red {
            background: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- 1. HEADER SECTION -->
        <div class="card header-card">
            <h1 class="header-title">تقرير مقارنة التقدم</h1>
            <div class="header-subtitle">
                مقارنة بين نتائج التقييم الأساسي، والتقييم اللاحق لبيان مدى الاستجابة للخطة العلاجية.
            </div>

            <table class="info-grid">
                <tr>
                    <td class="info-cell" colspan="2">
                        <span class="info-label">اسم الطفل</span>
                        <span class="info-value">{{ $patient->name }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="info-cell">
                        <span class="info-label">التقييم الأساسي (1)</span>
                        <strong class="info-value"
                            style="color: #4f46e5;">{{ $eval1->title ?: 'التقييم الأول' }}</strong>
                        <div style="font-size: 9pt; color: #6b7280; margin-top: 4px;">التاريخ:
                            {{ $eval1->evaluation_date ? $eval1->evaluation_date->format('Y/m/d') : 'غير محدد' }}
                        </div>
                    </td>
                    <td class="info-cell">
                        <span class="info-label">التقييم اللاحق (2)</span>
                        <strong class="info-value"
                            style="color: #4f46e5;">{{ $eval2->title ?: 'التقييم الثاني' }}</strong>
                        <div style="font-size: 9pt; color: #6b7280; margin-top: 4px;">التاريخ:
                            {{ $eval2->evaluation_date ? $eval2->evaluation_date->format('Y/m/d') : 'غير محدد' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 2. COMPARISON SECTION -->
        <div class="card">
            <h2 class="section-heading">مقارنة المقاييس والأبعاد</h2>
            <p style="font-size: 9pt; color: #6b7280; margin-bottom: 15px;">
                (انخفاض درجة الشدة الإجمالية يدل على نقص حدة الاضطراب وتحسن حالة الطفل)
            </p>

            <table class="grid-table thick-grid">
                <thead>
                    <tr>
                        <th style="text-align: right; width: 35%;">البعد / المقياس</th>
                        <th style="width: 25%;">{{ $eval1->title ?: 'التقييم الأول' }}<br><small>(الدرجة -
                                الشدة)</small></th>
                        <th style="width: 25%;">{{ $eval2->title ?: 'التقييم الثاني' }}<br><small>(الدرجة -
                                الشدة)</small></th>
                        <th style="width: 25%;">التغيير (التقدم)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($measurements as $measurement)
                        <tr>
                            <td colspan="4" class="scale-name">
                                المقياس: {{ $measurement['name'] }}
                            </td>
                        </tr>

                        @foreach ($measurement['dimensions'] as $dimension)
                            <tr>
                                <td style="text-align: right; font-weight: bold; color: #4b5563;">
                                    {{ $dimension['name'] }}
                                </td>

                                <!-- Eval 1 Score & Severity -->
                                <td>
                                    <span
                                        style="display: block; font-weight: bold; font-size: 11pt;">{{ $dimension['score_1'] }}</span>
                                    @php
                                        $sev1 = $dimension['severity_1'];
                                        $sev1Class = match ($sev1) {
                                            Severity::OK => 'badge-green',
                                            Severity::LOW => 'badge-yellow',
                                            Severity::MID => 'badge-orange',
                                            Severity::HIGH => 'badge-red',
                                            default => 'badge-green',
                                        };
                                    @endphp
                                    <span class="badge {{ $sev1Class }}">{{ $sev1->value }}</span>
                                </td>

                                <!-- Eval 2 Score & Severity -->
                                <td>
                                    <span
                                        style="display: block; font-weight: bold; font-size: 11pt;">{{ $dimension['score_2'] }}</span>
                                    @php
                                        $sev2 = $dimension['severity_2'];
                                        $sev2Class = match ($sev2) {
                                            Severity::OK => 'badge-green',
                                            Severity::LOW => 'badge-yellow',
                                            Severity::MID => 'badge-orange',
                                            Severity::HIGH => 'badge-red',
                                            default => 'badge-green',
                                        };
                                    @endphp
                                    <span class="badge {{ $sev2Class }}">{{ $sev2->value }}</span>
                                </td>

                                <!-- Difference / Status -->
                                <td>
                                    <span class="badge"
                                        style="background-color: {{ $dimension['status_color'] }}; color: white; padding: 5px 12px; font-size: 9pt;">
                                        {{ $dimension['status_label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #6b7280; padding: 20px;">
                                لا يوجد مقاييس مشتركة تمت الإجابة عليها في كلا التقييمين لمقارنتها.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 3. SPECIALIST MEDICAL PLAN (Optional) -->
        @if (!empty(strip_tags($patient->medical_plan)))
            <div class="card">
                <h2 class="section-heading">الخطة العلاجية/الطبية للمتابعة</h2>
                <div style="font-size: 10pt; line-height: 1.8; color: #374151;">
                    {!! $patient->medical_plan !!}
                </div>
            </div>
        @endif

    </div>
</body>

</html>