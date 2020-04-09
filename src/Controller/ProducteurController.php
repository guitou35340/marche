<?php

namespace App\Controller;

use App\Entity\Producteur;
use App\Form\ProducteurType;
use App\Repository\ProducteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/producteur")
 * @IsGranted("ROLE_ADMIN")
 */
class ProducteurController extends AbstractController
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
    $this->passwordEncoder=$passwordEncoder;
    }


    /**
     * @Route("/", name="producteur_index", methods={"GET"})
     */
    public function index(ProducteurRepository $producteurRepository): Response
    {
        return $this->render('admin/producteur/index.html.twig', [
            'producteurs' => $producteurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="producteur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $producteur = new Producteur();
        $form = $this->createForm(ProducteurType::class, $producteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $producteur->setPassword($this->passwordEncoder->encodePassword($producteur,$producteur->getPassword()));
            $entityManager->persist($producteur);
            $entityManager->flush();

            return $this->redirectToRoute('producteur_index');
        }

        return $this->render('admin/producteur/new.html.twig', [
            'producteur' => $producteur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="producteur_show", methods={"GET"})
     */
    public function show(Producteur $producteur): Response
    {
        return $this->render('admin/producteur/show.html.twig', [
            'producteur' => $producteur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="producteur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Producteur $producteur): Response
    {
        $form = $this->createForm(ProducteurType::class, $producteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('producteur_index');
        }

        return $this->render('admin/producteur/edit.html.twig', [
            'producteur' => $producteur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="producteur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Producteur $producteur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$producteur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($producteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('producteur_index');
    }
}
