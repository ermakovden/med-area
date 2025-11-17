<?php

declare(strict_types=1);

namespace Shared\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Shared\DTO\BaseDTO;

abstract class BaseRequest extends FormRequest
{
    abstract public function getDTO(): BaseDTO;
}
