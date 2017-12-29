<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use App\Api\Event;
use App\Api\Payload;
use App\Model\VerificationSession\SessionExpiration;
use Prooph\Common\Messaging\Message;

final class VerificationSession
{
    public static function start(Message $startVerificationSession): \Generator {
        $payload = $startVerificationSession->payload();
        $payload[Payload::KEY_VERIFICATION_SESSION_EXPIRATION] = SessionExpiration::in60Minutes()->toString();
        yield [Event::VERIFICATION_SESSION_STARTED, $payload];
    }

    public static function whenVerificationSessionStarted(Message $verificationSessionStarted): VerificationSessionState {
        return VerificationSessionState::fromArray($verificationSessionStarted->payload());
    }
}
