<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tree\TreeService;
use Tree\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;

class FindByIdController {

    /**
     * @var TreeService
     */
    protected $service;

    public function __construct(TreeService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, $id)
    {
        try {
            $node = $this->service->findById(Uuid::fromString($id));

            return new JsonResponse(['node' => $node]);
        } catch (NotFoundException $e) {
            return new JsonResponse([
                'errors' => [
                    sprintf('Node with id "%s" not found', $id)
                ]
            ]);
        }
    }
}
