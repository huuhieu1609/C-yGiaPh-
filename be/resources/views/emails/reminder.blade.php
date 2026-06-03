<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nhắc Nhở Sự Kiện Dòng Họ</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif, Arial, sans-serif;
            background-color: #f1f2f6;
            margin: 0;
            padding: 0;
            color: #2f3542;
        }
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 40px 20px;
            text-align: center;
            border-bottom: 3px solid #d4af37;
        }
        .header h1 {
            color: #d4af37;
            margin: 0;
            font-size: 26px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header p {
            color: #a0aec0;
            margin: 8px 0 0 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 20px;
        }
        .alert-box {
            background-color: #fffaf0;
            border-left: 4px solid #d4af37;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .event-title {
            font-size: 22px;
            font-weight: bold;
            color: #b38d21;
            margin: 0 0 15px 0;
            text-transform: uppercase;
        }
        .info-grid {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px dashed #f1f2f6;
        }
        .info-label {
            width: 120px;
            font-weight: bold;
            color: #747d8c;
        }
        .info-value {
            flex-grow: 1;
            color: #2f3542;
        }
        .description-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-style: italic;
            line-height: 1.6;
            margin-top: 15px;
        }
        .footer {
            background-color: #0f172a;
            padding: 25px;
            text-align: center;
            color: #a0aec0;
            font-size: 12px;
            border-top: 1px solid #1e293b;
        }
        .footer p {
            margin: 5px 0;
        }
        .btn-link {
            display: inline-block;
            background-color: #d4af37;
            color: #0f172a !important;
            text-decoration: none;
            padding: 12px 30px;
            font-weight: bold;
            border-radius: 30px;
            margin-top: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(212,175,55,0.3);
            transition: all 0.2s;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Hệ Thống Gia Phả Số</h1>
            <p>Kính Báo Sự Kiện Tộc Họ</p>
        </div>
        
        <div class="content">
            <div class="greeting">Kính chào Quý bà con dòng họ,</div>
            
            <p>Ban Quản trị Gia phả kính báo sự kiện dòng tộc sắp diễn ra trong <strong>3 ngày tới</strong> để bà con chủ động sắp xếp thời gian tham dự/tế lễ kính cẩn:</p>
            
            <div class="alert-box">
                <div class="event-title">{{ $eventData['tieu_de'] }}</div>
                
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Loại sự kiện:</div>
                        <div class="info-value"><strong>{{ $eventData['loai'] }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Thời gian:</div>
                        <div class="info-value"><strong>{{ $eventData['thoi_gian'] }}</strong></div>
                    </div>
                    @if(!empty($eventData['dia_diem']))
                    <div class="info-row">
                        <div class="info-label">Địa điểm:</div>
                        <div class="info-value"><strong>{{ $eventData['dia_diem'] }}</strong></div>
                    </div>
                    @endif
                </div>

                @if(!empty($eventData['noi_dung']))
                <div class="description-box">
                    <strong>Nội dung:</strong><br>
                    {{ $eventData['noi_dung'] }}
                </div>
                @endif
            </div>

            <p style="line-height: 1.6;">Để biết thêm chi tiết sự kiện và xem danh sách bà con đăng ký tham dự, quý bà con vui lòng nhấp vào liên kết dưới đây để truy cập hệ thống:</p>
            
            <div style="text-align: center;">
                <a href="http://localhost:5173/su-kien" class="btn-link" target="_blank">Truy cập Trang Sự Kiện</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>CÔNG TY GIA PHẢ SỐ VIỆT NAM</strong></p>
            <p>Hệ thống quản lý phả hệ số, gìn giữ nguồn cội và kết nối dòng tộc tộc dòng.</p>
            <p>Vui lòng không trả lời email này.</p>
        </div>
    </div>
</body>
</html>
