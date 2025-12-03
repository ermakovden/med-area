<?php

declare(strict_types=1);

namespace Application\Analys\Services;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Application\Analys\Services\Contracts\UserAnalysServiceContract;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;
use Shared\Exceptions\ServerErrorException;

class UserAnalysService implements UserAnalysServiceContract
{
    public function __construct(
        protected readonly UserAnalysRepositoryContract $userAnalysRepository,
    ) {}

    /**
     * Add new analysis for users
     *
     * @param CreateUserAnalysisRequestDTO $dto
     * @return array<UserAnalysDTO>
     *
     * @throws ServerErrorException
     */
    public function createUserAnalysis(CreateUserAnalysisRequestDTO $dto): array
    {

        DB::beginTransaction();

        try {
            $createdRecords = [];

            foreach ($dto->analysis as $userAnalys) {
                $createdRecords[] = UserAnalysDTO::from(
                    $this->userAnalysRepository->create($userAnalys)
                );
            }

            DB::commit();

            return $createdRecords;

        } catch (\Throwable $e) {
            DB::rollback();

            \Log::critical('Failed to save to DB user analys.', [
                'class' => UserAnalysService::class,
                'method' => 'createUserAnalysis',
                'message' => $e->getMessage(),
            ]);

            throw new ServerErrorException();
        }
    }

    /**
     * Get UserAnalys Models by filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return Collection<array-key, UserAnalys>
     *
     * @throws ServerErrorException
     */
    public function getUserAnalysis(FilterUserAnalysDTO $filters): Collection
    {
        try {
            return $this->userAnalysRepository->getMany($filters);
        } catch (\Throwable $e) {
            \Log::critical('Failed to get user analysis from Db.', [
                'class' => UserAnalysService::class,
                'method' => 'getUserAnalysis',
                'message' => $e->getMessage(),
            ]);

            throw new ServerErrorException();
        }
    }

    /**
     * Delete UserAnalys Models by filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return void
     *
     * @throws ServerErrorException
     */
    public function deleteUserAnalysis(FilterUserAnalysDTO $filters): void
    {
        try {
            $this->userAnalysRepository->deleteMany($filters);
        } catch (\Throwable $e) {
            \Log::critical('Failed to delete user analysis from DB.', [
                'class' => UserAnalysService::class,
                'method' => 'deleteUserAnalysis',
                'message' => $e->getMessage(),
            ]);

            throw new ServerErrorException();
        }
    }
}
