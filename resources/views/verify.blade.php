<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Email Verification</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body {
      background: #ff0b55;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      width: 360px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #cf0f47;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      margin-top: 14px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Email Verification</h2>
    <p>We’ve sent a 6-digit verification code to your email. Please enter it below.</p>

    <input type="text" id="code" placeholder="Enter code" />
    <button id="verifyBtn">Verify</button>

    <p id="message" style="margin-top:10px; font-size:13px;"></p>
  </div>

  <script>
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const btn = document.getElementById('verifyBtn');

  btn.addEventListener('click', async () => {
    const code = document.getElementById('code').value.trim();
    if (!code) return alert('Please enter the code.');

    btn.disabled = true;
    btn.textContent = 'Verifying...';

    try {
      const res = await fetch('{{ route("register.verifyCode") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ code })
      });

      const data = await res.json();

      if (data.success) {
        alert(data.message);
        // Redirect to timeline (or home)
        window.location.href = "{{ route('timeline') }}";
      } else {
        alert(data.message);
        btn.disabled = false;
        btn.textContent = 'Verify';
      }
    } catch (err) {
      alert('Error verifying code.');
      btn.disabled = false;
      btn.textContent = 'Verify';
    }
  });
</script>
</body>
</html>
