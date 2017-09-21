<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Tree\TreeService;

class GetRootController {

	/**
	 * @var TreeService
	 */
	protected $service;

	public function __construct(TreeService $service)
	{
		$this->service = $service;
	}

	public function __invoke()
	{
		$root = $this->service->getRoot();

		return new JsonResponse(['root' => $root]);
	}
}
