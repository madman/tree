<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tree\TreeService;
use Tree\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;

class ParentsController {

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

            try {
                $parents = $this->service->parents($node);

                return new JsonResponse(['parents' => $parents]);
            } catch (NotFoundException $e) {                
                return new JsonResponse([
                    'errors' => [
                        sprintf('Parents of node with id "%s" not found', $id)
                    ]
                ]);
            }    

        } catch (NotFoundException $e) {
            return new JsonResponse([
                'errors' => [
                    sprintf('Target node with id "%s" not found', $id)
                ]
            ]);
        }
    }
}
