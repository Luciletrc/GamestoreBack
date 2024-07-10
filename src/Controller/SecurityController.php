<?php

namespace App\Controller;


use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\{RequestBody, Property, JsonContent, MediaType, Schema};
use Symfony\Component\Uid\Uuid;



#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {
    }

    #[Route('/registration', name: 'registration', methods: 'POST')]
    #[OA\Post(
        path:"/api/registration",
        summary: "Inscription d'un nouvel utilisateur",
        requestBody: new RequestBody(
            required: true,
            description: "Données d'inscription nouvel utilisateur",
            content: [new MediaType(mediaType: "application/json",
            schema: new Schema(type: "object", properties: [new Property(
                property: "email",
                type: "string",
                example: "adresse@mail.com"
            ),
            new Property(
                property: "password",
                type: "string",
                example: "Mot de Passe"
            )]))]
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'Utilisateur inscrit avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: "email",
                        type: "string",
                        example: "adresse@mail.com"
                    ),
                    new OA\Property(
                        property: "password",
                        type: "string",
                        example: "Mot de Passe"
                    ),
                    new OA\Property(
                        property: 'apiToken',
                        type: 'string',
                        example: '31al4687357yoieu6876468e87'
                    ),
                    new OA\Property(
                        property: 'role',
                        type: 'string',
                        example: 'ROLE_USER'
                    )
                ]
            )
        )
    )]
    



    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {        
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new DateTimeImmutable('2000-01-01'));
        $user->setUuid(Uuid::v4()); // Génération automatique de l'UUID
        $user->setUsername($user->getEmail()); // Définir l'email comme nom d'utilisateur

        $this->manager->persist($user);
        $this->manager->flush();
        return new JsonResponse(
            ['user' => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()],
            Response::HTTP_CREATED
        );
    }

    #[Route('/login', name: 'login', methods: 'POST')]
    #[OA\Post(
        path:"/api/login",
        summary: "Connexion d'un utilisateur",
        requestBody: new RequestBody(
            required: true,
            description: "Données de connexion de l'utilisateur",
            content: [new MediaType(mediaType: "application/json",
            schema: new Schema(type: "object", properties: [new Property(
                property: "username",
                type: "string",
                example: "adresse@mail.com"
            ),
            new Property(
                property: "password",
                type: "string",
                example: "Mot de Passe"
            )]))]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Connexion réussie',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: "username",
                        type: "string",
                        example: "Nom d'utilisateur"
                    ),
                    new OA\Property(
                        property: 'apiToken',
                        type: 'string',
                        example: '31al4687357yoieu6876468e87'
                    ),
                    new OA\Property(
                        property: 'role',
                        type: 'string',
                        example: 'ROLE_USER'
                    )
                ]
            )
        )
    )]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'user'  => $user->getUserIdentifier(),
            'username' => $user->getUsername(), // Ajouter le nom d'utilisateur à la réponse
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles(),
            ]);
    }
}