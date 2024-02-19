<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class JwtResponseListener
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();

        // Identified user data collection
        $user = $event->getUser();


        // Serialize user data in JSON using the "identifiedUser" group
        $data['userInformation'] = json_decode($this->serializer->serialize($user, 'json', ["groups" => "identifiedUser"]));

        $event->setData($data);
    }
}
