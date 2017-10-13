<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Event\UpdateNodeEvent;
use App\Type\UpdateNodeDataType;
use Tree\TreeService;
use Tree\Node;
use Ramsey\Uuid\Uuid;

class UpdateNodeController {

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
            ]);
        }

        $event = UpdateNodeEvent::fromNode($node);

        $form = $this->form_factory
            ->createBuilder(UpdateNodeDataType::class, $event)
            ->getForm();
           
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $event = $form->getData();

            $new = new Node($node->getId(), $event->name, $event->title, $event->content);

            try {
                $this->service->update($new);

                return new JsonResponse(['new' => $new]);
            } catch (\Exception $e) {

                return new JsonResponse([
                    'errors' => [
                        'Cannot update node'
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
            ]);
        }
    }
}
