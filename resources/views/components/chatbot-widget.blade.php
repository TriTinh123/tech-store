<!-- AI Chatbot Widget -->
<div id="chatbot-widget" class="chatbot-widget">
    <!-- Chatbot Toggle Button -->
    <div id="chatbot-toggle" class="chatbot-toggle" onclick="toggleChatbot()">
        <i class="fas fa-comments"></i>
        <span class="chatbot-badge">AI</span>
    </div>

    <!-- Chatbot Container -->
    <div id="chatbot-container" class="chatbot-container">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-title">
                <i class="fas fa-robot"></i> TechStore AI Assistant
                <span class="online-status">
                    <span class="online-dot"></span> Online
                </span>
            </div>
            <button onclick="closeChatbot()" class="chatbot-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Messages Container -->
        <div id="chatbot-messages" class="chatbot-messages">
            <!-- Initial greeting message (will be updated by API) -->
            <div class="chatbot-message bot-message">
                <div class="message-content">
                    Xin chào! 👋<br>
                    Mình có thể giúp gì cho bạn? 😊<br><br>
                    Mình hỗ trợ:<br>
                    💻 Tìm sản phẩm phù hợp<br>
                    💰 So sánh giá và tính năng<br>
                    🛒 Hỗ trợ mua sắm<br>
                    ❓ Trả lời câu hỏi<br>
                    🎁 Mã giảm giá
                </div>
                <div class="message-time">Vừa xong</div>
            </div>
        </div>

        <!-- Input Form -->
        <div class="chatbot-footer">
            <form id="chatbot-form" onsubmit="sendMessage(event)">
                <div class="chatbot-input-group">
                    <input 
                        type="text" 
                        id="chatbot-input" 
                        class="chatbot-input" 
                        placeholder="Nhập câu hỏi..." 
                        required
                    >
                    <button type="submit" class="chatbot-send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Chatbot Widget Styles */
    .chatbot-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        font-family: 'Poppins', sans-serif;
        z-index: 9999;
    }

    .chatbot-toggle {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: white;
        font-size: 24px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
        position: relative;
    }

    .chatbot-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.6);
    }

    .chatbot-toggle.active {
        display: none;
    }

    .chatbot-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #f5576c;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        border: 2px solid white;
    }

    .chatbot-container {
        position: absolute;
        bottom: 90px;
        right: 0;
        width: 380px;
        max-width: calc(100vw - 20px);
        height: 500px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        animation: slideUp 0.3s ease-in-out;
        display: none;
    }

    .chatbot-container.active {
        display: flex;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Header */
    .chatbot-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
    }

    .chatbot-header-title {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
    }

    .chatbot-header-title i {
        font-size: 18px;
    }

    .online-status {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 400;
    }

    .online-dot {
        width: 8px;
        height: 8px;
        background: #48bb78;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .chatbot-close-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 20px;
        padding: 0;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s;
    }

    .chatbot-close-btn:hover {
        transform: rotate(90deg);
    }

    /* Messages Container */
    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chatbot-messages::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }

    .chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* Message Styles */
    .chatbot-message {
        display: flex;
        flex-direction: column;
        gap: 4px;
        animation: messageIn 0.3s ease-in-out;
    }

    @keyframes messageIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chatbot-message.user-message {
        align-items: flex-end;
    }

    .chatbot-message.bot-message {
        align-items: flex-start;
    }

    .message-content {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 12px;
        word-wrap: break-word;
        line-height: 1.6;
        font-size: 15px;
        letter-spacing: 0.3px;
    }

    .user-message .message-content {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .bot-message .message-content {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0ff 100%);
        color: #1f2937;
        border-bottom-left-radius: 4px;
        border-left: 3px solid #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        font-weight: 400;
    }

    .message-time {
        font-size: 12px;
        color: #718096;
        padding: 0 4px;
    }

    /* Product Suggestions */
    .product-suggestion {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafb 100%);
        border: 1.5px solid #e8eef7;
        border-radius: 10px;
        padding: 14px;
        margin-top: 8px;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.08);
        transition: all 0.2s ease;
    }
    
    .product-suggestion:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        transform: translateY(-2px);
    }

    .product-suggestion-name {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 15px;
        color: #1f2937;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .product-suggestion-price {
        font-family: 'Poppins', sans-serif;
        color: #667eea;
        font-weight: 700;
        font-size: 16px;
        letter-spacing: 0.5px;
    }

    /* Footer */
    .chatbot-footer {
        padding: 12px;
        border-top: 1px solid #e2e8f0;
        background: white;
        border-radius: 0 0 12px 12px;
    }

    .chatbot-input-group {
        display: flex;
        gap: 8px;
    }

    .chatbot-input {
        flex: 1;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        transition: all 0.2s;
    }

    .chatbot-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .chatbot-send-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 6px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 16px;
    }

    .chatbot-send-btn:hover {
        transform: scale(1.05);
    }

    .chatbot-send-btn:active {
        transform: scale(0.95);
    }

    .chatbot-suggestions {
        margin-top: 8px;
        text-align: center;
        color: #718096;
    }

    /* Responsive Design */
    @media (max-width: 480px) {
        .chatbot-widget {
            bottom: 10px;
            right: 10px;
        }

        .chatbot-container {
            width: calc(100vw - 20px);
            height: calc(100vh - 160px);
            max-width: none;
        }

        .message-content {
            max-width: 90%;
        }

        .chatbot-toggle {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }

    /* Loading state */
    .message-loading {
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .loading-dot {
        width: 8px;
        height: 8px;
        background: #cbd5e0;
        border-radius: 50%;
        animation: loading 1.4s infinite;
    }

    .loading-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .loading-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes loading {
        0%, 100% {
            opacity: 0.3;
            transform: translateY(0);
        }
        50% {
            opacity: 1;
            transform: translateY(-8px);
        }
    }
</style>

<script>
    let conversationId = null;
    const csrfToken = '{{ csrf_token() }}';

    // Toggle chatbot visibility
    function toggleChatbot() {
        const container = document.getElementById('chatbot-container');
        const toggle = document.getElementById('chatbot-toggle');
        const navbarBadge = document.getElementById('chat-badge-navbar');
        const advisorBadge = document.getElementById('nu-advisor-badge');
        
        container.classList.add('active');
        if (toggle) toggle.classList.add('active');

        // Hide all badges when opened
        if (navbarBadge) navbarBadge.style.display = 'none';
        if (advisorBadge) advisorBadge.style.display = 'none';
        
        // Initialize conversation on first open
        if (!conversationId) {
            initializeChatbot();
        }
    }

    // Close chatbot
    function closeChatbot() {
        const container = document.getElementById('chatbot-container');
        const toggle = document.getElementById('chatbot-toggle');
        
        container.classList.remove('active');
        if (toggle) toggle.classList.remove('active');
    }

    // Show notification badge on navbar
    function showChatNotification() {
        const container = document.getElementById('chatbot-container');
        
        // Only show badge if chatbot is not open
        if (!container.classList.contains('active')) {
            const navbarBadge = document.getElementById('chat-badge-navbar');
            const advisorBadge = document.getElementById('nu-advisor-badge');
            
            if (navbarBadge) {
                navbarBadge.style.display = 'inline-block';
            }
            if (advisorBadge) {
                advisorBadge.style.display = 'flex';
            }
        }
    }

    // Initialize chatbot conversation
    async function initializeChatbot() {
        try {
            const response = await fetch('/api/chatbot/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            const data = await response.json();
            console.log('🤖 Chatbot API Response:', data);
            
            if (data.success) {
                conversationId = data.conversation_id;
                
                // Clear and rebuild greeting message
                const messagesContainer = document.getElementById('chatbot-messages');
                if (messagesContainer) {
                    // Remove all default messages
                    messagesContainer.innerHTML = '';
                    
                    // Create new greeting message from API response
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'chatbot-message bot-message';
                    
                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'message-content';
                    
                    // Format message with line breaks
                    if (data.message) {
                        console.log('📝 Message from API:', data.message);
                        const lines = data.message.split('\n');
                        let html = '';
                        
                        for (let i = 0; i < lines.length; i++) {
                            const line = lines[i];
                            if (line.trim() === '') {
                                // Empty line for spacing
                                html += '<div style="height: 8px;"></div>';
                            } else if (line.includes('💻') || line.includes('💰') || line.includes('🛒') || 
                                       line.includes('❓') || line.includes('🎁')) {
                                // Support/feature lines
                                html += '<div style="margin: 4px 0; line-height: 1.6;">' + line + '</div>';
                            } else {
                                // Regular lines (greeting, etc)
                                html += '<div style="margin: 2px 0; line-height: 1.6;">' + line + '</div>';
                            }
                        }
                        
                        contentDiv.innerHTML = html;
                    }
                    
                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'message-time';
                    timeDiv.textContent = 'Vừa xong';
                    
                    messageDiv.appendChild(contentDiv);
                    messageDiv.appendChild(timeDiv);
                    messagesContainer.appendChild(messageDiv);
                }
            } else {
                console.error('❌ API Error:', data);
            }
        } catch (error) {
            console.error('❌ Chatbot Error:', error);
        }
    }

    // Send message
    async function sendMessage(event) {
        event.preventDefault();
        
        if (!conversationId) {
            await initializeChatbot();
        }

        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message) return;

        // Add user message to UI
        addMessageToUI(message, 'user');
        input.value = '';

        // Show loading state
        showLoadingMessage();

        try {
            const response = await fetch('/api/chatbot/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    message: message
                })
            });

            // Remove loading message first
            removeLoadingMessage();

            // Check if response is ok
            if (!response.ok) {
                const errorData = await response.json();
                console.error('API Error:', errorData);
                addMessageToUI('❌ ' + (errorData.message || 'Lỗi từ server. Vui lòng thử lại.'), 'bot');
                return;
            }

            const data = await response.json();
            
            if (data.success) {
                // Add bot response
                addMessageToUI(data.bot_message, 'bot');

                // Show notification if chatbot is minimized
                showChatNotification();

                // Add product suggestions if any
                if (data.suggested_products && data.suggested_products.length > 0) {
                    addProductSuggestions(data.suggested_products);
                }
            } else {
                const errorMsg = data.message || 'Xin lỗi, tôi gặp lỗi. Vui lòng thử lại sau.';
                console.error('Bot Error:', errorMsg);
                addMessageToUI('❌ ' + errorMsg, 'bot');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            removeLoadingMessage();
            addMessageToUI('❌ Lỗi kết nối: ' + error.message, 'bot');
        }
    }

    // Add message to UI
    function addMessageToUI(message, sender) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${sender}-message`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.innerHTML = message;

        const timeDiv = document.createElement('div');
        timeDiv.className = 'message-time';
        timeDiv.textContent = 'Vừa xong';

        messageDiv.appendChild(contentDiv);
        messageDiv.appendChild(timeDiv);
        messagesContainer.appendChild(messageDiv);

        // Auto-scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Show loading message
    function showLoadingMessage() {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message bot-message';
        messageDiv.id = 'loading-message';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content message-loading';
        contentDiv.innerHTML = '<span class="loading-dot"></span><span class="loading-dot"></span><span class="loading-dot"></span>';

        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);

        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Remove loading message
    function removeLoadingMessage() {
        const loadingMsg = document.getElementById('loading-message');
        if (loadingMsg) {
            loadingMsg.remove();
        }
    }

    // Add product suggestions
    function addProductSuggestions(products) {
        const messagesContainer = document.getElementById('chatbot-messages');
        
        products.forEach(product => {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chatbot-message bot-message';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'product-suggestion';
            contentDiv.innerHTML = `
                <div class="product-suggestion-name">${product.name || 'Sản phẩm'}</div>
                <div class="product-suggestion-price">💰 ${(product.price || 0).toLocaleString('vi-VN')}đ</div>
            `;

            messageDiv.appendChild(contentDiv);
            messagesContainer.appendChild(messageDiv);
        });

        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Quick reply suggestions (can be added later)
    function addQuickReply(text) {
        document.getElementById('chatbot-input').value = text;
    }
</script>
