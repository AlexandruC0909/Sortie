<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Tweet;
use App\Entity\Ville;
use App\Form\SearchParticipantType;
use App\Form\SearchVilleType;
use App\Form\SiteType;
use App\Form\SortieFormType;
use App\Form\SortieSearchType;
use App\Form\UpdateStatutParticipantType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * @Route("/administrator", name="administrator_")
 */
class AdministratorController extends AbstractController
{
    /**
     * @Route("/", name="administrator")
     */
    public function list(
        EntityManagerInterface $entityManager
    ): Response
    {


        return $this->render('administrator/index.html.twig', [


        ]);
    }

    /**
     * @Route("/list/participants", name="listParticipants")
     */
    public function listParticipants(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {

        $participantRepository = $entityManager->getRepository(Participant::class);
        $participants = $participantRepository->findAll();

        $searchFormParticipant = $this->createForm(SearchParticipantType::class);
        $search = $searchFormParticipant->handleRequest($request);

        if ($searchFormParticipant->isSubmitted() && $searchFormParticipant->isValid()){
            $participants = $participantRepository->searchParticipant($searchFormParticipant->get('pseudo')->getData());
        }



        return $this->render('administrator/participants.html.twig', [
            'searchFormParticipant' => $searchFormParticipant->createView(),
            'participants' => $participants,
        ]);
    }


    /**
     * @Route("/list/sorties", name="listSortie")
     */
    public function listSorties(
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortieRepository = $entityManager->getRepository(Sortie::class);
        $sorties = $sortieRepository->findAll();

        return $this->render('administrator/sorties.html.twig', [

            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/list/sites", name="sites")
     */
    public function listSite(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $siteRepository = $entityManager->getRepository(Site::class);
        $sites = $siteRepository->findAll();

        $site = new Site();

        $siteForm = $this->createForm(SiteType::class, $site);
        $siteForm->handleRequest($request);


        if ($siteForm->isSubmitted() && $siteForm->isValid()) {

            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('administrator_sites');
        }
        return $this->render('administrator/sites.html.twig', [
            'formViewSite' => $siteForm->createView(),
            'sites' => $sites,
        ]);
    }

    /**
     * @Route("/list/villes", name="villes")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function listVille(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        /*-------------------  Affichage villes  -------------------------------*/
        $villesRepository = $entityManager->getRepository(Ville::class);
        $villes = $villesRepository->findAll();
        /*----------------------- Creation ville  ------------------------------*/
        $ville = new Ville();

        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);


        if ($villeForm->isSubmitted() && $villeForm->isValid()) {

            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('administrator_villes');
        }
        /*-------------------------  Recherche ville  ----------------------*/
        $VilleRepository = $entityManager->getRepository(Ville::class);
        $villes = $VilleRepository->findAll();
        $formSearchVille = $this->createForm(SearchVilleType::class);
        $search = $formSearchVille->handleRequest($request);

        if ($formSearchVille->isSubmitted() && $formSearchVille->isValid()){
            $villes = $VilleRepository->searchVille(
                $formSearchVille->get('nom')->getData()
            );
        }
        return $this->render('administrator/villes.html.twig', [
            'formSearchVille' => $formSearchVille->createView(),
            'formViewVille' => $villeForm->createView(),
            'villes' => $villes,
        ]);
    }

    /**
     * @Route("/delete/ville/{id}", name="deleteVille")
     */
    public function deleteVille(
        $id,
        EntityManagerInterface $entityManager,

        Request $request
    )
    {
        $ville = $entityManager->getRepository(Ville::class)->find($id);

        $request->getSession()->invalidate();
        if (!$ville) {
            return $this->createNotFoundException("no ville to delete found");
        }


        $entityManager->remove($ville);
        $entityManager->flush();

        $this->addFlash('success', 'cville deleted!.');
        return $this->redirectToRoute('administrator_villes');

    }

    /**
     * @Route("/delete/site/{id}", name="deleteSite")
     */
    public function deleteSite(
        $id,
        EntityManagerInterface $entityManager,

        Request $request
    )
    {
        $site = $entityManager->getRepository(Site::class)->find($id);

        $request->getSession()->invalidate();
        if (!$site) {
            return $this->createNotFoundException("no site to delete found");
        }


        $entityManager->remove($site);
        $entityManager->flush();

        $this->addFlash('success', 'conmpte deleted!.');
        return $this->redirectToRoute('administrator_sites');

    }


    /**
     * @Route("/{id}", name="detail")
     */
    public function detail(
        $id,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $participantRepository = $entityManager->getRepository(Participant::class);
        $participant = $participantRepository->find($id);
        $formUpdateParticipant = $this->createForm(UpdateStatutParticipantType::class, $participant);
        $formUpdateParticipant->handleRequest($request);
        if ($formUpdateParticipant->isSubmitted() && $formUpdateParticipant->isValid()) {
            if (!$participant) {
                return $this->createNotFoundException("participant incorect");
            }
            $entityManager->persist($participant);
            $entityManager->flush();
            return $this->redirectToRoute('administrator_detail',
                [
                    'id' => $id,
                    'participant' => $participant,
                    'updateSortieForm' => $formUpdateParticipant->createView()
                ]);
        }
        return $this->render('administrator/detailsParticipant.html.twig', [
            'participant' => $participant,
            'updateSortieForm' => $formUpdateParticipant->createView()
        ]);
    }

    /**
     * @Route("/delete/participant/{id}", name="personne")
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deletePersonne(
        $id,
        EntityManagerInterface $entityManager,

        Request $request
    )
    {
        $participant = $entityManager->getRepository(Participant::class)->find($id);

        $request->getSession()->invalidate();
        if (!$participant) {
            return $this->createNotFoundException("no participant to delete found");
        }


        $entityManager->remove($participant);
        $entityManager->flush();

        $this->addFlash('success', 'conmpte deleted!.');
        return $this->redirectToRoute('administrator_administrator');

    }
}




