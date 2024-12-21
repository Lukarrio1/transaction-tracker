<?php

namespace App;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Cache;

trait SendEmailTrait
{
    public function sendEmail($to, $subject, $body)
    {

        // Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $settings = collect(Cache::get('settings'));

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output
            $mail->isSMTP(); // Send using SMTP
            $mail->Host = $settings->where('key', 'mail_host')->first()->properties ?? 'fallback_host'; // Set the SMTP server to send through
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = $settings->where('key', 'mail_username')->first()->properties ?? null; // SMTP username
            $mail->Password = $settings->where('key', 'mail_password')->first()->properties ?? null; // SMTP password
            $mail->SMTPSecure = $settings->where('key', 'mail_encryption')->first()->properties ?? PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
            $mail->Port = $settings->where('key', 'mail_port')->first()->properties ?? 2525; // TCP port to connect to

            // Recipients
            $mail->setFrom($settings->where('key', 'mail_from_address')->first()->properties, $settings->where('key', 'mail_from_name')->first()->properties);
            $mail->addAddress($to); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
