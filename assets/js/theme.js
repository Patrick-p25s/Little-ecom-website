(function () {
  var root = document.documentElement;
  var toggle = document.getElementById('themeToggle');
  var saved = localStorage.getItem('theme');

  function apply(theme) {
    root.setAttribute('data-bs-theme', theme);
    if (toggle) {
      toggle.textContent = theme === 'dark' ? 'Mode clair' : 'Mode sombre';
    }
  }

  apply(saved === 'dark' ? 'dark' : 'light');

  if (toggle) {
    toggle.addEventListener('click', function () {
      var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      localStorage.setItem('theme', next);
      apply(next);
    });
  }
})();
