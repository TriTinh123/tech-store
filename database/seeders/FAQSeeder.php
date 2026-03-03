<?php

namespace Database\Seeders;

use App\Models\FAQ;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // Shipping & Delivery
            [
                'question' => 'Bao lâu thì giao hàng?',
                'answer' => '⏱️ **Thời gian giao hàng:**\n'
                    .'• **Nội thành TP.HCM**: 24 giờ\n'
                    .'• **Các tỉnh khác**: 2-3 ngày làm việc\n'
                    .'• **Miền Nam**: 3-5 ngày\n'
                    .'• **Miền Bắc/Trung**: 5-7 ngày\n\n'
                    .'Bạn có thể theo dõi đơn hàng thời gian thực trên trang Đơn hàng của tôi.',
                'category' => 'shipping',
            ],
            [
                'question' => 'Có miễn phí vận chuyển không?',
                'answer' => '🚚 **Miễn phí vận chuyển:**\n'
                    .'✅ Miễn phí vận chuyển cho:\n'
                    .'• Đơn hàng từ 500.000đ trở lên (TP.HCM)\n'
                    .'• Đơn hàng từ 1.000.000đ trở lên (các tỉnh)\n\n'
                    .'💳 Phí vận chuyển tiêu chuẩn:\n'
                    .'• TP.HCM: 15.000đ\n'
                    .'• Đến 100km: 25.000đ\n'
                    .'• Trên 100km: 40.000đ',
                'category' => 'shipping',
            ],

            // Payment
            [
                'question' => 'Những phương thức thanh toán nào?',
                'answer' => '💳 **Phương thức thanh toán:**\n'
                    .'1️⃣ **COD (Thanh toán khi nhận)** - Không tính phí\n'
                    .'2️⃣ **Transfer ngân hàng** - Miễn phí\n'
                    .'   • Techcombank: 1234567890 (TechStore)\n'
                    .'   • Vietcombank: 0987654321 (TechStore)\n'
                    .'3️⃣ **Ví điện tử** - Tìm đang phát triển\n\n'
                    .'✅ Đơn hàng được xác nhận ngay sau khi thanh toán!',
                'category' => 'payment',
            ],
            [
                'question' => 'Tại sao thanh toán không được?',
                'answer' => '❌ **Hướng dẫn khắc phục lỗi thanh toán:**\n\n'
                    .'1️⃣ **Kiểm tra kết nối internet** - Đảm bảo kết nối ổn định\n'
                    .'2️⃣ **Làm rỗi cache và cookie** - F12 → Storage → Clear All\n'
                    .'3️⃣ **Dùng trình duyệt khác** - Chrome, Firefox, Safari\n'
                    .'4️⃣ **Kiểm tra hạn mức** - Thẻ/ví còn đủ tiền không?\n'
                    .'5️⃣ **Thử COD** - Thanh toán khi nhận hàng\n\n'
                    .'❓ Vẫn lỗi? Liên hệ: support@techstore.com',
                'category' => 'payment',
            ],

            // Returns & Exchanges
            [
                'question' => 'Chính sách đổi/trả hàng là gì?',
                'answer' => '🔄 **CHÍNH SÁCH ĐỔI/TRẢ HÀNG:**\n\n'
                    .'✅ **Quyền đổi/trả:**\n'
                    .'• Trong **7 ngày** kể từ khi nhận hàng\n'
                    .'• Sản phẩm **chưa qua sử dụng**\n'
                    .'• **Hộp nguyên, tem nguyên**\n\n'
                    .'❌ **Không đổi/trả:**\n'
                    .'• Hàng cũ, vỡ do người dùng\n'
                    .'• Quá 7 ngày\n'
                    .'• Hàng đã mở/sử dụng\n\n'
                    .'📧 **Khiếu nại:** support@techstore.com',
                'category' => 'returns',
            ],
            [
                'question' => 'Cách hoàn tiền như thế nào?',
                'answer' => '💰 **QUÁ TRÌNH HOÀN TIỀN:**\n\n'
                    .'1️⃣ Liên hệ support để yêu cầu trả hàng\n'
                    .'2️⃣ Nhân viên sẽ cấp mã RMA\n'
                    .'3️⃣ Gửi hàng về (địa chỉ sẽ được cung cấp)\n'
                    .'4️⃣ Chúng tôi kiểm tra hàng (2-3 ngày)\n'
                    .'5️⃣ Nếu hợp lệ → Hoàn tiền trong 3-5 ngày\n\n'
                    .'📌 Tiền sẽ về tài khoản gốc hoặc ví nạp.',
                'category' => 'returns',
            ],

            // Warranty
            [
                'question' => 'Bảo hành sản phẩm bao lâu?',
                'answer' => '🔧 **THỜI HẠN BẢO HÀNH:**\n\n'
                    .'⏱️ **Bảo hành theo loại sản phẩm:**\n'
                    .'• **Chuột, Bàn phím**: 12 tháng\n'
                    .'• **Headset**: 6 tháng\n'
                    .'• **Monitor, SSD**: 3 năm\n'
                    .'• **Laptop**: 12-24 tháng (tùy hãng)\n\n'
                    .'📝 **Điều kiện bảo hành:**\n'
                    .'✅ Còn hóa đơn\n'
                    .'✅ Lỗi nhà sản xuất\n'
                    .'❌ Hỏng do va đập, tẩm nước\n\n'
                    .'📞 Liên hệ: support@techstore.com',
                'category' => 'warranty',
            ],

            // General
            [
                'question' => 'Có hỗ trợ khách hàng 24/7 không?',
                'answer' => '💬 **HỖ TRỢ KHÁCH HÀNG:**\n\n'
                    .'🤖 **AI Chatbot** (24/7):\n'
                    .'• Tư vấn sản phẩm\n'
                    .'• So sánh giá\n'
                    .'• Kiểm tra đơn hàng\n'
                    .'• Mã giảm giá\n\n'
                    .'👥 **Nhân viên support**:\n'
                    .'• 8:00 - 20:00 (Thứ 2-7)\n'
                    .'• 9:00 - 18:00 (Chủ nhật)\n\n'
                    .'📧 Email: support@techstore.com\n'
                    .'📱 Hotline: 1900-XXXX\n'
                    .'💬 Facebook: TechStore.vn',
                'category' => 'general',
            ],
            [
                'question' => 'Có sản phẩm chính hãng không?',
                'answer' => '✅ **CHÍNH HIỆU 100%**\n\n'
                    .'🏆 TechStore là:\n'
                    .'• **Nhà phân phối chính thức** của các hãng lớn\n'
                    .'• **Bảo hành toàn quốc**\n'
                    .'• **Hóa đơn, chứng chỉ** cho tất cả sản phẩm\n\n'
                    .'🔍 **Cách xác minh:**\n'
                    .'1. Kiểm tra hộp sản phẩm\n'
                    .'2. Xem mã serial trên sản phẩm\n'
                    .'3. Liên hệ hãng để kiểm tra (tôi sẽ hướng dẫn)\n\n'
                    .'❓ Nghi ngờ? Liên hệ support!',
                'category' => 'general',
            ],
        ];

        foreach ($faqs as $faq) {
            FAQ::create($faq);
        }
    }
}
