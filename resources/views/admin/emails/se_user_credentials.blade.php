<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Street Entrepreneurs 3.0</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .credentials-box {
            background-color: #f0f7ff;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .credentials-box h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .credential-item {
            margin: 12px 0;
            font-family: monospace;
            font-size: 14px;
        }
        .credential-label {
            font-weight: bold;
            color: #2c3e50;
            min-width: 80px;
            display: inline-block;
        }
        .note {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .contact {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            text-align: center;
        }
        .funding {
            font-size: 11px;
            color: #888;
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 15px;
        }
        .warmly {
            margin: 25px 0 15px 0;
            font-style: italic;
        }
        @media (max-width: 480px) {
            .content {
                padding: 20px;
            }
            .credential-label {
                display: block;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- <div class="header">
            <h1>Street Entrepreneurs 3.0</h1>
        </div> --}}
        
        <div class="content">
            <h2>Dear {{ explode(' ', $user->name)[0] }},</h2>

            <p>Welcome to the Street Entrepreneurs 3.0 — <strong>we're thrilled to have you join us</strong>. Your commitment and the energy you brought to your application have already set you apart, and this marks the beginning of a powerful, transformative journey.</p>

            <p>Below, you'll find your access credentials for the <strong>Street Entrepreneurs 3.0 App</strong>. This will be your daily hub — the place where you log in each day to explore new materials, complete your check‑ins, and stay connected to everything happening in the program. <strong>For the best experience, please use the Chrome browser when accessing the app.</strong></p>

            <div class="credentials-box">
                <h3>Street Entrepreneurs 3.0 App Login</h3>
                <div class="credential-item">
                    <span class="credential-label">URL:</span> 
                    <span>{{ url('/') }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Username:</span> 
                    <span>{{ $user->email }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span> 
                    <span>{{ $password }}</span>
                </div>
            </div>

            {{-- <div class="note">
                <strong>💡 Recommendation:</strong> We recommend changing your password after your first login.
            </div> --}}

            <div class="contact">
                <strong>Our team is always here to help.</strong><br>
                Please feel free to contact us anytime at 
                <a href="mailto:info@urbanrezsolutions.com">info@urbanrezsolutions.com</a>
            </div>

            <div class="warmly">
                Warmly,<br>
                <strong>Your Street Entrepreneurs 3.0 Team</strong>
            </div>

            <div class="funding">
                Street Entrepreneurs is funded by the Government of Canada's Future Skills Program.<br>
                Le projet Street Entrepreneurs est financé par le gouvernement du Canada dans le cadre du programme Compétences futures.
            </div>
        </div>
    </div>
</body>
</html>