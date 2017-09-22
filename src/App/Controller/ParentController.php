<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tree\TreeService;
use Tree\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;

class ParentController {

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
                $parent = $this->service->parent($node);

                return new JsonResponse(['parent' => $parent]);
            } catch (NotFoundException $e) {                
                return new JsonResponse([
                    'errors' => [
                        sprintf('Parent of node with id "%s" not found', $id)
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
