<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Created</title>
</head>

<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#333;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:20px 0;">
    <tr>
        <td align="center">
            <!-- Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;">

                <!-- Header -->
                <tr>
                    <td align="center" style="background-color:#1e293b;padding:30px 20px;color:#ffffff;">
                        <h1 style="margin:0;font-size:26px;">Booking Successful</h1>
                        <p style="margin:8px 0 0;font-size:14px;color:#e5e7eb;">
                            A reservation has been created on your behalf
                        </p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:30px;">
                        <p style="font-size:16px;color:#1e293b;font-weight:bold;margin:0 0 20px;">
                            Hello {{ $mailData['first_name'] }},
                        </p>

                        <p style="font-size:14px;line-height:1.6;margin:0 0 20px;">
                            This is to confirm that you successfully created a booking.
                            Please review the details below and keep this email for your records.
                        </p>

                        <!-- Booking Details -->
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#f8fafc;border-left:4px solid #1e293b;margin-bottom:20px;">
                            <tr>
                                <td style="padding:16px;font-size:14px;line-height:1.7;">
                                    <strong>Room:</strong> {{ $mailData['room_name'] }}<br><br>
                                    <strong>Booking Type:</strong> {{ $mailData['tour_type'] }}<br><br>
                                    <strong>Check-in:</strong> {{ $mailData['check_in'] }}<br><br>
                                    <strong>Check-out:</strong> {{ $mailData['check_out'] }}<br><br>
                                    <strong>Guests:</strong> {{ $mailData['guests_count'] }} person(s)
                                </td>
                            </tr>
                        </table>

                        <!-- Pricing Summary -->
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#ecfeff;margin-bottom:20px;">
                            <tr>
                                <td style="padding:16px;">
                                    <p style="margin:0 0 8px;font-size:13px;color:#475569;">
                                        Total Amount
                                    </p>
                                    <p style="margin:0;font-size:24px;font-weight:bold;color:#0f172a;">
                                        {{ $mailData['amount'] }}
                                    </p>
                                    <p style="margin:6px 0 0;font-size:12px;color:#64748b;">
                                        This amount was calculated based on your selected dates and booking type.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:13px;line-height:1.6;color:#475569;margin:0;">
                            If any of the details above are incorrect or if you need to make changes,
                            please contact us as soon as possible.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background-color:#f8fafc;padding:20px;font-size:12px;color:#64748b;">
                        <p style="margin:6px 0 0;">
                            © 2026 Chandava Resort. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>