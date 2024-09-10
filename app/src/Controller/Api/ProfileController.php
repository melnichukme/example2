<?php

namespace App\Controller\Api;

use App\Annotation\Get;
use App\Annotation\Post;
use App\Annotation\Put;
use App\Request\ProfileRequest;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/api/me", name: "me_")]
class ProfileController extends AbstractController
{
    /**
     * @param UserService $userService
     */
    public function __construct(
        protected UserService $userService,
    ) {
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Get('', name: 'profile')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        return $this->json($user, Response::HTTP_OK, [], [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
            'groups' => 'default'
        ]);
    }

    /**
     * @param ProfileRequest $request
     * @return Response
     * @throws \Exception
     */
    #[Put('', name: 'update')]
    public function update(ProfileRequest $request): Response
    {
        $this->userService->update(
            $this->getUser()->getId(),
            $request->toArray()
        );

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Post('/photo', name: 'photo')]
    public function photo(Request $request): Response
    {
        $photoUrl = $this->userService->uploadPhoto(
            $request->files->get('file')
        );

        return $this->json(['photo_path' => $photoUrl], Response::HTTP_OK);
    }
}
