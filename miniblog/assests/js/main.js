// ----------------------------
// Dark mode toggle with persistence
// ----------------------------
(function(){
  const btn = document.getElementById('darkToggle'); // Get the toggle button by ID
  if(!btn) return; // If button not found, exit immediately

  // Function to set the theme
  function setTheme(t){
    // Set data-theme attribute on <html> for CSS styling
    document.documentElement.setAttribute('data-theme', t);
    // Save current theme to localStorage so it persists across reloads
    localStorage.setItem('theme', t);
    // Update button text to indicate opposite theme
    btn.textContent = t === 'dark' ? 'Light' : 'Dark';
  }

  // Load saved theme from localStorage or default to 'light'
  const saved = localStorage.getItem('theme') || 'light';
  setTheme(saved);

  // Listen for button click to toggle theme
  btn.addEventListener('click', ()=> 
    setTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark')
  );
})();

// ----------------------------
// Like button action (AJAX/fetch)
// ----------------------------
function likePost(postId){
  // Send POST request to like.php with post_id
  fetch('../like.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'post_id=' + encodeURIComponent(postId)
  })
  .then(r => r.json()) // Parse JSON response
  .then(data => {
    if(data.success) {
      // Update like count on the page
      let el = document.getElementById('like-count-' + postId);
      if(el) el.textContent = data.likes; // Update count if element exists
    } else {
      // Show error message if request failed
      alert(data.message || 'Error');
    }
  })
  .catch(err => console.error('Like request failed:', err)); // Log network errors
}
