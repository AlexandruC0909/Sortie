<?php

    namespace App\Controller;

    use App\Entity\Etat;
    use App\Entity\Lieu;
    use App\Entity\Participant;
    use App\Entity\Site;
    use App\Entity\Sortie;

    use App\Form\ParticipantType;
    use App\Form\RaisonAnnulationType;
    use App\Form\SortieFormType;

    use App\Form\SortieSearchType;
    use App\Repository\EtatRepository;
    use App\Repository\SortieRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
            $participant = $this->getUser();

            if ($formSearch->isSubmitted() && $formSearch->isValid()) {
                if ($search->get('organisateur')->getData()) {
                    $organisateurId = $this->getUser();
                } else {
                    $organisateurId = null;
                }
                if ($search->get('participant')->getData()) {
                    $participantId = $this->getUser();
                } else {
                    $participantId = null;
                }
                if ($search->get('notParticipant')->getData()) {
                    $notParticipantId = $this->getUser();
                } else {
                    $notParticipantId = null;
                }
                $sorties = $SortieRepository->search(
                    $search->get('nom')->getData(),
                    $search->get('site')->getData(),
                    $organisateurId,
                    $participantId,
                    $notParticipantId,
                    $search->get('dateInf')->getData(),
                    $search->get('dateSup')->getData()
                );
            }
            return $this->render('sortie/index.html.twig', [
                'formSearch' => $formSearch->createView(),
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
        ): Response
        {
            $sortieRepository = $entityManager->getRepository(Sortie::class);
            $sortie = $sortieRepository->find($id);

            return $this->render('sortie/detail.html.twig',
                [
                    'sortie' => $sortie,
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

            $sortieForm = $this->createForm(SortieFormType::class, $sortie);
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

            $this->addFlash("warning","Vous avez été désinscit de cette sortie avec succès");

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

            $this->addFlash("primary","Vous avez été inscrit à cette sortie avec succès");

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
            $sortie = $sortieRepository->changerStatutSortie($etat, $id);

            if (!$sortie) {
                return $this->createNotFoundException("Aucune sortie à publier");
            }

            $entityManager->flush();

            $this->addFlash('success', 'La sortie à bien été publiée !');
            return $this->redirectToRoute('sortie_list');

        }

        /**
         * @Route("annuler/{id}", name="annuler", priority=1000)
         */
       public function annuler(Request $request, EntityManagerInterface $entityManager, Sortie $sortie, EtatRepository $etatRepository)
       {

           $participant = $this->getUser();

           $form = $this->createForm(RaisonAnnulationType::class, $sortie);
           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()){
               $sortie->setMotif($form['motif']->getData());
               $sortie->setEtat($etatRepository->findOneBy(['id' => 6]));

               $entityManager->flush();
               $this->addFlash('danger', 'La sortie à été annulée ! Le motif d\'annulation à été envoyé aux participants inscrits ! ');

               return $this->redirectToRoute('sortie_list');
           }
           return $this->render('sortie/annulerSortie.html.twig', [
               'page_name' => 'Formulaire d\'annulation de la sortie',
               'sortie' => $sortie,
               'participants' => $participant,
               'form' => $form->createView()
           ]);
       }

        /**
         * @Route("/update/sortie/{id}", name="updateSortie")
         */
        public function update(
            $id,
            Request $request,
            EntityManagerInterface $entityManager
        )
        {

            $sortieRepository = $entityManager->getRepository(Sortie::class);
            $sortie = $sortieRepository->find($id);

            $form = $this->createForm(SortieFormType::class, $sortie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {


                if (!$sortie) {
                    return $this->createNotFoundException("participant incorrect");
                }
                $entityManager->persist($sortie);
                $entityManager->flush();


                return $this->redirectToRoute('sortie_list', [
                    'id' => $id,
                ]);
            }

            return $this->render('sortie/update.html.twig', [
                'updateSortieForm' => $form->createView(),
            ]);
        }

        /**
         * @Route("/delete/sortie/{id}", name="deleteSortie")
         */
        public function delete(
            $id,
            EntityManagerInterface $entityManager

        )
        {
            $sortie = $entityManager->getRepository(Sortie::class)->find($id);
            if (!$sortie) {
                return $this->createNotFoundException("no sortie to delete found");
            }


            $entityManager->remove($sortie);
            $entityManager->flush();

            $this->addFlash('danger', 'Sortie effacée !');
            return $this->redirectToRoute('sortie_list');

        }

    }
