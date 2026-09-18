{{-- resources/views/components/chatbot.blade.php --}}
<div id="hkc-wrap">
    <div id="hkc-tooltip">&#x1F4AC; Need help?</div>
    <button id="hkc-fab" aria-label="Open support chat">
        <svg id="hkc-icon-chat" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <svg id="hkc-icon-close" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        <span id="hkc-badge" style="display:none">1</span>
    </button>

    <div id="hkc-panel">
        <div id="hkc-head">
            <div id="hkc-head-left">
                <div id="hkc-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    <span id="hkc-dot"></span>
                </div>
                <div>
                    <div id="hkc-name">Hamro Koseli Support</div>
                    <div id="hkc-status">&#9679; Online now</div>
                </div>
            </div>
            <button id="hkc-minimize" aria-label="Close chat">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div id="hkc-body">
            <div id="hkc-msgs">
                <div class="hkc-time-label">Today</div>
                <div class="hkc-row hkc-row-bot">
                    <div class="hkc-bubble hkc-bubble-bot">
Namaste 🙏 Welcome to <strong>Hamro Koseli!</strong><br>I can help you with orders, payments, shipping, and more.
                    </div>
                </div>
                <div id="hkc-chips">
                    <button class="hkc-chip" data-q="How do I place an order?">🛒 Place order</button>
                    <button class="hkc-chip" data-q="What payment methods do you accept?">💳 Payments</button>
                    <button class="hkc-chip" data-q="How can I track my order?">📦 Track order</button>
                    <button class="hkc-chip" data-q="What is the return policy?">↩️ Returns</button>
                </div>
            </div>
        </div>

        <div id="hkc-foot">
            <form id="hkc-form" autocomplete="off">
                <input id="hkc-input" type="text" placeholder="Ask something..." maxlength="500" aria-label="Message">
                <button type="submit" id="hkc-send" aria-label="Send">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
            <div id="hkc-powered">Hamro Koseli &bull; AI Support</div>
        </div>
    </div>
</div>

<style>
/* =====================================================
   Hamro Koseli Chatbot — Brand-themed UI
   Primary   : #1F3D2E (forest green)
   Secondary : #C65A3A (terracotta)
   Cream     : #FFF7EF / #F5E8D6
   Text      : #3A2A1F
   ===================================================== */

#hkc-wrap{
    position:fixed;bottom:28px;right:28px;z-index:999999;
    font-family:'Plus Jakarta Sans',system-ui,sans-serif;
}

/* ── FAB Button ── */
#hkc-fab{
    width:62px;height:62px;border-radius:50%;border:none;
    background:linear-gradient(145deg,#C65A3A 0%,#1F3D2E 100%);
    color:#fff;cursor:pointer;
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 6px 24px rgba(198,90,58,0.50),0 2px 8px rgba(31,61,46,0.30);
    transition:transform .2s,box-shadow .2s;
    position:relative;
    animation:hkc-fab-bounce 3s ease-in-out infinite;
}
#hkc-fab:hover{
    transform:scale(1.12);
    box-shadow:0 10px 32px rgba(198,90,58,0.60),0 4px 14px rgba(31,61,46,0.35);
    animation:none;
}
#hkc-fab:active{transform:scale(0.95);animation:none;}

/* Pulse rings — terracotta brand color */
#hkc-fab::before{
    content:'';position:absolute;inset:-5px;border-radius:50%;
    border:2.5px solid rgba(198,90,58,0.60);
    animation:hkc-pulse-ring 2.4s cubic-bezier(0.4,0,0.6,1) infinite;
    pointer-events:none;
}
#hkc-fab::after{
    content:'';position:absolute;inset:-5px;border-radius:50%;
    border:2px solid rgba(198,90,58,0.30);
    animation:hkc-pulse-ring 2.4s cubic-bezier(0.4,0,0.6,1) infinite .8s;
    pointer-events:none;
}

/* Tooltip */
#hkc-tooltip{
    position:absolute;right:74px;top:50%;
    background:#1F3D2E;color:#FFF7EF;
    font-size:12px;font-weight:700;letter-spacing:.02em;
    white-space:nowrap;padding:7px 14px;border-radius:10px;
    pointer-events:none;opacity:0;
    transition:opacity .22s,transform .22s;
    transform:translateY(-50%) translateX(8px);
    box-shadow:0 3px 12px rgba(31,61,46,0.25);
    border:1px solid rgba(198,90,58,0.20);
}
#hkc-tooltip::after{
    content:'';position:absolute;left:100%;top:50%;transform:translateY(-50%);
    border:6px solid transparent;border-left-color:#1F3D2E;
}
#hkc-wrap:hover #hkc-tooltip{opacity:1;transform:translateY(-50%) translateX(0);}

/* Animations */
@keyframes hkc-fab-bounce{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-6px)}
}
@keyframes hkc-pulse-ring{
    0%{transform:scale(1);opacity:1}
    75%{transform:scale(1.40);opacity:0}
    100%{transform:scale(1.40);opacity:0}
}

/* Stop animations when panel is open */
#hkc-wrap.hkc-is-open #hkc-fab{animation:none;}
#hkc-wrap.hkc-is-open #hkc-fab::before,
#hkc-wrap.hkc-is-open #hkc-fab::after{display:none;}
#hkc-wrap.hkc-is-open #hkc-tooltip{display:none;}

/* Badge */
#hkc-badge{
    position:absolute;top:-3px;right:-3px;
    background:#C65A3A;color:#fff;
    width:19px;height:19px;border-radius:50%;
    font-size:11px;font-weight:700;
    display:flex;align-items:center;justify-content:center;
    border:2.5px solid #fff;
    box-shadow:0 2px 6px rgba(198,90,58,0.4);
}

/* ── Panel ── */
#hkc-panel{
    position:absolute;bottom:76px;right:0;
    width:375px;max-width:calc(100vw - 32px);
    height:545px;max-height:calc(100dvh - 115px);
    background:#FFF7EF;border-radius:22px;
    box-shadow:0 24px 64px rgba(31,61,46,0.18),0 4px 16px rgba(0,0,0,0.08);
    border:1px solid rgba(198,90,58,0.15);
    display:none;flex-direction:column;overflow:hidden;
}
#hkc-panel.hkc-open{
    display:flex;
    animation:hkc-slide-up .25s cubic-bezier(.34,1.56,.64,1) forwards;
}
@keyframes hkc-slide-up{
    from{opacity:0;transform:translateY(18px) scale(0.96)}
    to{opacity:1;transform:translateY(0) scale(1)}
}

/* ── Header ── */
#hkc-head{
    background:linear-gradient(135deg,#1F3D2E 0%,#2d5941 100%);
    padding:16px 18px;
    display:flex;align-items:center;justify-content:space-between;
    flex-shrink:0;
    border-bottom:2px solid rgba(198,90,58,0.35);
}
#hkc-head-left{display:flex;align-items:center;gap:13px}
#hkc-avatar{
    width:42px;height:42px;border-radius:50%;
    background:linear-gradient(135deg,#C65A3A,#e07a5a);
    display:flex;align-items:center;justify-content:center;
    position:relative;flex-shrink:0;
    border:2px solid rgba(255,247,239,0.30);
    box-shadow:0 2px 8px rgba(0,0,0,0.20);
}
#hkc-dot{
    position:absolute;bottom:1px;right:1px;
    width:11px;height:11px;border-radius:50%;
    background:#4ade80;border:2.5px solid #1F3D2E;
    animation:hkc-dot-pulse 2s ease-in-out infinite;
}
@keyframes hkc-dot-pulse{
    0%,100%{box-shadow:0 0 0 0 rgba(74,222,128,0.5)}
    50%{box-shadow:0 0 0 4px rgba(74,222,128,0)}
}
#hkc-name{
    color:#FFF7EF;font-size:14px;font-weight:700;
    letter-spacing:.01em;text-shadow:0 1px 2px rgba(0,0,0,0.15);
}
#hkc-status{color:#86efac;font-size:11.5px;margin-top:2px;font-weight:600;}
#hkc-minimize{
    background:rgba(255,247,239,0.10);border:1px solid rgba(255,247,239,0.15);
    color:rgba(255,247,239,0.70);width:32px;height:32px;border-radius:50%;
    cursor:pointer;display:flex;align-items:center;justify-content:center;
    transition:background .15s,color .15s,border-color .15s;flex-shrink:0;
}
#hkc-minimize:hover{
    background:rgba(198,90,58,0.30);
    border-color:rgba(198,90,58,0.50);
    color:#FFF7EF;
}

/* ── Messages area ── */
#hkc-body{flex:1;overflow:hidden;display:flex;flex-direction:column;}
#hkc-msgs{
    flex:1;overflow-y:auto;
    padding:18px 16px 12px;
    display:flex;flex-direction:column;gap:10px;
    background:#F5E8D6;
    scroll-behavior:smooth;
}
#hkc-msgs::-webkit-scrollbar{width:3px}
#hkc-msgs::-webkit-scrollbar-track{background:transparent}
#hkc-msgs::-webkit-scrollbar-thumb{background:rgba(198,90,58,0.30);border-radius:3px}

.hkc-time-label{
    text-align:center;font-size:11px;color:rgba(58,42,31,0.45);
    font-weight:600;letter-spacing:.06em;
    text-transform:uppercase;margin-bottom:2px;
}
.hkc-row{display:flex;align-items:flex-end;gap:8px}
.hkc-row-bot{flex-direction:row}
.hkc-row-user{flex-direction:row-reverse}

/* Bot bubble */
.hkc-bubble{
    padding:11px 15px;
    max-width:80%;font-size:13.5px;line-height:1.58;
    word-break:break-word;white-space:pre-wrap;
}
.hkc-bubble-bot{
    background:#FFF7EF;color:#3A2A1F;
    border-radius:4px 18px 18px 18px;
    box-shadow:0 1px 4px rgba(58,42,31,0.10);
    border:1px solid rgba(198,90,58,0.18);
}
/* User bubble — terracotta */
.hkc-bubble-user{
    background:linear-gradient(135deg,#C65A3A,#b04a2c);
    color:#FFF7EF;
    border-radius:18px 18px 4px 18px;
    box-shadow:0 2px 8px rgba(198,90,58,0.30);
}
.hkc-bubble-error{
    background:#fff0f0;color:#9b2c2c;
    border:1px solid #fca5a5;
    border-radius:4px 18px 18px 18px;
    font-size:13px;
}

/* Quick-reply chips */
#hkc-chips{display:flex;flex-wrap:wrap;gap:7px;padding-top:4px}
.hkc-chip{
    padding:7px 13px;border-radius:20px;
    border:1.5px solid rgba(198,90,58,0.35);
    background:#FFF7EF;
    color:#C65A3A;font-size:12px;
    font-family:'Plus Jakarta Sans',system-ui,sans-serif;
    font-weight:600;cursor:pointer;
    transition:border-color .15s,background .15s,color .15s,transform .1s;
    white-space:nowrap;
}
.hkc-chip:hover{
    border-color:#C65A3A;background:#C65A3A;color:#FFF7EF;
    transform:translateY(-1px);
}

/* Typing dots */
.hkc-typing{
    display:flex;align-items:center;gap:5px;
    padding:13px 16px;background:#FFF7EF;
    border:1px solid rgba(198,90,58,0.18);
    border-radius:4px 18px 18px 18px;
    box-shadow:0 1px 4px rgba(58,42,31,0.08);
    width:fit-content;
}
.hkc-typing span{
    width:7px;height:7px;border-radius:50%;
    background:#C65A3A;display:block;
    animation:hkc-blink 1.2s infinite;
}
.hkc-typing span:nth-child(2){animation-delay:.2s}
.hkc-typing span:nth-child(3){animation-delay:.4s}
@keyframes hkc-blink{
    0%,80%,100%{transform:scale(1);opacity:.35}
    40%{transform:scale(1.35);opacity:1}
}

/* ── Footer / Input ── */
#hkc-foot{
    background:#FFF7EF;
    border-top:1px solid rgba(198,90,58,0.18);
    padding:12px 14px 9px;
    flex-shrink:0;
}
#hkc-form{display:flex;align-items:center;gap:8px}
#hkc-input{
    flex:1;min-width:0;
    padding:10px 16px;
    border:1.5px solid rgba(198,90,58,0.28);
    border-radius:24px;
    font-size:13.5px;
    font-family:'Plus Jakarta Sans',system-ui,sans-serif;
    background:#fff;color:#3A2A1F;
    outline:none;
    transition:border-color .15s,box-shadow .15s;
}
#hkc-input:focus{
    border-color:#C65A3A;
    box-shadow:0 0 0 3px rgba(198,90,58,0.12);
}
#hkc-input::placeholder{color:rgba(58,42,31,0.35)}
#hkc-send{
    width:42px;height:42px;border-radius:50%;
    border:none;
    background:linear-gradient(135deg,#C65A3A,#1F3D2E);
    color:#fff;
    cursor:pointer;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    transition:transform .15s,box-shadow .15s;
    box-shadow:0 3px 10px rgba(198,90,58,0.35);
}
#hkc-send:hover{transform:scale(1.08);box-shadow:0 5px 14px rgba(198,90,58,0.45);}
#hkc-send:active{transform:scale(0.95);}
#hkc-powered{
    text-align:center;font-size:10.5px;
    color:rgba(58,42,31,0.35);
    margin-top:6px;letter-spacing:.02em;font-weight:500;
}

@media(max-width:480px){
    #hkc-wrap{bottom:18px;right:18px}
    #hkc-panel{
        position:fixed;top:0;left:0;right:0;bottom:0;
        width:100%;max-width:100%;
        height:100%;max-height:100%;
        border-radius:0;
    }
    #hkc-input{font-size:16px}
}
</style>

<script>
(function(){
    var wrap=document.getElementById('hkc-wrap'),
        fab=document.getElementById('hkc-fab'),
        panel=document.getElementById('hkc-panel'),
        minimize=document.getElementById('hkc-minimize'),
        msgs=document.getElementById('hkc-msgs'),
        form=document.getElementById('hkc-form'),
        inp=document.getElementById('hkc-input'),
        chips=document.getElementById('hkc-chips'),
        iconChat=document.getElementById('hkc-icon-chat'),
        iconClose=document.getElementById('hkc-icon-close'),
        csrf=document.querySelector('meta[name="csrf-token"]')?.content,
        history=[],open=false;

    function openPanel(){
        open=true;
        panel.classList.add('hkc-open');
        wrap.classList.add('hkc-is-open');
        iconChat.style.display='none';
        iconClose.style.display='block';
        setTimeout(function(){inp.focus();},250);
    }
    function closePanel(){
        open=false;
        panel.classList.remove('hkc-open');
        wrap.classList.remove('hkc-is-open');
        iconChat.style.display='block';
        iconClose.style.display='none';
    }
    fab.addEventListener('click',function(){open?closePanel():openPanel();});
    minimize.addEventListener('click',closePanel);
    document.addEventListener('keydown',function(e){if(e.key==='Escape'&&open)closePanel();});

    function scroll(){msgs.scrollTop=msgs.scrollHeight;}

    function addBot(html){
        var row=document.createElement('div');
        row.className='hkc-row hkc-row-bot';
        var bub=document.createElement('div');
        bub.className='hkc-bubble hkc-bubble-bot';
        bub.innerHTML=html;
        row.appendChild(bub);
        msgs.appendChild(row);
        scroll();
    }
    function addUser(text){
        var row=document.createElement('div');
        row.className='hkc-row hkc-row-user';
        var bub=document.createElement('div');
        bub.className='hkc-bubble hkc-bubble-user';
        bub.textContent=text;
        row.appendChild(bub);
        msgs.appendChild(row);
        scroll();
    }
    function addTyping(){
        var row=document.createElement('div');
        row.className='hkc-row hkc-row-bot';
        row.id='hkc-typing-row';
        var t=document.createElement('div');
        t.className='hkc-typing';
        t.innerHTML='<span></span><span></span><span></span>';
        row.appendChild(t);
        msgs.appendChild(row);
        scroll();
        return row;
    }
    function addError(msg){
        var row=document.createElement('div');
        row.className='hkc-row hkc-row-bot';
        var bub=document.createElement('div');
        bub.className='hkc-bubble hkc-bubble-error';
        bub.textContent=msg;
        row.appendChild(bub);
        msgs.appendChild(row);
        scroll();
    }

    chips.addEventListener('click',function(e){
        var chip=e.target.closest('.hkc-chip');
        if(!chip)return;
        chips.style.display='none';
        sendMsg(chip.dataset.q);
    });

    form.addEventListener('submit',function(e){
        e.preventDefault();
        var text=inp.value.trim();
        if(!text)return;
        chips.style.display='none';
        inp.value='';
        sendMsg(text);
    });

    function formatReply(text){
        return text
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>')
            .replace(/\n/g,'<br>');
    }

    async function sendMsg(text){
        addUser(text);
        history.push({role:'user',content:text});
        var typingRow=addTyping();
        try{
            var res=await fetch('/chatbot/send',{
                method:'POST',
                headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
                body:JSON.stringify({messages:history})
            });
            var data=await res.json();
            typingRow.remove();
            if(!res.ok||!data.reply){
                addError(data.error||'Something went wrong. Try again.');
                return;
            }
            addBot(formatReply(data.reply));
            history.push({role:'assistant',content:data.reply});
        }catch(err){
            typingRow.remove();
            addError('Network error. Check your connection.');
        }
    }
})();
</script>