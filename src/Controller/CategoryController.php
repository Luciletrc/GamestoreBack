<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\{RequestBody, Property, JsonContent, MediaType, Schema};

#[Route('api/category', name: 'app_api_category_')]
class CategoryController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {
    }

    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path:"/api/category",
        summary: "Créer une catégorie",
        requestBody: new RequestBody(
            required: true,
            description: "Données de catégorie à créer",
            content: [new MediaType(mediaType: "application/json",
            schema: new Schema(type: "object", properties: [new Property(
                property: "name",
                type: "string",
                example: "Nom de la catégorie"
            )]))]
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'Catégorie créée avec succès',
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
                        example: "Nom de la catégorie"
                    )]))
    )]
    #[OA\Response(
        response: 404,
        description: 'Erreur dans la création de la catégorie'
    )]
    public function new(Request $request): JsonResponse
    {
        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json');
        $category->setUuid(Uuid::v4());

        $this->manager->persist($category);
        $this->manager->flush();

        return new JsonResponse(
            ['category' => $category->getId()],
            Response::HTTP_CREATED,
        );
    }

    #[Route(name: 'show', methods: 'GET')]
    #[OA\Get(
        path:"/api/category",
        summary: "Afficher une catégorie par son id",
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la catégorie à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Catégorie trouvée avec succès',
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
                        example: "Nom de la catégorie"
                    )]))
    )]
    #[OA\Response(
        response: 404,
        description: 'Catégorie non trouvée'
    )]
    public function show(int $id, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->findOneBy(['id' => $id]);
        if (!$category) {
            $responseData = $this->serializer->serialize($category, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route(name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path: "/api/category",
        summary: "Modifier une catégorie par son id",
        requestBody: new RequestBody(
            required: true,
            description: "Données de la catégorie à modifier",
            content: [new MediaType(mediaType: "application/json",
            schema: new Schema(type: "object", properties: [new Property(
                property: "name",
                type: "string",
                example: "Nom de la catégorie"
            )]))]
        ),
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la catégorie à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Informations de la catégorie modifiées avec succès',
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
                example: "Nom de la catégorie"
            )])
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Echec de la modification'
    )]
    public function edit(int $id): JsonResponse
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            $category = $this->serializer->deserialize(
                $request->getContent(),
                Category::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $category]
            );
            $category->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route(name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path:"/api/category",
        summary: "Supprimer une catégorie par son id"
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la catégorie à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Catégorie supprimée avec succès',
    )]
    #[OA\Response(
        response: 404,
        description: 'Impossible de supprimer la catégorie'
    )]
    public function delete(int $id): JsonResponse
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            $this->manager->remove($category);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}