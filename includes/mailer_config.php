<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// [PENTING] Mode Pengembangan sekarang dinonaktifkan
define('DEVELOPMENT_MODE', false);

// [PERBAIKAN] Menambahkan tanda $ pada parameter $subject
function send_email($recipient_email, $subject, $body) {
    if (DEVELOPMENT_MODE) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['dev_email_body'] = $body;
        return true;
    }

    $mail = new PHPMailer(true);
    try {
        // Pengaturan Server SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sauvatte1@gmail.com'; // GANTI DENGAN ALAMAT GMAIL ANDA
        $mail->Password   = 'dwhz rdat rvba mgeo'; // GANTI DENGAN SANDI APLIKASI ANDA
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Pengirim dan Penerima
        $mail->setFrom('no-reply@sauvatte.com', 'Sauvatte');
        $mail->addAddress($recipient_email);

        // Konten Email
        $mail->isHTML(true);
        // [PERBAIKAN] Menambahkan tanda $ pada variabel $subject
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
