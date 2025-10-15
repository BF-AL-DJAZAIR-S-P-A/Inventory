<?php

namespace App\Controller;

use App\Entity\ProduitEntrepot;
use App\Form\ProduitEntrepotType;
use App\Repository\ProduitEntrepotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stock')]
final class ProduitEntrepotController extends AbstractController
{
    #[Route(name: 'app_produit_entrepot_index', methods: ['GET'])]
    public function index(ProduitEntrepotRepository $produitEntrepotRepository): Response
    {
        return $this->render('produit_entrepot/index.html.twig', [
            'produit_entrepots' => $produitEntrepotRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_entrepot_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produitEntrepot = new ProduitEntrepot();
        $form = $this->createForm(ProduitEntrepotType::class, $produitEntrepot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitEntrepot->setUtilisateur($this->getUser());
            $entityManager->persist($produitEntrepot);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_entrepot_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_entrepot/new.html.twig', [
            'produit_entrepot' => $produitEntrepot,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_entrepot_show', methods: ['GET'])]
    public function show(ProduitEntrepot $produitEntrepot): Response
    {
        return $this->render('produit_entrepot/show.html.twig', [
            'produit_entrepot' => $produitEntrepot,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_entrepot_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProduitEntrepot $produitEntrepot, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitEntrepotType::class, $produitEntrepot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_entrepot_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_entrepot/edit.html.twig', [
            'produit_entrepot' => $produitEntrepot,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_entrepot_delete', methods: ['POST'])]
    public function delete(Request $request, ProduitEntrepot $produitEntrepot, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produitEntrepot->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produitEntrepot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_entrepot_index', [], Response::HTTP_SEE_OTHER);
    }
}
