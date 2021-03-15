<?php

namespace App\Controller;

use App\Entity\Participant;

use App\Entity\Tweet;
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
     * @Route("/delete/{id}", name="delete")
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function delete(
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
