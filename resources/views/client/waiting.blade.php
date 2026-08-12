@extends('client.layouts.app')

@section('title', 'N ap tann — Kay-Y')

@section('content')

<div class="waiting-page">
    <div class="waiting-header">
        <div class="chef-anim">👨‍🍳</div>
        <h1>Manje ou ap vini!</h1>
        <p>Tab {{ $tableId }}</p>
    </div>

    <!-- Si gen plizyè kòmand aktif -->
    @if($hasMultipleOrders)
    <div class="multi-orders">
        <h4>📋 Kòmand ou yo</h4>
        @foreach($commandesActives as $cmd)
        <a href="/waiting/{{ $tableId }}/{{ $cmd->id }}" class="order-chip {{ $commande && $cmd->id == $commande->id ? 'current' : '' }}">
            <div class="chip-left">
                <strong>#{{ $cmd->id }}</strong>
                <span>{{ $cmd->items->count() }} plat • {{ number_format($cmd->total,0) }} HTG</span>
            </div>
            <span class="chip-status status-{{ $cmd->statut }}">
                {{ $cmd->statut === 'prete' ? '✅ Pare' : ($cmd->statut === 'en_preparation' ? '👨‍🍳 Kwit' : '📝 Nouvo') }}
            </span>
        </a>
        @endforeach
    </div>
    @endif

    <!-- Kòmand prensipal la -->
    @if($commande)
    <div class="status-card">
        <div class="status-top">
            <h3>Kòmand #{{ $commande->id }}</h3>
            <span class="status-badge" id="statusBadge" data-status="{{ $commande->statut }}">
                {{ $commande->statut === 'prete' ? 'Pare' : ($commande->statut === 'en_preparation' ? 'Ap kwit' : 'Nouvo') }}
            </span>
        </div>

        <div class="steps">
            <div class="step done"><div class="step-icon">📝</div><span>Reçue</span></div>
            <div class="step-line {{ $commande->statut != 'nouvelle' ? 'done' : '' }}" id="line1"></div>
            <div class="step {{ $commande->statut != 'nouvelle' ? 'done' : '' }}" id="stepCuisine"><div class="step-icon">👨‍🍳</div><span>Kwizin</span></div>
            <div class="step-line {{ $commande->statut == 'prete' ? 'done' : '' }}" id="line2"></div>
            <div class="step {{ $commande->statut == 'prete' ? 'done' : '' }}" id="stepReady"><div class="step-icon">🍽️</div><span>Pare</span></div>
        </div>

        <div class="timer-box">
            <div>⏱️ Rete</div>
            <strong id="timer">20:00</strong>
        </div>
        <p id="statusText" class="status-msg">
            {{ $commande->statut === 'prete' ? '🍽️ Kòmand ou pare! Bòn apeti.' : ($commande->statut === 'en_preparation' ? '👨‍🍳 Chef la ap kwit byen cho...' : '📝 Nou resevwa kòmand ou') }}
        </p>

        @if($commande->items->count())
        <div class="order-details">
            <h4>🍽 Detay kòmand lan</h4>
            @foreach($commande->items as $item)
            <div class="order-item">
                <div class="item-left">
                    <span class="item-qty">x{{ $item->quantite }}</span>
                    <span>{{ $item->plat->nom }}</span>
                </div>
                <strong>{{ number_format($item->plat->prix * $item->quantite,0) }} HTG</strong>
            </div>
            @endforeach
            <div class="order-total">
                <span>Total</span>
                <strong>{{ number_format($commande->total,2) }} HTG</strong>
            </div>
            @if($commande->note)
            <div class="order-note-box">
                📝 {{ $commande->note }}
            </div>
            @endif
        </div>
        @endif
        
        <!-- Bouton nouvelle commande si pare oswa si nap pèmèt toujou -->
        <div id="newOrderBtn" style="display:{{ $commande->statut == 'prete' ? 'block' : 'none' }};margin-top:20px;">
            <button onclick="clearActiveAndGoMenu()" class="btn-new-order">
                <i class="fa-solid fa-plus"></i> Pase yon lòt kòmand
            </button>
        </div>
    </div>
    @else
    <div class="status-card" style="text-align:center;padding:40px 20px;">
        <div style="font-size:50px;margin-bottom:15px;">🍽️</div>
        <h3>Ou pa gen kòmand aktif</h3>
        <p style="color:var(--terre);margin:10px 0 20px;">Kòmanse kòmande pou ou ka swiv manje ou a.</p>
        <button onclick="window.location.href='/menu/{{ $tableId }}'" class="btn-new-order">
            <i class="fa-solid fa-utensils"></i> Wè meni an
        </button>
    </div>
    @endif

    <!-- Jè Tap-Tap Amelyore -->
    <div class="game-card">
        <div class="game-header">
            <h2>🚛 Tap-Tap Kay-Y</h2>
            <p>Evite twou ak wòch, ranmasse manje! <span id="gameLevel" style="color:var(--lò);font-weight:700;">Nivo 1</span></p>
        </div>
        <div class="game-wrap">
            <canvas id="gameCanvas" width="350" height="200"></canvas>
            <div id="gameOverlay" style="display:none;">
                <div class="go-content">
                    <div style="font-size:40px;">💥</div>
                    <h3>Tap-Tap kraze!</h3>
                    <p>Score: <span id="finalScore">0</span></p>
                    <button onclick="startGame()">▶ Rejwe</button>
                </div>
            </div>
        </div>
        <div class="game-controls">
            <div class="game-score">Score: <span id="gameScore">0</span></div>
            <button id="gameBtn" onclick="startGame()">▶ Kòmanse</button>
        </div>
        <div class="game-legend">
            <span>🍗 +10</span> <span>🥥 +10</span> <span>🥘 +20</span> <span>⭐ Invinsib</span>
        </div>
        <p class="game-hint">Tape ekran an oswa bouton <b>Sote</b> pou kontrole</p>
    </div>
</div>

<style>
.waiting-page{padding:20px;max-width:500px;margin:0 auto;padding-bottom:100px;}
.waiting-header{text-align:center;margin-bottom:25px;padding-top:20px;}
.chef-anim{font-size:60px;margin-bottom:15px;animation:chefBounce 2s infinite;display:inline-block;}
@keyframes chefBounce{0%,100%{transform:translateY(0) rotate(0);}50%{transform:translateY(-12px) rotate(5deg);}}
.waiting-header h1{font-family:var(--font-bistro);font-size:28px;color:var(--kreyòl);margin-bottom:6px;}
.waiting-header p{color:var(--terre);font-size:14px;}

/* Multi-orders */
.multi-orders{margin-bottom:20px;}
.multi-orders h4{font-family:var(--font-bistro);font-size:16px;margin-bottom:12px;color:var(--kreyòl);}
.order-chip{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:var(--blan);border-radius:16px;margin-bottom:8px;text-decoration:none;border:1px solid var(--sable-fonce);box-shadow:0 2px 8px rgba(0,0,0,.04);transition:.2s;}
.order-chip:active{transform:scale(.98);}
.order-chip.current{border-color:var(--lò);background:linear-gradient(90deg,var(--blan),#fff8e1);}
.chip-left{display:flex;flex-direction:column;gap:2px;}
.chip-left strong{font-size:14px;color:var(--kreyòl);}
.chip-left span{font-size:12px;color:var(--terre);}
.chip-status{font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;}
.status-nouvelle{background:#f5f5f5;color:#666;}
.status-en_preparation{background:#fff3e0;color:#e65100;}
.status-prete{background:#e8f5e9;color:#2e7d32;}

.status-card{background:var(--blan);border-radius:24px;padding:25px;box-shadow:0 8px 25px rgba(30,58,95,.08);border:1px solid var(--sable-fonce);margin-bottom:25px;}
.status-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
.status-card h3{font-family:var(--font-bistro);font-size:20px;color:var(--kreyòl);}
.status-badge{background:var(--sable);color:var(--terre);padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;transition:.3s;}
.status-badge.prete{background:var(--vèt);color:#fff;}

.steps{display:flex;align-items:center;justify-content:center;margin:25px 0;}
.step{text-align:center;width:70px;transition:.4s;}
.step-icon{width:50px;height:50px;border-radius:50%;background:var(--sable);display:flex;align-items:center;justify-content:center;font-size:20px;margin:0 auto 8px;border:2px solid var(--sable-fonce);transition:.4s;}
.step span{font-size:11px;color:var(--terre);font-weight:600;}
.step.done .step-icon{background:linear-gradient(135deg,var(--vèt),#2ecc71);color:#fff;border-color:var(--vèt);box-shadow:0 4px 15px rgba(39,174,96,.3);transform:scale(1.1);}
.step.done span{color:var(--vèt);font-weight:700;}
.step-line{flex:1;height:3px;background:var(--sable-fonce);border-radius:3px;position:relative;overflow:hidden;}
.step-line::after{content:'';position:absolute;left:0;top:0;height:100%;width:0%;background:var(--vèt);transition:width .6s ease;}
.step-line.done::after{width:100%;}

.timer-box{background:linear-gradient(135deg,var(--sable),var(--sable-fonce));border-radius:16px;padding:20px;text-align:center;margin-top:20px;border:2px dashed var(--lò);}
.timer-box div{font-size:12px;color:var(--terre);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
.timer-box strong{font-family:var(--font-mono);font-size:32px;color:var(--rouge-brik);}
.status-msg{text-align:center;color:var(--terre);font-size:14px;margin-top:15px;min-height:22px;}

.order-details{margin-top:25px;padding-top:20px;border-top:2px dashed var(--sable-fonce);}
.order-details h4{font-family:var(--font-bistro);margin-bottom:15px;color:var(--kreyòl);font-size:16px;}
.order-item{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--sable-fonce);font-size:14px;}
.item-left{display:flex;align-items:center;gap:10px;}
.item-qty{background:var(--sable);padding:2px 8px;border-radius:6px;font-family:var(--font-mono);font-size:12px;font-weight:700;}
.order-total{display:flex;justify-content:space-between;margin-top:15px;padding-top:15px;border-top:2px solid var(--sable-fonce);font-size:18px;font-weight:700;color:var(--kreyòl);}
.order-total strong{font-family:var(--font-mono);color:var(--rouge-brik);}
.order-note-box{margin-top:12px;background:var(--sable);padding:12px;border-radius:12px;font-size:13px;color:var(--terre);border-left:3px solid var(--lò);}

.btn-new-order{width:100%;padding:14px;background:linear-gradient(135deg,var(--bleu-haiti),var(--bleu-fonce));color:#fff;border:none;border-radius:16px;font-weight:700;cursor:pointer;font-size:15px;box-shadow:0 6px 20px rgba(30,58,95,.25);transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px;}
.btn-new-order:active{transform:scale(.98);}

/* Game */
.game-card{background:var(--blan);border-radius:24px;padding:25px;box-shadow:0 8px 25px rgba(30,58,95,.08);border:1px solid var(--sable-fonce);text-align:center;}
.game-header{margin-bottom:18px;}
.game-header h2{font-family:var(--font-bistro);font-size:22px;color:var(--kreyòl);margin-bottom:6px;}
.game-header p{color:var(--terre);font-size:13px;}
.game-wrap{position:relative;border-radius:16px;overflow:hidden;border:3px solid var(--kreyòl);margin:0 auto;max-width:350px;}
#gameCanvas{background:linear-gradient(180deg,#87CEEB 0%,#E0F6FF 55%,#f4e4c1 55%,#d4a843 100%);display:block;width:100%;touch-action:none;}
#gameOverlay{position:absolute;inset:0;background:rgba(30,58,95,.85);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(2px);}
.go-content{color:#fff;text-align:center;padding:20px;}
.go-content h3{font-family:var(--font-bistro);font-size:22px;margin:10px 0;}
.go-content p{font-family:var(--font-mono);font-size:18px;margin-bottom:15px;}
.go-content button{padding:10px 28px;background:linear-gradient(135deg,var(--rouge-brik),var(--rouge-fonce));color:#fff;border:none;border-radius:12px;font-weight:700;cursor:pointer;font-size:14px;}
.game-controls{display:flex;justify-content:space-between;align-items:center;margin-top:15px;padding:0 5px;}
.game-score{font-family:var(--font-mono);font-size:18px;color:var(--kreyòl);font-weight:700;}
#gameBtn{padding:10px 24px;background:linear-gradient(135deg,var(--rouge-brik),var(--rouge-fonce));color:#fff;border:none;border-radius:12px;font-weight:700;cursor:pointer;font-size:14px;transition:.2s;}
#gameBtn:active{transform:scale(.95);}
.game-legend{display:flex;justify-content:center;gap:12px;margin-top:12px;font-size:12px;color:var(--terre);}
.game-hint{text-align:center;font-size:12px;color:var(--terre);margin-top:10px;}

/* Dark */
body.dark .order-chip{background:#241c16;border-color:#3e3028;}
body.dark .order-chip.current{background:linear-gradient(90deg,#241c16,#3e3028);}
body.dark .status-card, body.dark .game-card{background:#241c16;border-color:#3e3028;}
body.dark .timer-box{background:linear-gradient(135deg,#2a201a,#332820);border-color:#3e3028;}
body.dark .order-details{border-color:#3e3028;}
body.dark .order-item{border-color:#3e3028;}
body.dark .order-note-box{background:#2a201a;}
</style>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
const STATUS = "{{ $commande->statut ?? 'nouvelle' }}";
const CREATED_AT = {{ $commande->created_at->timestamp ?? time() }};
// TABLE_ID deja defini nan app.blade.php
const COMMANDE_ID = "{{ $commande->id ?? '' }}";

/* ===== TIMER ===== */
const PREP_TIME = 20*60;
function updateTimer(){
    if(STATUS==='prete'){document.getElementById('timer').innerHTML='00:00';return;}
    let now=Math.floor(Date.now()/1000), elapsed=now-CREATED_AT, remaining=PREP_TIME-elapsed;
    if(remaining<=0) remaining=0;
    let m=Math.floor(remaining/60), s=remaining%60;
    document.getElementById('timer').innerHTML=(m<10?'0':'')+m+':'+(s<10?'0':'')+s;
}
updateTimer(); 
let timerInterval = setInterval(updateTimer,1000);

/* ===== PUSHER ===== */
@if($commande)
const pusher = new Pusher('{{ config("broadcasting.connections.reverb.key") }}',{
    wsHost:'{{ config("broadcasting.connections.reverb.options.host") }}',
    wsPort:{{ config("broadcasting.connections.reverb.options.port") }},
    forceTLS:false, disableStats:true, cluster:'mt1'
});
const channel = pusher.subscribe('commande.'+COMMANDE_ID);

channel.bind('order-accepted',()=>{
    document.getElementById('stepCuisine').classList.add('done');
    document.getElementById('line1').classList.add('done');
    document.getElementById('statusText').innerHTML='👨‍🍳 Chef la ap kwit byen cho...';
    const badge = document.getElementById('statusBadge');
    badge.innerText='Ap kwit'; badge.className='status-badge status-en_preparation';
    showToast('👨‍🍳 Kòmand ou an kwit!');
});

channel.bind('order-ready',()=>{
    clearInterval(timerInterval);
    document.getElementById('stepCuisine').classList.add('done');
    document.getElementById('stepReady').classList.add('done');
    document.getElementById('line2').classList.add('done');
    document.getElementById('timer').innerHTML='00:00';
    document.getElementById('statusText').innerHTML='🍽️ Kòmand ou pare! Bòn apeti.';
    const badge = document.getElementById('statusBadge');
    badge.innerText='Pare'; badge.className='status-badge status-prete';
    document.getElementById('newOrderBtn').style.display='block';
    showToast('🍽️ Manje ou pare!');
});
@endif

function clearActiveAndGoMenu(){
    // Nou pa efase tout kòmand yo, nou jis retire dènye a nan localStorage
    // pou pèmèt yon nouvo kòmand
    localStorage.removeItem('kayy_cmd_'+TABLE_ID);
    window.location.href='/menu/'+TABLE_ID;
}

/* ===== JÈ TAP-TAP AMELYORE ===== */
const canvas=document.getElementById('gameCanvas');
const ctx=canvas.getContext('2d');
let gameLoop, score=0, isPlaying=false, tapY=140, tapVel=0, gravity=0.5, 
    obstacles=[], foods=[], particles=[], clouds=[], frame=0, level=1, speed=3, invincible=0;

function drawTap(x,y){
    // Kò
    ctx.fillStyle='#c0392b'; ctx.fillRect(x, y, 44, 22);
    ctx.fillRect(x+2, y-8, 40, 8); // toit
    // Fenet
    ctx.fillStyle='#f1c40f'; 
    ctx.fillRect(x+5, y+3, 10, 7); 
    ctx.fillRect(x+19, y+3, 10, 7);
    ctx.fillRect(x+33, y+3, 8, 7);
    // Rou
    ctx.fillStyle='#2c1810';
    ctx.beginPath(); ctx.arc(x+10, y+22, 6, 0, Math.PI*2); ctx.fill();
    ctx.beginPath(); ctx.arc(x+34, y+22, 6, 0, Math.PI*2); ctx.fill();
    // Dekorasyon jòn
    ctx.fillStyle='#f1c40f'; ctx.fillRect(x+2, y+14, 40, 3);
    // Vèyik
    if(isPlaying){
        ctx.fillStyle='rgba(255,255,255,.35)';
        ctx.fillRect(x-12, y+6, 8, 2);
        ctx.fillRect(x-16, y+12, 6, 2);
    }
    // Boukliye
    if(invincible>0){
        ctx.strokeStyle='rgba(212,168,67,'+(Math.abs(Math.sin(frame/5))*0.8+0.2)+')';
        ctx.lineWidth=2;
        ctx.beginPath(); ctx.arc(x+22, y+7, 30, 0, Math.PI*2); ctx.stroke();
    }
}

function drawObs(o){
    if(o.type==='hole'){
        ctx.fillStyle='#5d4037'; ctx.fillRect(o.x, 160-o.h, 24, o.h);
        ctx.fillStyle='#3e2723'; ctx.fillRect(o.x+2, 160-o.h, 20, 4);
        ctx.fillStyle='#8d6e63'; ctx.fillRect(o.x, 160-o.h, 24, 3);
    } else {
        // Wòch
        ctx.fillStyle='#795548';
        ctx.beginPath(); ctx.moveTo(o.x+12, 160-o.h); ctx.lineTo(o.x+24, 160); ctx.lineTo(o.x, 160); ctx.fill();
        ctx.fillStyle='#5d4037'; ctx.fillRect(o.x+8, 160-o.h+5, 8, 4);
    }
}

function drawFood(f){
    const icons=['🍗','🍌','🥥','🌶️','🍚','🥘'];
    ctx.font='20px serif';
    ctx.shadowColor='rgba(0,0,0,.2)'; ctx.shadowBlur=4;
    ctx.fillText(icons[f.type%6], f.x, f.y);
    ctx.shadowBlur=0;
}

function drawCloud(){
    clouds.forEach(c=>{
        ctx.fillStyle='rgba(255,255,255,.75)';
        ctx.beginPath(); ctx.arc(c.x, c.y, c.r, 0, Math.PI*2); ctx.fill();
        ctx.beginPath(); ctx.arc(c.x+c.r*.6, c.y-c.r*.3, c.r*.8, 0, Math.PI*2); ctx.fill();
        ctx.beginPath(); ctx.arc(c.x-c.r*.6, c.y-c.r*.2, c.r*.6, 0, Math.PI*2); ctx.fill();
        c.x -= c.speed;
    });
    if(frame%100===0) clouds.push({x:360, y:15+Math.random()*50, r:12+Math.random()*12, speed:0.4+Math.random()*0.6});
    clouds = clouds.filter(c=>c.x>-60);
}

function createParticles(x,y,color){
    for(let i=0;i<10;i++){
        particles.push({
            x:x, y:y, vx:(Math.random()-.5)*5, vy:(Math.random()-.5)*5,
            life:25, color:color||'#f1c40f', size:2+Math.random()*3
        });
    }
}

function drawParticles(){
    for(let i=particles.length-1;i>=0;i--){
        let p=particles[i]; p.x+=p.vx; p.y+=p.vy; p.life--; p.vy+=0.15;
        ctx.globalAlpha=p.life/25;
        ctx.fillStyle=p.color;
        ctx.fillRect(p.x, p.y, p.size, p.size);
        ctx.globalAlpha=1;
        if(p.life<=0) particles.splice(i,1);
    }
}

function startGame(){
    if(isPlaying) return;
    isPlaying=true; score=0; frame=0; tapY=140; tapVel=0; 
    obstacles=[]; foods=[]; particles=[]; clouds=[]; level=1; speed=3; invincible=0;
    document.getElementById('gameBtn').innerText='⏸ Kanpe';
    document.getElementById('gameBtn').onclick=stopGame;
    document.getElementById('gameScore').innerText='0';
    document.getElementById('gameLevel').innerText='Nivo 1';
    document.getElementById('gameOverlay').style.display='none';
    gameLoop=setInterval(updateGame, 20);
}

function stopGame(){
    isPlaying=false; clearInterval(gameLoop);
    document.getElementById('gameBtn').innerText='▶ Kòmanse';
    document.getElementById('gameBtn').onclick=startGame;
}

function jump(){
    if(!isPlaying) return;
    tapVel = -6.5;
}

function gameOver(){
    stopGame();
    document.getElementById('gameOverlay').style.display='flex';
    document.getElementById('finalScore').innerText=score;
}

// Kontwòl
canvas.addEventListener('touchstart',(e)=>{e.preventDefault();jump();},{passive:false});
canvas.addEventListener('mousedown',(e)=>{e.preventDefault();jump();});
document.addEventListener('keydown',(e)=>{if(e.code==='Space'||e.code==='ArrowUp'){e.preventDefault();jump();}});

function updateGame(){
    ctx.clearRect(0,0,350,200);
    
    // Solèy
    ctx.fillStyle='rgba(255,215,0,.25)';
    ctx.beginPath(); ctx.arc(300, 35, 22, 0, Math.PI*2); ctx.fill();
    ctx.fillStyle='rgba(255,215,0,.1)';
    ctx.beginPath(); ctx.arc(300, 35, 32, 0, Math.PI*2); ctx.fill();
    
    drawCloud();
    
    // Sol
    ctx.fillStyle='#d4a843'; ctx.fillRect(0, 160, 350, 40);
    ctx.fillStyle='#b8933f'; ctx.fillRect(0, 160, 350, 4);
    // Liy wout
    ctx.fillStyle='rgba(255,255,255,.25)';
    for(let i=0;i<6;i++){
        let lx=((frame*speed)%60)+i*60;
        ctx.fillRect(lx, 178, 25, 3);
    }
    
    frame++;
    tapVel += gravity;
    tapY += tapVel;
    if(tapY > 140){ tapY=140; tapVel=0; }
    if(tapY < 0) tapY=0;
    
    // Nivo
    if(frame%400===0){ level++; speed+=0.25; document.getElementById('gameLevel').innerText='Nivo '+level; }
    
    // Boukliye
    if(invincible>0) invincible--;
    
    drawTap(50, tapY);
    
    // Obstacles
    let spawnRate = Math.max(55, 95 - level*4);
    if(frame % spawnRate === 0){
        let type = Math.random()>0.75 ? 'rock' : 'hole';
        obstacles.push({x:350, h:18+Math.random()*22, type:type});
    }
    for(let i=obstacles.length-1;i>=0;i--){
        let o=obstacles[i]; o.x -= speed; drawObs(o);
        if(!invincible && o.x<88 && o.x>22 && tapY>128-o.h){
            createParticles(50, tapY+10, '#c0392b');
            gameOver(); return;
        }
        if(o.x<-40) obstacles.splice(i,1);
    }
    
    // Manje
    if(frame%65===0){
        foods.push({x:350, y:65+Math.random()*75, type:Math.floor(Math.random()*6)});
    }
    for(let i=foods.length-1;i>=0;i--){
        let f=foods[i]; f.x -= speed; drawFood(f);
        if(f.x<95 && f.x>30 && Math.abs(f.y-(tapY+8))<24){
            let pts = (f.type===5?20:10);
            score += pts;
            createParticles(f.x, f.y, '#f1c40f');
            foods.splice(i,1);
            document.getElementById('gameScore').innerText=score;
            if(score%50===0 && score>0){ invincible=150; showToast('⭐ Boukliye aktive!'); }
        } else if(f.x<-20){
            foods.splice(i,1);
        }
    }
    
    drawParticles();
    
    // UI anndan canvas
    ctx.fillStyle='rgba(30,58,95,.55)';
    ctx.beginPath(); ctx.roundRect(8, 8, 90, 28, 8); ctx.fill();
    ctx.fillStyle='#fff'; ctx.font='bold 13px "IBM Plex Mono",monospace';
    ctx.fillText(score+' pts', 16, 26);
}
</script>
@endsection