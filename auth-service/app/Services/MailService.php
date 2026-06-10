<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class MailService
{
    private array $config;

    public function __construct()
    {
        $this->config = [];
        $envPath = __DIR__ . '/../../.env';
        if (file_exists($envPath)) {
            $parsed = @parse_ini_file($envPath);
            if (is_array($parsed)) {
                $this->config = $parsed;
            }
        }
    }

    private function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Envia el correo de verificación al usuario con PHPMailer.
     * @param object $user  Objeto User (propiedades: email, name)
     * @param string $token
     * @return bool
     */
    public function sendVerificationEmail($user, string $token): bool
    {
        $host = $this->get('MAIL_HOST', 'smtp.gmail.com');
        $port = (int) $this->get('MAIL_PORT', 587);
        $username = $this->get('MAIL_USERNAME', '');
        $password = $this->get('MAIL_PASSWORD', '');
        $encryption = $this->get('MAIL_ENCRYPTION', 'tls');
        $fromAddress = $this->get('MAIL_FROM_ADDRESS', $username ?: 'no-reply@nexuslib.local');
        $fromName = $this->get('MAIL_FROM_NAME', 'NexusLib');

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->SMTPSecure = $encryption;
            $mail->Port = $port;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($user->email, $user->name ?? '');

            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu cuenta en NexusLib';

            $link = 'http://localhost/nexuslib/frontend/public/index.php?view=auth/email_verification&token=' . urlencode($token);

            $html = <<<HTML
<html>
<body style="font-family:Arial,Helvetica,sans-serif;background:#0f172a;color:#cbd5e1;padding:20px;">
  <div style="max-width:600px;margin:0 auto;background:#0b1220;border-radius:8px;padding:24px;border:1px solid rgba(148,163,184,0.06)">
    <h2 style="color:#06b6d4;margin-bottom:8px">Bienvenido a NexusLib</h2>
    <p style="color:#cbd5e1;line-height:1.5">Gracias por registrarte. Para activar tu cuenta, haz clic en el siguiente botón:</p>
    <p style="text-align:center;margin:28px 0">
      <a href="{$link}" style="background:linear-gradient(90deg,#06b6d4,#3b82f6);color:#04263b;padding:12px 20px;border-radius:6px;text-decoration:none;font-weight:600">Verificar mi cuenta</a>
    </p>
    <p style="color:#94a3b8;font-size:13px">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
    <p style="color:#94a3b8;font-size:13px;word-break:break-all">{$link}</p>
    <hr style="border:none;border-top:1px solid rgba(148,163,184,0.06);margin:18px 0" />
    <p style="color:#94a3b8;font-size:12px">Si no te registraste en NexusLib, ignora este correo.</p>
  </div>
</body>
</html>
HTML;

            $mail->Body = $html;
            $mail->AltBody = 'Verifica tu cuenta en NexusLib: ' . $link;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Fail silently; caller can decide next steps. Logging could be added here.
            return false;
        }
    }
}
