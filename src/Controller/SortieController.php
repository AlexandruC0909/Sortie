<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;

use App\Form\SortieFormType;

use App\Form\SortieSearchType;
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
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $SortieRepository = $entityManager->getRepository(Sortie::class);
        $sorties = $SortieRepository->findAll();
        $formSearch = $this->createForm(SortieSearchType::class);
        $search = $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()){
            $sorties = $SortieRepository->search(
                $search->get('nom')->getData(),
                $search->get('site')->getData()
            );
        }
        return $this->render('sortie/index.html.twig', [
            'formSearch' =>$formSearch->createView(),
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
        $etatRepository = $entityManager->getRepository(Etat::class);
        $etat = $etatRepository->find(1);
        $sortie = new Sortie();
        $lieu = new Lieu();

        $user = $this->getUser();
        $lieu->addSortie($sortie);
        $user->addOrganisateurSortie($sortie);
        $etat->addSortie($sortie);

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
    /**
     *
     * @Route("/desister/{id}", name="desister",priority=1000)
     */
    public function desister(
        $id,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortieRepository = $entityManager->getRepository(Sortie::class);
        $sortie = $sortieRepository->find($id);

        $user = $this->getUser();
        $user->removeInscritSortie($sortie);
        $entityManager->persist($user);
        $entityManager->flush();


        return $this->redirectToRoute('sortie_list');
    }

    /**
     *
     * @Route("/participer/{id}", name="participer",priority=1000)
     */
    public function participer(
        $id,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortieRepository = $entityManager->getRepository(Sortie::class);
        $sortie = $sortieRepository->find($id);

        $user = $this->getUser();
        $user->addInscritSortie($sortie);
        $entityManager->persist($user);
        $entityManager->flush();


        return $this->redirectToRoute('sortie_list');
    }

    /**
     *
     * @Route("/publier/{id}", name="publier",priority=1000)
     */
    public function publier(
        $id,
        EntityManagerInterface $entityManager,

        Request $request
    )
    {
        $etat = $entityManager->getRepository(Etat::class)->find(2);
        $sortieRepository = $entityManager->getRepository(Sortie::class);
        $sortie = $sortieRepository->changerStatutSortie($etat,$id);

        if (!$sortie) {
            return $this->createNotFoundException("no sortie a publie");
        }

        $entityManager->flush();

        $this->addFlash('success', '$sortie publier!.');
        return $this->redirectToRoute('sortie_list');

    }
    /**
     *
     * @Route("/anuler/{id}", name="anuler",priority=1000)
     */
    public function anuler(
        $id,
        EntityManagerInterface $entityManager,

        Request $request
    )
    {
        $etat = $entityManager->getRepository(Etat::class)->find(3);
        $sortieRepository = $entityManager->getRepository(Sortie::class);
        $sortie = $sortieRepository->changerStatutSortie($etat,$id);

        if (!$sortie) {
            return $this->createNotFoundException("no sortie a anuller");
        }

        $entityManager->flush();

        $this->addFlash('success', '$sortie anuler!.');
        return $this->redirectToRoute('sortie_list');

    }
}
