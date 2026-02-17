<?php

declare(strict_types=1);

namespace Domain\File\Enums;

enum FileType: string
{
    case PNG = 'png';

    case PDF = 'pdf';

    case JPG = 'jpg';

    case JPEG = 'jpeg';

    case XLS = 'xls';

    case XLSX = 'xlsx';
}
