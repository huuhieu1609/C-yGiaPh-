<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Lời Mời Truy Cập Nhánh Tộc</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased;">
    <!-- Outer wrapper -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f0f2f5;">
        <tr>
            <td align="center" style="padding: 40px 16px;">
                <!-- Email Card -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">

                    <!-- ============ HEADER ============ -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); padding: 48px 40px 40px; text-align: center;">
                            <!-- Icon -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 20px;">
                                <tr>
                                    <td style="width: 72px; height: 72px; background-color: rgba(255,255,255,0.18); border-radius: 50%; text-align: center; vertical-align: middle; font-size: 36px;">
                                        🏛️
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin: 0 0 8px; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.3px;">
                                Lời Mời Truy Cập Nhánh Tộc
                            </h1>
                            <p style="margin: 0; color: rgba(255,255,255,0.8); font-size: 15px; font-weight: 400;">
                                Heritage Archivist — Hệ Thống Quản Lý Gia Phả
                            </p>
                        </td>
                    </tr>

                    <!-- ============ BODY ============ -->
                    <tr>
                        <td style="padding: 40px 40px 16px;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.7;">
                                Xin chào <strong style="color: #1a1a2e;">{{ $targetUser->ho_ten ?? 'bạn' }}</strong>,
                            </p>

                            <p style="margin: 0 0 28px; font-size: 16px; color: #555555; line-height: 1.7;">
                                Bạn vừa được <strong style="color: #6a11cb;">{{ $partnerUser->ho_ten ?? 'Quản trị viên' }}</strong> cấp quyền truy cập và xem thông tin một nhánh tộc trên hệ thống.
                            </p>

                            <!-- Branch Info Card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #f8f4ff 0%, #eef2ff 100%); border-radius: 12px; border: 1px solid #e8e0f7; padding: 24px 28px;">
                                        <!-- Branch Name -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 14px;">
                                            <tr>
                                                <td style="width: 28px; vertical-align: top; padding-top: 2px;">
                                                    <span style="font-size: 18px;">🌳</span>
                                                </td>
                                                <td style="padding-left: 10px;">
                                                    <p style="margin: 0 0 2px; font-size: 12px; color: #8b7db5; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                                        Tên dòng họ / Nhánh
                                                    </p>
                                                    <p style="margin: 0; font-size: 18px; color: #2d2b55; font-weight: 700;">
                                                        {{ $chiNhanh->ten_chi ?? 'Không rõ' }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- Divider -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="border-top: 1px solid #ddd4f0; padding-top: 14px;">
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="width: 28px; vertical-align: top; padding-top: 2px;">
                                                                <span style="font-size: 18px;">📋</span>
                                                            </td>
                                                            <td style="padding-left: 10px;">
                                                                <p style="margin: 0 0 2px; font-size: 12px; color: #8b7db5; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                                                    Mô tả
                                                                </p>
                                                                <p style="margin: 0; font-size: 15px; color: #444466; line-height: 1.6;">
                                                                    {{ $chiNhanh->mo_ta ?? 'Không có mô tả' }}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; font-size: 16px; color: #555555; line-height: 1.7;">
                                Bạn có thể đăng nhập ngay để tra cứu gia phả và xem các thành viên thuộc nhánh tộc này.
                            </p>
                        </td>
                    </tr>

                    <!-- ============ CTA BUTTON ============ -->
                    <tr>
                        <td style="padding: 16px 40px 40px; text-align: center;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="border-radius: 50px; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); box-shadow: 0 6px 20px rgba(106,17,203,0.35);">
                                        <a href="{{ $link ?? 'http://localhost:5173' }}"
                                           target="_blank"
                                           style="display: inline-block; padding: 16px 48px; font-size: 16px; font-weight: 700; color: #ffffff; text-decoration: none; letter-spacing: 0.5px;">
                                            Truy Cập Hệ Thống Ngay &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Fallback link -->
                            <p style="margin: 20px 0 0; font-size: 13px; color: #999999; line-height: 1.5;">
                                Nếu nút trên không hoạt động, hãy sao chép đường dẫn sau vào trình duyệt:<br>
                                <a href="{{ $link ?? 'http://localhost:5173' }}" style="color: #6a11cb; text-decoration: underline; word-break: break-all;">
                                    {{ $link ?? 'http://localhost:5173' }}
                                </a>
                            </p>
                        </td>
                    </tr>

                    <!-- ============ DIVIDER ============ -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-top: 1px solid #eeeeee;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============ FOOTER ============ -->
                    <tr>
                        <td style="padding: 28px 40px 32px; text-align: center;">
                            <p style="margin: 0 0 6px; font-size: 13px; color: #aaaaaa; line-height: 1.6;">
                                &copy; {{ date('Y') }} Heritage Archivist — Hệ Thống Quản Lý Gia Phả
                            </p>
                            <p style="margin: 0; font-size: 13px; color: #bbbbbb;">
                                Đây là email tự động, vui lòng không trả lời.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Email Card -->
            </td>
        </tr>
    </table>
</body>
</html>
