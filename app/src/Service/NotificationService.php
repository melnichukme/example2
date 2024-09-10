<?php

namespace App\Service;


use App\Entity\Notification;
use App\Entity\NotificationRead;
use App\Entity\User;
use App\Enums\PusherChannelEnum;
use App\Repository\NotificationReadRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Pusher\PusherException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationService
{
    public function __construct(
        protected EntityManagerInterface     $manager,
        protected UserRepository             $userRepository,
        protected NotificationRepository     $notificationRepository,
        protected Security                   $security,
        protected NotificationReadRepository $notificationReadRepository,
        private readonly ParameterBagInterface $params,
        private readonly HubInterface $hub,
    )
    {
    }

    /**
     * @param string $type
     * @param string $message
     * @param int $userId
     * @param string $entityId
     * @return Notification
     * @throws JsonException
     */
    final public function create(
        string  $type,
        string  $message,
        int    $userId,
        string $entityId
    ): Notification
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        $notification = new Notification();
        $notification->setUser($user);
        $notification->setRole(null);
        $notification->setType($type);
        $notification->setEntityId($entityId);
        $notification->setPayload(['message' => $message]);
        $notification->setCreatedAt(new \DateTime());

        $this->manager->persist($notification);
        $this->manager->flush();


        $update = new Update(
            'notify',
            json_encode([
                'id' => $notification->getId(),
                'payload' => [
                    'message' => $message
                ],
                'createdAt' => (new \DateTime())->format('Y-m-d\TH:i:sP'),
                'user_id' => $userId
            ], JSON_THROW_ON_ERROR)
        );

        $this->hub->publish($update);

        return $notification;
    }

    /**
     * @return Notification[]
     */
    final public function getList(): array
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        $oldNotifications = $this->notificationRepository->getOldNotifications($user);
        $newNotifications = $this
            ->notificationRepository
            ->getNewNotifications($user, $this->getNotificationsIds($oldNotifications), $user->getRoles());

        return [
            'new_notifications' => $newNotifications,
            'old_notifications' => $oldNotifications,
            'is_new_message' => (bool)$newNotifications
        ];
    }

    /**
     * @param array $notifications
     * @return array
     */
    private function getNotificationsIds(array $notifications): array
    {
        $ids = [];

        foreach ($notifications as $notification) {
            assert($notification instanceof Notification);
            $ids[] = $notification->getId();
        }

        return $ids;
    }

    /**
     * @param array $ids
     * @return void
     */
    final public function readNotifications(array $ids): void
    {
        $user = $this->security->getUser();
        assert($user instanceof User);
        $notifications = $this->notificationRepository->getNotificationsByIds($ids, $user);

        foreach ($notifications as $notification) {
            assert($notification instanceof Notification);
            $notificationRead = new NotificationRead();
            $notificationRead->setNotification($notification);
            $notificationRead->setUser($user);
            $this->manager->persist($notificationRead);
        }

        $this->manager->flush();
    }

    /**
     * @return array
     */
    final public function getSettings(): array
    {
        $host = parse_url($this->params->get('mercure.default_hub'), PHP_URL_HOST);
        $port = parse_url($this->params->get('mercure.default_hub'), PHP_URL_PORT);
        $environment = $this->params->get('kernel.environment');

        $isProd = false;

        if ($environment === 'prod') {
            $isProd = true;
        }

        return ['url' => $host . ':' . $port, 'is_prod' => $isProd];
    }
}
