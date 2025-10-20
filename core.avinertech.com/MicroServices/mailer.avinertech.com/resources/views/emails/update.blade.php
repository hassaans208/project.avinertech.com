<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We received your message</title>
    <style>
        @media (prefers-color-scheme: dark) {
            body { background: #0b0f14 !important; color: #e6f1ff !important; }
        }
        body { margin:0; padding:0; background:#0f172a; color:#0b1220; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial; }
        .wrap { width:100%; background: linear-gradient(135deg, #0f172a 0%, #020617 100%); padding:24px; }
        .box { max-width:640px; margin:0 auto; background: rgba(2,6,23,.75); border:1px solid rgba(148,163,184,.15); border-radius:16px; overflow:hidden; backdrop-filter: blur(6px); }
        .top { padding:28px; background: radial-gradient(1200px 300px at 10% -50%, rgba(34,197,94,.22), transparent 60%), radial-gradient(1200px 300px at 90% -50%, rgba(20,184,166,.22), transparent 60%), rgba(2,6,23,.6); }
        .brand { font-weight:800; letter-spacing:.5px; color:#e2e8f0; font-size:18px; }
        .title { padding:22px 28px 0; color:#f0f9ff; font-size:24px; font-weight:700; }
        .content { padding:12px 28px 24px; color:#dbe7ff; font-size:15px; line-height:1.7; }
        .tag { display:inline-block; margin:16px 28px; padding:8px 12px; background: linear-gradient(90deg, rgba(34,197,94,.2), rgba(20,184,166,.2)); color:#bbf7d0; border:1px solid rgba(148,163,184,.25); border-radius:999px; font-size:11px; letter-spacing:.3px; }
        .footer { padding:18px 28px 28px; color:#7387a7; font-size:12px; border-top:1px solid rgba(148,163,184,.12); }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    </style>
</head>
<body>
  <div class="wrap">
    <div class="box">
      <div class="top">
        <div class="brand">AVINERTECH</div>
      </div>
      <div class="title">Your message is on its way 🚀</div>
      <div class="content">
        Hi {{ $toName ?: 'there' }},<br><br>
        We’ve successfully delivered your message regarding <span class="mono">{{ $originalSubject }}</span> to our sales team.
        Our specialists will review and get back to you shortly.
      </div>
      <div class="tag">Delivery Confirmation</div>
      <div class="footer">If you didn’t initiate this, please reply to this email and our team will assist you.</div>
    </div>
  </div>
</body>
</html>


