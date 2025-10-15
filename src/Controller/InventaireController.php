<?php

namespace App\Controller;

use App\Entity\Inventaire;
use App\Entity\InventaireItem;
use App\Repository\InventaireRepository;
use App\Repository\ProduitRepository;
use App\Repository\EntrepotRepository;
use App\Form\InventaireType;
use App\Entity\InventaireHistorique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inventaire')]
final class InventaireController extends AbstractController
{
    #[Route(name: 'app_inventaire_index', methods: ['GET'])]
    public function index(InventaireRepository $inventaireRepository): Response
    {
        return $this->render('inventaire/index.html.twig', [
            'inventaires' => $inventaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_inventaire_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $inventaire = new Inventaire();
    $form = $this->createForm(InventaireType::class, $inventaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // ❌ Ne plus pré-remplir les InventaireItems automatiquement
        $em->persist($inventaire);
        $em->flush();

        return $this->redirectToRoute('app_inventaire_index');
    }

    return $this->render('inventaire/new.html.twig', [
        'inventaire' => $inventaire,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_inventaire_show', methods: ['GET'])]
    public function show(Inventaire $inventaire): Response
    {
        return $this->render('inventaire/show.html.twig', [
            'inventaire' => $inventaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inventaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inventaire $inventaire, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(InventaireType::class, $inventaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_inventaire_index');
        }

        return $this->render('inventaire/edit.html.twig', [
            'inventaire' => $inventaire,
            'form' => $form,
        ]);
    }

 #[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/{id}/scan', name: 'inventaire_scan', methods: ['GET', 'POST'])]
public function scan(
    Request $request,
    Inventaire $inventaire,
    ProduitRepository $produitRepository,
    EntrepotRepository $entrepotRepo, // <-- ajouter ceci
    EntityManagerInterface $em
): Response {
    $user = $this->getUser(); // Utilisateur connecté

    if ($request->isMethod('POST')) {
        $code = trim($request->request->get('codeArticle'));
        if (!$code) {
            return $this->json(['error' => 'Code vide'], 400);
        }

        // Recherche par codeArticle ou codeBarre
        $produit = $produitRepository->createQueryBuilder('p')
            ->where('p.codeArticle = :code OR p.codeBarre = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], 404);
        }

        // Vérifie si l'item existe déjà
        $item = $em->getRepository(InventaireItem::class)->findOneBy([
            'inventaire' => $inventaire,
            'produit' => $produit,
        ]);

        if (!$item) {
        $item = new InventaireItem();
        $item->setInventaire($inventaire);
        $item->setProduit($produit);
        $item->setQuantiteTheorique((float) $produit->getStockActuel());
        $item->setQuantiteReelle(1);
        if ($request->request->get('entrepot')) {
            $entrepot = $entrepotRepo->find($request->request->get('entrepot'));
            if ($entrepot) $item->setEntrepot($entrepot);
        }
        $em->persist($item);
        } else {
            $item->setQuantiteReelle($item->getQuantiteReelle() + 1); // incrémentation
            $item->setQuantiteTheorique((float) $produit->getStockActuel());
        }

        // Historique
        $historique = new \App\Entity\InventaireHistorique();
        $historique->setInventaire($inventaire);
        $historique->setUtilisateur($user); // ✅ plus null
        $historique->setAction('comptage');
        $historique->setMessage("Produit scanné: {$produit->getNom()} (Qté réelle: {$item->getQuantiteReelle()})");
        $historique->setDateAction(new \DateTimeImmutable());
        $em->persist($historique);

        $em->flush();

        return $this->json([
            'produit' => $produit->getNom(),
            'quantiteReelle' => $item->getQuantiteReelle(),
            'quantiteTheorique' => $item->getQuantiteTheorique(),
            'codeArticle' => $produit->getCodeArticle(),
            'codeBarre' => $produit->getCodeBarre(),
        ]);
    }

    // GET : affiche la page scan
    return $this->render('inventaire/scan.html.twig', [
        'inventaire' => $inventaire,
        'items' => $inventaire->getInventaireItems(),
    ]);
}

    #[Route('/{id}/update-quantite', name: 'inventaire_update_quantite', methods: ['POST'])]
    public function updateQuantite(
        Request $request,
        Inventaire $inventaire,
        EntrepotRepository $entrepotRepo, 
        EntityManagerInterface $em
    ): Response {
        $code = trim($request->request->get('codeArticle'));
        $quantite = (int) $request->request->get('quantite');
        $entrepotId = $request->request->get('entrepot');
        $entrepot = $entrepotId ? $entrepotRepo->find($entrepotId) : null;

      foreach ($inventaire->getInventaireItems() as $item) {
        $produit = $item->getProduit();
        if ($produit && ($produit->getCodeArticle() === $code || $produit->getCodeBarre() === $code)) {
            $item->setQuantiteReelle($quantite);
            if ($entrepot) {
                $item->setEntrepot($entrepot);
            }
            $em->flush();
            return $this->json(['success' => true]);
        }
    }

        return $this->json(['error' => 'Produit introuvable'], 404);
    }

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/{id}/valider', name: 'inventaire_valider', methods: ['POST', 'GET'])]
public function valider(Inventaire $inventaire, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour valider un inventaire.');
    }

    // ✅ Vérifier que l'inventaire n'est pas déjà validé
    if ($inventaire->getStatut() === Inventaire::STATUT_VALIDE) {
        $this->addFlash('warning', 'Cet inventaire est déjà validé.');
        return $this->redirectToRoute('app_inventaire_show', ['id' => $inventaire->getId()]);
    }

    // Mettre à jour le statut et la date de validation
    $inventaire->setStatut(Inventaire::STATUT_VALIDE);
    $inventaire->setDateValidation(new \DateTimeImmutable());
    $em->persist($inventaire);

    // Ajouter un historique
    $historique = new \App\Entity\InventaireHistorique();
    $historique->setInventaire($inventaire);
    $historique->setUtilisateur($user);
    $historique->setAction('validation');
    $historique->setMessage(
        'Inventaire validé / mis à jour le ' . $inventaire->getDateValidation()->format('d/m/Y H:i')
    );
    $historique->setDateAction(new \DateTimeImmutable());
    $em->persist($historique);

    $em->flush();

    $this->addFlash('success', '✅ Inventaire validé avec succès.');
    return $this->redirectToRoute('app_inventaire_show', ['id' => $inventaire->getId()]);
}


#[IsGranted('ROLE_ADMIN')]
#[Route('/{id}/appliquer', name: 'app_inventaire_appliquer', methods: ['POST'])]
public function appliquerStocks(Inventaire $inventaire, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour appliquer les stocks.');
    }

    // Vérifier que l'inventaire est validé
    if ($inventaire->getStatut() !== Inventaire::STATUT_VALIDE) {
        $this->addFlash('warning', 'Impossible d’appliquer les stocks : l’inventaire n’est pas validé.');
        return $this->redirectToRoute('app_inventaire_show', ['id' => $inventaire->getId()]);
    }

    // Vérifier si l'inventaire a déjà été appliqué
    if ($inventaire->isApplique()) {
        $this->addFlash('warning', 'Cet inventaire a déjà été appliqué.');
        return $this->redirectToRoute('app_inventaire_show', ['id' => $inventaire->getId()]);
    }

    foreach ($inventaire->getInventaireItems() as $item) {
        $produit = $item->getProduit();
        if ($produit) {
            // Applique la quantité réelle au stock actuel
            $produit->setStockActuel($item->getQuantiteReelle());

            // Met à jour la quantité théorique pour que ce soit cohérent
            $item->setQuantiteTheorique($item->getQuantiteReelle());

            $em->persist($produit);
            $em->persist($item);
        }
    }

    // Marquer l'inventaire comme appliqué, qui l'applique et la date
    $inventaire->setApplique(true);
    $inventaire->setAppliquePar($user);
    $inventaire->setDateFin(new \DateTimeImmutable()); // <-- si tu ajoutes cette propriété
    $inventaire->setStatut(Inventaire::STATUT_APPLIQUE);

    $em->persist($inventaire);

    // Historique
    $historique = new InventaireHistorique();
    $historique->setInventaire($inventaire);
    $historique->setUtilisateur($user);
    $historique->setAction('application_stocks');
    $historique->setMessage('Stocks appliqués depuis l’inventaire.');
    $historique->setDateAction(new \DateTimeImmutable());
    $em->persist($historique);

    $em->flush();

    $this->addFlash('success', '✅ Stocks appliqués avec succès.');
    return $this->redirectToRoute('app_inventaire_show', ['id' => $inventaire->getId()]);
}


    #[Route('/{id}/delete', name: 'app_inventaire_delete', methods: ['POST'])]
public function delete(Request $request, Inventaire $inventaire, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete' . $inventaire->getId(), $request->request->get('_token'))) {
        $em->remove($inventaire);
        $em->flush();
    }

    return $this->redirectToRoute('app_inventaire_index');
}
}
