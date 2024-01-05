<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\SubscriptionIsExtendedType;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class SubscriptionController extends ResourceController  
{
    public function show(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);
        $resource = $this->findOr404($configuration);

        // Accéder à la méthode getSyliusOrder() de l'entité Subscription
        // $order = $resource->getSyliusOrder();
        $order = $resource->getRecurringOrder();

        $form = $this->createForm(SubscriptionIsExtendedType::class, $resource);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->get('doctrine.orm.entity_manager')->flush();

            }
        }

        $event = $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $resource);
        $eventResponse = $event->getResponse();
        if (null !== $eventResponse) {
            return $eventResponse;
        }



        if ($configuration->isHtmlRequest()) {
            return $this->render('bundles/Mediproductions/Admin/Subscription/show.html.twig', [
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $resource,
                $this->metadata->getName() => $resource,
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        return $this->createRestView($configuration, $resource);
    }

    // Permet d'update le renouvellement de la subscription 
    public function updateExtended(Request $request, int $id): JsonResponse
    {
        $subscription = $this->findOr404($this->requestConfigurationFactory->create($this->metadata, $request));
        
        // Converti la valeur en booléen
        $isExtended = filter_var($request->request->get('isExtended'), FILTER_VALIDATE_BOOLEAN);

        $subscription->setIsExtended($isExtended);

        $this->get('doctrine.orm.entity_manager')->flush();

        return new JsonResponse(['success' => true]);
    }
    
}