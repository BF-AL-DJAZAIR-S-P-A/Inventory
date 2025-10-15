<?php

namespace App\Controller;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\ProduitImportType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Entity\Categorie;
use App\Entity\Entrepot;
use TCPDF;

use App\Entity\ProduitEntrepot;


#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

         if (empty($produit->getCodeArticle())) {
        // Par exemple : P-0001, P-0002, ...
        $produit->setCodeArticle('BF-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT));
        }

    $entityManager->persist($produit);

  
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

#[Route('/{id}/barcode', name: 'produit_barcode')]
public function barcode(Produit $produit): Response
{
    $code = $produit->getCodeArticle() ?? 'BF-' . str_pad($produit->getId() ?? rand(1, 9999), 6, '0', STR_PAD_LEFT);
    $nomProduit = htmlspecialchars($produit->getNom(), ENT_QUOTES);

    // Génération du code-barres "à la main" en SVG
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $barcodePng = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 60);
    $barcodeBase64 = base64_encode($barcodePng);

    // SVG final avec nom en haut et code en bas
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="120">
    <text x="50%" y="15" text-anchor="middle" font-size="14" font-family="Arial">$nomProduit</text>
    <image x="10" y="25" width="280" height="60" href="data:image/png;base64,$barcodeBase64"/>
    <text x="50%" y="110" text-anchor="middle" font-size="12" font-family="Arial">$code</text>
</svg>
SVG;

    return new Response($svg, 200, ['Content-Type' => 'image/svg+xml']);
}

    #[Route('/etiquettes/selection', name: 'produit_etiquettes_selection')]
    public function etiquettesSelection(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        return $this->render('produit/etiquettes_selection.html.twig', [
            'produits' => $produits,
        ]);
    }

   #[Route('/etiquettes/print', name: 'produit_etiquettes_print', methods: ['POST'])]
    public function etiquettesPrint(Request $request, ProduitRepository $produitRepository): Response
    {
        // Récupère correctement le tableau 'quantite' depuis le formulaire
        $quantites = $request->request->all('quantite');

        $produits = [];

        foreach ($quantites as $id => $qte) {
            if ($qte > 0) {
                $produit = $produitRepository->find($id);
                if ($produit) {
                    $produits[] = [
                        'produit' => $produit,
                        'quantite' => (int) $qte,
                    ];
                }
            }
        }

        return $this->render('produit/etiquettes_print.html.twig', [
            'produits' => $produits,
        ]);
    }

#[Route('/etiquettes/pdf', name: 'produit_etiquettes_pdf', methods: ['POST'])]
public function etiquettesPdf(Request $request, ProduitRepository $produitRepository): Response
{
    $data = $request->request->all();
    $quantites = $data['quantite'] ?? [];

    $produits = [];
    foreach ($quantites as $id => $qte) {
        if ($qte > 0) {
            $produit = $produitRepository->find($id);
            if ($produit) {
                $produits[] = ['produit' => $produit, 'quantite' => (int)$qte];
            }
        }
    }

    if (count($produits) === 0) {
        return new Response('Aucun produit sélectionné.', 400);
    }

    // ✅ Configuration du PDF
    $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 10);

    // ✅ Dimensions
    $pageWidth = 210;
    $width = 64.3;
    $height = 70.5;
    $hSpacing = 2;
    $vSpacing = 0;

    $cols = 3;
    $rows = 4;

    $totalWidthUsed = ($cols * $width) + (($cols - 1) * $hSpacing);
    $marginLeft = ($pageWidth - $totalWidthUsed) / 2;
    $marginTop = 8;

    $compteur = 0;

    // ✅ Chemin du logo (à adapter)
   $logoPath = $this->getParameter('kernel.project_dir') . '/public/logo.png';
    if (!file_exists($logoPath)) {
        $logoPath = null;
    }

    foreach ($produits as $item) {
        for ($i = 0; $i < $item['quantite']; $i++) {

            if ($compteur > 0 && $compteur % 12 == 0) {
                $pdf->AddPage();
            }

            $positionInPage = $compteur % 12;
            $col = $positionInPage % $cols;
            $row = intdiv($positionInPage, $cols);

            $x = $marginLeft + $col * ($width + $hSpacing);
            $y = $marginTop + $row * ($height + $vSpacing);

            // ✅ Bordure optionnelle
           // $pdf->Rect($x, $y, $width, $height, 'D');

            // ✅ Logo centré
            if ($logoPath) {
                $logoWidth = 25; // largeur du logo en mm
                $logoHeight = 10; // hauteur du logo
                $logoX = $x + ($width / 2) - ($logoWidth / 2);
                $logoY = $y + 2; // un peu de marge en haut
                $pdf->Image($logoPath, $logoX, $logoY, $logoWidth, $logoHeight, '', '', '', false, 300);
            }

            // ✅ Récupération de référence
            $reference= '';
            if (method_exists($item['produit'], 'getReference')) {
                $reference = $item['produit']->getReference();
            } 
            if (empty($reference)) {
                $reference = $item['produit']->getCodeArticle();
            }

            // ✅ Référence du produit (sous le logo)
            $pdf->SetFont('dejavusans', '', 9);
            $pdf->SetXY($x, $y + 14);
            $pdf->MultiCell($width, 6, 'REF: '.$reference, 0, 'C', false, 1);

            // ✅ Code-barres centré
            $innerPad = 3;
            $barcodeHeight = 20;
            $barcodeWidth = $width - ($innerPad * 2);
            $barcodeX = $x + $innerPad;
            $barcodeY = $y + ($height / 2) - ($barcodeHeight / 2);

            $style = [
                'position' => '',
                'align' => 'C',
                'stretch' => true,
                'fitwidth' => true,
                'cellfitalign' => 'C',
                'border' => false,
                'padding' => 0,
                'fgcolor' => [0, 0, 0],
                'bgcolor' => false,
            ];

            $pdf->write1DBarcode(
                $item['produit']->getCodeArticle(),
                'C128',
                $barcodeX,
                $barcodeY,
                $barcodeWidth,
                $barcodeHeight,
                0.4,
                $style,
                'N'
            );

            // ✅ Code article (en bas)
            $pdf->SetFont('dejavusans', '', 8);
            $pdf->SetXY($x, $barcodeY + $barcodeHeight + 1);
            $pdf->MultiCell($width, 5, $item['produit']->getCodeArticle(), 0, 'C', false, 1);

            $compteur++;
        }
    }

    $pdfContent = $pdf->Output('etiquettes.pdf', 'I');

    return new Response($pdfContent, 200, [
        'Content-Type' => 'application/pdf'
    ]);
}







    #[Route('/generer-code/{categorieId}', name: 'produit_generer_code', methods: ['GET'])]
public function genererCode(
    int $categorieId,
    ProduitRepository $produitRepository
): JsonResponse {
    // Récupérer les produits de cette catégorie
    $lastProduit = $produitRepository->findOneBy(
        ['categorie' => $categorieId],
        ['id' => 'DESC']
    );

    $prefix = str_pad($categorieId, 2, '0', STR_PAD_LEFT);

    $nextNumber = 1;
    if ($lastProduit && $lastProduit->getCodeArticle()) {
        $parts = explode('-', $lastProduit->getCodeArticle());
        if (isset($parts[1])) {
            $nextNumber = (int)$parts[1] + 1;
        }
    }

    $newCode = sprintf('%s-%05d', $prefix, $nextNumber);

    return new JsonResponse(['code' => $newCode]);
}




     #[Route('/excel/import', name: 'produit_import')]
    public function import(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitImportType::class);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['fichier']->getData();

            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // ignorer l'entête

                [$codeArticle, $codeBarre, $nom, $reference, $description, $unite,
                 $prixAchat, $stockActuel, $stockMinimum, $datePeremption,
                 $categorieNom, $entrepotNom] = $row;

                $produit = new Produit();
                $produit->setCodeArticle($codeArticle);
                $produit->setCodeBarre($codeBarre);
                $produit->setNom($nom);
                $produit->setReference($reference);
                $produit->setDescription($description);
                $produit->setUnite($unite);
                $produit->setPrixAchat($prixAchat ?? 0);
                $produit->setStockActuel($stockActuel ?? 0);
                $produit->setStockMinimum($stockMinimum ?? 0);

                if ($datePeremption) {
                    $produit->setDatePeremption(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($datePeremption));
                }

                if ($categorieNom) {
                    $categorie = $em->getRepository(Categorie::class)->findOneBy(['nom' => $categorieNom]);
                    if (!$categorie) {
                        $categorie = new Categorie();
                        $categorie->setNom($categorieNom);
                        $em->persist($categorie);
                    }
                    $produit->setCategorie($categorie);
                }

                $em->persist($produit);

                if ($entrepotNom) {
                    $entrepot = $em->getRepository(Entrepot::class)->findOneBy(['nom' => $entrepotNom]);
                    if (!$entrepot) {
                        $entrepot = new Entrepot();
                        $entrepot->setNom($entrepotNom);
                        $em->persist($entrepot);
                    }

                    $produitEntrepot = new ProduitEntrepot();
                    $produitEntrepot->setProduit($produit);
                    $produitEntrepot->setEntrepot($entrepot);
                    $produitEntrepot->setUtilisateur($this->getUser());
                    $produitEntrepot->setQuantite($stockActuel ?? 0);

                    $em->persist($produitEntrepot);
                    $produit->addProduitEntrepot($produitEntrepot);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Importation terminée !');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/import.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
