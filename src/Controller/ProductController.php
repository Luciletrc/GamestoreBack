<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
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

#[Route('api/product', name: 'app_api_product_')]
class ProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {
    }

    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path:"/api/product",
        summary: "Créer un nouvel article",
        requestBody: new RequestBody(
            required: true,
            description: "Données de l'article à créer",
            content: [new MediaType(mediaType: "application/json",
            schema: new Schema(type: "object", properties: [new Property(
                property: "name",
                type: "string",
                example: "Jeux n°1"
            ),
            new Property(
                property: "description",
                type: "string",
                example: "Description de l'article"
            ),
            new Property(
                property: "Pegi",
                type: "integer",
                example: "13"
            ),
            new Property(
                property: "Stock",
                type: "integer",
                example: "4"
            ),
            new Property(
                property: "Price",
                type: "float",
                format: "money_format"
            ),
            new Property(
                property: "Category",
                type: "string",
                format: "action"
            ),
            new Property(
                property: "Store",
                type: "integer",
                format: "2"
            )]))]
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'Article créé avec succès',
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
                        example: "Jeux n°1"
                    ),
                    new Property(
                        property: "description",
                        type: "string",
                        example: "Description de l'article"
                    ),
                    new Property(
                        property: "Pegi",
                        type: "integer",
                        example: "13"
                    ),
                    new Property(
                        property: "Stock",
                        type: "integer",
                        example: "4"
                    ),
                    new Property(
                        property: "Price",
                        type: "float",
                        format: "money_format"
                    ),
                    new Property(
                        property: "Category",
                        type: "string",
                        format: "action"
                    ),
                    new Property(
                        property: "Store",
                        type: "integer",
                        format: "2"
                    )
                ]
            )
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Erreur dans la création de l\'article'
    )]
    public function new(Request $request): JsonResponse
    {
        $pegi = array('3', '7', '12', '16', '18');

        $product = new Product();
        $product->setUuid(Uuid::v4());
        $product->setDescription('Cette description est géniale!');
        $product->setPegi(array_rand($pegi));
        $product->setStock(mt_rand(1, 5));
        $product->setPrice(mt_rand(30, 60));

        $storeIds = $request->get('Store');
        $stores = $this->storeRepository->findBy(['id' => $storeIds]);
        foreach ($stores as $store) {
            $product->addStore($store);
        }

        $categoryId = $request->get('category');
        $category = $this->storeRepository->findBy(['id' => $categoryId]);
        $product->addCategory($category);
    
        $product = $this->serializer->deserialize($request->getContent(), Product::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $product]);

        $product->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($product);
        $this->manager->flush();

        return new JsonResponse(
            ['product' => $product->getId()],
            Response::HTTP_CREATED,
        );
    }

    #[Route(name: 'show', methods: 'GET')]
    #[OA\Get(
        path:"/api/product",
        summary: "Afficher un article par son id",
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de l\'article à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Article trouvé avec succès',
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
                    ),
                    new Property(
                        property: "description",
                        type: "string",
                        example: "Description de l'article"
                    ),
                    new Property(
                        property: "Pegi",
                        type: "integer",
                        example: "13"
                    ),
                    new Property(
                        property: "Stock",
                        type: "integer",
                        example: "4"
                    ),
                    new Property(
                        property: "Price",
                        type: "float",
                        format: "money_format"
                    ),
                    new Property(
                        property: "Category",
                        type: "string",
                        format: "action"
                    ),
                    new Property(
                        property: "Store",
                        type: "integer",
                        format: "2"
                    )
                ]
            )
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Article non trouvé'
    )]
    public function show(int $id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->findOneBy(['id' => $id]);
        if (!$product) {
            $responseData = $this->serializer->serialize($product, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route(name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path: "/api/product",
        summary: "Modifier un article par son id",
        requestBody: new RequestBody(
            required: true,
            description: "Données de l\'article à modifier",
            content: [new MediaType(mediaType: "application/json",
            schema: new Schema(type: "object", properties: [new Property(
                property: "name",
                type: "string",
                example: "Nom de l'article"
            ),
            new Property(
                property: "description",
                type: "string",
                example: "Description de l'article"
            ),
            new Property(
                property: "Pegi",
                type: "integer",
                example: "13"
            ),
            new Property(
                property: "Stock",
                type: "integer",
                example: "4"
            ),
            new Property(
                property: "Price",
                type: "float",
                format: "money_format"
            ),
            new Property(
                property: "Category",
                type: "string",
                format: "action"
            ),
            new Property(
                property: "Store",
                type: "integer",
                format: "2"
            )]))]
        ),
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de l\'article à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Informations de l\'article modifiées avec succès',
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
                example: "Nom de l\'article"
            ),
            new Property(
                property: "description",
                type: "string",
                example: "Description de l'article"
            ),
            new Property(
                property: "Pegi",
                type: "integer",
                example: "13"
            ),
            new Property(
                property: "Stock",
                type: "integer",
                example: "4"
            ),
            new Property(
                property: "Price",
                type: "float",
                format: "money_format"
            ),
            new Property(
                property: "Category",
                type: "string",
                format: "action"
            ),
            new Property(
                property: "Store",
                type: "integer",
                format: "2"
            )])
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Echec de la modification'
    )]
    public function edit(int $id): JsonResponse 
    {
        $product = $this->repository->findOneBy(['id' => $id]);
        if (!$product) {
            $product = $this->serializer->deserialize(
                $request->getContent(),
                Product::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $product]
            );
            $product->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route(name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path:"/api/product",
        summary: "Supprimer un article par son id"
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de l\'article à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Article supprimé avec succès',
    )]
    #[OA\Response(
        response: 404,
        description: 'Impossible de supprimer l\'article'
    )]
    public function delete(int $id): JsonResponse
    {
        $product = $this->repository->findOneBy(['id' => $id]);
        if (!$product) {
            $this->manager->remove($product);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    
    #[Route('/updateStock', name: 'update_stock', methods: 'PUT')]
    public function updateStock(Request $request, Connection $connection): Response
    {
        $data = json_decode($request->getContent(), true);

        $sql = 'UPDATE products SET stock = stock - 1 WHERE id = ? AND stock > 0';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['id']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return new Response('Stock mis à jour avec succès !');
        } else {
            return new Response('Le produit est en rupture de stock.');
        }
    }
}
