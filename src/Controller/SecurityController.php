<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher){
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route('/api/register', name: 'app_register', methods: 'POST')]
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);

        $email = $data['email'];
        $password = $data['password'];

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user){
            return new JsonResponse([
                'status'=>false,
                'message'=>'Cet email existe deja!'
            ]);
        }

        $user = new User();
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'status'=>true,
            'data' => $user,
            'message'=>'Compte cree avec succes'
        ]);

    }
}
