<?php

namespace App\Controller;

use App\Entity\InventaireItem;
use App\Form\InventaireItemType;
use App\Repository\InventaireItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inventaire-item')]
final class InventaireItemController extends AbstractController
{
    #[Route(name: 'app_inventaire_item_index', methods: ['GET'])]
    public function index(InventaireItemRepository $inventaireItemRepository): Response
    {
        return $this->render('inventaire_item/index.html.twig', [
            'inventaire_items' => $inventaireItemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_inventaire_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $inventaireItem = new InventaireItem();
        $form = $this->createForm(InventaireItemType::class, $inventaireItem);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                    $produit = $inventaireItem->getProduit();

            if ($produit) {
                // ⚡ Ici on utilise le stock actuel comme quantité théorique
                $inventaireItem->setQuantiteTheorique((float) $produit->getStockActuel());
            }
            $entityManager->persist($inventaireItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_inventaire_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inventaire_item/new.html.twig', [
            'inventaire_item' => $inventaireItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inventaire_item_show', methods: ['GET'])]
    public function show(InventaireItem $inventaireItem): Response
    {
        return $this->render('inventaire_item/show.html.twig', [
            'inventaire_item' => $inventaireItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inventaire_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InventaireItem $inventaireItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InventaireItemType::class, $inventaireItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_inventaire_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inventaire_item/edit.html.twig', [
            'inventaire_item' => $inventaireItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inventaire_item_delete', methods: ['POST'])]
    public function delete(Request $request, InventaireItem $inventaireItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inventaireItem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($inventaireItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inventaire_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
