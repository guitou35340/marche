<?php

namespace App\Controller;

use App\Entity\Producteur;
use App\Entity\Produit;
use App\Form\ProduitType1;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/user")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{

    private $produitRepository;



    public function __construct(ProduitRepository $produitRepository)
    {
        $this->produitRepository=$produitRepository;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        $user= $this->getUser();
        $produits = $this->produitRepository->findByProducteur($user->getId());
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'produits'=> $produits
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType1::class, $produit);
        $form->handleRequest($request);
        $user= $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $producteur=new Producteur();
            $producteur=$user;
            $produit->setProducteur($producteur);
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('user/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType1::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}