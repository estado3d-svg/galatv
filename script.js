const toast = document.getElementById("toast");
function notify(text){
  toast.textContent=text;
  toast.classList.add("show");
  clearTimeout(window.__toast);
  window.__toast=setTimeout(()=>toast.classList.remove("show"),2200);
}
function playLive(){ notify("Conectando a la transmisión en vivo…"); }
function togglePlay(){ notify("Reproductor: reproducción"); }
function showInfo(){ notify("Contenido exclusivo de Gala TV Streaming"); }
function subscribeNow(){ notify("Planes de suscripción disponibles próximamente"); }
function fullscreenPlayer(){
  const p=document.querySelector(".player-wrap");
  if(document.fullscreenElement) document.exitFullscreen();
  else if(p.requestFullscreen) p.requestFullscreen();
}
document.querySelectorAll(".nav a").forEach(a=>{
  a.addEventListener("click",()=>{
    document.querySelectorAll(".nav a").forEach(x=>x.classList.remove("active"));
    a.classList.add("active");
  });
});
