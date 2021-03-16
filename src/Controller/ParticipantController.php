<?php

namespace App\Controller;

use App\Entity\Participant;

use App\Form\ParticipantType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/update/participant/{id}", name="updateParticipant")
     */
    public function update(
        $id,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $participantRepository = $entityManager->getRepository(Participant::class);
        $participant = $participantRepository->find($id);

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
                $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $form->get('plainPassword')->getData()
                )
            );


            if (!$participant) {
                return $this->createNotFoundException("participant incorect");
            }
            $entityManager->persist($participant);
            $entityManager->flush();


            return $this->redirectToRoute('participant_participant',[
                'id' => $id,
            ]);
        }

        return $this->render('participant/update.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



}
