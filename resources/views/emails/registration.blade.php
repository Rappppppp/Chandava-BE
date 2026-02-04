<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success!</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #f1fdf5 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(34, 197, 94, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            padding: 40px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .checkmark {
            display: inline-block;
            position: relative;
            z-index: 1;
        }
        
        .checkmark svg {
            width: 80px;
            height: 80px;
            animation: scaleIn 0.6s ease-out;
        }
        
        h1 {
            color: #ffffff;
            font-size: 32px;
            margin-top: 20px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .subtitle {
            color: #22c55e;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .message {
            color: #4b5563;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .details {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .details p {
            color: #374151;
            font-size: 14px;
            margin: 8px 0;
        }
        
        .details strong {
            color: #16a34a;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #ffffff;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(34, 197, 94, 0.3);
        }
        
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer p {
            color: #6b7280;
            font-size: 14px;
            margin: 8px 0;
        }
        
        .footer-links {
            margin-top: 15px;
        }
        
        .footer-links a {
            color: #22c55e;
            text-decoration: none;
            margin: 0 10px;
            font-size: 13px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .decorative-leaf {
            display: inline-block;
            margin: 0 5px;
            font-size: 20px;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .message {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="checkmark">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h1>Registration Success!</h1>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="subtitle">
                <span class="decorative-leaf">🌿</span>
                Your registration has been confirmed
                <span class="decorative-leaf">🌿</span>
            </div>
            
            <p class="message">
                Thank you {{$mailData['first_name']}} {{$mailData['last_name']}} for your participation. We're thrilled to have you on board. Your action has been successfully processed and we're excited to move forward with you.
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p style="font-weight: 600; color: #374151; margin-bottom: 10px;">Need help?</p>
            <p>Contact our support team at <strong>chandavalakeresortresto@gmail.com</strong></p>
            <p style="margin-top: 20px; font-size: 12px; color: #a0aec0;">
                © 2026 Chandava. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
