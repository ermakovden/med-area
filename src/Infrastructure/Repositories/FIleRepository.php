<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Infrastructure\Repositories\Contracts\FileRepositoryContract;
use Shared\Repositories\BaseRepository;

class FileRepository extends BaseRepository implements FileRepositoryContract {}
