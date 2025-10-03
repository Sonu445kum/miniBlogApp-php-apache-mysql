// Dark mode toggle persisted in localStorage
(function(){
  const btn = document.getElementById('darkToggle');
  if(!btn) return; // Button missing? Exit

  function setTheme(t){
    document.documentElement.setAttribute('data-theme', t);
    localStorage.setItem('theme', t);
    btn.textContent = t === 'dark' ? 'Light' : 'Dark';
  }

  const saved = localStorage.getItem('theme') || 'light';
  setTheme(saved);

  btn.addEventListener('click', ()=> 
    setTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark')
  );
})();


// Like action via fetch
function likePost(postId){
  fetch('../like.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'post_id=' + encodeURIComponent(postId)
  })
  .then(r=>r.json())
  .then(data=>{
    if(data.success) {
      let el = document.getElementById('like-count-'+postId);
      if(el) el.textContent = data.likes;
    } else {
      alert(data.message || 'Error');
    }
  })
  .catch(err => console.error('Like request failed:', err));
}
