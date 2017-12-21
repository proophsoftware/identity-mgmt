<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http;

use Interop\Http\Server\RequestHandlerInterface;
use Prooph\EventMachine\EventMachine;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

final class VerificationHandler implements RequestHandlerInterface
{
    /**
     * @var EventMachine
     */
    private $eventMachine;

    public function __construct(EventMachine $eventMachine)
    {
        $this->eventMachine = $eventMachine;
    }

    /**
     * @inheritdoc
     */
    public function handle(ServerRequestInterface $request)
    {
        //@TODO: Redirect to a verification ok page defined by user type schema
        if(!$verificationId = $request->getAttribute('verification', false)) {
            throw new \InvalidArgumentException("Missing verification id");
        }

        //@TODO: Use a finder to check if verification id is valid and linked with an IdentityId, prepare command and dispatch
        return new JsonResponse(['verification' => $request->getAttribute('verification')]);
    }
}