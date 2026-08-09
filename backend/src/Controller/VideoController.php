<?php

declare(strict_types=1);

namespace Movie\Controller;

use Movie\Http\JsonResponse;
use Movie\Http\Request;
use Movie\Service\VideoCatalog;

final class VideoController
{
    public function __construct(private readonly VideoCatalog $catalog)
    {
    }

    public function index(): never
    {
        JsonResponse::send(['items' => $this->catalog->all()]);
    }

    public function show(Request $request, array $params): never
    {
        JsonResponse::send($this->catalog->find($params['id']));
    }
}
