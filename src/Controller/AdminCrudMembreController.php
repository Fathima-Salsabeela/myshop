<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\AdminMembreType;
use App\Repository\MembreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCrudMembreController extends AbstractController
{
    #[Route('/admin/crud/membre', name: 'app_admin_crud_membre')]
    public function index(MembreRepository $repo, EntityManagerInterface $manager): Response
    {
        $colonnes = $manager->getClassMetadata(Membre::class)->getFieldNames();
        $membre = $repo->findAll();
        return $this->render('admin_crud_membre/index.html.twig', [
            'membre' => $membre,
            'colonnes' => $colonnes
        ]);
    }

    #[Route('/admin/crud/membre/new', name: 'admin_crud_membre_new')]
    #[Route('/admin/crud/membre/edit/{id}', name: 'admin_crud_membre_edit')]

    public function form(Membre $membre = null, EntityManagerInterface $manager, Request $rq, UserPasswordHasherInterface $hasher)
    {
        if (!$membre) {
            $membre = new Membre;
            $membre->setDateEnregistrement(new \DateTime());
        }

        $form = $this->createForm(AdminMembreType::class, $membre);

        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()) {

            if(!$membre->getId() && $form->get('plainPassword')->getData() == null)
            {
                $this->addFlash('danger', "Un nouveau membre doit avoir un mot de passe.");
                return $this->redirectToRoute('admin_crud_membre_new');
            
            }

            if ($form->get('plainPassword')->getData()) {
                $membre->setPassword($hasher->hashPassword($membre, $form->get('plainPassword')->getData()));
           
            }

            $manager->persist($membre);
            $manager->flush();
            $this->addFlash("success", "Le membre a bien été enregistré !");
            return $this->redirectToRoute('app_admin_crud_membre');
        }
        return $this->renderForm('admin_crud_membre/form.html.twig', [
            'form' => $form,
            'editMode' => $membre->getId() != NULL
        ]);

    }
    #[Route('/admin/crud/membre/delete/{id}', name: 'admin_crud_membre_delete')]
    public function delete(Membre $membre = null, EntityManagerInterface $manager)
    {
        if ($membre) {
            $manager->remove($membre);
            $manager->flush();
            $this->addFlash('success', 'Le membre a bien été supprimé !');
        }
        return $this->redirectToRoute('app_admin_crud_membre');
    }
}    