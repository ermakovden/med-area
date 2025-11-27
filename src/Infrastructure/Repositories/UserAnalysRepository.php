<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;
use Shared\Exceptions\ServerErrorException;

class UserAnalysRepository implements UserAnalysRepositoryContract
{
    /**
     * @var class-string<UserAnalys>
     */
    protected $model = UserAnalys::class;

    /**
     * Create many models UserAnalys
     *
     * @param array<UserAnalysDTO> $data
     * @return array<UserAnalys>
     *
     * @throws ServerErrorException
     */
    public function createMany(array $data): array
    {
        DB::beginTransaction();

        try {
            $createdRecords = [];

            foreach ($data as $userAnalys) {
                $createdRecords[] = UserAnalys::query()->create($userAnalys->toArray());
            }

            DB::commit();

            return $createdRecords;

        } catch (\Throwable $e) {
            DB::rollback();

            \Log::critical('Failed to save to DB user analysis.', [
                'class' => UserAnalysRepositoryContract::class,
                'method' => 'createMany',
                'message' => $e->getMessage(),
            ]);

            throw new ServerErrorException();
        }
    }
}
