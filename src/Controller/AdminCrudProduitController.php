<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCrudProduitController extends AbstractController
{
    #[Route('/admin/crud/produit', name: 'app_admin_crud_produit')]
    public function index(ProduitRepository $repo, EntityManagerInterface $manager): Response
    {   $colonnes = $manager->getClassMetadata(Produit::class)->getFieldNames();
        $produit = $repo->findAll();

        return $this->render('admin_crud_produit/index.html.twig', [
            'colonnes' => $colonnes,
            'produit' => $produit
        ]);
    }
    #[Route('/admin/crud/produit/new', name: 'admin_crud_produit_new')]
    #[Route('/admin/crud/produit/edit/{id}', name: 'admin_crud_produit_edit')]
    public function form(Produit $produit =null, Request $rq, EntityManagerInterface $manager)
    {
         if(!$produit){
            $produit = new Produit;
            $produit->setDateEnregistrement(new \DateTime());
        }
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()){
            $manager->persist($produit);
            $manager->flush();
          

            return $this->redirectToRoute('app_admin_crud_produit');
        }
        return $this->renderForm('admin_crud_produit/form.html.twig', [
            'form' => $form,
            'editMode' => $produit->getId() != NULL
        ]);
    }
    #[Route('/admin/crud/produit/delete/{id}', name: 'admin_crud_produit_delete')]
    public function delete(Produit $produit = null, EntityManagerInterface $manager)
    {
        if ($produit) {
            $manager->remove($produit);
            $manager->flush();
            
        }
        return $this->redirectToRoute('app_admin_crud_produit');
    }
}
