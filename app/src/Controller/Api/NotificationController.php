<?php

namespace App\Controller\Api;

use App\Annotation\Get;
use App\Annotation\Post;
use App\Service\NotificationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[Route(path: "/api/notifications", name: "notification_")]
class NotificationController extends AbstractController
{
    public function __construct(
        private readonly NotificationService $notificationsService
    ) {
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Get(path: '', name: 'index')]
    public function index(Request $request): Response
    {
        $notifications = $this->notificationsService->getList();

        return $this->json($notifications, Response::HTTP_OK, [], [
            'groups' => 'default',
        ]);
    }
//
//    /**
//     * @param NotificationReadRequest $request
//     * @return Response
//     */
//    #[Post(path: '/read-notifications', name: 'readMessage')]
//    public function readMessage(NotificationReadRequest $request): Response
//    {
//        $this->notificationsService->readNotifications($request->toArray()['ids']);
//
//        return $this->json(null, Response::HTTP_NO_CONTENT);
//    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Get(path: '/settings', name: 'settings')]
    public function getSettings(Request $request): Response
    {
        $settings = $this->notificationsService->getSettings();

        return $this->json($settings, Response::HTTP_OK);
    }
}
