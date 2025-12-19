<?php

declare(strict_types=1);

namespace Domain\AI\Recognise\Enums;

/**
 * Enum for status of recognise files
 *
 * Statuses that define the user: canceled, saved
 * saved - only for success status
 *
 * Statuses that define the system: processed, failed, success
 */
enum RecogniseStatus: string
{
    case CANCELED = 'canceled';

    case PROCESSED = 'processed';

    case FAILED = 'failed';

    case SUCCESS = 'success';

    case SAVED = 'saved';
}
