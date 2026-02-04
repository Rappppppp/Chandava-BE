<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
</head>

<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#333;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:20px 0;">
    <tr>
        <td align="center">
            <!-- Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;">
                
                <!-- Header -->
                <tr>
                    <td align="center" style="background-color:#2d7a3e;padding:30px 20px;color:#ffffff;">
                        <h1 style="margin:0;font-size:28px;">Booking Confirmed</h1>
                        <p style="margin:8px 0 0;font-size:14px;">
                            Your reservation for {{ $mailData['room_name'] }} is locked in
                        </p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:30px;">
                        <p style="font-size:16px;color:#2d7a3e;font-weight:bold;margin:0 0 20px;">
                            Hello {{ $mailData['first_name'] }},
                        </p>

                        <p style="font-size:14px;line-height:1.6;margin:0 0 20px;">
                            Thank you for your booking! Your reservation has been successfully confirmed.
                        </p>

                        <!-- Booking Details -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8faf8;border-left:4px solid #2d7a3e;margin-bottom:20px;">
                            <tr>
                                <td style="padding:15px;font-size:14px;">
                                    <strong>Booking Date:</strong> {{ $mailData['created_at'] }}<br><br>
                                    <strong>Tour:</strong> {{ $mailData['tour_type'] }}<br><br>
                                    <strong>Room:</strong> {{ $mailData['room_name'] }}<br><br>
                                    <strong>Guests:</strong> {{ $mailData['guests_count'] }} people
                                </td>
                            </tr>
                        </table>

                        <!-- Summary -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#e8f4ea;margin-bottom:20px;">
                            <tr>
                                <td style="padding:15px;">
                                    <p style="margin:0 0 10px;font-size:14px;font-weight:bold;color:#1e5a2c;">
                                        Booking Summary
                                    </p>
                                    <p style="margin:0;font-size:14px;color:#666;">
                                        Total Amount
                                    </p>
                                    <p style="margin:5px 0 0;font-size:22px;font-weight:bold;color:#2d7a3e;">
                                        {{ $mailData['amount'] }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Notice -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff3cd;border-left:4px solid #ff9800;margin-bottom:20px;">
                            <tr>
                                <td style="padding:12px;font-size:13px;color:#664d00;">
                                    Please arrive 15 minutes early. Bring a valid ID and your confirmation email.
                                </td>
                            </tr>
                        </table>

                        <!-- Button -->
                        <!-- <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="padding:20px 0;">
                                    <a href="#"
                                       style="background-color:#2d7a3e;color:#ffffff;text-decoration:none;
                                              padding:12px 24px;font-size:14px;font-weight:bold;display:inline-block;">
                                        View Booking Details
                                    </a>
                                </td>
                            </tr>
                        </table> -->

                        <p style="font-size:13px;line-height:1.6;color:#666;margin:20px 0 0;">
                            Need to reschedule or cancel? You can manage your booking from your account dashboard.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background-color:#f8faf8;padding:20px;font-size:12px;color:#888;">
                        <p style="margin:0;">
                            This email was sent to chandavalakeresortresto@gmail.com
                        </p>
                        <p style="margin:8px 0 0;color:#999;">
                            © 2026 Chandava. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>