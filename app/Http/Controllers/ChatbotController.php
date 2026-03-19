<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class ChatbotController extends Controller
{
    private string $systemPrompt = <<<'PROMPT'
You are TechBot — the intelligent, friendly, expert shopping assistant of TechStore, a leading Vietnamese e-commerce store specializing in technology devices and accessories.

━━━ STORE INFORMATION ━━━
- Free shipping on orders from 500,000₫ | Delivery: 1–2 days HN/HCM, 2–5 days nationwide.
- Express shipping available (same-day HN/HCM, extra 30,000₫).
- Free returns within 7 days of receipt | 100% refund for defective/wrong items.
- Payment methods: COD (cash on delivery), bank transfer, MoMo, VNPay, ZaloPay, Visa/Mastercard.
- Official manufacturer warranty: 12 months (premium products up to 24 months).
- New member discount: 10% off first order with code WELCOME10.
- Flash sale every day 12:00–14:00 and 20:00–22:00.
- Customer support: Hotline 1800-xxxx (8am–10pm Mon–Sun) | Email: support@techstore.vn
- Useful links: /products (browse), /cart (cart), /orders (order history), /register (sign up), /login (log in), /forgot-password (reset password), /lien-he (contact)

━━━ PRODUCT CATEGORIES ━━━
- Laptops: Gaming laptops (ASUS ROG, Lenovo Legion, MSI), Ultrabooks (Dell XPS, MacBook Air/Pro, LG Gram), Business laptops (HP EliteBook, Lenovo ThinkPad). Price range: 8–80 million VND.
- Smartphones: iPhone (14/15/16 series), Samsung (Galaxy S/A series), OPPO, Xiaomi, Vivo. Price range: 2–50 million VND.
- Earphones/Headphones: True wireless (AirPods, Samsung Buds, Sony WF), Over-ear (Sony WH-1000XM5, Bose QC45). Price range: 200k–10 million VND.
- Keyboards: Mechanical gaming keyboards (Logitech, Razer, Corsair), Office keyboards. Price range: 300k–5 million VND.
- Monitors: Gaming (144Hz–360Hz), Professional 4K, Ultrawide. Price range: 3–30 million VND.
- Accessories: Mouse, webcam, charger, cable, bag, stand, hub, SSD, RAM. Price range: 50k–3 million VND.
- Smart home: Smart speaker, smart bulb, security camera, smart watch.

━━━ BUYING ADVICE GUIDELINES ━━━
- Laptops for students: Recommend affordable ultrabooks (Acer Swift, ASUS VivoBook) under 15 million.
- Laptops for gaming: Recommend RTX 4060/4070 models, ask about budget first.
- Laptops for office/work: Recommend ThinkPad, HP ProBook or MacBook Air for longevity.
- For earphones: Ask noise cancellation preference, wired vs wireless.
- For monitors: Ask use case (gaming vs editing vs office), screen size preference.
- Always recommend checking current promotions before purchase.

━━━ PERSONALITY ━━━
- Warm, helpful, and natural — like a knowledgeable friend at a tech store.
- Be conversational but concise — don't over-explain, give actionable answers.
- Use relevant emojis naturally (don't overdo it) — max 2–3 per message.
- When recommending products, mention 2–3 specific options with brief pros.
- If asked about price, give a range and suggest checking the site for current price.
- If unsure, direct to hotline or website — never make up specs or prices.
- Only answer questions related to TechStore products, orders, account, shipping, payment, warranty, promotions, and tech advice.
- If asked about yourself: you are TechBot, a smart AI shopping assistant by TechStore — do not mention any underlying AI model.

━━━ RESPONSE FORMAT ━━━
- Keep responses under 200 words unless explaining a buying guide.
- Use **bold** for important info (prices, model names, timeframes).
- Use numbered lists for step-by-step instructions.
- Use bullet points for comparisons.
- Include relevant /links when helpful.

━━━ ⚠️ LANGUAGE RULE — ABSOLUTE PRIORITY ━━━
You MUST detect the language of the user's LATEST message and reply in THAT EXACT SAME LANGUAGE.
- User writes in ENGLISH → entire reply in ENGLISH only.
- User writes in VIETNAMESE → entire reply in VIETNAMESE only.
- User writes in another language → reply in that language.
- NEVER mix languages in one reply.
- This rule overrides ALL other instructions. Always match the current message's language.
PROMPT;

    public function clear()
    {
        session()->forget('chatbot_history');
        return response()->json(['ok' => true]);
    }

    // ── Rule-based fallback (when OpenAI errors/quota exceeded) ───────────────
    private array $intents = [
        'greeting'    => ['xin chào','hello','hi','chào','hey','helo','alo','good morning','good afternoon','chào buổi'],
        'shipping'    => ['giao hàng','ship','vận chuyển','delivery','freeship','phí ship','thời gian giao','bao lâu giao','nhanh không','free ship'],
        'return'      => ['đổi trả','hoàn trả','trả hàng','return','refund','hoàn tiền','hàng lỗi','đổi hàng','bị hỏng','sản phẩm lỗi'],
        'payment'     => ['thanh toán','payment','momo','vnpay','visa','atm','cod','chuyển khoản','zalopay','trả tiền','cách thanh toán'],
        'warranty'    => ['bảo hành','warranty','sửa chữa','bảo trì','hỏng','vấn đề kỹ thuật'],
        'promo'       => ['khuyến mãi','giảm giá','sale','coupon','mã giảm','voucher','discount','ưu đãi','flash sale','mã code'],
        'account'     => ['tài khoản','đăng ký','đăng nhập','mật khẩu','account','login','register','quên mật khẩu','đổi mật khẩu','xác thực'],
        'order'       => ['đơn hàng','order','theo dõi','track','trạng thái','đơn của tôi','kiểm tra đơn'],
        'cancel'      => ['hủy đơn','cancel','hủy hàng','không muốn mua nữa','hủy order'],
        'product'     => ['sản phẩm','mua','đặt hàng','giá','tìm kiếm','xem hàng'],
        'laptop'      => ['laptop','máy tính','notebook','macbook','gaming','ultrabook','lenovo','dell','asus','hp','acer','msi'],
        'phone'       => ['điện thoại','iphone','samsung','smartphone','android','oppo','xiaomi','vivo','phone'],
        'earphone'    => ['tai nghe','airpods','earphone','headphone','earbud','âm thanh','bass','noise cancel'],
        'keyboard'    => ['bàn phím','keyboard','mechanical','gaming keyboard','logitech','razer','corsair'],
        'monitor'     => ['màn hình','monitor','display','4k','144hz','gaming monitor','ultrawide'],
        'accessory'   => ['phụ kiện','chuột','mouse','webcam','sạc','cáp','túi','balo','đế tản nhiệt','hub','ssd','ram'],
        'compare'     => ['so sánh','khác gì','tốt hơn','nên mua','lựa chọn','compare','versus','vs','hay hơn'],
        'recommend'   => ['tư vấn','gợi ý','nên mua','recommend','phù hợp','loại nào','cái nào tốt','mua gì'],
        'contact'     => ['liên hệ','hotline','hỗ trợ','contact','support','gặp nhân viên','tư vấn viên','gọi điện'],
        'thanks'      => ['cảm ơn','thank','thanks','cám ơn','oke','ok','được rồi','hiểu rồi','biết rồi'],
        'buy_help'    => ['giúp mua','hướng dẫn mua','mua như thế nào','cách mua','mua hàng','đặt hàng giúp','hướng dẫn đặt hàng'],
        'security'    => ['bảo mật','xác thực','otp','2fa','3fa','an toàn tài khoản','mất tài khoản','bị hack','security'],
    ];

    private array $responses = [
        'greeting' => [
            'vi' => ["👋 Xin chào! Mình là **TechBot**, trợ lý AI của TechStore. Bạn đang cần tìm gì hôm nay? 😊\n\nMình có thể giúp: **tư vấn sản phẩm** 💻📱, **đơn hàng** 📦, **giao hàng** 🚚, **bảo hành** 🛡️, **khuyến mãi** 🎉"],
            'en' => ["👋 Hello! I'm **TechBot**, TechStore's AI shopping assistant. What can I help you with today? 😊\n\nI can assist with: **product advice** 💻📱, **orders** 📦, **shipping** 🚚, **warranty** 🛡️, **promotions** 🎉"],
        ],
        'shipping' => [
            'vi' => ["🚚 **Chính sách giao hàng TechStore:**\n\n- **Miễn phí** cho đơn từ **500,000₫**\n- Nội thành HN/HCM: **1–2 ngày**\n- Tỉnh thành khác: **2–5 ngày** làm việc\n- Giao hàng nhanh cùng ngày (HN/HCM): thêm **30,000₫**\n\nSau khi đặt hàng bạn sẽ nhận mã tracking qua email 📧"],
            'en' => ["🚚 **TechStore Shipping Policy:**\n\n- **Free** on orders from **500,000₫**\n- Inner-city HN/HCM: **1–2 days**\n- Other provinces: **2–5 working days**\n- Same-day express (HN/HCM): +**30,000₫**\n\nTracking code sent by email after order confirmation 📧"],
        ],
        'return' => [
            'vi' => ["🔄 **Chính sách đổi trả:**\n\n- Đổi trả miễn phí trong **7 ngày** kể từ khi nhận hàng\n- Hàng lỗi kỹ thuật / sai sản phẩm: **hoàn tiền 100%**\n- Hàng nguyên đai nguyên kiện để được hỗ trợ tốt nhất\n\nCách yêu cầu: [Đơn hàng của tôi](/orders) → chọn đơn → **Yêu cầu đổi trả**\nHoặc gọi hotline **1800-xxxx** 📞"],
            'en' => ["🔄 **Return Policy:**\n\n- Free returns within **7 days** of receipt\n- Defective/wrong items: **100% refund**\n- Keep original packaging for best support\n\nHow to request: [My Orders](/orders) → select order → **Request Return**\nOr call hotline **1800-xxxx** 📞"],
        ],
        'payment' => [
            'vi' => ["💳 **Phương thức thanh toán:**\n\n- 💵 **COD** — thanh toán khi nhận hàng\n- 🏦 **Chuyển khoản** ngân hàng\n- 📱 **MoMo, VNPay, ZaloPay**\n- 💳 **Visa / Mastercard** (thanh toán quốc tế)\n\nMọi giao dịch được mã hóa bảo mật SSL 🔒"],
            'en' => ["💳 **Payment Methods:**\n\n- 💵 **COD** — pay on delivery\n- 🏦 **Bank transfer**\n- 📱 **MoMo, VNPay, ZaloPay**\n- 💳 **Visa / Mastercard** (international)\n\nAll transactions secured with SSL encryption 🔒"],
        ],
        'warranty' => [
            'vi' => ["🛡️ **Bảo hành chính hãng:**\n\n- Sản phẩm thông thường: **12 tháng**\n- Sản phẩm cao cấp (MacBook, iPhone, Sony...): **24 tháng**\n- Phụ kiện: **6–12 tháng**\n\nGặp vấn đề kỹ thuật? Liên hệ hotline **1800-xxxx** — TechStore hỗ trợ miễn phí 🆓"],
            'en' => ["🛡️ **Official Warranty:**\n\n- Standard products: **12 months**\n- Premium products (MacBook, iPhone, Sony...): **24 months**\n- Accessories: **6–12 months**\n\nTechnical issue? Call hotline **1800-xxxx** — free support from TechStore 🆓"],
        ],
        'promo' => [
            'vi' => ["🎉 **Ưu đãi TechStore:**\n\n- 🆕 Thành viên mới: giảm **10%** đơn đầu với mã **WELCOME10**\n- ⚡ Flash sale hằng ngày: **12:00–14:00** và **20:00–22:00**\n- 🎁 Tặng quà kèm đơn từ 2 triệu₫\n- 📧 Đăng ký email nhận thông báo sale sớm nhất\n\nXem tất cả ưu đãi tại [Cửa hàng](/products) 👀"],
            'en' => ["🎉 **TechStore Promotions:**\n\n- 🆕 New members: **10% off** first order with code **WELCOME10**\n- ⚡ Daily flash sales: **12:00–14:00** and **20:00–22:00**\n- 🎁 Free gift with orders over 2 million₫\n- 📧 Subscribe to email for early sale alerts\n\nSee all deals at [Store](/products) 👀"],
        ],
        'account' => [
            'vi' => ["👤 **Tài khoản TechStore:**\n\n- [Đăng ký](/register) miễn phí — chỉ 1 phút\n- Quên mật khẩu? → [Đặt lại mật khẩu](/forgot-password)\n- Đổi mật khẩu: Hồ sơ → Bảo mật → Đổi mật khẩu\n\nVẫn không vào được? Gọi hotline **1800-xxxx** 📞"],
            'en' => ["👤 **TechStore Account:**\n\n- [Sign up](/register) for free — takes 1 minute\n- Forgot password? → [Reset Password](/forgot-password)\n- Change password: Profile → Security → Change Password\n\nStill locked out? Call hotline **1800-xxxx** 📞"],
        ],
        'order' => [
            'vi' => ["📦 **Theo dõi đơn hàng:**\n\n1. Đăng nhập → [Đơn hàng của tôi](/orders)\n2. Chọn đơn cần xem → xem trạng thái realtime\n3. Mã tracking cũng được gửi qua **email** sau khi xác nhận\n\nĐơn đang xử lý có thể hủy trong **2 giờ** đầu sau khi đặt 🕐"],
            'en' => ["📦 **Track Your Order:**\n\n1. Log in → [My Orders](/orders)\n2. Select your order → view real-time status\n3. Tracking code also sent by **email** after confirmation\n\nPending orders can be cancelled within **2 hours** of placing 🕐"],
        ],
        'cancel' => [
            'vi' => ["❌ **Hủy đơn hàng:**\n\n- Đơn **đang xử lý**: hủy trong vòng **2 giờ** qua [Đơn hàng](/orders)\n- Đơn **đang giao**: liên hệ hotline **1800-xxxx ngay** để can thiệp kịp thời\n- Đơn **đã giao**: dùng chính sách [đổi trả](/orders) trong 7 ngày\n\n⚠️ Hủy sớm giúp hoàn tiền nhanh hơn!"],
            'en' => ["❌ **Cancel an Order:**\n\n- Order **processing**: cancel within **2 hours** via [My Orders](/orders)\n- Order **being shipped**: call hotline **1800-xxxx immediately**\n- Order **delivered**: use the [return policy](/orders) within 7 days\n\n⚠️ Cancelling early = faster refund!"],
        ],
        'product' => [
            'vi' => ["🛒 Xem tất cả sản phẩm tại [Cửa hàng](/products)\n\n**Danh mục nổi bật:**\n- 💻 Laptop (8–80 triệu₫)\n- 📱 Điện thoại (2–50 triệu₫)\n- 🎧 Tai nghe (200k–10 triệu₫)\n- ⌨️ Bàn phím & Chuột\n- 🖥️ Màn hình\n\nBạn đang tìm kiếm sản phẩm gì? Mình có thể tư vấn cụ thể hơn! 😊"],
            'en' => ["🛒 Browse all products at [Store](/products)\n\n**Top categories:**\n- 💻 Laptops (8–80M₫)\n- 📱 Smartphones (2–50M₫)\n- 🎧 Earphones (200k–10M₫)\n- ⌨️ Keyboards & Mice\n- 🖥️ Monitors\n\nWhat product are you looking for? I can give more specific advice! 😊"],
        ],
        'laptop' => [
            'vi' => ["💻 **Tư vấn laptop TechStore:**\n\n🎮 **Gaming**: ASUS ROG, Lenovo Legion, MSI — từ **18 triệu₫**\n📚 **Sinh viên / văn phòng**: ASUS VivoBook, Acer Swift, Dell Inspiron — từ **10 triệu₫**\n✈️ **Ultrabook mỏng nhẹ**: MacBook Air, LG Gram, Dell XPS — từ **22 triệu₫**\n💼 **Doanh nghiệp**: ThinkPad, HP EliteBook — từ **20 triệu₫**\n\nBạn dùng laptop cho mục đích gì? Mình tư vấn cụ thể hơn nhé 💡"],
            'en' => ["💻 **Laptop Recommendations:**\n\n🎮 **Gaming**: ASUS ROG, Lenovo Legion, MSI — from **18M₫**\n📚 **Student/Office**: ASUS VivoBook, Acer Swift, Dell Inspiron — from **10M₫**\n✈️ **Ultrabooks**: MacBook Air, LG Gram, Dell XPS — from **22M₫**\n💼 **Business**: ThinkPad, HP EliteBook — from **20M₫**\n\nWhat's your main use case? I'll give a more specific recommendation 💡"],
        ],
        'phone' => [
            'vi' => ["📱 **Tư vấn điện thoại:**\n\n🍎 **iPhone**: 14 (17M₫), 15 (22M₫), 16 (27M₫) — hệ sinh thái Apple, camera tốt\n📸 **Samsung**: Galaxy S24 (25M₫), A55 (12M₫) — màn hình đẹp, nhiều tùy chọn\n💰 **Tầm trung**: OPPO Reno11, Xiaomi 14T — hiệu năng tốt, giá hợp lý (7–12M₫)\n\nBạn ưu tiên camera, hiệu năng hay pin? Mình tư vấn thêm nhé 😊"],
            'en' => ["📱 **Smartphone Recommendations:**\n\n🍎 **iPhone**: 14 (17M₫), 15 (22M₫), 16 (27M₫) — Apple ecosystem, great camera\n📸 **Samsung**: Galaxy S24 (25M₫), A55 (12M₫) — beautiful display, versatile\n💰 **Mid-range**: OPPO Reno11, Xiaomi 14T — great performance, reasonable price (7–12M₫)\n\nWhat do you prioritize: camera, performance, or battery? 😊"],
        ],
        'earphone' => [
            'vi' => ["🎧 **Tư vấn tai nghe:**\n\n🔇 **Chống ồn tốt nhất**: Sony WH-1000XM5 (8M₫), Bose QC45 (7M₫)\n🎵 **True wireless phổ biến**: AirPods Pro 2 (6M₫), Samsung Buds2 Pro (3.5M₫), Sony WF-1000XM5 (5.5M₫)\n💰 **Tầm trung tốt**: Jabra Elite 4 (2M₫), Anker Soundcore (600k–1.5M₫)\n\nBạn muốn tai nghe in-ear hay over-ear? Có cần chống ồn không? 🎶"],
            'en' => ["🎧 **Earphone/Headphone Recommendations:**\n\n🔇 **Best ANC**: Sony WH-1000XM5 (8M₫), Bose QC45 (7M₫)\n🎵 **True wireless**: AirPods Pro 2 (6M₫), Samsung Buds2 Pro (3.5M₫), Sony WF-1000XM5 (5.5M₫)\n💰 **Mid-range value**: Jabra Elite 4 (2M₫), Anker Soundcore (600k–1.5M₫)\n\nIn-ear or over-ear? Do you need noise cancellation? 🎶"],
        ],
        'keyboard' => [
            'vi' => ["⌨️ **Tư vấn bàn phím:**\n\n🎮 **Gaming cơ học**: Logitech G Pro X (2.5M₫), Razer BlackWidow (2M₫)\n💼 **Văn phòng**: Logitech MX Keys (2M₫ — không dây, yên tĩnh)\n💰 **Giá tốt**: Keychron K2 (1.5M₫), Akko 3087 (700k)\n\nBạn cần dùng cho gaming hay văn phòng? Thích switch nhẹ hay nặng? ⌨️"],
            'en' => ["⌨️ **Keyboard Recommendations:**\n\n🎮 **Gaming mechanical**: Logitech G Pro X (2.5M₫), Razer BlackWidow (2M₫)\n💼 **Office**: Logitech MX Keys (2M₫ — wireless, quiet)\n💰 **Budget picks**: Keychron K2 (1.5M₫), Akko 3087 (700k)\n\nGaming or office use? Light or heavy switch feel? ⌨️"],
        ],
        'monitor' => [
            'vi' => ["🖥️ **Tư vấn màn hình:**\n\n🎮 **Gaming**: LG UltraGear 27\" 165Hz (7M₫), Samsung Odyssey G5 (8M₫)\n🎨 **Đồ họa / chỉnh màu**: LG 27\" 4K IPS (10M₫), Dell U2722D (12M₫)\n💼 **Văn phòng**: Dell 24\" FHD (4M₫), LG 27\" QHD (6M₫)\n\nBạn dùng để gaming, thiết kế hay văn phòng? Cần màn hình bao nhiêu inch? 🖥️"],
            'en' => ["🖥️ **Monitor Recommendations:**\n\n🎮 **Gaming**: LG UltraGear 27\" 165Hz (7M₫), Samsung Odyssey G5 (8M₫)\n🎨 **Design/Color work**: LG 27\" 4K IPS (10M₫), Dell U2722D (12M₫)\n💼 **Office**: Dell 24\" FHD (4M₫), LG 27\" QHD (6M₫)\n\nGaming, design, or office? What screen size do you prefer? 🖥️"],
        ],
        'accessory' => [
            'vi' => ["🔌 **Phụ kiện công nghệ:**\n\n- 🖱️ Chuột: Logitech MX Master 3 (2M₫), G304 (700k)\n- 📷 Webcam: Logitech C920 (1.5M₫)\n- 💾 SSD: Samsung 870 EVO, WD Blue (từ 500k)\n- 🔋 Sạc dự phòng: Anker, Xiaomi (300k–1M₫)\n- 🎒 Túi laptop: 200k–1M₫\n\nXem thêm tại [Phụ kiện](/products?category=accessories) 🛒"],
            'en' => ["🔌 **Tech Accessories:**\n\n- 🖱️ Mouse: Logitech MX Master 3 (2M₫), G304 (700k)\n- 📷 Webcam: Logitech C920 (1.5M₫)\n- 💾 SSD: Samsung 870 EVO, WD Blue (from 500k)\n- 🔋 Power bank: Anker, Xiaomi (300k–1M₫)\n- 🎒 Laptop bags: 200k–1M₫\n\nSee more at [Accessories](/products?category=accessories) 🛒"],
        ],
        'compare' => [
            'vi' => ["🔍 Để so sánh sản phẩm chính xác, bạn cho mình biết **tên cụ thể** của 2 sản phẩm muốn so sánh nhé!\n\nVí dụ: *\"So sánh MacBook Air M2 và Dell XPS 13\"* hoặc *\"iPhone 15 vs Samsung S24\"*\n\nMình sẽ phân tích điểm mạnh/yếu cho bạn ngay 📊"],
            'en' => ["🔍 To compare products accurately, please tell me the **specific names** of the 2 products!\n\nExample: *\"Compare MacBook Air M2 vs Dell XPS 13\"* or *\"iPhone 15 vs Samsung S24\"*\n\nI'll analyze pros and cons for you right away 📊"],
        ],
        'recommend' => [
            'vi' => ["💡 Mình rất vui được tư vấn! Để gợi ý đúng nhất, bạn cho mình biết:\n\n1️⃣ **Mục đích dùng** (gaming, học tập, văn phòng, đồ họa...)\n2️⃣ **Ngân sách** (khoảng bao nhiêu triệu₫?)\n3️⃣ **Sản phẩm nào** (laptop, điện thoại, tai nghe...?)\n\nTin tưởng mình, mình sẽ tìm ra lựa chọn tốt nhất cho bạn 🎯"],
            'en' => ["💡 Happy to help you choose! To give the best recommendation, please tell me:\n\n1️⃣ **Use case** (gaming, studies, office, design...)\n2️⃣ **Budget** (approximately how many million₫?)\n3️⃣ **Product type** (laptop, phone, earphones...?)\n\nTrust me, I'll find the best option for you 🎯"],
        ],
        'contact' => [
            'vi' => ["📞 **Liên hệ TechStore:**\n\n- ☎️ Hotline: **1800-xxxx** (8h–22h, Thứ 2–CN)\n- 📧 Email: **support@techstore.vn**\n- 💬 Chat: [Liên hệ trực tiếp](/lien-he)\n\nĐội ngũ hỗ trợ phản hồi trong **15 phút** trong giờ hành chính 🚀"],
            'en' => ["📞 **Contact TechStore:**\n\n- ☎️ Hotline: **1800-xxxx** (8am–10pm, Mon–Sun)\n- 📧 Email: **support@techstore.vn**\n- 💬 Chat: [Contact Us](/lien-he)\n\nSupport team responds within **15 minutes** during business hours 🚀"],
        ],
        'security' => [
            'vi' => ["🔐 **Bảo mật tài khoản TechStore:**\n\n- Hệ thống **xác thực 3 lớp (3FA)** bảo vệ tài khoản\n- AI tự động phát hiện đăng nhập bất thường\n- Nhận **cảnh báo email** khi có đăng nhập từ thiết bị lạ\n\n✅ Khuyến nghị: dùng mật khẩu mạnh + không chia sẻ OTP\nBị mất tài khoản? Gọi hotline **1800-xxxx** ngay!"],
            'en' => ["🔐 **TechStore Account Security:**\n\n- **3-factor authentication (3FA)** protects your account\n- AI automatically detects abnormal login attempts\n- Receive **email alerts** for logins from unknown devices\n\n✅ Tips: use strong passwords + never share your OTP\nAccount compromised? Call hotline **1800-xxxx** immediately!"],
        ],
        'thanks' => [
            'vi' => ["😊 Không có gì! Nếu cần thêm hỗ trợ cứ nhắn mình nhé. Chúc bạn mua sắm vui vẻ tại TechStore! 🛍️","🙌 Rất vui được giúp bạn! Có gì cần hỏi thêm không nào? TechBot luôn sẵn sàng 24/7 💪"],
            'en' => ["😊 You're welcome! Feel free to ask if you need anything else. Happy shopping at TechStore! 🛍️","🙌 Glad I could help! Any other questions? TechBot is here 24/7 💪"],
        ],
        'buy_help' => [
            'vi' => ["🛒 **Hướng dẫn mua hàng tại TechStore:**\n\n1️⃣ Vào [Cửa hàng](/products) → chọn sản phẩm → **Thêm vào giỏ**\n2️⃣ Vào [Giỏ hàng](/cart) → kiểm tra → nhấn **Đặt hàng**\n3️⃣ Nhập địa chỉ giao hàng → chọn phương thức thanh toán\n4️⃣ Xác nhận → nhận email xác nhận 📧\n5️⃣ Theo dõi đơn tại [Đơn hàng](/orders)\n\nCần tư vấn sản phẩm cụ thể không? 😊"],
            'en' => ["🛒 **How to shop at TechStore:**\n\n1️⃣ Go to [Store](/products) → select product → **Add to Cart**\n2️⃣ Go to [Cart](/cart) → review → click **Place Order**\n3️⃣ Enter shipping address → select payment method\n4️⃣ Confirm → receive confirmation email 📧\n5️⃣ Track order at [My Orders](/orders)\n\nNeed advice on a specific product? 😊"],
        ],
        'fallback' => [
            'vi' => ["🤔 Mình chưa hiểu rõ câu hỏi của bạn. Bạn có thể hỏi về:\n\n💻 **Sản phẩm** (laptop, điện thoại, tai nghe...)\n📦 **Đơn hàng** (trạng thái, hủy đơn)\n🚚 **Giao hàng & đổi trả**\n💳 **Thanh toán**\n🎉 **Khuyến mãi**\n\nHoặc gọi hotline **1800-xxxx** để được hỗ trợ trực tiếp!","🙋 Mình chưa rõ ý bạn muốn hỏi. Thử diễn đạt lại hoặc chọn chủ đề bên dưới nhé! Nếu cần gấp: hotline **1800-xxxx** (8h–22h) 📞"],
            'en' => ["🤔 I'm not sure I understood your question. You can ask about:\n\n💻 **Products** (laptops, phones, earphones...)\n📦 **Orders** (status, cancellation)\n🚚 **Shipping & Returns**\n💳 **Payment**\n🎉 **Promotions**\n\nOr call hotline **1800-xxxx** for direct support!","🙋 I didn't quite get that. Try rephrasing or choose a topic below! For urgent help: hotline **1800-xxxx** (8am–10pm) 📞"],
        ],
    ];

    private function detectLanguage(string $text): string
    {
        // Vietnamese-specific characters
        $viPattern = '/[àáâãèéêìíòóôõùúýăđơưạảấầẩẫậắằẳẵặẹẻẽếềểễệỉịọỏốồổỗộớờởỡợụủứừửữựỳỵỷỹ]/iu';
        if (preg_match($viPattern, $text)) {
            return 'vi';
        }
        // Vietnamese words without diacritics
        $viWords = ['ban', 'toi', 'minh', 'hang', 'san pham', 'don hang', 'gui hang', 'thanh toan', 'ho tro'];
        $lower = mb_strtolower($text);
        foreach ($viWords as $w) {
            if (str_contains($lower, $w)) return 'vi';
        }
        return 'en';
    }

    private function detectIntent(string $text): string
    {
        $text = mb_strtolower($text);
        $best = 'fallback';
        $bestScore = 0;
        foreach ($this->intents as $intent => $keywords) {
            $score = 0;
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) {
                    $score += mb_strlen($kw);
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $intent;
            }
        }
        return $best;
    }

    private function ruleBasedReply(string $userMsg): string
    {
        $intent = $this->detectIntent($userMsg);
        $lang   = $this->detectLanguage($userMsg);
        $pool   = $this->responses[$intent][$lang]
               ?? $this->responses[$intent]['en']
               ?? $this->responses['fallback'][$lang]
               ?? $this->responses['fallback']['en'];
        return $pool[array_rand($pool)];
    }

    // ── POST /chatbot/reply ───────────────────────────────────────────
    public function reply(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMsg = trim($request->input('message'));

        // Detect language and inject hint so AI always replies in correct language
        $lang         = $this->detectLanguage($userMsg);
        $langHint     = $lang === 'vi'
            ? '[SYSTEM: Reply in Vietnamese ONLY]'
            : '[SYSTEM: Reply in English ONLY]';
        $userMsgForAI = "{$langHint}\n{$userMsg}";

        // Build conversation history from session (last 6 turns)
        $history  = session('chatbot_history', []);
        $messages = [['role' => 'system', 'content' => $this->systemPrompt]];
        foreach (array_slice($history, -10) as $turn) {
            // Normalize legacy 'bot' role → 'assistant'
            $role = $turn['role'] === 'bot' ? 'assistant' : $turn['role'];
            if (!in_array($role, ['user', 'assistant'])) continue;
            $messages[] = ['role' => $role, 'content' => $turn['msg']];
        }
        $messages[] = ['role' => 'user', 'content' => $userMsgForAI];

        $reply = null;

        // 1. Try Gemini native REST API
        $geminiKey = config('services.gemini.key');
        if (!empty($geminiKey) && $reply === null) {
            $reply = $this->callGemini($geminiKey, $messages);
        }

        // 2. Fallback Groq if Gemini errors
        $groqKey = config('services.groq.key');
        if (!empty($groqKey) && $reply === null) {
            try {
                $client   = OpenAI::factory()
                    ->withApiKey($groqKey)
                    ->withBaseUri('api.groq.com/openai/v1')
                    ->make();
                $response = $client->chat()->create([
                    'model'       => config('services.groq.model', 'llama-3.3-70b-versatile'),
                    'messages'    => $messages,
                    'max_tokens'  => 600,
                    'temperature' => 0.65,
                ]);
                $reply = trim($response->choices[0]->message->content ?? '');
            } catch (\Throwable $e) {
                \Log::warning('ChatbotController Groq error: ' . $e->getMessage());
            }
        }

        // 3. Fallback rule-based
        if (empty($reply)) {
            $reply = $this->ruleBasedReply($userMsg);
        }

        // Save to session history
        $history[] = ['role' => 'user',      'msg' => mb_substr($userMsg, 0, 500)];
        $history[] = ['role' => 'assistant',  'msg' => mb_substr($reply,   0, 500)];
        session(['chatbot_history' => array_slice($history, -20)]);

        return response()->json(['reply' => $reply]);
    }

    private function callGemini(string $apiKey, array $messages): ?string
    {
        try {
            // Convert messages: extract system prompt, convert to Gemini format
            $systemText = '';
            $contents   = [];
            foreach ($messages as $msg) {
                if ($msg['role'] === 'system') {
                    $systemText = $msg['content'];
                    continue;
                }
                $role = $msg['role'] === 'assistant' ? 'model' : 'user';
                $contents[] = ['role' => $role, 'parts' => [['text' => $msg['content']]]];
            }

            $body = [
                'system_instruction' => ['parts' => [['text' => $systemText]]],
                'contents'           => $contents,
                'generationConfig'   => ['maxOutputTokens' => 600, 'temperature' => 0.65],
            ];

            $model = config('services.gemini.model', 'gemini-2.0-flash');
            $url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($body),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 15,
            ]);
            $raw  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($code !== 200) {
                \Log::warning("ChatbotController Gemini HTTP {$code}: " . $raw);
                return null;
            }

            $data = json_decode($raw, true);
            return trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
        } catch (\Throwable $e) {
            \Log::warning('ChatbotController Gemini error: ' . $e->getMessage());
            return null;
        }
    }
}

