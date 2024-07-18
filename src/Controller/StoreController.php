<?php

namespace App\Controller;

use App\Entity\Store;
use App\Repository\StoreRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\{RequestBody, Property, JsonContent, MediaType, Schema};

#[Route('api/store', name: 'app_api_store_')]
class StoreController extends AbstractController
{
    private EntityManagerInterface $manager;
    private StoreRepository $repository;
    private SerializerInterface $serializer;

    public function __construct(
        EntityManagerInterface $manager,
        StoreRepository $repository,
        SerializerInterface $serializer,
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path:"/api/store",
        summary: "Créer une boutique",
        requestBody: new RequestBody(
            required: true,
            description: "Données de la boutique à créer",
            content: [new MediaType(mediaType: "application/json",
            schema: new Schema(type: "object", properties: [new Property(
                property: "name",
                type: "string",
                example: "Nom de la boutique"
            ),
            new Property(
                property: "description",
                type: "string",
                example: "Description de la boutique"
            ),
            new Property(
                property: "Horaires d'ouverture",
                type: "string",
                format: "date-time"
            ),
            new Property(
                property: "Horaires de fermeture",
                type: "string",
                format: "date-time"
            )]))]
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'Boutique créée avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: "id",
                        type: "integer",
                        example: "1"
                    ),
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Nom de la boutique"
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Description de la boutique'
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        format: 'date-time'
                    ),
                    new Property(
                        property: "Horaires d'ouverture",
                        type: "string",
                        format: "date-time"
                    ),
                    new Property(
                        property: "Horaires de fermeture",
                        type: "string",
                        format: "date-time"
                    )]))
    )]
    #[OA\Response(
        response: 404,
        description: 'Erreur dans la création de la boutique'
    )]
    public function new(Request $request): JsonResponse
    {
        $store = $this->serializer->deserialize($request->getContent(), Store::class, 'json');
        $store->setCreatedAt(new DateTimeImmutable());
        $store->setUuid(Uuid::v4());
        $store->setOpeningTime();
        $store->setClosingTime();

        $this->manager->persist($store);
        $this->manager->flush();

        return new JsonResponse(
            ['store' => $store->getId()],
            Response::HTTP_CREATED,
        );
    }

    #[Route(name: 'show', methods: 'GET')]
    #[OA\Get(
        path:"/api/store",
        summary: "Afficher une boutique par son id"
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la boutique à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Boutique trouvée avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: "id",
                        type: "integer",
                        example: "1"
                    ),
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Nom de la boutique"
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Description de la boutique'
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        format: 'date-time'
                    ),
                    new Property(
                        property: "Horaires d'ouverture",
                        type: "string",
                        format: "date-time"
                    ),
                    new Property(
                        property: "Horaires de fermeture",
                        type: "string",
                        format: "date-time"
                    )]))
    )]
    #[OA\Response(
        response: 404,
        description: 'Boutique non trouvée'
    )]
    public function show(int $id): JsonResponse
    {
        $store = $this->repository->findOneBy(['id' => $id]);
        if (!$store) {
            $responseData = $this->serializer->serialize($store, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route(name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path:"/api/store",
        summary: "Modifier une boutique par son id",
        requestBody: new RequestBody(
            required: true,
            description: "Données de la boutique à modifier",
            content: [new MediaType(mediaType: "application/json",
            schema: new Schema(type: "object", properties: [new Property(
                property: "name",
                type: "string",
                example: "Nom de la boutique"
            ),
            new Property(
                property: "description",
                type: "string",
                example: "Description de la boutique"
            )]))]
        ),
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la boutique à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Informations de la boutique modifiées avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: "id",
                        type: "integer",
                        example: "1"
                    ),
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Nom de la boutique"
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Description de la boutique'
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        format: 'date-time'
                    )]))
    )]
    #[OA\Response(
        response: 404,
        description: 'Echec de la mofication'
    )]
    public function edit(int $id, Request $request): JsonResponse 
    {
        $store = $this->repository->findOneBy(['id' => $id]);
        if (!$store) {
            $store = $this->serializer->deserialize(
                $request->getContent(),
                Store::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $store]
            );
            $store->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route(name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path:"/api/store",
        summary: "Supprimer une boutique par son id"
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la boutique à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Boutique supprimée avec succès',
    )]
    #[OA\Response(
        response: 404,
        description: 'Impossible de supprimer la boutique'
    )]
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
