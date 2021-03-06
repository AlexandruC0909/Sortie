<?php

    namespace App\Controller;

    use App\Entity\Etat;
    use App\Entity\Lieu;
    use App\Entity\Participant;
    use App\Entity\Site;
    use App\Entity\Sortie;

    use App\Form\LieuType;
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
    use Symfony\Component\HttpFoundation\JsonResponse;

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
            //Recuperation du premier etat, 'Cr??e'
            $etatRepository = $entityManager->getRepository(Etat::class);
            $etat = $etatRepository->find(1);

            //entite pour ajouter une sortie/lien
            $sortie = new Sortie();
            $lieu = new Lieu();

            //recuperation Utilisateur conect??
            $user = $this->getUser();

            //creation formulaire sortie
            $sortieForm = $this->createForm(SortieFormType::class, $sortie);
            $sortieForm->handleRequest($request);
            //creation formulaire lieu
            $lieuForm = $this->createForm(LieuType::class,$lieu);
            $lieuForm->handleRequest($request);



            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

                //si le formulaire est valide, j'ajoute le organisateur et l'etat ?? l'entit??
                $user->addOrganisateurSortie($sortie);
                $etat->addSortie($sortie);
                //ajout de l'entit?? ?? la base de donn??es
                $entityManager->persist($sortie);
                $entityManager->flush();

                return $this->redirectToRoute('sortie_list');
            }
            if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

                $entityManager->persist($lieu);
                $entityManager->flush();


            }
            return $this->render('sortie/new.html.twig',
                [
                    'sortieFormView' => $sortieForm->createView(),
                    'lieuFormView' =>$lieuForm->createView()
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
            if($sortie->getListeParticipants()->count() < $sortie->getNbInscriptionsMax()){
                $etatRepository = $entityManager->getRepository(Etat::class);
                $etat = $etatRepository->find(2);
                $etat->addSortie($sortie);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("warning","Vous avez ??t?? d??sinscit de cette sortie avec succ??s");

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
            if($sortie->getListeParticipants()->count() == $sortie->getNbInscriptionsMax()){
                $etatRepository = $entityManager->getRepository(Etat::class);
                $etat = $etatRepository->find(3);
                $etat->addSortie($sortie);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("primary","Vous avez ??t?? inscrit ?? cette sortie avec succ??s");

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
            //Recuperation du deuxieme etat, 'Ouvert'
            $etat = $entityManager->getRepository(Etat::class)->find(2);
            $sortieRepository = $entityManager->getRepository(Sortie::class);
            //Changement de l'etat
            $sortie = $sortieRepository->changerStatutSortie($etat, $id);

            if (!$sortie) {
                return $this->createNotFoundException("Aucune sortie ?? publier");
            }

            $entityManager->flush();

            $this->addFlash('success', 'La sortie ?? bien ??t?? publi??e !');
            return $this->redirectToRoute('sortie_list');

        }

        /**
         * @Route("annuler/{id}", name="annuler", priority=1000)
         */
       public function annuler(
           Request $request,
           EntityManagerInterface $entityManager,
           Sortie $sortie,
           EtatRepository $etatRepository
       )
       {



           $form = $this->createForm(RaisonAnnulationType::class, $sortie);
           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()){
               //insert dans le setMotif de l'entity Sortie, le motif r??cuperer via le formulaire
               $sortie->setMotif($form['motif']->getData());
               //modifie l'etat de la sortie en Annul??e via l'etatRepository
               $sortie->setEtat($etatRepository->findOneBy(['id' => 6]));

               //mise en bdd
               $entityManager->flush();
               //message flash une fois l'annulation effective
               $this->addFlash('danger', 'La sortie ?? ??t?? annul??e ! Le motif d\'annulation ?? ??t?? envoy?? aux participants inscrits ! ');

               return $this->redirectToRoute('sortie_list');
           }
           return $this->render('sortie/annulerSortie.html.twig', [
               'page_name' => 'Formulaire d\'annulation de la sortie',
               'sortie' => $sortie,
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

            $this->addFlash('danger', 'Sortie effac??e !');
            return $this->redirectToRoute('sortie_list');

        }

    }
