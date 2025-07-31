<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة من {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Tahoma, Arial, sans-serif';
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
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            direction: rtl;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
            direction: rtl;
            text-align: right;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
            text-align: right;
        }
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.8;
            color: #555;
            text-align: right;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e9ecef;
            direction: rtl;
        }
        .security-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
            text-align: right;
            direction: rtl;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">
                منصة تصميم البطاقات الذكية
            </p>
        </div>
        
        <div class="content">
            <div class="greeting">
                مرحباً،
            </div>
            
            <div class="message">
                {{ $messageContent }}
            </div>
            
            <div class="message">
                شكراً لك على استخدام منصة {{ config('app.name') }}.
            </div>
        </div>
        
        <div class="footer">
           
            <p style="margin-top: 10px; font-size: 12px; opacity: 0.7;">
                هذا بريد إلكتروني تلقائي، يرجى عدم الرد عليه.
            </p>
        </div>
    </div>
</body>
</html>
