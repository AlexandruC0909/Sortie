<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;

use App\Entity\Site;
use App\Entity\Tweet;
use App\Entity\Ville;
use App\Form\SiteType;
use App\Form\SortieFormType;
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
        $ParticipantRepository = $entityManager->getRepository(Participant::class);
        $participants = $ParticipantRepository->findAll();

        return $this->render('administrator/index.html.twig', [

            'participants' => $participants,
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

        $siteForm = $this->createForm(SiteType::class,$site);
        $siteForm->handleRequest($request);


        if ($siteForm->isSubmitted() && $siteForm->isValid()) {

            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('administrator_sites');
        }
        return $this->render('administrator/sites.html.twig', [
            'formViewSite'=>$siteForm->createView(),
            'sites' => $sites,
        ]);
    }
    /**
     * @Route("/list/villes", name="villes")
     */
    public function listVille(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $villesRepository = $entityManager->getRepository(Ville::class);
        $villes = $villesRepository->findAll();

        $ville = new Ville();

        $villeForm = $this->createForm(VilleType::class,$ville);
        $villeForm->handleRequest($request);


        if ($villeForm->isSubmitted() && $villeForm->isValid()) {

            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('administrator_villes');
        }
        return $this->render('administrator/villes.html.twig', [
            'formViewVille'=>$villeForm->createView(),
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
        EntityManagerInterface $entityManager
    ): Response
    {
        $participantRepository = $entityManager->getRepository(Participant::class);
        $participant = $participantRepository->find($id);
        return $this->render('administrator/detailsParticipant.html.twig', [
            'participant' => $participant,
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
