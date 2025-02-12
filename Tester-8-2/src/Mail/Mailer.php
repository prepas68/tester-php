<?php
declare(strict_types=1);

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer {
    private PHPMailer $mailer;
    private array $config;
    
    public function __construct(array $config) {
        $this->config = $config;
        $this->mailer = new PHPMailer(true);
        
        $this->setupMailer();
    }
    
    private function setupMailer(): void {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['smtp_host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->config['smtp_username'];
        $this->mailer->Password = $this->config['smtp_password'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $this->config['smtp_port'];
        
        $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
        $this->mailer->isHTML(true);
    }
    
    public function sendPasswordReset(string $to, string $resetToken, string $username): bool {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->Subject = 'Reset Your Password - WebTester';
            
            $resetLink = $this->config['app_url'] . '/resetPassword.php?token=' . urlencode($resetToken);
            
            // HTML verzia emailu
            $this->mailer->Body = $this->getPasswordResetHtmlTemplate($username, $resetLink);
            // Textová verzia emailu
            $this->mailer->AltBody = $this->getPasswordResetTextTemplate($username, $resetLink);
            
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function getPasswordResetHtmlTemplate(string $username, string $resetLink): string {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { 
                    background-color: #007bff; 
                    color: white; 
                    padding: 10px 20px; 
                    text-decoration: none; 
                    border-radius: 5px; 
                    display: inline-block; 
                }
                .footer { margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Password Reset Request</h2>
                <p>Hello {$username},</p>
                <p>We received a request to reset your password for your WebTester account.</p>
                <p>To reset your password, click on the button below:</p>
                <p><a href="{$resetLink}" class="button">Reset Password</a></p>
                <p>If you didn't request this password reset, you can safely ignore this email.</p>
                <p>The password reset link will expire in 24 hours.</p>
                <div class="footer">
                    <p>This is an automated message, please do not reply.</p>
                    <p>WebTester &copy; 2025</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
    
    private function getPasswordResetTextTemplate(string $username, string $resetLink): string {
        return <<<TEXT
        Password Reset Request
        
        Hello {$username},
        
        We received a request to reset your password for your WebTester account.
        
        To reset your password, copy and paste the following link into your browser:
        {$resetLink}
        
        If you didn't request this password reset, you can safely ignore this email.
        
        The password reset link will expire in 24 hours.
        
        This is an automated message, please do not reply.
        WebTester © 2025
        TEXT;
    }
}