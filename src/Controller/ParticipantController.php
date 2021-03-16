<?php

namespace App\Controller;

use App\Entity\Participant;

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
    public function register(
        $id,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Participant();
        $participantRepository = $entityManager->getRepository(Participant::class);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $participant = $participantRepository->updateProfil($user,$id);
            if (!$participant) {
                return $this->createNotFoundException("participant incorect");
            }
            $entityManager->flush();


            return $this->redirectToRoute('participant_participant');
        }

        return $this->render('participant/update.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



}
