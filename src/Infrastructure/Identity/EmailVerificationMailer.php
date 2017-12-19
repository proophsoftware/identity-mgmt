<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\Identity;

use App\Api\MsgDesc;
use function App\Infrastructure\assert_allowed_message;
use App\Model\Identity\Verification;
use Prooph\Common\Messaging\Message;

final class EmailVerificationMailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $fromName;

    /**
     * @var string
     */
    private $baseUrl;

    private $allowedMessages = [MsgDesc::EVT_IDENTITY_ADDED];

    public function __construct(\Swift_Mailer $mailer, string $from, string $fromName, string $baseUrl)
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->fromName = $fromName;
        $this->baseUrl = $baseUrl;
    }

    public function __invoke(Message $message)
    {
        assert_allowed_message($message, $this->allowedMessages);

        $verification = Verification::fromArray($message->payload()[MsgDesc::KEY_VERIFICATION]);

        $mail = new \Swift_Message();

        $mail->setFrom($this->from, $this->fromName);

        $link = $this->baseUrl . '/' . $verification->verificationId()->toString();

        $mail->setBody("Hi,\n
            \nplease click the following link to verify your email:
            \n{$link}
            \n
            \nRegards,
            \nprooph software Team");

        //@TODO: Use configurable mail templates and subjects from user type schema and pass user data in
        $html = "<html>
                <body>
                    <p>Hi,</p>
                    <p>please click the following link to verify your email:</p>
                    <p><a href='$link'>$link</a></p>
                    <p></p>
                    <p>Regards,</p>
                    <p>prooph software Team</p>
                </body>
            </html>";

        $mail->addPart($html, 'text/html');
        $mail->addTo($message->payload()[MsgDesc::KEY_EMAIL]);
        $mail->setSubject("Verify your email");

        $this->mailer->send($mail);
    }
}