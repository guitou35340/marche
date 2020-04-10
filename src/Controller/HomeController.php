<?php

namespace App\Controller;

use App\Entity\Producteur;
use App\Repository\ProducteurRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $produitRepository;
    private $producteurRepository;

    public function __construct(ProduitRepository $produitRepository,ProducteurRepository $producteurRepository )
    {
        $this->produitRepository= $produitRepository;
        $this->producteurRepository = $producteurRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',

        ]);
    }

    /**
     * @Route("/home/cafe", name="home_pascal")
     */
    public function cafepascal()
    {
        return $this->render('home/cafepascal.html.twig', [
            'controller_name' => 'HomeController',

        ]);
    }

    /**
     * @Route("/home", name="home_index")
     */
    public function producteurlist()
    {
        return $this->render('home/producteur.html.twig',[
            'producteurs'=> $this->producteurRepository->findAll()
        ]);
    }

    /**
     * @Route("/homes/{id}", name="home_showlist", methods={"GET"})
     */
    public function showlist(Producteur $producteur): Response
    {
        $produits=$this->produitRepository->findByProducteur($producteur->getId());
        return $this->render('home/produitslist.html.twig',[
            'produits'=> $produits,
            'producteur' => $producteur,
        ]);

    }

    /**
     * @Route("/home/{id}", name="home_show", methods={"GET"})
     */
    public function show(Producteur $producteur): Response
    {
        $produits=$this->produitRepository->findByProducteur($producteur->getId());
        return $this->render('home/produits.html.twig',[
            'produits'=> $produits,
            'producteur' => $producteur,
        ]);

    }


}
