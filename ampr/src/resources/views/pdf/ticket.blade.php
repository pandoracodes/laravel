<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>E-Ticket Mediterania Court</title>
    <style>
        @page {
            margin: 0;
            size: A5 portrait; /* Ukuran kertas pas HP */
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 15px; /* Margin luar tipis saja */
            background-color: #EAEFF5;
            color: #1A5F7A;
        }

        .wrapper {
            width: 100%;
            max-width: 100%;
        }

        /* CARD STYLE */
        .ticket-card {
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            /* Mencegah kartu terpotong ke halaman 2 */
            page-break-inside: avoid; 
        }

        /* HEADER (Lebih Compact) */
        .header {
            background-color: #1A5F7A;
            padding: 20px 20px; /* Padding dikurangi */
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .brand-small {
            font-size: 9px;
            font-weight: bold;
            color: #BEF264;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .brand-title {
            font-size: 20px; /* Font size disesuaikan */
            font-weight: 900;
            margin: 0;
            line-height: 1;
        }

        .unit-badge {
            background-color: rgba(255, 255, 255, 0.15);
            padding: 6px 12px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .unit-label { font-size: 7px; text-transform: uppercase; color: #BEF264; display: block; }
        .unit-number { font-size: 12px; font-weight: bold; color: #ffffff; display: block; }

        /* BODY CONTENT (Lebih Rapat) */
        .content {
            padding: 15px 20px; /* Padding dikurangi dari 30px */
        }

        /* QR AREA */
        .qr-section {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .qr-img {
            width: 110px; /* Diperkecil agar muat */
            height: 110px;
        }

        /* INFO TABLE */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding-bottom: 8px; /* Jarak antar baris diperkecil */
            vertical-align: top;
        }

        .label {
            font-size: 8px;
            color: #94A3B8;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            display: block;
        }

        .value {
            font-size: 14px;
            font-weight: bold;
            color: #1A5F7A;
        }

        .value-large {
            font-size: 18px;
            font-weight: 900;
            color: #1A5F7A;
        }

        /* BOOKING CODE BOX */
        .code-box {
            background-color: #F8FAFC;
            border: 2px dashed #CBD5E1;
            padding: 10px; /* Padding dikurangi */
            border-radius: 10px;
            text-align: center;
            margin: 15px 0;
        }

        .code-text {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            color: #1A5F7A;
            letter-spacing: 2px;
        }

        /* SEPARATOR */
        .separator {
            border-top: 1px dashed #E2E8F0;
            margin: 10px 0;
        }

        /* FOOTER */
        .footer {
            background-color: #F1F5F9;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #E2E8F0;
        }

        .footer-text {
            font-size: 9px;
            color: #64748B;
            line-height: 1.4;
        }

        .status-badge {
            background-color: #1A5F7A;
            color: #BEF264;
            font-size: 9px;
            padding: 3px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <div class="ticket-card">
            
            <!-- HEADER -->
            <div class="header">
                <table width="100%">
                    <tr>
                        <td align="left">
                            <div class="brand-small">Mediterania Court</div>
                            <h1 class="brand-title">E-TICKET</h1>
                        </td>
                        <td align="right" width="80">
                            <div class="unit-badge">
                                <span class="unit-label">UNIT</span>
                                <span class="unit-number">{{ $booking->unit->unit_number }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- BODY -->
            <div class="content">
                
                <!-- QR CODE -->
                <div class="qr-section">
                    <img src="{{ $qrCode }}" class="qr-img" />
                    <div style="margin-top:4px; font-size:9px; color:#94A3B8;">Scan for entry</div>
                </div>

                <!-- INFO GRID -->
                <table class="info-table">
                    <tr>
                        <td width="55%">
                            <span class="label">Date</span>
                            <span class="value">
                                {{ \Carbon\Carbon::parse($booking->start_time)->locale('en')->isoFormat('ddd, D MMM Y') }}
                            </span>
                        </td>
                        <td width="45%" align="right">
                            <span class="label">Time</span>
                            <span class="value">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="label">Status</span>
                            <span class="status-badge">CONFIRMED</span>
                        </td>
                        <td align="right">
                            <span class="label">Facility</span>
                            <span class="value">Tennis Court</span>
                        </td>
                    </tr>
                </table>

                <!-- BOOKING CODE -->
                <div class="code-box">
                    <span class="label" style="display:block; margin-bottom:2px;">BOOKING CODE</span>
                    <span class="code-text">{{ strtoupper($booking->booking_code) }}</span>
                </div>

                <!-- SEPARATOR -->
                <div class="separator"></div>

                <!-- PLAYER INFO -->
                <table class="info-table" style="margin-bottom:0;">
                    <tr>
                        <td>
                            <span class="label">Registered Player</span>
                            <!-- word-wrap penting agar nama panjang turun ke bawah, bukan memanjang ke samping -->
                            <span class="value-large" style="word-wrap: break-word;">
                                {{ is_array($booking->player_names) ? $booking->player_names[0] : $booking->player_names }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- FOOTER -->
            <div class="footer">
                <div class="footer-text">
                    <b>TERMS & CONDITIONS</b><br>
                    Please show this ticket to the security.<br>
                    Arrive 10 minutes early.
                </div>
            </div>

        </div>
    </div>

</body>
</html>