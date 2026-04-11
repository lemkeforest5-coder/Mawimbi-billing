
<!DOCTYPE html>
<html>
<head>
  <title>Mawimbi Hotspot + Voucher</title>
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="expires" content="-1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <h1>Mawimbi Wi‑Fi Hotspot</h1>

  <p><strong>Step 1:</strong> Choose a package and pay with M‑Pesa, or use an existing voucher.</p>
  <p><strong>Step 2:</strong> We automatically connect you to the internet after payment.</p>

  <p id="clientInfo" style="font-size: 0.9em; color: #555;"></p>

  <!-- MikroTik hidden CHAP/PAP form (required) -->
  $(if chap-id)
  <form name="sendin" action="$(link-login-only)" method="post" style="display:none;">
    <input type="hidden" name="username" />
    <input type="hidden" name="password" />
    <input type="hidden" name="dst" value="$(link-orig)" />
    <input type="hidden" name="popup" value="true" />
  </form>
  <script type="text/javascript" src="md5.js"></script>
  <script type="text/javascript">
    function doLogin() {
      document.sendin.username.value = document.login.username.value;
      document.sendin.password.value =
        hexMD5('$(chap-id)' + document.login.password.value + '$(chap-challenge)');
      document.sendin.submit();
      return false;
    }
  </script>
  $(else)
  <form name="sendin" action="$(link-login-only)" method="post" style="display:none;">
    <input type="hidden" name="username" />
    <input type="hidden" name="password" />
    <input type="hidden" name="dst" value="$(link-orig)" />
    <input type="hidden" name="popup" value="true" />
  </form>
  <script type="text/javascript">
    function doLogin() {
      document.sendin.username.value = document.login.username.value;
      document.sendin.password.value = document.login.password.value;
      document.sendin.submit();
      return false;
    }
  </script>
  $(endif)

  <!-- Visible manual login, uses MikroTik login form "login" -->
  <h2>Manual Login</h2>
  <form name="login" method="post" action="$(link-login-only)" onsubmit="return doLogin();">
    <input type="hidden" name="dst" value="$(link-orig)" />
    <input type="hidden" name="popup" value="true" />
    <label>Username:
      <input type="text" name="username" value="$(username)" />
    </label><br><br>
    <label>Password:
      <input type="password" name="password" />
    </label><br><br>
    <button type="submit">Login</button>
  </form>

  <hr>

  <!-- Voucher Login -->
  <h2>Voucher Login</h2>
  <p>Already bought a voucher? Enter the code (+ optional phone), we verify it and auto‑connect you.</p>

  <label>Voucher code:
    <input type="text" id="voucherCode">
  </label><br><br>

  <label>Phone number (optional):
    <input type="text" id="voucherPhone">
  </label><br><br>

  <button id="voucherButton" type="button">Connect with voucher</button>

  <p id="status"></p>

  <hr>

  <!-- Buy via M-Pesa (STK) -->
  <h2>Buy voucher via M‑Pesa</h2>
  <p>Select a package, enter your Safaricom number, and you will receive an STK push.
     After payment, your voucher code will appear below and be filled into Voucher Login.</p>

  <label>Phone number (2547...):
    <input type="text" id="mpesaPhone">
  </label><br><br>

  <div class="packages">
    <button type="button" class="mpesa-package" data-amount="10" data-plan-key="kumi">
      KUMI – 40 minutes – Ksh 10
    </button>
    <button type="button" class="mpesa-package" data-amount="20" data-plan-key="mbao">
      MBAO – 2 hours – Ksh 20
    </button>
    <button type="button" class="mpesa-package" data-amount="80" data-plan-key="daily">
      DAILY – 24 hours – Ksh 80
    </button>
    <button type="button" class="mpesa-package" data-amount="280" data-plan-key="weekly-solo">
      WEEKLY SOLO – 7 days – Ksh 280
    </button>
    <button type="button" class="mpesa-package" data-amount="720" data-plan-key="monthly-solo">
      MONTHLY SOLO – 30 days – Ksh 720
    </button>
    <button type="button" class="mpesa-package" data-amount="4200" data-plan-key="qtrly-family-x4">
      QTRLY FAMILY x4 – 90 days – Ksh 4200
    </button>
  </div>

  <p id="mpesaStatus"></p>

  <script>
  (function() {
    function qs(name) {
      const url = new URL(window.location.href);
      return url.searchParams.get(name) || '';
    }

    var mac = qs('mac');
    var ip  = qs('ip');

    var clientInfo = document.getElementById('clientInfo');
    if (clientInfo) {
      clientInfo.textContent = 'Device: MAC ' + mac + '  IP ' + ip;
    }

    // --- Voucher login ---

    var statusEl = document.getElementById('status');
    function show(msg) {
      if (statusEl) statusEl.textContent = msg;
    }

    var btn = document.getElementById('voucherButton');
    if (!btn) {
      console.error('voucherButton not found');
      return;
    }

    btn.addEventListener('click', function() {
      var code  = (document.getElementById('voucherCode').value || '').trim().toUpperCase();
      var phone = (document.getElementById('voucherPhone').value || '').trim();

      console.log('voucher click: code=', code, 'phone=', phone);

      if (!code) { show('Enter voucher code.'); return; }

      show('Checking voucher…');

      // Call Mawimbi Billing API (force JSON) via LAN IP
      fetch('http://192.168.1.73/api/mb/v1/voucher/login', {
        method: 'POST',
        mode: 'cors',
        credentials: 'omit',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          voucher_code: code,
          phone: phone || null,
          mac_address: mac,
          ip_address: ip,
          router_id: 1
        })
      })
      .then(function(r) {
        console.log('voucher HTTP status:', r.status, r.url, r.headers.get('content-type'));
        return r.text();               // debug: get raw body first
      })
      .then(function(text) {
        console.log('voucher raw response:', text.substring(0, 400));

        var trimmed = text.trim();
        if (!trimmed.startsWith('{') && !trimmed.startsWith('[')) {
          show('Server returned HTML instead of JSON. Please try again later.');
          return;
        }

        var data = JSON.parse(trimmed);
        console.log('voucher parsed JSON:', data);

        if (!data.ok) {
          if (data.reason === 'expired') {
            show('This voucher has expired. Please purchase a new voucher.');
          } else if (data.reason === 'used') {
            show('This voucher has already been used. Please purchase a new voucher.');
          } else if (data.reason === 'not_found') {
            show('Voucher not found. Check the code and try again.');
          } else {
            show('Voucher error: ' + (data.message || data.reason || 'unknown'));
          }
          return;
        }

        show('Voucher accepted, logging in…');

        var loginForm = document.forms['login'];
        if (!loginForm) {
          show('Missing login form.');
          return;
        }

        loginForm.username.value = data.mk_user || code;
        loginForm.password.value = data.mk_pass || code;

        console.log('logging in with:', loginForm.username.value, loginForm.password.value);
        doLogin();
      })
      .catch(function(err) {
        console.error('voucher fetch error:', err);
        show('Network error redeeming voucher. Please try again.');
      });
    });

    // --- M-Pesa packages (STK) ---

    var mpesaStatus = document.getElementById('mpesaStatus');
    function showMpesa(msg) {
      if (mpesaStatus) mpesaStatus.textContent = msg;
    }

    var pkgButtons = document.querySelectorAll('.mpesa-package');
    pkgButtons.forEach(function(btn) {
      btn.addEventListener('click', function() {
        var phoneInput = document.getElementById('mpesaPhone');
        var phone = (phoneInput && phoneInput.value || '').trim();
        var amount = parseInt(btn.getAttribute('data-amount'), 10);
        var planKey = btn.getAttribute('data-plan-key');

        if (!phone) {
          showMpesa('Enter phone number (2547...).');
          return;
        }

        showMpesa('Sending STK push...');

        fetch('http://192.168.1.73/api/mb/v1/pay/mpesa', {
          method: 'POST',
          mode: 'cors',
          credentials: 'omit',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            phone: phone,
            amount: amount,
            plan_key: planKey,
            mac_address: mac,
            ip_address: ip,
            router_id: 1
          })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          console.log('mpesa init response:', data);
          if (data.ok) {
            showMpesa('STK push sent. Enter your M‑Pesa PIN on your phone.');

            var paymentId = data.payment_id;

            // Start polling for voucher linked to this payment
            var poll = setInterval(function() {
              fetch('http://192.168.1.73/api/mb/v1/payment/' + paymentId + '/voucher', {
                method: 'GET',
                mode: 'cors',
                credentials: 'omit',
                headers: {
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                }
              })
              .then(function(r) { return r.json(); })
              .then(function(v) {
                console.log('voucher poll response:', v);
                if (!v.ok) {
                  // Still not ready; do nothing yet
                  return;
                }

                clearInterval(poll);

                // Show voucher code to user
                showMpesa(
                  'Payment successful. Your voucher code is: ' + v.code +
                  '. It has ' + Math.round((v.time_seconds || 0) / 60) +
                  ' minutes. Use it above in Voucher Login.'
                );

                // Auto-fill voucher field so they can just click "Connect with voucher"
                var vc = document.getElementById('voucherCode');
                if (vc) {
                  vc.value = v.code;
                }
              })
              .catch(function(err) {
                console.error('voucher poll error:', err);
                // keep polling silently
              });
            }, 5000); // every 5 seconds

          } else {
            showMpesa('Error: ' + (data.message || 'could not send STK'));
          }
        })
        .catch(function(err) {
          console.error('mpesa init error:', err);
          showMpesa('Network error initiating STK. Please try again.');
        });
      });
    });
  })();
  </script>

  <hr>
  <p style="font-size: 0.9em;">
    Need help? WhatsApp <strong>07xx xxx xxx</strong> (Mawimbi support).
  </p>
</body>
</html>
