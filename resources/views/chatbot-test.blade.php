<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .status-ok { color: green; font-weight: bold; }
        .status-error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Chatbot Debug System</h1>

        <div class="test-section">
            <h3>1️⃣ Routes Check</h3>
            <div id="routes-status">Loading...</div>
        </div>

        <div class="test-section">
            <h3>2️⃣ API Endpoints</h3>
            <button class="btn btn-primary" onclick="testStartConversation()">Test /api/chatbot/start</button>
            <div id="api-status" style="margin-top: 10px;"></div>
        </div>

        <div class="test-section">
            <h3>3️⃣ Widget Status</h3>
            <div id="widget-status">Checking...</div>
        </div>

        <div class="test-section">
            <h3>4️⃣ Manual Trigger</h3>
            <button class="btn btn-success" onclick="testToggleChatbot()">Open Chatbot Manually</button>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // Check if routes are working
        async function checkRoutes() {
            try {
                const response = await fetch('/api/chatbot/start', {
                    method: 'HEAD',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                document.getElementById('routes-status').innerHTML = 
                    '<p class="status-ok">✅ Routes configured correctly (Status: ' + response.status + ')</p>';
            } catch (error) {
                document.getElementById('routes-status').innerHTML = 
                    '<p class="status-error">❌ Routes error: ' + error.message + '</p>';
            }
        }

        // Test API call
        async function testStartConversation() {
            try {
                const response = await fetch('/api/chatbot/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();
                
                if (response.ok && data.success) {
                    document.getElementById('api-status').innerHTML = 
                        '<p class="status-ok">✅ API Working! Conversation ID: ' + data.conversation_id + '</p>' +
                        '<p>Message: ' + data.message + '</p>';
                } else {
                    document.getElementById('api-status').innerHTML = 
                        '<p class="status-error">❌ API Error: ' + (data.message || 'Unknown error') + '</p>';
                }
            } catch (error) {
                document.getElementById('api-status').innerHTML = 
                    '<p class="status-error">❌ Network Error: ' + error.message + '</p>';
            }
        }

        // Check widget
        function checkWidget() {
            const widget = document.getElementById('chatbot-widget');
            const toggle = document.getElementById('chatbot-toggle');
            const container = document.getElementById('chatbot-container');

            let status = '<ul>';
            
            if (widget) {
                status += '<li class="status-ok">✅ Widget container found</li>';
            } else {
                status += '<li class="status-error">❌ Widget container NOT found</li>';
            }

            if (toggle) {
                status += '<li class="status-ok">✅ Toggle button found</li>';
            } else {
                status += '<li class="status-error">❌ Toggle button NOT found</li>';
            }

            if (container) {
                status += '<li class="status-ok">✅ Container found</li>';
            } else {
                status += '<li class="status-error">❌ Container NOT found</li>';
            }

            if (typeof toggleChatbot === 'function') {
                status += '<li class="status-ok">✅ toggleChatbot() function exists</li>';
            } else {
                status += '<li class="status-error">❌ toggleChatbot() function NOT found</li>';
            }

            status += '</ul>';
            document.getElementById('widget-status').innerHTML = status;
        }

        // Test toggle
        function testToggleChatbot() {
            if (typeof toggleChatbot === 'function') {
                toggleChatbot();
                alert('Chatbot should open now!');
            } else {
                alert('❌ toggleChatbot function not found!');
            }
        }

        // Run checks on page load
        window.addEventListener('DOMContentLoaded', function() {
            checkRoutes();
            setTimeout(checkWidget, 500);
        });
    </script>
</body>
</html>
