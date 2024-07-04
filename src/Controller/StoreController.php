<?php

namespace App\Controller;

use App\Entity\Stores;
use App\Repository\StoreRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/store', name: 'app_api_store_')]
class StoreController extends AbstractController
{
    #[Route(name: 'new', methods: 'POST')]
    public function new(): Response 
    {
        $store = new Store();
        $store->setName('Gamestore');
        $store->setDescription('La meilleure boutique en ligne de Jeux Vidéo');
        $store->setCreatedAt(new DateTimeImmutable());

        // Tell Doctrine you want to (eventually) save the restaurant (no queries yet)
        $this->manager->persist($store);
        // Actually executes the queries (i.e. the INSERT query)
        $this->manager->flush();

        return $this->json(
            ['message' => "store resource created with {$store->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/show', name: 'show', methods: 'GET')]
    public function show(int $id): Response 
    {
        $store = $this->repository->findOneBy(['id' => $id]);

        if (!$store) {
            throw $this->createNotFoundException("No store found for {$id} id");
        }

        return $this->json(
            ['message' => "A store was found : {$store->getName()} for {$store->getId()} id"]
        );
    }

    #[Route('/edit', name: 'edit', methods: 'PUT')]
    public function edit(int $id): Response 
    {
        $store = $this->repository->findOneBy(['id' => $id]);

        if (!$store) {
            throw $this->createNotFoundException("No store found for {$id} id");
        }

        $store->setName('store name updated');
        $this->manager->flush();

        return $this->redirectToRoute('app_api_store_show', ['id' => $store->getId()]);
    }

    #[Route('/delete', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): Response 
    {
        $store = $this->repository->findOneBy(['id' => $id]);
        if (!$store) {
            throw $this->createNotFoundException("No store found for {$id} id");
        }

        $this->manager->remove($store);
        $this->manager->flush();

        return $this->json(['message' => "store resource deleted"], Response::HTTP_NO_CONTENT);
    }
}
