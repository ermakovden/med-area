<?php

declare(strict_types=1);

namespace Application\S3\DTO;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Shared\DTO\BaseDTO;
use Shared\Enums\Storage;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;

class FileDTO extends BaseDTO
{
    public string|Optional $id;

    public string|Optional $user_id;

    public Storage|Optional $storage;

    public string|Optional $endpoint;

    public string|Optional $bucket;

    public string|Optional $key;

    public int|Optional $size;

    public UploadedFile|Optional $content;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional $created_at;

    #[WithCast(DateTimeInterfaceCast::class)]
    public string|Optional $updated_at;

    #[WithCast(DateTimeInterfaceCast::class)]
    public string|Optional $deleted_at;
}
