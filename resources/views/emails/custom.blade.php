<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 20px 0;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .email-header img {
            max-height: 45px;
            margin-bottom: 15px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .email-body {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }

        .email-footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-content">
        <div class="email-header">
            <img src="https://vsetancy.ru/images/logo-black.png" alt="ВсеТанцы"
                 style="max-height:80px; height:auto; width:auto; display:block; margin:0 auto 15px;">
            {{ $subject }}
        </div>

        <div class="email-body">
            {!! $content !!}
        </div>

        <div class="email-footer">
            Если у вас возникли вопросы, свяжитесь с нами: <a href="mailto:support@vsetancy.ru">support@vsetancy.ru</a>
        </div>
    </div>
</div>
</body>
</html>
