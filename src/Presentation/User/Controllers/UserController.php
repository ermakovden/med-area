<?php

declare(strict_types=1);

namespace Presentation\User\Controllers;

use Application\User\Services\Contracts\RegistrationServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use Presentation\User\Requests\RegisterUserRequest;
use Presentation\User\Resources\UserResource;

class UserController extends BaseController
{
    public function __construct(
        protected readonly RegistrationServiceContract $registrationService,
    ) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $dto = $request->getDTO();

        $user = $this->registrationService->register($dto);

        return Response::json(new UserResource($user));
    }
}
