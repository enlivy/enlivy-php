<h1>Connect</h1>
<p class="lead">
    Sell a billing package on your own page: pick a package and a customer, see the price, open a
    checkout session and take the payment with the checkout embed. Everything runs through
    <code>enlivy/enlivy-php</code>.
</p>

<form method="post" class="card narrow" id="connect">
    <?= csrf_field() ?>
    <label>
        API key
        <input type="password" name="api_key" required autocomplete="off" placeholder="1|…">
    </label>
    <label>
        API base URL
        <input type="url" name="api_base" value="<?= h($apiBase) ?>">
    </label>
    <label class="check">
        <input type="checkbox" id="remember"> Remember the key in this browser
    </label>
    <p class="hint" id="remembered" hidden>
        Filled in from this browser. <button type="button" class="link" id="forget">Forget it</button>
    </p>
    <p class="hint">
        The key stays in this server's PHP session until you disconnect. Writes (opening, correcting
        and expiring sessions) run only against a sandbox organization.
    </p>
    <div class="actions">
        <button name="action" value="connect">Connect</button>
    </div>
</form>

<script src="/connect.js"></script>
