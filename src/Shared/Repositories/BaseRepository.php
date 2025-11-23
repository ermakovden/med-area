<?php

declare(strict_types=1);

namespace Shared\Repositories;

use Illuminate\Database\Eloquent\Model;
use Shared\DTO\BaseDTO;

abstract class BaseRepository
{
    /**
     * @var class-string<Model>
     */
    protected string $model;

    public function create(BaseDTO $userDTO): Model
    {
        return $this->model::create($userDTO->toArray());
    }
}
