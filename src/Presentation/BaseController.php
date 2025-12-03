<?php

declare(strict_types=1);

namespace Presentation;

use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(title: 'MedArea RESTful API', version: '1.0.4')]
abstract class BaseController extends Controller
{
    //
}
