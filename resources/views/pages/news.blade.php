@extends('layouts.app')

@section('title', 'Tin tức - TechStore')

@section('content')
<div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    <h1 style="font-size: 32px; margin-bottom: 20px; color: #333;">Tin tức công nghệ</h1>
    <p style="color: #666; margin-bottom: 30px;">Cập nhật những tin tức mới nhất về phụ kiện công nghệ</p>
    
    <div style="display: grid; gap: 20px;">
        <!-- News Item 1 -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; gap: 20px;">
            <div style="flex: 0 0 200px;">
                <img src="/Image/keyboard.jpg" alt="Bàn phím" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; margin-bottom: 10px;"><a href="#" style="color: #333; text-decoration: none;">TOP 5 bàn phím cơ gaming tốt nhất 2026 - Chọn loại nào phù hợp?</a></h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">Khám phá những chiếc bàn phím cơ gaming hàng đầu năm 2026 với công nghệ mới nhất và hiệu suất vượt trội...</p>
                <p style="color: #999; font-size: 12px;">📅 03/02/2026 | ✍️ TechStore</p>
            </div>
        </div>

        <!-- News Item 2 -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; gap: 20px;">
            <div style="flex: 0 0 200px;">
                <img src="/Image/wired.jpg" alt="Chuột" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; margin-bottom: 10px;"><a href="#" style="color: #333; text-decoration: none;">Chuột gaming không dây vs có dây - Cái nào tốt hơn cho gamer?</a></h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">So sánh chi tiết giữa chuột gaming không dây và có dây để bạn lựa chọn sản phẩm phù hợp nhất...</p>
                <p style="color: #999; font-size: 12px;">📅 02/02/2026 | ✍️ TechStore</p>
            </div>
        </div>

        <!-- News Item 3 -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; gap: 20px;">
            <div style="flex: 0 0 200px;">
                <img src="/Image/gaming.jpg" alt="Tai nghe" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; margin-bottom: 10px;"><a href="#" style="color: #333; text-decoration: none;">Tai nghe gaming chuyên nghiệp - Lựa chọn tốt nhất cho streamer 2026</a></h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">Những chiếc tai nghe gaming chuyên nghiệp dành cho các streamer chuyên nghiệp với âm thanh sống động...</p>
                <p style="color: #999; font-size: 12px;">📅 31/01/2026 | ✍️ TechStore</p>
            </div>
        </div>

        <!-- News Item 4 -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; gap: 20px;">
            <div style="flex: 0 0 200px;">
                <img src="/Image/monitor.jpg" alt="Màn hình" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; margin-bottom: 10px;"><a href="#" style="color: #333; text-decoration: none;">Màn hình 4K 144Hz - Chuẩn mực mới cho gaming cao cấp</a></h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">Tìm hiểu về những màn hình 4K 144Hz mới nhất với công nghệ hiển thị tiên tiến...</p>
                <p style="color: #999; font-size: 12px;">📅 29/01/2026 | ✍️ TechStore</p>
            </div>
        </div>
    </div>
</div>
@endsection
