<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiVersionController {

	protected $version;

	public function __construct($version)
	{
		$this->version = $version;
	}

	public function __invoke()
	{
		return new JsonResponse(['version' => $this->version]);
	}
}
