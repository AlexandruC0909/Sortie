<?php

namespace App\Controller;

use App\Entity\Participant;

use App\Form\ParticipantType;
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
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return Response
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
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function update(
        $id,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $participant = $this->getUser();
        $form = $this->createForm(ParticipantType::class, $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $originalPassword = $participant->getPassword();
            if (!$participant) {
                return $this->createNotFoundException("participant incorect");
            }
            if (!empty($form['password']->getData())) {
                $participant->setPassword(
                    $passwordEncoder->encodePassword(
                        $participant,
                        $form['password']->getData()
                    )
                );
            } else {
                // set old password

                $participant->setPassword($originalPassword);
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
