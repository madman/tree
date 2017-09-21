<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Type\NodeDataType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tree\TreeService;
use Tree\Node;
use App\Event\CreateRootEvent;

class CreateRootController {

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

    public function __invoke(Request $request)
    {
        $event = new CreateRootEvent();

        $form = $this->form_factory
            ->createBuilder(NodeDataType::class, $event)
            ->getForm();
           
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $event = $form->getData();

            $root = Node::create($event->name, $event->title, $event->content);
            $this->service->create($root);

            return new JsonResponse(['root' => $root]);
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
