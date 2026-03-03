<?php

namespace App\Http\Controllers;

use App\Models\ChatbotAnalytic;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\Coupon;
use App\Models\FAQ;
use App\Models\Order;
use App\Models\Product;
use App\Models\SuspiciousLogin;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    /**
     * Start or get existing conversation
     */
    public function startConversation(Request $request)
    {
        $user = Auth::user();

        // Get or create user preferences
        if ($user) {
            UserPreference::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'favorite_categories' => [],
                    'price_range' => ['min' => 0, 'max' => 100000000],
                    'viewed_products' => [],
                ]
            );
        }

        // Create new conversation
        $conversation = ChatbotConversation::create([
            'user_id' => $user?->id,
            'topic' => $request->input('topic', 'general'),
            'message_count' => 0,
        ]);

        // Build personalized greeting message
        $userName = 'bạn';
        if ($user) {
            $fullName = trim($user->name);
            // Check if name has space
            if (strpos($fullName, ' ') !== false) {
                // Has space: take last name (Nguyễn Tinh -> Tinh) - Vietnamese naming convention
                $nameParts = explode(' ', $fullName);
                $userName = end($nameParts) ?: 'bạn';
            } else {
                // No space: use full name (NguyễnTinh -> NguyễnTinh)
                $userName = $fullName;
            }
        }

        $greeting = "Xin chào $userName! 👋\nMình có thể giúp gì cho bạn? 😊\nMình hỗ trợ:\n💻 Tìm sản phẩm phù hợp\n💰 So sánh giá và tính năng\n🛒 Hỗ trợ mua sắm\n❓ Trả lời câu hỏi\n🎁 Mã giảm giá";

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'user_name' => $userName,
            'user_full_name' => $user?->name,
            'message' => $greeting,
        ]);
    }

    /**
     * Send message to chatbot
     */
    public function sendMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'conversation_id' => 'required|exists:chatbot_conversations,id',
                'message' => 'required|string|max:1000',
            ]);

            $conversation = ChatbotConversation::find($validated['conversation_id']);
            if (! $conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc trò chuyện không tồn tại',
                ], 404);
            }

            $userMessage = $validated['message'];

            // Store user message
            ChatbotMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'user',
                'message' => $userMessage,
            ]);

            // Process message and generate response
            $botResponse = $this->processUserMessage($userMessage, $conversation);

            if (! $botResponse) {
                throw new \Exception('Bot response is null');
            }

            // Store bot response
            ChatbotMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'bot',
                'message' => $botResponse['message'],
                'suggested_products' => json_encode($botResponse['products'] ?? []),
                'metadata' => json_encode($botResponse['metadata'] ?? []),
            ]);

            // Update conversation
            $conversation->update([
                'message_count' => $conversation->message_count + 2,
                'last_message_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'bot_message' => $botResponse['message'],
                'suggested_products' => $botResponse['products'] ?? [],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: '.implode(', ', $e->errors()['message'] ?? []),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process user message and generate bot response
     */
    private function processUserMessage($message, $conversation)
    {
        $message_lower = trim(strtolower($message));
        $products = [];
        $metadata = ['intent' => 'general'];
        $response = '';

        // Enhanced Vietnamese product keywords with aliases
        $productKeywords = [
            // Mice
            'chuột' => ['mouse', 'chuột', 'chuột game', 'chuột gaming'],
            'mouse' => ['mouse', 'chuột'],

            // Keyboards
            'bàn phím' => ['keyboard', 'bàn phím', 'keyboard cơ', 'bàn phím cơ'],
            'keyboard' => ['keyboard', 'bàn phím'],

            // Headsets
            'headset' => ['headset', 'tai nghe', 'tai nghe chơi game'],
            'tai nghe' => ['headset', 'tai nghe'],
            'audio' => ['headset', 'speaker', 'tai nghe'],

            // Monitors
            'monitor' => ['monitor', 'màn hình'],
            'màn hình' => ['monitor', 'màn hình'],
            'display' => ['monitor', 'màn hình'],

            // Laptops
            'laptop' => ['laptop', 'máy tính xách tay', 'notebook'],
            'máy tính' => ['laptop', 'máy tính', 'desktop'],

            // Gaming gear
            'gaming' => ['mouse', 'keyboard', 'headset', 'monitor', 'gaming chair'],

            // PC & Components
            'pc' => ['laptop', 'máy tính', 'pc'],
            'cpu' => ['processor', 'cpu'],
            'gpu' => ['graphics card', 'gpu', 'vga'],
            'ram' => ['ram', 'memory'],
            'ssd' => ['ssd', 'ổ cứng'],
        ];

        // 1. GREETING - Check first for friendly interaction
        if (preg_match('/^(xin chào|hello|hi|chào|yêu|thank|cảm ơn)[^?]*$/', $message_lower)) {
            $metadata['intent'] = 'greeting';
            $response = "Xin chào! 👋 Rất vui được gặp bạn! Tôi là AI Assistant của TechStore.\n\n"
                ."Tôi có thể giúp bạn:\n"
                ."💻 **Tìm sản phẩm** - hỏi tôi về chuột, bàn phím, headset, monitor, laptop, v.v.\n"
                ."💰 **Tìm theo giá** - ví dụ: 'Sản phẩm dưới 5 triệu' hay 'từ 3-10 triệu'\n"
                ."⚡ **So sánh** - ví dụ: 'So sánh chuột A và B'\n"
                ."� **Kiểm tra đơn hàng** - hỏi 'Đơn hàng của tôi ở đâu?'\n"
                ."🎁 **Mã giảm giá** - hỏi về khuyến mãi có sẵn\n"
                ."❓ **Hỏi FAQ** - hỏi về chính sách, thanh toán, giao hàng\n"
                ."📞 **Nhận hỗ trợ** - hỏi về vấn đề\n\n"
                .'Bạn muốn tìm gì hôm nay?';

            return [
                'message' => $response,
                'products' => [],
                'metadata' => $metadata,
            ];
        }

        // 1.5 ORDER TRACKING - Check for order-related keywords
        if (preg_match('/đơn hàng|order|kiểm tra|check|track|giao|vị trí|ở đâu|status/', $message_lower)) {
            // This will be handled by API endpoint
            $metadata['intent'] = 'check-order';
            $response = '📦 Vui lòng chờ, tôi đang kiểm tra đơn hàng của bạn...';

            $this->trackMetric('intent', 'check-order', $conversation->id);

            return [
                'message' => $response,
                'products' => [],
                'metadata' => $metadata,
            ];
        }

        // 1.7 COMPARISON - Detect comparison requests
        if (preg_match('/so sánh|nên mua|cái nào|khác gì|tốt hơn|vs|versus/', $message_lower)) {
            $metadata['intent'] = 'comparison';

            // Try to extract product names from conversation history
            $lastMessages = ChatbotMessage::where('conversation_id', $conversation->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->pluck('message')
                ->toArray();

            $response = "📊 **So Sánh Sản Phẩm**\n\n"
                ."Tôi có thể giúp bạn so sánh giữa các sản phẩm khác nhau!\n\n"
                ."Hãy nói tên 2-3 sản phẩm bạn muốn so sánh, ví dụ:\n"
                ."• 'So sánh chuột Logitech và Razer'\n"
                ."• 'Monitor 144Hz nào tốt nhất?'\n"
                ."• 'Nên mua keyboard cơ hay keyboard màn hình?'\n\n"
                .'Tôi sẽ giúp bạn so sánh giá, ưu điểm và nhược điểm! 💪';

            $this->trackMetric('intent', 'comparison', $conversation->id);

            return [
                'message' => $response,
                'products' => [],
                'metadata' => $metadata,
            ];
        }

        // 2. PRICE RANGE SEARCH - Handle "từ X đến Y", "dưới X", "trên X"
        if (preg_match('/từ\s*(\d+)\s*đến\s*(\d+)\s*(triệu|k)?/i', $message_lower, $matches) ||
            preg_match('/từ\s*(\d+)\s*-\s*(\d+)\s*(triệu|k)?/i', $message_lower, $matches)) {

            $minPrice = (int) $matches[1];
            $maxPrice = (int) $matches[2];
            $unit = $matches[3] ?? 'triệu';

            $minPriceVND = ($unit === 'k' ? $minPrice * 1000 : $minPrice * 1000000);
            $maxPriceVND = ($unit === 'k' ? $maxPrice * 1000 : $maxPrice * 1000000);

            $metadata['intent'] = 'price-range-search';
            $metadata['price_range'] = ['min' => $minPrice, 'max' => $maxPrice, 'unit' => $unit];

            $products = Product::whereBetween('price', [$minPriceVND, $maxPriceVND])
                ->orderBy('price', 'asc')
                ->limit(6)
                ->get()
                ->toArray();

            $minFormatted = number_format($minPriceVND, 0, '.', ',');
            $maxFormatted = number_format($maxPriceVND, 0, '.', ',');

            if (empty($products)) {
                $response = "Xin lỗi, chúng tôi không có sản phẩm nào trong khoảng {$minFormatted}đ - {$maxFormatted}đ. "
                    ."Dưới đây là các sản phẩm gần với giá bạn tìm:\n\n";
                $products = Product::whereBetween('price', [$minPriceVND * 0.8, $maxPriceVND * 1.2])
                    ->orderBy('price', 'asc')
                    ->limit(5)
                    ->get()
                    ->toArray();
            } else {
                $response = '✅ Tôi tìm thấy '.count($products)." sản phẩm trong khoảng {$minFormatted}đ - {$maxFormatted}đ:\n\n";
            }

            return $this->generateSmartProductResponse($response, $products, $metadata);
        }

        // 3. SIMPLE PRICE SEARCH - "dưới 5 triệu", "trên 10 triệu"
        if (preg_match('/(dưới|under|below)\s*(\d+)\s*(triệu|k)?/i', $message_lower, $matches)) {
            $maxPrice = (int) $matches[2];
            $unit = $matches[3] ?? 'triệu';
            $maxPriceVND = ($unit === 'k' ? $maxPrice * 1000 : $maxPrice * 1000000);

            $metadata['intent'] = 'price-search-below';
            $metadata['max_price'] = $maxPrice;

            $products = Product::where('price', '<=', $maxPriceVND)
                ->orderBy('price', 'asc')
                ->limit(6)
                ->get()
                ->toArray();

            if (empty($products)) {
                $response = 'Xin lỗi, chúng tôi không có sản phẩm nào dưới '.number_format($maxPriceVND, 0, '.', ',').'đ. '
                    ."Dưới đây là các sản phẩm giá rẻ nhất của chúng tôi:\n\n";
                $products = Product::orderBy('price', 'asc')->limit(5)->get()->toArray();
            } else {
                $response = '💰 Tôi tìm thấy '.count($products).' sản phẩm dưới '.number_format($maxPriceVND, 0, '.', ',')."đ:\n\n";
            }

            return $this->generateSmartProductResponse($response, $products, $metadata);
        }

        if (preg_match('/(trên|above|over)\s*(\d+)\s*(triệu|k)?/i', $message_lower, $matches)) {
            $minPrice = (int) $matches[2];
            $unit = $matches[3] ?? 'triệu';
            $minPriceVND = ($unit === 'k' ? $minPrice * 1000 : $minPrice * 1000000);

            $metadata['intent'] = 'price-search-above';
            $metadata['min_price'] = $minPrice;

            $products = Product::where('price', '>=', $minPriceVND)
                ->orderBy('price', 'desc')
                ->limit(6)
                ->get()
                ->toArray();

            if (empty($products)) {
                $response = 'Hiện tại chúng tôi không có sản phẩm nào trên '.number_format($minPriceVND, 0, '.', ',')."đ.\n\n";
            } else {
                $response = '⭐ Tôi tìm thấy '.count($products).' sản phẩm cao cấp trên '.number_format($minPriceVND, 0, '.', ',')."đ:\n\n";
            }

            return $this->generateSmartProductResponse($response, $products, $metadata);
        }

        // 4. PRODUCT SEARCH BY KEYWORD/CATEGORY
        foreach ($productKeywords as $keyword => $searchTerms) {
            if (strpos($message_lower, $keyword) !== false) {
                $metadata['intent'] = 'product-search';
                $metadata['product_type'] = $keyword;

                // Build smart search query
                $query = Product::query();
                foreach ($searchTerms as $term) {
                    $query->orWhere('name', 'like', '%'.$term.'%')
                        ->orWhere('description', 'like', '%'.$term.'%');
                }

                $products = $query->limit(6)->get()->toArray();

                if (empty($products)) {
                    $products = Product::inRandomOrder()->limit(4)->get()->toArray();
                    $response = "Hmmm, chúng tôi hiện tại không có $keyword nào. "
                        ."Nhưng đây là một số sản phẩm khác mà bạn có thể thích:\n\n";
                } else {
                    $response = '✨ Tôi tìm thấy '.count($products)." sản phẩm $keyword cho bạn "
                        ."(được xếp theo độ liên quan):\n\n";
                }

                return $this->generateSmartProductResponse($response, $products, $metadata);
            }
        }

        // 5. COMPARISON REQUEST - "so sánh", "cái nào tốt hơn"
        if (preg_match('/so sánh|cái nào|khác gì|tốt hơn|difference|vs/', $message_lower)) {
            $metadata['intent'] = 'comparison';

            $response = "📊 **So Sánh Sản Phẩm**\n\n"
                ."Bạn có thể hỏi tôi về:\n"
                ."• 'So sánh chuột A và chuột B'\n"
                ."• 'Chuột gaming nào tốt nhất?'\n"
                ."• 'Bàn phím cơ hay bàn phím màn hình?'\n\n"
                .'Hãy nói cụ thể 2 sản phẩm bạn muốn so sánh nhé! 😊';

            return [
                'message' => $response,
                'products' => [],
                'metadata' => $metadata,
            ];
        }

        // 6. RECOMMENDATION REQUEST
        if (preg_match('/gợi ý|recommend|nên|tốt nhất|best|hay nhất/', $message_lower)) {
            $metadata['intent'] = 'recommendation';

            $response = "🏆 **Sản Phẩm Được Khuyến Nghị**\n\n"
                ."Dựa trên tìm kiếm phổ biến, dưới đây là các sản phẩm bán chạy nhất:\n\n";

            $products = Product::orderBy('id', 'desc')->limit(6)->get()->toArray();

            return $this->generateSmartProductResponse($response, $products, $metadata);
        }

        // 6.5 DISCOUNT / COUPON REQUEST
        if (preg_match('/mã|giảm|discount|coupon|khuyến|promo|sale/', $message_lower)) {
            $metadata['intent'] = 'discount-request';
            $response = "🎁 **MÃ GIẢM GIÁ**\n\n"
                ."Tôi đang tìm các mã giảm giá có sẵn cho bạn...\n"
                .'Vui lòng chờ! ⏳';

            $this->trackMetric('intent', 'discount-request', $conversation->id);

            return [
                'message' => $response,
                'products' => [],
                'metadata' => $metadata,
            ];
        }

        // 6.7 FAQ/KNOWLEDGE BASE
        if (preg_match('/hỏi|câu hỏi|faq|thường gặp|cách|hướng dẫn|làm sao|như thế nào/', $message_lower)) {
            $metadata['intent'] = 'faq-search';
            $response = "❓ **TRÍCH CẦU HỎI THƯỜNG GẶP**\n\n"
                ."Hãy cụ thể hơn về câu hỏi của bạn:\n"
                ."• Thanh toán như thế nào?\n"
                ."• Chính sách đổi trả là gì?\n"
                ."• Bao lâu mới giao hàng?\n"
                ."• Bảo hành sản phẩm?\n\n"
                .'Hoặc mô tả vấn đề cụ thể của bạn!';

            $this->trackMetric('intent', 'faq-request', $conversation->id);

            return [
                'message' => $response,
                'products' => [],
                'metadata' => $metadata,
            ];
        }

        // 7. SUPPORT REQUEST
        if (preg_match('/hỗ trợ|support|giúp|vấn đề|lỗi|không hoạt động|đơn hàng|thanh toán|trả hàng|refund/', $message_lower)) {
            $metadata['intent'] = 'support';
            $response = "📞 **Trung tâm Hỗ trợ TechStore**\n\n"
                ."Tôi có thể giúp bạn về:\n\n"
                ."📦 **Đơn hàng & Vận chuyển**\n"
                ."   - Kiểm tra trạng thái đơn hàng\n"
                ."   - Vấn đề giao hàng\n\n"
                ."💳 **Thanh Toán**\n"
                ."   - Hỗ trợ thanh toán\n"
                ."   - Các phương thức thanh toán có sẵn\n\n"
                ."🔄 **Đổi & Trả Hàng**\n"
                ."   - Chính sách đổi trả\n"
                ."   - Yêu cầu hoàn tiền\n\n"
                ."❓ **Câu Hỏi Chung**\n"
                ."   - Bảo hành sản phẩm\n"
                ."   - Thông tin sản phẩm\n\n"
                ."📧 **Liên hệ trực tiếp**: support@techstore.com\n"
                .'📱 **Hotline**: 1900-XXXX-XXXX';

            return [
                'message' => $response,
                'products' => [],
                'metadata' => $metadata,
            ];
        }

        // 8. GET USER PREFERENCES - Remember past searches
        if (preg_match('/lịch sử|đã tìm|tìm gần đây|danh sách|yêu thích/', $message_lower)) {
            $metadata['intent'] = 'history';

            $userMessages = ChatbotMessage::where('conversation_id', $conversation->id)
                ->where('sender', 'user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->pluck('message')
                ->toArray();

            if (empty($userMessages)) {
                $response = 'Bạn chưa tìm kiếm sản phẩm nào trong cuộc trò chuyện này. '
                    .'Hãy hỏi tôi về sản phẩm nào bạn quan tâm! 😊';
            } else {
                $response = "📋 **Các Tìm Kiếm Gần Đây Của Bạn**\n\n";
                foreach ($userMessages as $index => $msg) {
                    $response .= ($index + 1).'. '.substr($msg, 0, 50)."...\n";
                }
            }

            return [
                'message' => $response,
                'products' => [],
                'metadata' => $metadata,
            ];
        }

        // 9. DEFAULT - Smart fallback with products + helpful tips
        $response = "🤔 Tôi chưa hiểu rõ lắm... nhưng đây là một số sản phẩm nổi bật cho bạn:\n\n";
        $products = Product::inRandomOrder()->limit(6)->get()->toArray();

        $response .= "**💡 Mẹo:** Bạn có thể nói:\n"
            ."• 'Chuột gaming' hoặc 'bàn phím cơ'\n"
            ."• 'Sản phẩm dưới 5 triệu' hoặc 'từ 3-10 triệu'\n"
            ."• 'So sánh [sản phẩm A] và [sản phẩm B]'\n"
            ."• 'Hỗ trợ' để liên hệ với chúng tôi\n\n";

        return $this->generateSmartProductResponse($response, $products, $metadata);
    }

    /**
     * Generate smart product response with rich formatting
     */
    private function generateSmartProductResponse($headerMessage, $products, $metadata)
    {
        $response = $headerMessage;

        if (! empty($products)) {
            foreach (array_slice($products, 0, 6) as $index => $product) {
                $productName = $product['name'] ?? 'Sản phẩm';
                $productPrice = number_format($product['price'] ?? 0, 0, '.', ',');
                $productId = $product['id'] ?? '';

                // Add emoji based on product type
                $emoji = $this->getProductEmoji($product['name'] ?? '');

                $response .= ($index + 1).". {$emoji} **".$productName."**\n";
                $response .= '   💰 Giá: **'.$productPrice."đ**\n";

                if (isset($product['description']) && ! empty($product['description'])) {
                    $description = substr(strip_tags($product['description']), 0, 80);
                    $response .= '   📝 '.$description."...\n";
                }

                $response .= "\n";
            }
        } else {
            $response .= 'Hiện tại chúng tôi không có sản phẩm phù hợp. Vui lòng liên hệ hỗ trợ!';
        }

        return [
            'message' => $response,
            'products' => $products,
            'metadata' => $metadata,
        ];
    }

    /**
     * Generate product response (legacy method - kept for compatibility)
     */
    private function generateProductResponse($headerMessage, $products, $metadata)
    {
        return $this->generateSmartProductResponse($headerMessage, $products, $metadata);
    }

    /**
     * Get emoji for product type
     */
    private function getProductEmoji($productName)
    {
        $name_lower = strtolower($productName);

        if (strpos($name_lower, 'chuột') !== false || strpos($name_lower, 'mouse') !== false) {
            return '🖱️';
        } elseif (strpos($name_lower, 'bàn phím') !== false || strpos($name_lower, 'keyboard') !== false) {
            return '⌨️';
        } elseif (strpos($name_lower, 'headset') !== false || strpos($name_lower, 'tai nghe') !== false) {
            return '🎧';
        } elseif (strpos($name_lower, 'monitor') !== false || strpos($name_lower, 'màn hình') !== false) {
            return '🖥️';
        } elseif (strpos($name_lower, 'laptop') !== false || strpos($name_lower, 'máy tính') !== false) {
            return '💻';
        } else {
            return '⚙️';
        }
    }

    /**
     * Get conversation history
     */
    public function getHistory(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:chatbot_conversations,id',
        ]);

        $messages = ChatbotMessage::where('conversation_id', $validated['conversation_id'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Detect suspicious login
     */
    public function checkSuspiciousLogin(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false], 401);
        }

        $ipAddress = $request->ip();
        $isSuspicious = false;

        // Check if IP is new
        $hasSameIp = $user->loginLogs()->where('ip_address', $ipAddress)->exists();
        if (! $hasSameIp) {
            $isSuspicious = true;
            SuspiciousLogin::create([
                'user_id' => $user->id,
                'ip_address' => $ipAddress,
                'risk_level' => 'medium',
                'reason' => 'Đăng nhập từ IP mới',
            ]);
        }

        return response()->json([
            'success' => true,
            'is_suspicious' => $isSuspicious,
        ]);
    }

    /**
     * Get personalized recommendations based on conversation history
     */
    public function getRecommendations(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'conversation_id' => 'required|exists:chatbot_conversations,id',
        ]);

        $conversation = ChatbotConversation::find($validated['conversation_id']);

        // Get user preferences if authenticated
        if ($user) {
            $userPref = UserPreference::where('user_id', $user->id)->first();

            // If user has favorite categories, recommend from those
            if ($userPref && ! empty($userPref['favorite_categories'])) {
                $recommendations = Product::whereIn('category', $userPref['favorite_categories'])
                    ->orderByRaw('RAND()')
                    ->limit(5)
                    ->get();
            } else {
                // Default: highest rated / best sellers
                $recommendations = Product::orderByRaw('RAND()')
                    ->limit(5)
                    ->get();
            }
        } else {
            // Guest user: show popular products
            $recommendations = Product::inRandomOrder()->limit(5)->get();
        }

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Compare multiple products with detailed specs
     */
    public function compareProducts(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_ids' => 'required|array|min:2|max:5',
                'product_ids.*' => 'exists:products,id',
            ]);

            $products = Product::whereIn('id', $validated['product_ids'])
                ->get(['id', 'name', 'price', 'description', 'stock'])
                ->toArray();

            if (count($products) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cần ít nhất 2 sản phẩm để so sánh',
                ], 400);
            }

            $comparison = $this->generateComparison($products);

            return response()->json([
                'success' => true,
                'comparison' => $comparison,
                'message' => $comparison['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi so sánh: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate detailed product comparison
     */
    private function generateComparison($products)
    {
        $response = "📊 **SO SÁNH CHI TIẾT SẢN PHẨM**\n\n";

        // Build comparison table
        $response .= '| Tiêu Chí | ';
        foreach ($products as $p) {
            $response .= $p['name'].' | ';
        }
        $response .= "\n";
        $response .= '|---|';
        foreach ($products as $p) {
            $response .= '---|';
        }
        $response .= "\n";

        // Price comparison
        $response .= '| **Giá** | ';
        foreach ($products as $p) {
            $response .= number_format($p['price'], 0, '.', ',').'đ | ';
        }
        $response .= "\n";

        // Stock
        $response .= '| **Kho** | ';
        foreach ($products as $p) {
            $response .= ($p['stock'] > 0 ? '✅ '.$p['stock'].' sản phẩm' : '❌ Hết hàng').' | ';
        }
        $response .= "\n";

        // Price advantage
        $minPrice = min(array_column($products, 'price'));
        $response .= "\n💰 **PHÂN TÍCH GIÁ:**\n";
        foreach ($products as $p) {
            $diff = $p['price'] - $minPrice;
            if ($diff == 0) {
                $response .= '• **'.$p['name']."**: GIÁ RẺ NHẤT ⭐\n";
            } else {
                $percentage = round(($diff / $minPrice) * 100, 1);
                $response .= '• **'.$p['name'].'**: +$'.number_format($diff, 0, '.', ',')."đ (+{$percentage}%)\n";
            }
        }

        // Recommendation
        $response .= "\n✅ **KHUYẾN NGHỊ:**\n";
        if (count($products) == 2) {
            $response .= "Nếu bạn coi trọng **giá rẻ** → Chọn sản phẩm có giá tốt nhất\n";
            $response .= "Nếu bạn coi trọng **chất lượng** → Hỏi tôi thêm chi tiết specs\n";
        }

        return [
            'message' => $response,
            'products' => $products,
        ];
    }

    /**
     * Check order status
     */
    public function checkOrderStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'conversation_id' => 'required|exists:chatbot_conversations,id',
                'order_id' => 'nullable|integer',
            ]);

            $user = Auth::user();
            $conversation = ChatbotConversation::find($validated['conversation_id']);

            // Get orders
            $query = Order::query();
            if ($validated['order_id'] ?? null) {
                $query->where('id', $validated['order_id']);
            } elseif ($user) {
                $query->where('user_id', $user->id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để kiểm tra đơn hàng',
                ], 401);
            }

            $orders = $query->orderBy('created_at', 'desc')->limit(3)->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => '📦 Bạn chưa có đơn hàng nào',
                    'orders' => [],
                ]);
            }

            $message = "📦 **TRẠNG THÁI ĐƠN HÀNG**\n\n";
            foreach ($orders as $order) {
                $statusEmoji = $this->getStatusEmoji($order->status);
                $message .= $statusEmoji.' **Đơn #'.$order->id.'** ('.number_format($order->total_amount, 0, '.', ',')."đ)\n";
                $message .= '   📅 Đặt: '.$order->created_at->format('d/m/Y H:i')."\n";
                $message .= '   📍 Trạng thái: '.$this->getStatusVietnamese($order->status)."\n";
                if ($order->status == 'shipped') {
                    $message .= "   🚚 Dự kiến: 24h nữa\n";
                } elseif ($order->status == 'delivered') {
                    $message .= "   ✅ Đã giao\n";
                }
                $message .= "\n";
            }

            $message .= "💡 **Cần hỗ trợ?** Hãy nói 'thanh toán', 'trả hàng', hoặc 'vấn đề' để được giúp đỡ!";

            // Track metric
            $this->trackMetric('order_checked', 'check-order', $conversation->id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'orders' => $orders->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get discount codes and suggestions
     */
    public function getSuggestedDiscounts(Request $request)
    {
        try {
            $validated = $request->validate([
                'conversation_id' => 'required|exists:chatbot_conversations,id',
                'total_amount' => 'nullable|numeric|min:0',
            ]);

            $totalAmount = $validated['total_amount'] ?? 0;
            $conversation = ChatbotConversation::find($validated['conversation_id']);
            $user = Auth::user();

            // Get active coupons
            $coupons = Coupon::where('is_active', true)
                ->where('expiry_date', '>', now())
                ->get();

            $applicableCoupons = $coupons->filter(function ($coupon) use ($totalAmount) {
                return $totalAmount >= $coupon->min_order_amount;
            });

            $message = "🎁 **MÃ GIẢM GIÁ CÓ SẴN**\n\n";

            if ($applicableCoupons->isEmpty()) {
                $message .= "Hiện tại không có mã giảm giá phù hợp🙁\n\n";
                $message .= '💡 Hãy theo dõi để nhận thông báo mã mới!';

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'coupons' => [],
                ]);
            }

            foreach ($applicableCoupons as $coupon) {
                $discount = $coupon->discount_type == 'percentage'
                    ? $coupon->discount_value.'%'
                    : number_format($coupon->discount_value, 0, '.', ',').'đ';

                $message .= '✨ **'.$coupon->code."**\n";
                $message .= '   Giảm: '.$discount."\n";
                $message .= '   Điều kiện: Từ '.number_format($coupon->min_order_amount, 0, '.', ',')."đ\n";
                $message .= '   Hết hạn: '.$coupon->expiry_date->format('d/m/Y')."\n";
                $message .= '   → Nhập mã: `'.$coupon->code."`\n\n";
            }

            // Track metric
            $this->trackMetric('discount_suggested', 'get-discount', $conversation->id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'coupons' => $applicableCoupons->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get FAQ with search
     */
    public function searchFAQ(Request $request)
    {
        try {
            $validated = $request->validate([
                'conversation_id' => 'required|exists:chatbot_conversations,id',
                'query' => 'required|string|max:200',
            ]);

            $queryStr = strtolower($validated['query']);
            $conversation = ChatbotConversation::find($validated['conversation_id']);

            // Simple keyword mapping instead of complex query
            $faqMapping = [
                'thanh toán' => 1, 'payment' => 1, 'tiền' => 1,
                'giao hàng' => 2, 'shipping' => 2, 'delivery' => 2,
                'đổi trả' => 5, 'return' => 5, 'exchange' => 5,
                'bảo hành' => 7, 'warranty' => 7, 'guarantee' => 7,
            ];

            $faqId = null;
            foreach ($faqMapping as $keyword => $id) {
                if (strpos($queryStr, $keyword) !== false) {
                    $faqId = $id;
                    break;
                }
            }

            if ($faqId) {
                $faqRecord = FAQ::find($faqId);
                if ($faqRecord) {
                    $message = "❓ **CÂU HỎI THƯỜNG GẶP**\n\n"
                        .'**Q: '.$faqRecord->question."**\n\n"
                        ."**Trả lời:**\n"
                        .$faqRecord->answer."\n\n"
                        ."👍 Câu trả lời có hữu ích không?\n";

                    // Track metric
                    $this->trackMetric('faq_viewed', $faqRecord->category ?? 'general', $conversation->id);

                    return response()->json([
                        'success' => true,
                        'message' => $message,
                    ]);
                }
            }

            // Default: Suggest FAQ categories
            $message = "❓ **TRÍCH CẦU HỎI THƯỜNG GẶP**\n\n"
                ."Tôi có sẵn các câu trả lời về:\n"
                ."📦 **Giao hàng & Vận chuyển** - Hỏi 'giao hàng bao lâu?'\n"
                ."💳 **Thanh toán** - Hỏi 'thanh toán như thế nào?'\n"
                ."🔄 **Đổi & Trả** - Hỏi 'đổi trả thế nào?'\n"
                ."🔧 **Bảo hành** - Hỏi 'bảo hành bao lâu?'\n\n"
                .'Hoặc hãy liên hệ: **support@techstore.com**';

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get admin analytics dashboard data
     */
    public function getAdminAnalytics(Request $request)
    {
        $user = Auth::user();

        // Verify admin access
        if (! $user || $user->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập',
            ], 403);
        }

        try {
            $today = now()->startOfDay();
            $thisWeek = now()->startOfWeek();
            $thisMonth = now()->startOfMonth();

            // Top questions
            $topQuestions = ChatbotAnalytic::where('metric_type', 'intent')
                ->where('date', '>=', $thisMonth)
                ->groupBy('metric_name')
                ->selectRaw('metric_name, COUNT(*) as total')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            // Top products viewed via chat
            $topProducts = ChatbotAnalytic::where('metric_type', 'product_viewed')
                ->where('date', '>=', $thisMonth)
                ->groupBy('metric_name')
                ->selectRaw('metric_name, COUNT(*) as views')
                ->orderByDesc('views')
                ->limit(10)
                ->get();

            // Conversation count
            $todayConversations = ChatbotConversation::where('created_at', '>=', $today)->count();
            $weekConversations = ChatbotConversation::where('created_at', '>=', $thisWeek)->count();
            $monthConversations = ChatbotConversation::where('created_at', '>=', $thisMonth)->count();

            // Most common intents
            $topIntents = ChatbotAnalytic::where('metric_type', 'intent')
                ->where('date', '>=', $today)
                ->groupBy('metric_name')
                ->selectRaw('metric_name, COUNT(*) as count')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            // Messages per conversation
            $avgMessages = ChatbotMessage::whereHas('conversation', function ($q) {
                $q->where('created_at', '>=', now()->startOfMonth());
            })->count();
            $avgPerConversation = $monthConversations > 0 ? round($avgMessages / $monthConversations, 2) : 0;

            $message = "📊 **ANALYTICS CHO ADMIN**\n\n"
                ."**📈 CHỈ SỐ HÔM NAY:**\n"
                .'• Cuộc trò chuyện: '.$todayConversations."\n"
                .'• Tuần này: '.$weekConversations."\n"
                .'• Tháng này: '.$monthConversations."\n\n"
                ."**🏆 TOP NHƯ CẦU HÔMS:**\n";

            foreach ($topIntents as $intent) {
                $message .= '• '.ucfirst($intent->metric_name).': '.$intent->count." lần\n";
            }

            $message .= "\n**📦 TOP SẢN PHẨM:**\n";

            foreach ($topProducts->take(5) as $product) {
                $message .= '• '.$product->metric_name.': '.$product->views." lượt xem\n";
            }

            $message .= "\n**💬 BỤC HỎI THƯỜNG GẶP:**\n";
            foreach ($topQuestions->take(5) as $question) {
                $message .= '• '.$question->metric_name.': '.$question->total." hỏi\n";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'today_conversations' => $todayConversations,
                    'week_conversations' => $weekConversations,
                    'month_conversations' => $monthConversations,
                    'avg_messages_per_conversation' => $avgPerConversation,
                    'top_intents' => $topIntents,
                    'top_products' => $topProducts,
                    'top_questions' => $topQuestions,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track user interactions for analytics
     */
    private function trackMetric($metricType, $metricName, $conversationId = null)
    {
        try {
            ChatbotAnalytic::create([
                'conversation_id' => $conversationId,
                'metric_type' => $metricType,
                'metric_name' => $metricName,
                'date' => now()->startOfDay(),
            ]);
        } catch (\Exception $e) {
            // Silently fail, don't interrupt chat
        }
    }

    /**
     * Helper: Get status emoji
     */
    private function getStatusEmoji($status)
    {
        return match ($status) {
            'pending' => '⏳',
            'confirmed' => '✅',
            'shipped' => '🚚',
            'delivered' => '📦',
            'cancelled' => '❌',
            default => '❓',
        };
    }

    /**
     * Helper: Get Vietnamese status text
     */
    private function getStatusVietnamese($status)
    {
        return match ($status) {
            'pending' => 'Đợi xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipped' => 'Đang giao hàng',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    /**
     * Enhanced context-aware message processing
     */
    private function getConversationContext($conversation)
    {
        $messages = ChatbotMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'last_intent' => $messages->first()?->metadata['intent'] ?? null,
            'topics' => $messages->pluck('metadata.intent')->filter()->unique()->toArray(),
            'products_discussed' => $messages->pluck('metadata.product_type')->filter()->unique()->toArray(),
        ];
    }
}
