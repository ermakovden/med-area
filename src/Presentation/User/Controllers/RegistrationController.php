<?php

declare(strict_types=1);

namespace Presentation\User\Controllers;

use Application\User\Services\Contracts\RegistrationServiceContract;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use Presentation\User\Requests\RegisterUserRequest;
use Presentation\User\Resources\UserResource;
use OpenApi\Attributes as OA;

/**
 * Controller responsible for registration users
 */
class RegistrationController extends BaseController
{
    public function __construct(
        protected readonly RegistrationServiceContract $registrationService,
    ) {}

    #[OA\Post(
        path: '/api/register',
        operationId: 'apiUsersRegister',
        description: 'Registration of new user.',
        tags: ['user', 'auth', 'api'],
        requestBody: new OA\RequestBody(ref: RegisterUserRequest::class),
    )]
    #[OA\Response(
        response: 200,
        description: 'User created.',
        content: new OA\JsonContent(ref: UserResource::class)
    )]
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $dto = $request->getDTO();

        $user = $this->registrationService->register($dto);

        return Response::json(new UserResource($user));
    }

    #[OA\Get(
        path: '/email/verify/{id}/{hash}',
        operationId: 'apiUsersEmailVerify',
        description: 'Verify email of user.',
        tags: ['user', 'verification', 'auth', 'api'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'id verification', required: true),
            new OA\Parameter(name: 'hash', in: 'path', description: 'hash verification', required: true),
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Email verified.',
    )]
    #[OA\Response(
        response: 401,
        description: 'Anauthorized.',
    )]
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return Response::json(['message' => 'Email verified successfully.']);
    }
}
