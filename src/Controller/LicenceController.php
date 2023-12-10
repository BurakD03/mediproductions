<?php

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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class LicenceController extends ResourceController
{

    // public function __construct(
    //     private AbstractBrowser $clientt,
    //     private RequestStack $requestStack,
    //     EntityManagerInterface $em,
    // ) {
    // }
    
    #[Route('/admin/create/licence', name: 'app_create_licence')]
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
        ]);
    }


    /**
     * @When I look for a variant with :phrase in descriptor within the :product product
     */
    #[Route('/admin/create/licence', name: 'app_create_licence', defaults: ['phrase' => '1'])]
    public function getLicenceCodeCrm($phrase, AbstractBrowser $clientt, RequestStack $requestStack): void
    {
        $clientt->getCookieJar()->set(new Cookie($requestStack->getSession()->getName(), $requestStack->getSession()->getId()));
        $clientt->request(
            'GET',
            '/admin/order/search',
            ['phrase' => $phrase],
            [],
            ['ACCEPT' => 'application/json'],
        );
    }
    // public function getLicenceCodeCrm(Request $request)
    // {
    //     // $repo = $this->get('App\Repository\LicenceRepository');
    //     $repo = $em->getRepository(Licence::class);
    //     $criteria = $request->query->get('phrase');
    //     $q = $criteria['search']['value'];

    //     $items = $repo->findByNamePart($q);

    //     return new JsonResponse([
    //         '_embedded' => [
    //             'items' => $items,
    //         ]
    //     ]);
    // }
}
