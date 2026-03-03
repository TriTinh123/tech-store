@extends('layouts.app')

@section('title', 'Liên hệ - TechStore')

@section('content')
<div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    <h1 style="font-size: 32px; margin-bottom: 20px; color: #333;">Liên hệ với chúng tôi</h1>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Contact Info -->
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="color: #00b894; margin-bottom: 20px;">Thông tin liên hệ</h2>
            
            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 14px; color: #333; margin-bottom: 5px;">📍 Địa chỉ</h3>
                <p style="color: #666;">123 Đường Lý Thường Kiệt, Đống Đa, Hà Nội</p>
            </div>

            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 14px; color: #333; margin-bottom: 5px;">📞 Điện thoại</h3>
                <p style="color: #666;">1900-1234</p>
                <p style="color: #666;">024-1234-5678</p>
            </div>

            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 14px; color: #333; margin-bottom: 5px;">📧 Email</h3>
                <p style="color: #666;">support@techstore.com</p>
                <p style="color: #666;">sales@techstore.com</p>
            </div>

            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 14px; color: #333; margin-bottom: 5px;">🕒 Giờ làm việc</h3>
                <p style="color: #666;">Thứ 2 - Thứ 6: 08:00 - 18:00</p>
                <p style="color: #666;">Thứ 7: 09:00 - 17:00</p>
                <p style="color: #666;">Chủ nhật: 10:00 - 16:00</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="color: #00b894; margin-bottom: 20px;">Gửi tin nhắn cho chúng tôi</h2>

            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.submit') }}">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Tên của bạn</label>
                    <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Email</label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Điện thoại</label>
                    <input type="tel" name="phone" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Chủ đề</label>
                    <input type="text" name="subject" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Tin nhắn</label>
                    <textarea name="message" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;"></textarea>
                </div>

                <button type="submit" style="width: 100%; padding: 12px; background: #00b894; color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 14px;">
                    Gửi tin nhắn
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
