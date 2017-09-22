<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tree\TreeService;
use Tree\Exception\NotFoundException;

class FindByPathController {

    /**
     * @var TreeService
     */
    protected $service;

    public function __construct(TreeService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, $path)
    {
        try {
            $node = $this->service->findByPath($path);

            return new JsonResponse(['node' => $node]);
        } catch (NotFoundException $e) {
            return new JsonResponse([
                'errors' => [
                    sprintf('Nothing found by path "%s"', $path)
                ]
            ]);
        }
    }
}
