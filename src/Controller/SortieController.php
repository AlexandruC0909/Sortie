<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;

use App\Form\SortieFormType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{

    /**
     * @Route("/", name="list")
     */
    public function list(
        EntityManagerInterface $entityManager
    ): Response
    {
        $SortieRepository = $entityManager->getRepository(Sortie::class);
        $sorties = $SortieRepository->findAll();

        return $this->render('sortie/index.html.twig', [

            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/{id}", name="detail")
     */
    public function detail(
    $id,
    EntityManagerInterface $entityManager,
    Request $request
    ):Response{
        $sortieRepository = $entityManager->getRepository(Sortie::class);
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/detail.html.twig',
            [
                'sortie'=>$sortie,
            ]
        );
    }

    /**
     *
     * @Route("/new", name="newSortie", priority=500)
     */
    public function new(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $sortie = new Sortie();

        $user = $this->getUser();
        $user->addOrganisateurSortie($sortie);

        $sortieForm = $this->createForm(SortieFormType::class,$sortie);
        $sortieForm->handleRequest($request);


        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_list');
        }
        return $this->render('sortie/new.html.twig',
            [
                'sortieFormView' => $sortieForm->createView(),
            ]
        );
    }
}
