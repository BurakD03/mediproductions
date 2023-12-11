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

#[AsController]
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

        // $criteria = $request->query->get('phrase');
        // echo $criteria ;
        // $items = $this->container->get('app.repository.licence')->findByNamePart($criteria);

        // Appeler l'action getLicenceCodeCrm pour récupérer les données
        // $ajaxJson = $this->getLicenceCodeCrm($request);
        // $data = json_decode($ajaxJson->getContent(), true);
        // $items = $data['_embedded']['items'];

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


    public function getLicenceCodeCrm(Request $request)
    {
        
        $criteria = $request->query->get('phrase','');
        // echo $criteria ;
        
        // $items = $this->container->get('app.repository.licence')->findByNamePart($criteria, 5);
        $items = $this->container->get('app.repository.product')->findByNameProductVariant($criteria,'fr_FR');
        // dd($items);
        $responseArray = [
            'success' => true,
            'results' => [],
        ];
        foreach ($items as $item) {
            $responseArray['results'][] = [
                'name' => $item['code'], // Remplacez par le nom réel de votre propriété dans l'entité Licence
                'value' => $item['code'], // Remplacez par la valeur réelle de votre propriété dans l'entité Licence
                'text' => $item['code'], // Remplacez par le texte réel de votre propriété dans l'entité Licence
            ];
        }

        return new JsonResponse($responseArray);

    }
}
