<?php

declare(strict_types=1);

namespace Application\Analys\Services;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Application\Analys\Services\Contracts\UserAnalysServiceContract;
use Domain\Analys\Enums\Analys;
use Domain\Analys\Models\UserAnalys;
use Domain\Analys\Repositories\UserAnalysRepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
                if ($userAnalys->isNotEmptyValue('analys_id') && $userAnalys->emptyValue('analys_name')) {
                    /** @var Analys $analysId */
                    $analysId = $userAnalys->analys_id;
                    logger()->debug('[UserAnalysService] computing analys_name from enum', [
                        'analys_id'   => $analysId->value,
                        'analys_name' => $analysId->name,
                    ]);
                    $userAnalys->analys_name = $analysId->name;
                }

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
