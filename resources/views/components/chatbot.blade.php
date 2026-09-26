<div class="chatbot" data-chatbot data-endpoint="{{ $endpoint }}">
    <button class="chatbot-toggle" type="button" data-chatbot-toggle aria-expanded="false" aria-controls="chatbot-panel">
        <span class="material-symbols-rounded">support_agent</span>
        <span>Support</span>
    </button>

    <section class="chatbot-panel" id="chatbot-panel" data-chatbot-panel hidden aria-label="Support assistant">
        <header class="chatbot-header">
            <div>
                <p class="chatbot-kicker">RBAC Payroll</p>
                <h2>How can we help?</h2>
            </div>
            <button class="chatbot-close" type="button" data-chatbot-close aria-label="Close support assistant">
                <span class="material-symbols-rounded">close</span>
            </button>
        </header>

        <div class="chatbot-messages" data-chatbot-messages aria-live="polite">
            <div class="chatbot-message chatbot-message-assistant">
                I can help with navigation, permissions, and general payroll workflow questions. I cannot access private records or change account data.
            </div>
        </div>

        <div class="chatbot-typing" data-chatbot-typing hidden aria-label="Support assistant is responding">
            <span></span><span></span><span></span>
        </div>

        <form class="chatbot-form" data-chatbot-form>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <label class="sr-only" for="chatbot-message-{{ md5($endpoint) }}">Ask the support assistant</label>
            <textarea id="chatbot-message-{{ md5($endpoint) }}" data-chatbot-input rows="2" maxlength="2000" placeholder="Ask a support question..." required></textarea>
            <button class="chatbot-send" type="submit" aria-label="Send message">
                <span class="material-symbols-rounded">send</span>
            </button>
        </form>
        <p class="chatbot-status" data-chatbot-status role="status"></p>
    </section>
</div>

<style>
    .chatbot { position: fixed; right: 1.25rem; bottom: 1.25rem; z-index: 60; font-family: Outfit, ui-sans-serif, system-ui, sans-serif; }
    .chatbot-panel[hidden] { display: none !important; }
    .chatbot-toggle { display: inline-flex; align-items: center; gap: .5rem; border: 1px solid rgba(255,255,255,.2); border-radius: 999px; background: linear-gradient(135deg, #10b981, #0284c7); color: #fff; padding: .75rem 1rem; box-shadow: 0 18px 45px rgba(2, 132, 199, .3); font-size: .85rem; font-weight: 600; cursor: pointer; }
    .chatbot-toggle:hover { transform: translateY(-1px); }
    .chatbot-panel { position: absolute; right: 0; bottom: 3.5rem; display: grid; width: min(22rem, calc(100vw - 2rem)); overflow: hidden; border: 1px solid rgba(148,163,184,.25); border-radius: 1.25rem; background: #0f172a; box-shadow: 0 24px 70px rgba(2, 6, 23, .42); }
    .chatbot-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; border-bottom: 1px solid rgba(148,163,184,.16); padding: 1rem; color: #fff; background: linear-gradient(135deg, rgba(14,165,233,.35), rgba(16,185,129,.2)); }
    .chatbot-kicker { margin: 0; color: #a7f3d0; font-size: .68rem; font-weight: 600; letter-spacing: .13em; text-transform: uppercase; }
    .chatbot-header h2 { margin: .3rem 0 0; font-size: 1rem; font-weight: 600; }
    .chatbot-close { border: 0; background: transparent; color: #cbd5e1; cursor: pointer; padding: .15rem; }
    .chatbot-messages { display: grid; gap: .65rem; max-height: 18rem; overflow-y: auto; padding: 1rem; }
    .chatbot-message { max-width: 88%; border-radius: .9rem; padding: .7rem .8rem; font-size: .82rem; line-height: 1.5; white-space: pre-wrap; }
    .chatbot-message-assistant { justify-self: start; background: rgba(255,255,255,.09); color: #dbeafe; }
    .chatbot-message-user { justify-self: end; background: #0ea5e9; color: #fff; }
    .chatbot-typing { display: inline-flex; align-items: center; gap: .25rem; width: fit-content; margin: 0 1rem .35rem; border-radius: .75rem; background: rgba(255,255,255,.09); padding: .55rem .65rem; }
    .chatbot-typing[hidden] { display: none !important; }
    .chatbot-typing span { width: .35rem; height: .35rem; border-radius: 50%; background: #67e8f9; animation: chatbot-pulse 1s infinite ease-in-out; }
    .chatbot-typing span:nth-child(2) { animation-delay: .15s; }
    .chatbot-typing span:nth-child(3) { animation-delay: .3s; }
    @keyframes chatbot-pulse { 0%, 60%, 100% { opacity: .3; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-.2rem); } }
    .chatbot-form { display: flex; align-items: flex-end; gap: .5rem; border-top: 1px solid rgba(148,163,184,.16); padding: .8rem; }
    .chatbot-form textarea { min-width: 0; flex: 1; resize: none; border: 1px solid rgba(148,163,184,.24); border-radius: .75rem; background: rgba(2,6,23,.55); color: #f8fafc; padding: .65rem .7rem; font: inherit; font-size: .82rem; outline: none; }
    .chatbot-form textarea:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56,189,248,.15); }
    .chatbot-send { display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; flex: 0 0 2.5rem; border: 0; border-radius: .75rem; background: #10b981; color: #fff; cursor: pointer; }
    .chatbot-send:disabled { cursor: wait; opacity: .55; }
    .chatbot-status { min-height: 1.1rem; margin: 0; padding: 0 1rem .7rem; color: #fda4af; font-size: .74rem; }
    .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
    @media (max-width: 480px) { .chatbot { right: 1rem; bottom: 1rem; } .chatbot-toggle { padding: .7rem .85rem; } }
</style>

<script>
    (() => {
        const root = document.querySelector('[data-chatbot]');
        if (!root) return;

        const toggle = root.querySelector('[data-chatbot-toggle]');
        const panel = root.querySelector('[data-chatbot-panel]');
        const close = root.querySelector('[data-chatbot-close]');
        const form = root.querySelector('[data-chatbot-form]');
        const input = root.querySelector('[data-chatbot-input]');
        const send = root.querySelector('.chatbot-send');
        const messages = root.querySelector('[data-chatbot-messages]');
        const typing = root.querySelector('[data-chatbot-typing]');
        const status = root.querySelector('[data-chatbot-status]');
        const history = [];

        const setOpen = (open) => {
            panel.hidden = !open;
            toggle.setAttribute('aria-expanded', String(open));
            if (open) input.focus();
        };

        const addMessage = (role, content) => {
            const element = document.createElement('div');
            element.className = `chatbot-message chatbot-message-${role}`;
            element.textContent = content;
            messages.appendChild(element);
            messages.scrollTop = messages.scrollHeight;
        };

        toggle.addEventListener('click', () => setOpen(panel.hidden));
        close.addEventListener('click', () => setOpen(false));
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = input.value.trim();
            if (!message || send.disabled) return;

            addMessage('user', message);
            history.push({ role: 'user', content: message });
            input.value = '';
            send.disabled = true;
            typing.hidden = false;
            status.textContent = '';

            try {
                const response = await fetch(root.dataset.endpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                    },
                    body: JSON.stringify({ message, history: history.slice(-8) }),
                });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || 'The assistant is unavailable.');

                addMessage('assistant', payload.message);
                history.push({ role: 'assistant', content: payload.message });
                status.textContent = '';
            } catch (error) {
                status.textContent = error.message || 'The assistant is unavailable. Please try again.';
            } finally {
                typing.hidden = true;
                send.disabled = false;
                input.focus();
            }
        });
    })();
</script>
