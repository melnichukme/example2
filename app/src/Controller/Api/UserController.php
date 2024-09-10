<?php

namespace App\Controller\Api;

use App\Annotation\Get;
use App\Annotation\Post;
use App\Annotation\Put;
use App\Dto\User\UserCreateDto;
use App\Entity\User;
use App\Filter\UserFilter;
use App\Request\User\UserCreateRequest;
use App\Request\User\UserIndexRequest;
use App\Request\User\UserUpdateRequest;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/api/users", name: "user_")]
class UserController extends AbstractController
{
    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(
        protected UserService $userService,
    ) {
    }

    /**
     * @param UserIndexRequest $request
     * @return Response
     */
    #[Get('', name: 'index')]
    public function index(UserIndexRequest $request): Response
    {
        $users = $this->userService->getList(
            new UserFilter($request)
        );

        return $this->json($users, Response::HTTP_OK, [], [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
//            'groups' => ['default', 'user_index']
        ]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Get('/{id}', name: 'show')]
    public function show(
        int $id,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $group = $request->get('group');

        try {
            $user = $this->userService->getById($id);

            return $this->json($user, Response::HTTP_OK, [], [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                'groups' => [$group == 'edit' ? 'user_edit' : 'user_detail']
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param UserCreateRequest $request
     * @return Response
     * @throws Exception
     */
    #[Post('', name: 'create')]
    public function create(
        UserCreateRequest $request
    ): Response {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $user = $this->userService->create(UserCreateDto::createFromRequest($request));

        return $this->json($user, Response::HTTP_CREATED, [], [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
            'groups' => 'default'
        ]);
    }

    /**
     * @param int $id
     * @param UserUpdateRequest $request
     * @return Response
     * @throws Exception
     */
    #[Put('/{id}', name: 'update')]
    public function update(
        int $id,
        UserUpdateRequest $request
    ): Response {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = $request->toArray();

        $this->userService->update($id, $data);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $id
     * @return Response
     * @throws Exception
     */
    #[Put('/{id}/active', name: 'active')]
    public function active(
        int $id
    ): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $this->userService->changeActive($id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
