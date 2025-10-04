(function(){
  function fmtTime(s){ s=Math.floor(s||0); var m=Math.floor(s/60), r=s%60; return m+":"+(r<10?"0":"")+r; }
  function Player(root){
    this.root=root; this.audio=new Audio();
    this.items=[].slice.call(root.querySelectorAll('.nmp-item'));
    this.cover=root.querySelector('.nmp-cover img'); this.title=root.querySelector('.nmp-title');
    this.bpm=root.querySelector('.nmp-bpm'); this.key=root.querySelector('.nmp-key');
    this.btnToggle=root.querySelector('.nmp-toggle'); this.btnPrev=root.querySelector('.nmp-prev'); this.btnNext=root.querySelector('.nmp-next');
    this.timeCur=root.querySelector('.nmp-current'); this.timeDur=root.querySelector('.nmp-duration');
    this.bar=root.querySelector('.nmp-bar'); this.fill=root.querySelector('.nmp-fill'); this.buy=root.querySelector('.nmp-buy');
    this.buyLabel=root.getAttribute('data-buy-label')||'Buy'; this.index=0;
    var self=this;
    this.items.forEach(function(li,i){ li.addEventListener('click',function(){ self.load(i,true); }); });
    this.btnToggle.addEventListener('click',function(){ self.toggle(); });
    this.btnPrev.addEventListener('click',function(){ self.prev(); });
    this.btnNext.addEventListener('click',function(){ self.next(); });
    this.bar.addEventListener('click',function(e){ var rect=self.bar.getBoundingClientRect(); var pct=Math.max(0,Math.min(1,(e.clientX-rect.left)/rect.width)); self.audio.currentTime=pct*(self.audio.duration||0); });
    document.addEventListener('keydown',function(e){ if(!self.root.contains(document.activeElement)) return; if(e.code==='Space'){ e.preventDefault(); self.toggle(); } if(e.code==='ArrowRight'){ self.next(); } if(e.code==='ArrowLeft'){ self.prev(); } });
    this.audio.addEventListener('timeupdate',function(){ self.timeCur.textContent=fmtTime(self.audio.currentTime); self.timeDur.textContent=fmtTime(self.audio.duration); var pct=(self.audio.currentTime/(self.audio.duration||1))*100; self.fill.style.width=pct+'%'; });
    this.audio.addEventListener('ended',function(){ self.next(); });
    this.load(0,false);
  }
  Player.prototype.load=function(i,autoplay){
    if(i<0)i=this.items.length-1; if(i>=this.items.length)i=0; this.index=i;
    this.items.forEach(function(el){ el.classList.remove('is-active'); });
    var li=this.items[i]; li.classList.add('is-active');
    var a=li.getAttribute('data-audio'), t=li.getAttribute('data-title'), c=li.getAttribute('data-cover'), b=li.getAttribute('data-bpm'), k=li.getAttribute('data-key'), p=li.getAttribute('data-product');
    this.audio.src=a; this.title.textContent=t||''; if(this.bpm)this.bpm.textContent=b?(b+' BPM'):''; if(this.key)this.key.textContent=k||''; if(this.cover)this.cover.src=c||'';
    if(this.buy){ this.buy.textContent=this.buyLabel; this.buy.href=p||'#'; }
    if(autoplay)this.audio.play(); this.btnToggle.textContent='▶';
  };
  Player.prototype.toggle=function(){ if(this.audio.paused){ this.audio.play(); this.btnToggle.textContent='⏸'; } else { this.audio.pause(); this.btnToggle.textContent='▶'; } };
  Player.prototype.prev=function(){ this.load(this.index-1,true); };
  Player.prototype.next=function(){ this.load(this.index+1,true); };
  document.addEventListener('DOMContentLoaded',function(){ Array.prototype.forEach.call(document.querySelectorAll('.nmp-player'),function(el){ new Player(el); }); });
})();
