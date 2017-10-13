<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tree\TreeService;
use App\Event\DeleteNodeEvent;
use App\Type\NodeIdType;
use Tree\Node;
use Ramsey\Uuid\Uuid;

class DeleteNodeController {

    /**
     * @var TreeService
     */
    protected $service;
    protected $form_factory;

    public function __construct(TreeService $service, $form_factory)
    {
        $this->service = $service;
        $this->form_factory = $form_factory;
    }

    public function __invoke(Request $request, $id)
    {
        try {
            $node = $this->service->findById(Uuid::fromString($id));
        } catch (NotFoundException $e) {
            return new JsonResponse([
                'errors' => [
                    sprintf('Target node with id "%s" not found', $id)
                ]
            ], 404);
        }

        $event = new DeleteNodeEvent();

        $form = $this->form_factory
            ->createBuilder(NodeIdType::class, $event)
            ->getForm();
           
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $event = $form->getData();

            if (!$node->getId()->equals(Uuid::fromString($event->id))) {
                return new JsonResponse([
                    'errors' => [
                        sprintf('Target node id "%s" not equals to confirmation "%s"', $node->getId(), $event->id)
                    ],
                ], 400);
            }

            try {
                try {
                    $parent = $this->service->parent($node);
                } catch (NotFoundException $e) {
                    $parent = null;
                }    

                $this->service->delete($node);

                return new JsonResponse([
                    'deleted' => $node->getId(),
                    'parent'  => $parent,
                ]);
            } catch (\Doctrine\DBAL\DBALException $e) {

                return new JsonResponse([
                    'errors' => [
                        sprintf('Cannot delete node "%s"', $id)
                    ],
                ], 500);
            }

        } else {
            $errors = [];

            foreach ($form as $field) {
                $fieldErrors = $field->getErrors(true);

                if ($fieldErrors->count() > 0) {
                    foreach ($fieldErrors as $fieldError) {
                        $errors[$field->getName()][] = $fieldError->getMessage();
                    }
                }
            }

            return new JsonResponse([
                'errors' => $errors,
            ], 400);
        }
    }
}
