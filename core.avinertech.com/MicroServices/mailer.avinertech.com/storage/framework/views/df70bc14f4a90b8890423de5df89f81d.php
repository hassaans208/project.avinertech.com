<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($subjectLine); ?></title>
    <style>
        @media (prefers-color-scheme: dark) {
            body { background: #0b0f14 !important; color: #e6f1ff !important; }
        }
        body {
            margin: 0; padding: 0; background: #0f172a; color: #0b1220;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial;
        }
        .wrapper { width: 100%; background: linear-gradient(135deg, #0f172a 0%, #020617 100%); padding: 24px; }
        .container { max-width: 680px; margin: 0 auto; background: rgba(2, 6, 23, 0.75); border: 1px solid rgba(148,163,184,.15); border-radius: 16px; overflow: hidden; backdrop-filter: blur(6px); }
        .header { padding: 28px 28px 12px; background: radial-gradient(1200px 300px at 10% -50%, rgba(14,165,233,.25), transparent 60%), radial-gradient(1200px 300px at 90% -50%, rgba(99,102,241,.25), transparent 60%), rgba(2,6,23,.6); }
        .brand { font-weight: 800; letter-spacing: .5px; color: #e2e8f0; font-size: 18px; }
        .subtitle { color: #94a3b8; font-size: 12px; margin-top: 4px; }
        .hero { position: relative; padding: 32px 28px; background: linear-gradient(180deg, rgba(14,165,233,.15), rgba(99,102,241,.08), transparent); }
        .glow { position: absolute; inset: -40px; background: radial-gradient(600px 200px at 50% 0%, rgba(14,165,233,.25), transparent 60%); filter: blur(24px); opacity: .6; }
        h1 { margin: 0; color: #f8fafc; font-size: 28px; line-height: 1.2; }
        .meta { margin-top: 12px; color: #9fb1cc; font-size: 12px; }
        .card { margin: 20px 28px 28px; padding: 20px; border: 1px solid rgba(148,163,184,.15); border-radius: 14px; background: rgba(15,23,42,.6); }
        .label { color: #8ba3c7; font-size: 11px; letter-spacing: .4px; text-transform: uppercase; }
        .value { color: #e5edff; font-size: 14px; margin-top: 4px; }
        .content { margin: 16px 0 0; color: #dbe7ff; font-size: 15px; line-height: 1.7; }
        .footer { padding: 18px 28px 28px; color: #7387a7; font-size: 12px; border-top: 1px solid rgba(148,163,184,.12); }
        .pill {
            display: inline-block; padding: 8px 12px; border-radius: 999px; font-size: 11px; letter-spacing: .3px;
            background: linear-gradient(90deg, rgba(99,102,241,.2), rgba(14,165,233,.2)); color: #c7d2fe; border: 1px solid rgba(148,163,184,.25);
        }
        a { color: #7dd3fc; text-decoration: none; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    </style>
    <!--[if mso]>
      <style type="text/css">body, table, td, a {font-family: Arial, sans-serif !important;}</style>
    <![endif]-->
  </head>
  <body>
    <div class="wrapper">
      <div class="container">
        <div class="header">
          <div class="brand">AVINERTECH</div>
          <div class="subtitle">New customer message</div>
        </div>
        <div class="hero">
          <div class="glow"></div>
          <h1><?php echo e($subjectLine); ?></h1>
          <div class="meta">From <span class="mono"><?php echo e($fromName ?: $fromEmail); ?></span> to <span class="mono"><?php echo e($toName ?: $toEmail); ?></span></div>
          <div class="pill" style="margin-top:12px;">Customer Inquiry</div>
        </div>

        <div class="card">
          <div class="label">Message</div>
          <div class="content"><?php echo nl2br(e($messageContent)); ?></div>

          <div style="margin-top:16px; display:grid; gap:12px; grid-template-columns:repeat(2,minmax(0,1fr));">
            <div>
              <div class="label">From</div>
              <div class="value"><?php echo e($fromName ?: '—'); ?> &lt;<?php echo e($fromEmail); ?>&gt;</div>
            </div>
            <div>
              <div class="label">To</div>
              <div class="value"><?php echo e($toName ?: '—'); ?> &lt;<?php echo e($toEmail); ?>&gt;</div>
            </div>
          </div>
        </div>

        <div class="footer">
          You’re receiving this because your address is configured as <span class="mono">sales@avinertech.com</span> for inbound inquiries.
          <br>Reply directly to reach the sender.
        </div>
      </div>
    </div>
  </body>
</html>


<?php /**PATH D:\project.avinertech.com\core.avinertech.com\MicroServices\mailer.avinertech.com\resources\views\emails\sales.blade.php ENDPATH**/ ?>