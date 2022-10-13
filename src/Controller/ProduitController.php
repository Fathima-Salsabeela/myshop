<?php

namespace App\Controller;


use App\Service\CartService;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(ProduitRepository $repo, CartService $cs): Response
    {
        $total = $cs->getTotal();
        $produit = $repo->findAll();
        return $this->render('produit/index.html.twig', [
            'produit' => $produit,
            'total' => $total
        ]);
    }
}
