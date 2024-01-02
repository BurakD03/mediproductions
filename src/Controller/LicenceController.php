<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Licence\Licence;
use App\Repository\LicenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Exception\UpdateHandlingException;

#[AsController]
class LicenceController extends ResourceController  
{

    // public function __construct(
    //     private AbstractBrowser $clientt,
    //     private RequestStack $requestStack,
    //     EntityManagerInterface $em,
    // ) {
    // }
    
    // #[Route('/admin/create/licence', name: 'app_create_licence')]
    public function create(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $form = $this->resourceFormFactory->create($configuration, $newResource);
        $form->handleRequest($request);
        
        // if ($form->isSubmitted()) {
        //     $newResource = $form->getData();
        //     $newResource->setUpdatedAt(new \DateTime());
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->persist($newResource);
        //     dd($form->getData());
        // }


        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $newResource = $form->getData();

            $newResource->setUpdatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newResource);
            // dd($newResource);
            
            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                $eventResponse = $event->getResponse();
                if (null !== $eventResponse) {
                    return $eventResponse;
                }

                return $this->redirectHandler->redirectToIndex($configuration, $newResource);
            }
            
            if ($configuration->hasStateMachine()) {
                $stateMachine = $this->getStateMachine();
                $stateMachine->apply($configuration, $newResource);
            }

            $this->repository->add($newResource);

            if ($configuration->isHtmlRequest()) {
                $this->flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource);
            }

            $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource);

            if (!$configuration->isHtmlRequest()) {
                return $this->createRestView($configuration, $newResource, Response::HTTP_CREATED);
            }

            $postEventResponse = $postEvent->getResponse();
            if (null !== $postEventResponse) {
                return $postEventResponse;
            }

            if (!$configuration->isHtmlRequest()) {
                return $this->createRestView($configuration, $form, Response::HTTP_BAD_REQUEST);
            }

            $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(ResourceActions::CREATE, $configuration, $newResource);
            $initializeEventResponse = $initializeEvent->getResponse();
            if (null !== $initializeEventResponse) {
                return $initializeEventResponse;
            }
            
            return $this->redirectToRoute('app_admin_licence_index');
        }

        return $this->render('bundles/Mediproductions/Admin/Licence/create.html.twig', [
            'configuration' => $configuration,
            'resource' => $newResource,
            'metadata' => $this->metadata,
            'form' => $form->createView(),
            // 'codeCrm' => $items,
        ]);
    }

    //#[Route('/admin/licences/{id}/edit', name: 'app_create_licence', requirements: ['id' => '\d+'])]
    public function update(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $resource = $this->findOr404($configuration);

        $form = $this->resourceFormFactory->create($configuration, $resource);
        $form->handleRequest($request);
        // dd($form->get('syliusProductVariant')->getData()->getId());

        $label = $this->container->get('sylius.repository.product')->findByIdProductVariant($form->get('syliusProductVariant')->getData()->getId(),'en_US');
        if (
            in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) &&
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $resource = $form->getData();

            $resource->setUpdatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resource);

            /** @var ResourceControllerEvent $event */
            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                $eventResponse = $event->getResponse();
                if (null !== $eventResponse) {
                    return $eventResponse;
                }

                return $this->redirectHandler->redirectToResource($configuration, $resource);
            }

            try {
                $this->resourceUpdateHandler->handle($resource, $configuration, $this->manager);
            } catch (UpdateHandlingException $exception) {
                if (!$configuration->isHtmlRequest()) {
                    return $this->createRestView($configuration, $form, $exception->getApiResponseCode());
                }

                $this->flashHelper->addErrorFlash($configuration, $exception->getFlash());

                return $this->redirectHandler->redirectToReferer($configuration);
            }

            if ($configuration->isHtmlRequest()) {
                $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource);
            }

            $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource);

            if (!$configuration->isHtmlRequest()) {
                if ($configuration->getParameters()->get('return_content', false)) {
                    return $this->createRestView($configuration, $resource, Response::HTTP_OK);
                }

                return $this->createRestView($configuration, null, Response::HTTP_NO_CONTENT);
            }

            $postEventResponse = $postEvent->getResponse();
            if (null !== $postEventResponse) {
                return $postEventResponse;
            }

            // return $this->redirectHandler->redirectToResource($configuration, $resource);
            return $this->redirectToRoute('app_admin_licence_index');
        }

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->isSubmitted() && !$form->isValid()) {
            $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->createRestView($configuration, $form, Response::HTTP_BAD_REQUEST);
        }

        $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(ResourceActions::UPDATE, $configuration, $resource);
        $initializeEventResponse = $initializeEvent->getResponse();
        if (null !== $initializeEventResponse) {
            return $initializeEventResponse;
        }

        return $this->render('bundles/Mediproductions/Admin/Licence/update.html.twig', [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'resource' => $resource,
            $this->metadata->getName() => $resource,
            'label' => $label,
            'form' => $form->createView(),
        ], null, $responseCode ?? Response::HTTP_OK);
    }

    public function getLicenceCodeCrm(Request $request)
    {
        
        $criteria = $request->query->get('phrase','');
        
        // $items = $this->container->get('sylius.repository.licence')->findByNamePart($criteria, 5);
        $items = $this->container->get('sylius.repository.product')->findByNameProductVariant($criteria,'en_US');
        // dd($items);
        $responseArray = [
            'success' => true,
            'results' => [],
        ];
        foreach ($items as $item) {
            $responseArray['results'][] = [
                'name' => $item['TP']. ' - '. $item['TV'],
                'value' => $item['id'],
                'text' =>$item['TP']. ' - '.$item['durationValue']. ' '. $item['durationUnit'],

            ];

        }

        return new JsonResponse($responseArray);

    }
}
