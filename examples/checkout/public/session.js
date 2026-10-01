(function () {
  var status = document.getElementById('status');
  var events = document.getElementById('events');
  if (!status) return;

  var open = status.dataset.open === '1';
  var timer = null;

  function render(state) {
    Object.keys(state).forEach(function (key) {
      var cell = status.querySelector('[data-key="' + key + '"]');
      if (!cell) return;
      var value = state[key] === null ? '' : String(state[key]);
      if (key === 'status') {
        cell.innerHTML = '';
        var badge = document.createElement('span');
        badge.className = 'badge';
        badge.textContent = value;
        cell.appendChild(badge);
      } else {
        cell.textContent = value;
      }
    });
    if (state.status !== 'open' && open) {
      open = false;
      window.clearInterval(timer);
      window.setTimeout(function () { window.location.reload(); }, 1500);
    }
  }

  function poll() {
    fetch(status.dataset.url, { headers: { Accept: 'application/json' } })
      .then(function (response) { return response.ok ? response.json() : null; })
      .then(function (state) { if (state) render(state); });
  }

  if (open) timer = window.setInterval(poll, 5000);

  ['ready', 'change', 'add', 'step', 'confirm', 'error'].forEach(function (type) {
    document.addEventListener('enlivy-portal:' + type, function (event) {
      if (events) {
        var item = document.createElement('li');
        item.textContent = new Date().toLocaleTimeString() + '  ' + type + '  ' + JSON.stringify(event.detail || {});
        events.prepend(item);
      }
      if (type === 'confirm') poll();
    });
  });
})();
