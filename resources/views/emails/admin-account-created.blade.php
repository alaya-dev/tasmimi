<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حساب إداري جديد</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 30px -20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 0 10px;
        }
        .welcome-text {
            font-size: 18px;
            margin-bottom: 25px;
            color: #555;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        .credentials-box h3 {
            color: #495057;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .credential-item {
            margin: 15px 0;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .credential-label {
            font-weight: bold;
            color: #6c757d;
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }
        .credential-value {
            font-size: 16px;
            color: #212529;
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            margin: 25px 0;
            transition: transform 0.2s;
        }
        .login-button:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        .security-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            color: #856404;
        }
        .security-notice h4 {
            margin-top: 0;
            color: #856404;
            font-size: 16px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name', 'Bitaqati') }}</div>
            <h1>مرحباً بك في فريق الإدارة</h1>
        </div>

        <div class="content">
            <p class="welcome-text">
                السلام عليكم ورحمة الله وبركاته،
            </p>

            <p class="welcome-text">
             .{{ config('app.name', 'Bitaqati') }}   نحن سعداء لإعلامك بأنه تم إنشاء حساب إداري جديد لك في منصة 
                يمكنك الآن الوصول إلى لوحة التحكم الإدارية باستخدام المعلومات التالية:
            </p>

            <div class="credentials-box">
                <h3>معلومات تسجيل الدخول</h3>
                
                <div class="credential-item">
                    <span class="credential-label">البريد الإلكتروني:</span>
                    <div class="credential-value">{{ $user->email }}</div>
                </div>

                <div class="credential-item">
                    <span class="credential-label">كلمة المرور:</span>
                    <div class="credential-value">{{ $password }}</div>
                </div>

                <div class="credential-item">
                    <span class="credential-label">الدور:</span>
                    <div class="credential-value">
                        @if($user->role === 'admin')
                            مدير
                        @elseif($user->role === 'super_admin')
                            مدير عام
                        @else
                            {{ $user->role }}
                        @endif
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="login-button">
                    تسجيل الدخول إلى لوحة التحكم
                </a>
            </div>

            <div class="security-notice">
                <h4>🔒 ملاحظة أمنية مهمة:</h4>
                <ul style="margin: 10px 0; padding-right: 20px;">
                    <li>يُنصح بشدة بتغيير كلمة المرور بعد تسجيل الدخول الأول</li>
                    <li>لا تشارك معلومات تسجيل الدخول مع أي شخص آخر</li>
                    <li>تأكد من تسجيل الخروج بعد انتهاء جلسة العمل</li>
                </ul>
            </div>

            <p>
                إذا كان لديك أي أسئلة أو تحتاج إلى مساعدة، لا تتردد في التواصل مع فريق الدعم الفني.
            </p>

            <p>
                مع أطيب التحيات،<br>
                فريق {{ config('app.name', 'Bitaqati') }}
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Bitaqati') }}. جميع الحقوق محفوظة.</p>
            <p>هذا البريد الإلكتروني تم إرساله تلقائياً، يرجى عدم الرد عليه.</p>
        </div>
    </div>
</body>
</html>
