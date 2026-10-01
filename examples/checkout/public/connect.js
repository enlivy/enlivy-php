(function () {
  var STORAGE_KEY = 'enlivy-checkout-example:connection';
  var form = document.getElementById('connect');
  if (!form) return;

  var apiKey = form.elements.api_key;
  var apiBase = form.elements.api_base;
  var remember = document.getElementById('remember');
  var remembered = document.getElementById('remembered');

  function read() {
    try { return JSON.parse(window.localStorage.getItem(STORAGE_KEY) || 'null'); } catch (e) { return null; }
  }
  function write(value) {
    try {
      if (value) window.localStorage.setItem(STORAGE_KEY, JSON.stringify(value));
      else window.localStorage.removeItem(STORAGE_KEY);
    } catch (e) {}
  }

  var saved = read();
  if (saved && saved.apiKey) {
    apiKey.value = saved.apiKey;
    if (saved.apiBase) apiBase.value = saved.apiBase;
    remember.checked = true;
    remembered.hidden = false;
  }

  document.getElementById('forget').addEventListener('click', function () {
    write(null);
    apiKey.value = '';
    remember.checked = false;
    remembered.hidden = true;
  });

  form.addEventListener('submit', function () {
    write(remember.checked ? { apiKey: apiKey.value.trim(), apiBase: apiBase.value.trim() } : null);
  });
})();
