<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class LicenceController extends ResourceController
{
    #[Route('/admin/create/licence', name: 'app_create_licence')]
    public function create(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $form = $this->resourceFormFactory->create($configuration, $newResource);
        $form->handleRequest($request);
        // dd($form->get('startedAt')->getData());
        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            // dd($form->get('startedAt')->getData());
            $newResource = $form->getData();
            

        }

        return $this->render('bundles/Mediproductions/Admin/Licence/create.html.twig', [
            'configuration' => $configuration,
            'resource' => $newResource,
            'metadata' => $this->metadata,
            'form' => $form->createView(),
        ]);
    }
}
