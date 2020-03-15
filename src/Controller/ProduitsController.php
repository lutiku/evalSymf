<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\FicheProduitFormType;
use App\Form\ProduitsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitsController extends AbstractController
{
    /**
     * @Route("/produits", name="produits")
     */
    public function produits(Request $request, EntityManagerInterface $entityManager)
    {

        $produits = new Produit();



        $produitsRepository = $this->getDoctrine()->getRepository(Produit::class)->findAll();
        $form =$this->createForm(ProduitsFormType::class, $produits);
        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid())
        {
            $produits = $form->getData();


            $image = $produits->getPhoto();
            $imageName = md5(uniqid()).'.'.$image->guessExtension();
            $image->move($this->getParameter('upload_files'), $imageName);
            $produits ->setPhoto($imageName);

            $entityManager->persist($produits);
            $entityManager->flush();

            $this->redirectToRoute('produits');
        }






        return $this->render('produits/produits.html.twig', [
            'controller_name' => 'ProduitsController',
            'produits' =>$produitsRepository,
            'formProduits' => $form->createView()

        ]);
    }

    /**
     * @Route("/ficheProduit/{id}", name="ficheProduit")
     */

    public function produit($id, Request $request, EntityManagerInterface $entityManager)
    {

        $produitRepository = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->find($id);



        /* Afficher Le formuliare pour ajouter la quantité */
        $paniers = new Panier();



        $paniersRepository = $this->getDoctrine()->getRepository(Panier::class)->findAll();
        $form =$this->createForm(FicheProduitFormType::class, $paniers);
        $form->handleRequest($request);




        if($form->isSubmitted() && $form->isValid())
        {
            $paniers = $form->getData();

            /*Enter les valeurs dans la table Panier */
            $paniers->setDateAjout(new \DateTime());
            $paniers->setEtat(false);
            $paniers->setProduit($produitRepository);

            $entityManager->persist($paniers);
            $entityManager->flush();

            $this->redirectToRoute('produits');
        }



        return $this->render('produits/ficheProduit.html.twig', [
            'controller_name' => 'ProduitsController',
            'paniers' => $paniersRepository,
            'produit' => $produitRepository,
            'formPaniers' => $form->createView()


        ]);

    }






    /**
     * @Route("/", name="accueil")
     */
    public function index()
    {

        $PanierRepository = $this->getDoctrine()->getRepository(Panier::class)->findAll();

        /*Calcul du montant Panier */
        $produitsRepository = $this->getDoctrine()->getRepository(Produit::class)->findAll() ;

        $totalQuantite=0;
        $totalMontant=0;

        foreach ($PanierRepository as $panier){
            $totalQuantite+= $panier->getQuantite();
            $totalMontant+=$panier->getProduit()->getPrix();
            $prix = $totalQuantite+$totalMontant;
        }


        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'paniers'=>$PanierRepository,
            'montant' =>$prix,
            'quantite' =>  $totalQuantite


        ]);
    }




    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id, EntityManagerInterface $entityManager)
    {
        $produit = $this->getDoctrine()->getRepository(Panier::class)->find($id);

        $entityManager->remove($produit);
        $entityManager->flush();

        return $this->redirectToRoute('accueil');

    }

    /*Supprimer le produit de la Table Produit */

    /**
     * @Route("/removeProduits/{id}", name="removeProduits")
     */
    public function removeProduits($id, EntityManagerInterface $entityManager)
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->find($id) ;
        $entityManager->remove($produits);
        $entityManager->flush();
        return $this->redirectToRoute('produits') ;
    }
}
