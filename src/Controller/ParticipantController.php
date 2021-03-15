<?php

namespace App\Controller;

use App\Entity\Participant;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/participant", name="participant_")
 */
class ParticipantController extends AbstractController

{
    /**
     * @Route("/{id}", name="participant")
     */
    public function index(
        $id,
        EntityManagerInterface $entityManager
    ): Response
    {
       $participantRepository = $entityManager->getRepository(Participant::class);
       $participant = $participantRepository->find($id);
        return $this->render('participant/index.html.twig', [
            'participant' => $participant,
        ]);
    }



}
