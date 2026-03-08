<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير التقييم الشامل للمعالجة الحسية</title>
    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .container { max-width: 700px; margin: 0 auto; }

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
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-orange { background: #ffedd5; color: #c2410c; }
        .badge-red { background: #fee2e2; color: #b91c1c; }

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
        .legend-item { margin: 0 8px; }

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
            padding: 8px 0;
            border-bottom: 1px solid #f9fafb;
        }
        .weakness-text {
            font-size: 10pt;
            color: #1f2937;
            font-weight: bold;
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
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
<div class="container">

    <!-- ======================================== -->
    <!-- HEADER: Patient Info -->
    <!-- ======================================== -->
    <div class="card header-card">
        <div class="header-title">تقرير تقييم قائمة المعالجة الحسية</div>
        <div class="header-subtitle">تقرير طبي وتحليلي مفصل لحالة الطفل</div>

        <table class="info-grid">
            <tr>
                <td class="info-cell">
                    <span class="info-label">اسم المفحوص</span>
                    <span class="info-value">أحمد محمود علي</span>
                </td>
                <td class="info-cell">
                    <span class="info-label">النوع - العمر</span>
                    <span class="info-value">ذكر - 7 سنوات</span>
                </td>
            </tr>
            <tr>
                <td class="info-cell">
                    <span class="info-label">المدرسة - الصف</span>
                    <span class="info-value">مدرسة الأمل - الثاني الابتدائي</span>
                </td>
                <td class="info-cell">
                    <span class="info-label">تاريخ التطبيق - الأخصائي</span>
                    <span class="info-value">2026-03-05 - د. سارة</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- ======================================== -->
    <!-- SECTION 1: Summary Matrix -->
    <!-- ======================================== -->
    <div class="card">
        <div class="section-heading">أولاً: ملخص الدرجات ومستوى الشدة</div>

        <table class="summary-table">
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
            <!-- Mock Data for Visualizer -->
            <tr>
                <td class="scale-name">مقياس اضطراب المعالجة البصرية</td>
                <td style="font-weight: bold;">12</td>
                <td><span class="badge badge-orange">متوسط</span></td>
                <td style="font-weight: bold;">6</td>
                <td><span class="badge badge-green">طبيعي</span></td>
                <td style="font-weight: bold;">28</td>
                <td><span class="badge badge-red">شديد</span></td>
                <td style="font-weight: bold;">10</td>
                <td><span class="badge badge-yellow">بسيط</span></td>
            </tr>
            <tr>
                <td class="scale-name">مقياس اضطراب المعالجة السمعية</td>
                <td style="font-weight: bold;">8</td>
                <td><span class="badge badge-green">طبيعي</span></td>
                <td style="font-weight: bold;">22</td>
                <td><span class="badge badge-orange">متوسط</span></td>
                <td style="font-weight: bold;">14</td>
                <td><span class="badge badge-yellow">بسيط</span></td>
                <td style="font-weight: bold;">5</td>
                <td><span class="badge badge-green">طبيعي</span></td>
            </tr>
            <tr>
                <td class="scale-name">مقياس اضطراب الحس العضلي</td>
                <td style="font-weight: bold;">4</td>
                <td><span class="badge badge-green">طبيعي</span></td>
                <td style="font-weight: bold;">5</td>
                <td><span class="badge badge-green">طبيعي</span></td>
                <td style="font-weight: bold;">3</td>
                <td><span class="badge badge-green">طبيعي</span></td>
                <td style="font-weight: bold;">6</td>
                <td><span class="badge badge-green">طبيعي</span></td>
            </tr>
            </tbody>
        </table>

    </div>

    <div class="page-break"></div>

    <!-- ======================================== -->
    <!-- SECTION 2: Detailed Weaknesses -->
    <!-- ======================================== -->
    <div class="section-heading">ثانياً: التحليل المفصل لنقاط الضعف</div>

    <!-- Scale 1: Has Weaknesses -->
    <div class="scale-card">
        <div class="scale-card-header">
            <h3>1. مقياس اضطراب المعالجة البصرية</h3>
        </div>
        <div class="scale-card-body">

            <div class="dimension-block">
                <div class="dimension-header">
                    <span class="dimension-badge badge-orange">اضطراب متوسط</span>
                    <span class="dimension-title">البعد الأول: ضعف الاستجابة للمثيرات البصرية</span>
                </div>
                <div class="weakness-list">
                    <div class="weakness-item">
                        <div class="weakness-text">
                            <span class="weakness-marker">-</span>
                            يتأخر في توجيه نظره نحو مصدر المثير البصري.
                        </div>
                        <div class="weakness-recommendation">
                            توصية: تقليل المشتتات البصرية في مكان اللعب - استخدام مثيرات بصرية واضحة وبطيئة الحركة.
                        </div>
                        <div class="weakness-recommendation">
                            أهداف: أن يوجه الطفل نظره نحو المثير البصري خلال 3–5 ثوانٍ في 70% من المحاولات.
                        </div>
                        <div class="weakness-recommendation">
                            أنشطة: لعبة تتبع الضوء باستخدام شعاع ضوء يتحرك ببطء - نشاط الحواف والألوان.
                        </div>
                    </div>
                </div>
            </div>

            <div class="dimension-block">
                <div class="dimension-header">
                    <span class="dimension-badge badge-red">اضطراب شديد</span>
                    <span class="dimension-title">البعد الثالث: المتجنب الحسي للمثيرات البصرية</span>
                </div>
                <div class="weakness-list">
                    <div class="weakness-item">
                        <div class="weakness-text">
                            <span class="weakness-marker">-</span>
                            يتجنب الأماكن المضيئة أو المليئة بالألوان.
                        </div>
                        <div class="weakness-recommendation">
                            توصية: استخدام ألوان هادئة في غرفة الطفل وتقليل الزخرفة البصرية الزائدة.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scale 2: Has Weaknesses -->
    <div class="scale-card">
        <div class="scale-card-header">
            <h3>2. مقياس اضطراب المعالجة السمعية</h3>
        </div>
        <div class="scale-card-body">
            <div class="dimension-block">
                <div class="dimension-header">
                    <span class="dimension-badge badge-orange">اضطراب متوسط</span>
                    <span class="dimension-title">البعد الثاني: فرط الاستجابة للمثيرات السمعية</span>
                </div>
                <div class="weakness-list">
                    <div class="weakness-item">
                        <div class="weakness-text">
                            <span class="weakness-marker">-</span>
                            ينزعج بشكل واضح عند سماع الأصوات المرتفعة.
                        </div>
                        <div class="weakness-recommendation">
                            توصية: تقليل التعرض للأصوات العالية قدر الإمكان وتوفير واقيات أذن عند الحاجة.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scale 3: No Weaknesses -->
    <div class="scale-card">
        <div class="scale-card-header">
            <h3>3. مقياس اضطراب الحس العضلي</h3>
        </div>
        <div class="scale-card-body">
            <div class="no-weakness">لا توجد نقاط ضعف في هذا المقياس - النتائج طبيعية</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        تم استخراج هذا التقرير آلياً بواسطة نظام التقييم الحسي الإلكتروني © 2026
    </div>

</div>
</body>
</html>