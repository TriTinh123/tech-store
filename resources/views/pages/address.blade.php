@extends('layouts.app')

@section('title', 'Địa chỉ - TechStore')

@section('content')
<div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    <h1 style="font-size: 32px; margin-bottom: 30px; color: #333;">Địa chỉ cửa hàng</h1>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Map -->
        <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.5578840160236!2d105.80432097550449!3d21.003971888373826!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac6e8f8f8f8f%3A0x1f8f8f8f8f8f8f8f!2z4biz!5e0!3m2!1svi!2s!4v1234567890" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <!-- Address Info -->
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="color: #00b894; margin-bottom: 25px;">Thông tin chi tiết</h2>

            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 14px; color: #333; margin-bottom: 8px; font-weight: 600;">📍 Địa chỉ</h3>
                <p style="color: #666; line-height: 1.6;">
                    123 Đường Lý Thường Kiệt<br>
                    Phường Đống Đa<br>
                    Quận Đống Đa, Hà Nội
                </p>
            </div>

            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 14px; color: #333; margin-bottom: 8px; font-weight: 600;">📞 Điện thoại</h3>
                <p style="color: #666;">
                    <a href="tel:1900-1234" style="color: #00b894; text-decoration: none;">1900-1234</a><br>
                    <a href="tel:024-1234-5678" style="color: #00b894; text-decoration: none;">024-1234-5678</a>
                </p>
            </div>

            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 14px; color: #333; margin-bottom: 8px; font-weight: 600;">📧 Email</h3>
                <p style="color: #666;">
                    <a href="mailto:support@techstore.com" style="color: #00b894; text-decoration: none;">support@techstore.com</a>
                </p>
            </div>

            <div style="margin-bottom: 0;">
                <h3 style="font-size: 14px; color: #333; margin-bottom: 8px; font-weight: 600;">🕒 Giờ làm việc</h3>
                <p style="color: #666; line-height: 1.6;">
                    Thứ 2 - Thứ 6: 08:00 - 18:00<br>
                    Thứ 7: 09:00 - 17:00<br>
                    Chủ nhật: 10:00 - 16:00
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
