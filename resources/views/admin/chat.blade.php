{{-- resources/views/admin/chat/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'AI Assistant')

@section('content')
<style>
    /* === THEME SYNC === */
    :root {
        --chat-bg: #ffffff;
        --accent: #CF0F47;
        --accent-hover: #FF0B55;
        --bot-bubble: #f1f1f9;
        --text-main: #111315;
        --text-muted: #666666;
        --border-color: #eeeeee;
    }

    /* Force Poppins */
    .chat-dashboard, 
    .message, 
    .chat-input, 
    .chat-meta {
        font-family: 'Poppins', sans-serif !important;
    }

    .chat-dashboard {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 160px);
        max-height: 850px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .chat-title h1 { margin: 0; font-size: 22px; font-weight: 700; color: var(--text-main); }
    .chat-meta { color: var(--text-muted); font-size: 14px; }

    /* === MAIN CHAT CONTAINER === */
    .chat-card {
        background: var(--chat-bg);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid var(--border-color);
        position: relative;
    }

    /* Messages Area */
    .messages-container {
        flex-grow: 1;
        overflow-y: auto;
        padding: 30px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        background-color: #ffffff;
        background-image: radial-gradient(#f1f1f1 1px, transparent 1px);
        background-size: 20px 20px; /* Subtle grid pattern */
    }

    /* Scrollbar */
    .messages-container::-webkit-scrollbar { width: 5px; }
    .messages-container::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 10px; }

    /* Message Bubbles */
    .message {
        max-width: 75%;
        padding: 14px 20px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.6;
        animation: fadeIn 0.3s ease-out;
        position: relative;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Bot Message */
    .message.bot {
        align-self: flex-start;
        background: var(--bot-bubble);
        color: var(--text-main);
        border-bottom-left-radius: 4px;
        border: 1px solid #e8e8f5;
    }

    /* User Message */
    .message.user {
        align-self: flex-end;
        background: var(--accent);
        color: white;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 15px rgba(207, 15, 71, 0.2);
    }

    /* === INPUT AREA === */
    .input-area-wrapper {
        padding: 20px 30px;
        background: white;
        border-top: 1px solid var(--border-color);
    }

    .input-area {
        display: flex;
        gap: 12px;
        align-items: center;
        background: #f8f9fa;
        padding: 8px 8px 8px 20px;
        border-radius: 50px;
        border: 1px solid #eee;
        transition: 0.3s;
    }

    .input-area:focus-within {
        border-color: var(--accent);
        background: white;
        box-shadow: 0 0 0 4px rgba(207, 15, 71, 0.05);
    }

    .chat-input {
        flex-grow: 1;
        background: transparent;
        border: none;
        padding: 10px 0;
        color: var(--text-main);
        font-size: 14px;
        outline: none;
    }

    .send-btn {
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }

    .send-btn:hover {
        background: var(--accent-hover);
        transform: scale(1.05);
    }

    /* Typing Indicator */
    .typing-indicator {
        display: none;
        align-self: flex-start;
        background: var(--bot-bubble);
        padding: 12px 20px;
        border-radius: 18px;
        border-bottom-left-radius: 4px;
        gap: 5px;
    }
    .dot { width: 6px; height: 6px; background: #999; border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both; }
    .dot:nth-child(1) { animation-delay: -0.32s; }
    .dot:nth-child(2) { animation-delay: -0.16s; }
    @keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
</style>

<div class="chat-dashboard">
    <div class="chat-header">
        <div class="chat-title">
            <h1>Intelligence Assistant</h1>
            <div class="chat-meta">Analyzing reports and user trends for you</div>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="width:8px; height:8px; background:#6DD58C; border-radius:50%; box-shadow:0 0 8px #6DD58C;"></span>
            <span style="font-size:12px; font-weight:600; color:var(--text-muted);">AI Online</span>
        </div>
    </div>

    <div class="chat-card" role="main">
        <div id="messages" class="messages-container">
            <div class="message bot">
                Hello Administrator. I'm connected to the platform database. You can ask me things like <strong>"What is the most common accident type this month?"</strong> or <strong>"Summary of recent reports in Kabacan."</strong>
            </div>

            <div id="typing" class="typing-indicator">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>

        <div class="input-area-wrapper">
            <form id="chat-form" class="input-area">
                <input 
                    type="text" 
                    id="user-input" 
                    class="chat-input" 
                    placeholder="Type your question here..." 
                    autocomplete="off"
                >
                <button type="submit" class="send-btn" aria-label="Send Message">
                    <span class="material-icons" style="font-size: 20px;">send</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('chat-form');
        const input = document.getElementById('user-input');
        const messages = document.getElementById('messages');
        const typing = document.getElementById('typing');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const scrollToBottom = () => { messages.scrollTop = messages.scrollHeight; };

        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };

        const formatResponse = (text) => {
            let safeText = escapeHtml(text);
            safeText = safeText.replace(/\n/g, '<br>');
            safeText = safeText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            return safeText;
        };

        const appendMessage = (text, sender) => {
            const div = document.createElement('div');
            div.classList.add('message', sender);
            if (sender === 'bot') {
                div.innerHTML = formatResponse(text);
                messages.insertBefore(div, typing);
            } else {
                div.textContent = text;
                messages.insertBefore(div, typing);
            }
            scrollToBottom();
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            appendMessage(message, 'user');
            input.value = '';
            input.disabled = true;
            typing.style.display = 'flex';
            scrollToBottom();

            try {
                const response = await fetch("{{ route('admin.chat.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                typing.style.display = 'none';
                input.disabled = false;
                input.focus();

                if (response.ok && data.reply) {
                    appendMessage(data.reply, 'bot');
                } else {
                    appendMessage('I encountered an error accessing the database. Please try again.', 'bot');
                }
            } catch (error) {
                typing.style.display = 'none';
                input.disabled = false;
                appendMessage('Connection lost. Please check your network.', 'bot');
            }
        });
    });
</script>
@endsection