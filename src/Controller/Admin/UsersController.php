<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;


// #[Route('/admin/utilisateurs', name: 'admin_users_')]
class UsersController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/utilisateurs', name: 'admin_users_')]
    public function index(UserRepository $usersRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $usersRepository->findBy([], ['id' => 'asc']);
        return $this->render('admin/index.html.twig', compact('users'));
    }

    #[Route('/profil/{lastname}_{firstname}', name: 'users_show')]
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('profil/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/utilisateurs/{id}/delete', name: 'users_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('user_deletion' . $user->getId(), $request->request->get('csrf_token')))
        {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_users_');
    }
}

