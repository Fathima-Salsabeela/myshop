<?php

namespace App\Controller;

use cs;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Service\CartService;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DataController extends AbstractController
{   #[Route('/', name: 'root')]
    public function root()
    {
       return $this->redirectToRoute('app_data');
    }

    #[Route('/data', name: 'app_data')]
    public function index(ProduitRepository $repo): Response
    {
        return $this->render('data/index.html.twig', [
            'produit' => $repo->findAll()
        ]);
    }

    #[Route('/main/resa', name: 'resa_produit')]
    public function resa(Produit $produit = null, EntityManagerInterface $manager, Request $rq ,CartService $cs )
    {   
         $cartWithData = $cs->getCartWithData();
         foreach ($cartWithData As $item)
         {  
            $commande = new Commande;
            $commande->setMembre($this->getUser());
            $commande->setDateEnregistrement(new \DateTime());
            $commande->setProduit($item['produit']);
            $commande->setEtat('en cours de traitement');
            $commande->setQuantite($item['quantite']);
            $quantite=$item['quantite'];
            $prixunitaire=$item['produit']->getPrix();
            $montant=$quantite*$prixunitaire;

            $commande->setMontant($montant);
            $manager->persist($commande);
            $manager->flush();
         }   

         return $this->redirectToRoute('app_data');
    }
    
    #[Route('/data/profil', name: 'profil')]
    public function profil(CommandeRepository $repo)
    {
        $commande = $repo->findBy(['membre' => $this->getUser()]);

        return $this->render("data/profil.html.twig", [
            'commande' => $commande
        ]);
    }

    #[Route('commande/delete/{id}', name: 'profil_delete')]
    public function delete(Commande $commande = null, EntityManagerInterface $manager)
    {
        if ($commande) {
            $manager->remove($commande);
            $manager->flush();
            $this->addFlash('success', 'La commande a bien été supprimée !');
        }
        return $this->redirectToRoute('profil');
    }
}
