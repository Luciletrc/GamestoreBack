<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/product', name: 'app_api_product_')]
class ProductController extends AbstractController
{
    #[Route(name: 'new', methods: 'POST')]
    public function new(): Response 
    {
        $product = new Product();
        $product->setName('Gamestore_product');
        $product->setDescription('Cette description est géniale!');
        $product->setPrice(new money_format());
        $product->setCreatedAt(new DateTimeImmutable());

        // Tell Doctrine you want to (eventually) save the product (no queries yet)
        $this->manager->persist($product);
        // Actually executes the queries (i.e. the INSERT query)
        $this->manager->flush();

        return $this->json(
            ['message' => "product resource created with {$product->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/show', name: 'show', methods: 'GET')]
    public function show(int $id): Response 
    {
        $product = $this->repository->findOneBy(['id' => $id]);

        if (!$product) {
            throw $this->createNotFoundException("No product found for {$id} id");
        }

        return $this->json(
            ['message' => "A product was found : {$product->getName()} for {$product->getId()} id"]
        );
    }

    #[Route('/edit', name: 'edit', methods: 'PUT')]
    public function edit(int $id): Response 
    {
        $product = $this->repository->findOneBy(['id' => $id]);

        if (!$product) {
            throw $this->createNotFoundException("No product found for {$id} id");
        }

        $product->setName('product name updated');
        $this->manager->flush();

        return $this->redirectToRoute('app_api_product_show', ['id' => $product->getId()]);
    }

    #[Route('/delete', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): Response 
    {
        $product = $this->repository->findOneBy(['id' => $id]);
        if (!$product) {
            throw $this->createNotFoundException("No product found for {$id} id");
        }

        $this->manager->remove($product);
        $this->manager->flush();

        return $this->json(['message' => "product resource deleted"], Response::HTTP_NO_CONTENT);
    }
}
