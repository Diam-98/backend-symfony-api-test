<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\PostNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private PostRepository $postRepository;
    private SerializerInterface $serializer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PostRepository $postRepository
     */
    public function __construct(EntityManagerInterface $entityManager, PostRepository $postRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
        $this->serializer = $serializer;
    }


    #[Route('/posts', name: 'app_post', methods: 'GET')]
    public function index(): Response
    {
        $posts = $this->postRepository->findAll();

        $post_in_json = $this->serializer->serialize($posts, 'json');

        return new Response($post_in_json, 200);

    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/post/add', name: 'app_post_add', methods: 'POST')]
    public function addPost(Request $request, PostNotification $postNotification,  ValidatorInterface $validator): JsonResponse|Response
    {
//        Partie avec le form builder

//        $post = new Post();
//
//        $form = $this->createForm(PostType::class, $post);
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()){
//            $post = $form->getData();
//            $this->entityManager->persist($post);
//            $this->entityManager->flush();
//
//            $postNotification->sendNotification('team@devphantom.com', );
//
//            $response = $this->serializer->serialize($post, 'json');
//
//            return new Response($response, 201);
//        }
//
//        return new JsonResponse(['error' => 'Remplissez tous les champs'], 400);


        // Partie sans form builder
        $data = json_decode($request->getContent(),true);;
        $post = new Post();

        $post->setTitle($data['title']);
        $post->setDescription($data['description']);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $postNotification->sendNotification('diamil.dev@gmail.com', );

        return new Response('Post cree avec succes', 201);
    }

    #[Route('/api/post/show/{id}', name: 'app_post_show', methods: 'GET')]
    public function show(Post $post): Response
    {
        $response = $this->serializer->serialize($post, 'json');

        return new Response($response, 200);
    }


}


