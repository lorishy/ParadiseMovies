<?php

namespace App\Controller;
use App\Entity\Episode;
use App\Form\EditEpisodeType;
use App\Entity\Serie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;




class EpisodeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // #[Route('/vod/series/{serie_titre}_s{saison}e{episode}', name: 'episodes_show')]
    #[Route('/vod/series/{serie_titre}/{id}_{titre}', name: 'episodes_show')]
    public function show(Episode $episode): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('vod/series/episodes/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    #[Route('/vod/series/episode/{id}/edit', name: 'episodes_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, Episode $episode): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(EditEpisodeType::class, $episode, [
            // 'action' => $this->generateUrl('target_route'),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($episode);
            $this->entityManager->flush();

            return $this->redirectToRoute('episodes_edit', ['id' => $episode->getId()]);
        }

        return $this->render('vod/series/episodes/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/vod/series/episode/{id}/delete', name: 'episodes_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, Episode $episode, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('episode_deletion' . $episode->getTitre(), $request->request->get('csrf_token')))
        {
            $entityManager->remove($episode);
            $entityManager->flush();
        }
        return $this->redirectToRoute('series_index');
    }

}

  