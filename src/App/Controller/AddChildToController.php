<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tree\TreeService;
use Tree\Node;
use Tree\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;
use App\Type\NodeDataType;
use App\Event\NewNodeEvent;

class AddChildToController {

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
            $target = $this->service->findById(Uuid::fromString($id));

            $event = new NewNodeEvent();

            $form = $this->form_factory
                ->createBuilder(NodeDataType::class, $event)
                ->getForm();

            $form->submit($request->request->all());

            if ($form->isValid()) {
                $event = $form->getData();

                $new = Node::create($event->name, $event->title, $event->content);

                $node = $this->service->addChildTo($target, $new);

                return new JsonResponse(['new' => $node]);
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

        } catch (NotFoundException $e) {
            return new JsonResponse([
                'errors' => [
                  sprintf('Target node with id "%s" not found', $id)
                ]
            ]);
        }        
    }
}
