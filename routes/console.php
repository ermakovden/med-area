<?php

declare(strict_types=1);

use Application\S3\Commands\ForceDeleteFilesCommand;
use Illuminate\Support\Facades\Schedule;

// Commands
Schedule::command(ForceDeleteFilesCommand::class)->everySixHours();
