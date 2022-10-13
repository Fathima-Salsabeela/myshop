<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\AdminCommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCrudCommandeController extends AbstractController
{
    #[Route('/admin/crud/commande', name: 'admin_crud_commande')]
    public function index(CommandeRepository $repo, EntityManagerInterface $manager): Response
    {
        $colonnes = $manager->getClassMetadata(Commande::class)->getFieldNames();
        $commande = $repo->findAll();
        return $this->render('admin_crud_commande/index.html.twig', [
            'commande' => $commande,
            'colonnes' => $colonnes
        ]);
    }
    #[Route('/admin/crud/commande/edit/{id}', name: 'admin_crud_commande_edit')]
    public function form(Commande $commande = null, EntityManagerInterface $manager, Request $rq)
    {  if (!$commande) {
        $commande = new Commande;
        $commande->setDateEnregistrement(new \DateTime());
    }
    $form = $this->createForm(AdminCommandeType::class, $commande);

    $form->handleRequest($rq);

     if ($form->isSubmitted() && $form->isValid()) {
   
        $manager->persist($commande);
        $manager->flush();
       
        return $this->redirectToRoute('admin_crud_commande', [
            'id' =>$commande->getId()
        ]);
     }
        
        return $this->renderForm('admin_crud_commande/form.html.twig', [
            'form' => $form,
            'editMode' => $commande->getId() != NULL
        ]);
    }

    #[Route('/admin/crud/commande/delete/{id}', name: 'admin_crud_commande_delete')]
    public function delete(Commande $commande = null, EntityManagerInterface $manager)
    {
        if ($commande) {
            $manager->remove($commande);
            $manager->flush();
            $this->addFlash('success', 'La commande a bien été supprimée !');
        }
        return $this->redirectToRoute('admin_crud_commande');
    }
}
