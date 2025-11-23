<?php

declare(strict_types=1);

namespace Presentation\User\Controllers;

use Application\User\Services\Contracts\RegistrationServiceContract;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use Presentation\User\Requests\RegisterUserRequest;
use Presentation\User\Resources\UserResource;
use OpenApi\Attributes as OA;
use Presentation\User\Requests\SendEmailVerificationRequest;

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
            new OA\Parameter(name: 'id', in: 'path', description: 'id verification', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'hash', in: 'path', description: 'hash verification', required: true, schema: new OA\Schema(type: 'string')),
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Email verified.',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized.',
    )]
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return Response::json(['message' => 'Email verified successfully.']);
    }

    #[OA\Get(
        path: '/email/verification-notification',
        operationId: 'apiUsersEmailVerificationNotification',
        description: 'Send email verification notification.',
        tags: ['user', 'verification', 'auth', 'api'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Email notification sended.',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized.',
    )]
    public function sendEmailVerification(Request $request): JsonResponse
    {
        $this->registrationService->sendEmailVerificationNotification($request->user());

        return Response::json(['message' => 'Check your email!.']);
    }
}
