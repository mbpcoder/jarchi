<?php
declare(strict_types=1);

namespace App\NotificationChannels\Drivers;

use App\NotificationChannels\DTOs\MessageDTO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailDriver extends Base
{
    private PHPMailer $mailer;

    public function __construct(array $config)
    {
        parent::__construct();
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = $config['host'] ?? 'smtp.example.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $config['username'] ?? '';
        $this->mailer->Password = $config['password'] ?? '';
        $this->mailer->SMTPSecure = $config['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $config['port'] ?? 587;
    }

    public function sendMessage(MessageDTO $dto): bool
    {
        try {
            $this->mailer->setFrom('no-reply@example.com', 'Jarchi Alerts');
            $this->mailer->addAddress($dto->chatId); // chatId is used as email address
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Jarchi Alert';
            $this->mailer->Body = $dto->text;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            throw new \RuntimeException('Email could not be sent. Mailer Error: ' . $this->mailer->ErrorInfo);
        }
    }
}
