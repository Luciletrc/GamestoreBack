<?php

namespace App\Controller;

use App\Entity\Store;
use App\Repository\StoreRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/store', name: 'app_api_store_')]
class StoreController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private StoreRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse 
    {
        $store = $this->serializer->deserialize($request->getContent(), Store::class, 'json');
        $store->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($store);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($store, 'json');
        $location = $this->urlGenerator->generate('/{id}', ['id'=>$store->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse 
    {
        $store = $this->repository->findOneBy(['id' => $id]);
        if (!$store) {
            $responseData = $this->serializer->serialize($store, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse 
    {
        $store = $this->repository->findOneBy(['id' => $id]);
        if (!$store) {
            $store = $this->serializer->deserialize(
                $request->getContent(),
                Store::class,
                'json',
                [AbstractNormalizer::OBJET_TO_POPULATE => $store]
            );
            $store->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse 
    {
        $store = $this->repository->findOneBy(['id' => $id]);
        if (!$store) {
            $this->manager->remove($store);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
