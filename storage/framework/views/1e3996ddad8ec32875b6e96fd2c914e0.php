

<!-- Chatbot Teaser Tooltip -->
<div id="chatbot-teaser" class="chatbot-teaser d-none">
    <span>👋 How can I help you?</span>
    <button class="chatbot-teaser-close" id="chatbot-teaser-close" aria-label="Close">×</button>
</div>

<!-- Chatbot Toggle Button -->
<button id="chatbot-toggle" class="chatbot-toggle-btn" title="Chat with TechBot" aria-label="Open chatbot">
    <i class="fas fa-comment-dots fa-lg" id="chatbot-icon-open"></i>
    <i class="fas fa-times fa-lg d-none" id="chatbot-icon-close"></i>
    <span class="chatbot-badge d-none" id="chatbot-unread">1</span>
</button>

<!-- Chatbot Window -->
<div id="chatbot-window" class="chatbot-window d-none" role="dialog" aria-label="TechBot chat window">
    <!-- Header -->
    <div class="chatbot-header">
        <div class="d-flex align-items-center gap-2">
            <div class="chatbot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <div class="chatbot-name">TechBot</div>
                <div class="chatbot-status"><span class="chatbot-dot"></span> Online</div>
            </div>
        </div>
        <button class="chatbot-close-btn" id="chatbot-close" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Messages -->
    <div class="chatbot-messages" id="chatbot-messages" role="log" aria-live="polite">
        <!-- Welcome message injected by JS -->
    </div>

    <!-- Quick Replies -->
    <div class="chatbot-quick-replies" id="chatbot-quick">
        <button class="chatbot-chip" data-msg="Laptop advice">💻 Laptop</button>
        <button class="chatbot-chip" data-msg="Phone advice">📱 Phones</button>
        <button class="chatbot-chip" data-msg="Shipping Policy">🚚 Shipping</button>
        <button class="chatbot-chip" data-msg="Returns">🔄 Returns</button>
        <button class="chatbot-chip" data-msg="Payment Method">💳 Payment</button>
        <button class="chatbot-chip" data-msg="Promotions">🎉 Promotions</button>
        <button class="chatbot-chip" data-msg="Track Order">📦 Orders</button>
        <button class="chatbot-chip" data-msg="Warranty Policy">🛡️ Warranty</button>
    </div>

    <!-- Input -->
    <div class="chatbot-input-area">
        <input type="text"
               id="chatbot-input"
               class="chatbot-input"
               placeholder="Ask about products, orders, promotions..."
               maxlength="500"
               autocomplete="off"
               aria-label="Enter message">
        <button id="chatbot-send" class="chatbot-send-btn" aria-label="Send Message">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>


<style>
/* Ensure d-none works even without Bootstrap loaded */
#chatbot-window.d-none,
#chatbot-teaser.d-none,
#chatbot-unread.d-none,
#chatbot-icon-open.d-none,
#chatbot-icon-close.d-none { display: none !important; }
:root {
    --cb-primary:   #3b82f6;
    --cb-primary-d: #2563eb;
    --cb-bg:        #ffffff;
    --cb-bot-bg:    #f1f5f9;
    --cb-user-bg:   #3b82f6;
    --cb-text:      #1e293b;
    --cb-radius:    16px;
    --cb-shadow:    0 8px 32px rgba(0,0,0,.18);
}

/* Toggle button */
.chatbot-toggle-btn {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--cb-primary), var(--cb-primary-d));
    color: #fff;
    border: none;
    cursor: pointer;
    box-shadow: var(--cb-shadow);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s, box-shadow .2s;
}
.chatbot-toggle-btn:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(59,130,246,.4); }
@keyframes cbPulse {
    0%   { box-shadow: 0 0 0 0 rgba(59,130,246,.55); }
    70%  { box-shadow: 0 0 0 14px rgba(59,130,246,0); }
    100% { box-shadow: 0 0 0 0 rgba(59,130,246,0); }
}
.chatbot-toggle-btn.pulsing { animation: cbPulse 1.8s infinite; }

/* Teaser tooltip */
.chatbot-teaser {
    position: fixed;
    bottom: 102px;
    right: 28px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    padding: 10px 12px 10px 14px;
    font-size: 13px;
    color: #1e293b;
    z-index: 9997;
    max-width: 220px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    animation: cbSlideIn .3s ease;
}
.chatbot-teaser::after {
    content: '';
    position: absolute;
    bottom: -7px;
    right: 20px;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid #fff;
    filter: drop-shadow(0 2px 1px rgba(0,0,0,.08));
}
.chatbot-teaser-close {
    background: none; border: none; cursor: pointer;
    color: #94a3b8; font-size: 18px; line-height: 1;
    padding: 0 2px; flex-shrink: 0;
}
.chatbot-teaser-close:hover { color: #ef4444; }
@media (max-width: 480px) {
    .chatbot-teaser { right: 10px; max-width: 180px; }
}
.chatbot-badge {
    position: absolute;
    top: 4px; right: 4px;
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    width: 18px; height: 18px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}

/* Window */
.chatbot-window {
    position: fixed;
    bottom: 100px;
    right: 28px;
    width: 360px;
    max-height: 540px;
    border-radius: var(--cb-radius);
    background: var(--cb-bg);
    box-shadow: var(--cb-shadow);
    z-index: 9998;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: cbSlideIn .25s ease;
}
@keyframes cbSlideIn {
    from { opacity: 0; transform: translateY(20px) scale(.96); }
    to   { opacity: 1; transform: translateY(0)   scale(1); }
}
@media (max-width: 480px) {
    .chatbot-window {
        width: min(360px, calc(100vw - 16px));
        right: 8px;
        bottom: 90px;
        max-height: 540px;
    }
    .chatbot-toggle-btn { bottom: 14px; right: 14px; width: 52px; height: 52px; }
    .chatbot-teaser { display: none !important; }
}

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, var(--cb-primary), var(--cb-primary-d));
    color: #fff;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.chatbot-avatar {
    width: 38px; height: 38px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.chatbot-name   { font-weight: 600; font-size: 15px; }
.chatbot-status { font-size: 11px; opacity: .85; display: flex; align-items: center; gap: 4px; }
.chatbot-dot    { width: 7px; height: 7px; background: #4ade80; border-radius: 50%; display: inline-block; }
.chatbot-close-btn {
    background: none; border: none; color: #fff;
    font-size: 16px; cursor: pointer; opacity: .8; padding: 4px;
}
.chatbot-close-btn:hover { opacity: 1; }

/* Messages */
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 14px 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}
.chatbot-messages::-webkit-scrollbar { width: 4px; }
.chatbot-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.cb-msg {
    max-width: 82%;
    padding: 10px 13px;
    border-radius: 14px;
    font-size: 13.5px;
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
}
.cb-msg.bot  { background: var(--cb-bot-bg); color: var(--cb-text); align-self: flex-start; border-bottom-left-radius: 4px; }
.cb-msg.user { background: var(--cb-user-bg); color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }
.cb-msg strong { font-weight: 600; }
.cb-msg a { color: var(--cb-primary); text-decoration: underline; }
.cb-msg .cb-list-item { display: flex; align-items: baseline; gap: 6px; margin: 3px 0; }
.cb-msg .cb-step { background: var(--cb-primary); color: #fff; border-radius: 50%; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; flex-shrink: 0; }
.cb-msg .cb-bullet { margin: 2px 0; padding-left: 4px; }
.cb-msg .cb-hr { border: none; border-top: 1px solid #e2e8f0; margin: 6px 0; }

/* Typing indicator */
.cb-typing {
    display: flex; gap: 5px; align-items: center;
    padding: 10px 13px;
    background: var(--cb-bot-bg);
    border-radius: 14px;
    border-bottom-left-radius: 4px;
    align-self: flex-start;
    width: fit-content;
}
.cb-typing span {
    width: 7px; height: 7px;
    background: #94a3b8;
    border-radius: 50%;
    animation: cbBounce 1.2s infinite;
}
.cb-typing span:nth-child(2) { animation-delay: .2s; }
.cb-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes cbBounce {
    0%,60%,100% { transform: translateY(0); }
    30%          { transform: translateY(-6px); }
}

/* Quick replies */
.chatbot-quick-replies {
    padding: 6px 12px 4px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    border-top: 1px solid #f1f5f9;
}
.chatbot-chip {
    background: #eff6ff;
    color: var(--cb-primary);
    border: 1px solid #bfdbfe;
    border-radius: 999px;
    padding: 4px 11px;
    font-size: 12px;
    cursor: pointer;
    transition: background .15s;
    white-space: nowrap;
}
.chatbot-chip:hover { background: #dbeafe; }

/* Input */
.chatbot-input-area {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border-top: 1px solid #f1f5f9;
    gap: 8px;
}
.chatbot-input {
    flex: 1;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 8px 14px;
    font-size: 13.5px;
    outline: none;
    color: var(--cb-text);
    transition: border-color .15s;
}
.chatbot-input:focus { border-color: var(--cb-primary); }
.chatbot-send-btn {
    width: 36px; height: 36px;
    background: var(--cb-primary);
    color: #fff;
    border: none; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
    flex-shrink: 0;
}
.chatbot-send-btn:hover { background: var(--cb-primary-d); }
.chatbot-send-btn:disabled { background: #cbd5e1; cursor: not-allowed; }
</style>


<script>
(function () {
    const REPLY_URL  = '<?php echo e(route("chatbot.reply")); ?>';
    const CLEAR_URL  = '<?php echo e(route("chatbot.clear")); ?>';
    const CSRF_TOKEN = '<?php echo e(csrf_token()); ?>';

    const toggleBtn   = document.getElementById('chatbot-toggle');
    const window_     = document.getElementById('chatbot-window');
    const closeBtn    = document.getElementById('chatbot-close');
    const messages    = document.getElementById('chatbot-messages');
    const input       = document.getElementById('chatbot-input');
    const sendBtn     = document.getElementById('chatbot-send');
    const unreadBadge = document.getElementById('chatbot-unread');
    const iconOpen    = document.getElementById('chatbot-icon-open');
    const iconClose   = document.getElementById('chatbot-icon-close');
    const chips       = document.querySelectorAll('.chatbot-chip');
    const teaserEl    = document.getElementById('chatbot-teaser');
    const teaserClose = document.getElementById('chatbot-teaser-close');

    let isOpen = false;
    let greeted = false;

    // ── teaser: show after 3s if never opened (desktop only) ──────────────
    if (!sessionStorage.getItem('cb_opened') && window.innerWidth > 768) {
        setTimeout(() => {
            teaserEl.classList.remove('d-none');
            unreadBadge.classList.remove('d-none');
            toggleBtn.classList.add('pulsing');
        }, 3000);
    }
    function hideTeaser() {
        teaserEl.classList.add('d-none');
        toggleBtn.classList.remove('pulsing');
    }
    teaserEl.addEventListener('click', () => { hideTeaser(); openChat(); });
    teaserClose.addEventListener('click', e => { e.stopPropagation(); hideTeaser(); });

    // ── helpers ────────────────────────────────────────────
    function renderMarkdown(text) {
        // Escape HTML to prevent XSS from server responses
        let html = text
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        // Bold
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        // Links [text](url) — only allow relative paths or https
        html = html.replace(/\[(.+?)\]\(((?:https?:\/\/|\/)[^)]+)\)/g,
            '<a href="$2" style="color:var(--cb-primary);text-decoration:underline" target="_blank" rel="noopener">$1</a>');
        // Numbered list: 1️⃣ or 1. at start of line → styled item
        html = html.replace(/^([1-9][0-9]?)[️⃣.]\s(.+)$/gm,
            '<div class="cb-list-item"><span class="cb-step">$1</span> $2</div>');
        // Bullet list: - or • at start of line
        html = html.replace(/^[-•]\s(.+)$/gm,
            '<div class="cb-bullet">• $1</div>');
        // Horizontal rules (━━━)
        html = html.replace(/[━─]{3,}/g, '<hr class="cb-hr">');
        // Line breaks
        html = html.replace(/\n/g, '<br>');
        return html;
    }

    function appendMsg(text, role) {
        const el = document.createElement('div');
        el.className = 'cb-msg ' + role;
        el.innerHTML = renderMarkdown(text);
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }

    function showTyping() {
        const el = document.createElement('div');
        el.className = 'cb-typing';
        el.id = 'cb-typing-indicator';
        el.innerHTML = '<span></span><span></span><span></span>';
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
    }

    function removeTyping() {
        const el = document.getElementById('cb-typing-indicator');
        if (el) el.remove();
    }

    // ── open / close ────────────────────────────────────────
    function openChat() {
        isOpen = true;
        sessionStorage.setItem('cb_opened', '1');
        hideTeaser();
        window_.classList.remove('d-none');
        iconOpen.classList.add('d-none');
        iconClose.classList.remove('d-none');
        unreadBadge.classList.add('d-none');

        if (!greeted) {
            greeted = true;
            appendMsg(
                '👋 Hello! I am **TechBot**, TechStore\'s shopping assistant.\n\nI can help you with shipping, returns, payment, warranty, promotions and order tracking. How can I assist you?',
                'bot'
            );
        }
        input.focus();
    }

    function closeChat() {
        isOpen = false;
        window_.classList.add('d-none');
        iconOpen.classList.remove('d-none');
        iconClose.classList.add('d-none');
        unreadBadge.classList.add('d-none');

        // Clear chat history on close
        messages.innerHTML = '';
        greeted = false;
        document.getElementById('chatbot-quick').style.display = '';
        fetch(CLEAR_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
    }

    toggleBtn.addEventListener('click', () => isOpen ? closeChat() : openChat());
    closeBtn.addEventListener('click', closeChat);

    // ── send message ────────────────────────────────────────
    async function sendMessage(text) {
        text = text.trim();
        if (!text) return;

        appendMsg(text, 'user');
        input.value = '';
        sendBtn.disabled = true;
        showTyping();

        try {
            const res = await fetch(REPLY_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text }),
            });

            const data = await res.json();
            removeTyping();

            if (res.ok && data.reply) {
                appendMsg(data.reply, 'bot');
            } else {
                appendMsg('⚠️ An error occurred. Please try again later.', 'bot');
            }
        } catch (e) {
            removeTyping();
            appendMsg('⚠️ Connection failed. Please check your network.', 'bot');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    }

    sendBtn.addEventListener('click', () => sendMessage(input.value));
    input.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(input.value); } });

    // ── quick-reply chips ───────────────────────────────────
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            if (!isOpen) openChat();
            // Hide chips after use to free up chat space
            document.getElementById('chatbot-quick').style.display = 'none';
            sendMessage(chip.dataset.msg);
        });
    });
})();
</script>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\components\chatbot-widget.blade.php ENDPATH**/ ?>