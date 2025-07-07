<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}</title>
    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? 'Tahoma, Arial, sans-serif' : 'Arial, sans-serif' }};
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.8;
            color: #555;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .reset-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .security-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
        }
        .link-fallback {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            font-size: 14px;
            color: #666;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">
                {{ app()->getLocale() === 'ar' ? 'منصة تصميم البطاقات الذكية' : 'Smart Card Design Platform' }}
            </p>
        </div>
        
        <div class="content">
            <div class="greeting">
                {{ app()->getLocale() === 'ar' ? 'مرحباً،' : 'Hello,' }}
            </div>
            
            <div class="message">
                @if(app()->getLocale() === 'ar')
                    لقد تلقيت هذا البريد الإلكتروني لأنه تم طلب إعادة تعيين كلمة المرور لحسابك.
                @else
                    You are receiving this email because we received a password reset request for your account.
                @endif
            </div>
            
            <div class="button-container">
                <a href="{{ $url }}" class="reset-button">
                    {{ app()->getLocale() === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}
                </a>
            </div>
            
            <div class="security-note">
                <strong>{{ app()->getLocale() === 'ar' ? 'ملاحظة أمنية:' : 'Security Note:' }}</strong>
                {{ app()->getLocale() === 'ar' 
                    ? 'سينتهي صلاحية رابط إعادة تعيين كلمة المرور خلال 60 دقيقة.' 
                    : 'This password reset link will expire in 60 minutes.' }}
            </div>
            
            <div class="message">
                {{ app()->getLocale() === 'ar' 
                    ? 'إذا لم تطلب إعادة تعيين كلمة المرور، فلا حاجة لاتخاذ أي إجراء.' 
                    : 'If you did not request a password reset, no further action is required.' }}
            </div>
            
            <div class="link-fallback">
                <strong>{{ app()->getLocale() === 'ar' ? 'إذا كنت تواجه مشكلة في النقر على الزر أعلاه، انسخ والصق الرابط التالي في متصفحك:' : 'If you\'re having trouble clicking the button above, copy and paste the URL below into your web browser:' }}</strong>
                <br><br>
                <span style="color: #667eea;">{{ $url }}</span>
            </div>
        </div>
        
        <div class="footer">
            <p>
                {{ app()->getLocale() === 'ar' 
                    ? 'شكراً لاستخدامك منصة ' . config('app.name') 
                    : 'Thank you for using ' . config('app.name') }}
            </p>
            <p style="margin-top: 10px; font-size: 12px; opacity: 0.7;">
                {{ app()->getLocale() === 'ar' 
                    ? 'هذا بريد إلكتروني تلقائي، يرجى عدم الرد عليه.' 
                    : 'This is an automated email, please do not reply.' }}
            </p>
        </div>
    </div>
</body>
</html>