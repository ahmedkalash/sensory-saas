<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفعيل التطبيق</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
            color: #e2e8f0;
        }

        .activation-container {
            width: 100%;
            max-width: 520px;
            padding: 20px;
        }

        .card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 20px;
            padding: 40px;
            box-shadow:
                0 0 40px rgba(99, 102, 241, 0.1),
                0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }

        .logo-icon svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .logo h1 {
            font-size: 22px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 6px;
        }

        .logo p {
            font-size: 14px;
            color: #94a3b8;
        }

        .field-group {
            margin-bottom: 24px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .machine-id-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .machine-id-text {
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 14px;
            color: #a5b4fc;
            letter-spacing: 1px;
            direction: ltr;
            text-align: left;
            flex: 1;
            user-select: all;
            word-break: break-all;
        }

        .copy-btn {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .copy-btn:hover {
            background: rgba(99, 102, 241, 0.3);
            color: #c7d2fe;
        }

        .input-field {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 14px;
            color: #e2e8f0;
            font-family: 'Consolas', 'Courier New', monospace;
            direction: ltr;
            text-align: left;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .input-field::placeholder {
            color: #475569;
        }

        .input-field:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .input-field.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        .error-message {
            color: #fca5a5;
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .error-message::before {
            content: '✕';
            font-weight: bold;
            color: #ef4444;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #475569;
        }
    </style>
</head>

<body>
    <div class="activation-container">
        <div class="card">
            <div class="logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" />
                    </svg>
                </div>
                <h1>تفعيل التطبيق</h1>
                <p>أدخل مفتاح الترخيص لتفعيل التطبيق على هذا الجهاز</p>
            </div>

            <form method="POST" action="{{ route('license.activate') }}">
                @csrf

                <div class="field-group">
                    <label class="field-label">معرّف الجهاز (Machine ID)</label>
                    <div class="machine-id-box">
                        <span class="machine-id-text" id="machineId">{{ $machineId }}</span>
                        <button type="button" class="copy-btn" onclick="copyMachineId()">نسخ</button>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label" for="license_key">مفتاح الترخيص</label>
                    <textarea name="license_key" id="license_key"
                        class="input-field @error('license_key') error @enderror" rows="4"
                        placeholder="الصق مفتاح الترخيص هنا..." required>{{ old('license_key') }}</textarea>

                    @error('license_key')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">تفعيل التطبيق</button>
            </form>

            <div class="footer-text">
                تواصل مع المطور للحصول على مفتاح الترخيص
            </div>
        </div>
    </div>

    <script>
        function copyMachineId() {
            const text = document.getElementById('machineId').textContent.trim();
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            const btn = document.querySelector('.copy-btn');
            btn.textContent = 'تم النسخ ✓';
            setTimeout(() => btn.textContent = 'نسخ', 2000);
        }
    </script>
</body>

</html>