<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USER',     'tamiresmuller@gmail.com'); // seu Gmail
define('MAIL_PASS',     'COLE_SENHA_DE_APP_AQUI');  // senha de app Google
define('MAIL_FROM',     'tamiresmuller@gmail.com');
define('MAIL_FROM_NAME','Tamires Müller Veterinária');
define('SITE_URL',      'http://localhost:8000');

function criarMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    return $mail;
}

function templateEmail(string $titulo, string $corpo, string $email_destino, string $token): string {
    $url_desc = SITE_URL . '/descadastrar?token=' . urlencode($token) . '&email=' . urlencode($email_destino);
    $ano      = date('Y');
    return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$titulo}</title>
</head>
<body style="margin:0;padding:0;background:#F4F4F0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F4F0;padding:32px 16px;">
  <tr><td align="center">

    <!-- Container -->
    <table width="600" cellpadding="0" cellspacing="0"
           style="max-width:600px;width:100%;background:#fff;border-radius:12px;
                  overflow:hidden;border:1px solid #E5E5E0;">

      <!-- Header -->
      <tr>
        <td style="background:#1B4332;padding:28px 40px;text-align:center;">
          <p style="margin:0;font-size:11px;color:rgba(255,255,255,.6);
                    text-transform:uppercase;letter-spacing:.1em;">
            Tamires Müller Veterinária
          </p>
          <h1 style="margin:6px 0 0;font-size:22px;color:#fff;font-weight:700;">
            {$titulo}
          </h1>
        </td>
      </tr>

      <!-- Corpo -->
      <tr>
        <td style="padding:36px 40px;color:#3D3D35;font-size:16px;line-height:1.7;">
          {$corpo}
        </td>
      </tr>

      <!-- CTA -->
      <tr>
        <td style="padding:0 40px 36px;text-align:center;">
          <a href="{$url_desc}"
             style="display:inline-block;background:#1B4332;color:#fff;
                    text-decoration:none;padding:14px 32px;border-radius:999px;
                    font-size:15px;font-weight:600;">
            Ler no site →
          </a>
        </td>
      </tr>

      <!-- Divisor -->
      <tr>
        <td style="border-top:1px solid #E5E5E0;"></td>
      </tr>

      <!-- Footer LGPD-compliant -->
      <tr>
        <td style="padding:24px 40px;background:#F9F9F7;text-align:center;">
          <p style="margin:0 0 8px;font-size:12px;color:#888;line-height:1.6;">
            Você está recebendo este e-mail porque se cadastrou para receber<br>
            dicas de manejo e saúde animal em <strong>localhost:8000</strong>.
          </p>
          <p style="margin:0 0 12px;font-size:12px;color:#888;">
            São Martinho — Rio Grande do Sul, Brasil
          </p>
          <a href="{$url_desc}"
             style="font-size:12px;color:#888;text-decoration:underline;">
            Cancelar inscrição / Descadastrar
          </a>
          <p style="margin:12px 0 0;font-size:11px;color:#BBB;">
            © {$ano} Tamires Müller Serviços Veterinários · Todos os direitos reservados
          </p>
        </td>
      </tr>

    </table>
    <!-- /Container -->

  </td></tr>
</table>
</body>
</html>
HTML;
}