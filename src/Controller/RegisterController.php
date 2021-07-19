<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        // Instancie la class User
        $user = new User();

        // Je créer le form + injecte la class RegisterType + je lui passe l'objet $user
        $form = $this->createForm(RegisterType::class, $user);

        // Je passe la méthode HandleRequest 
        $form->handleRequest($request);

        // Est-ce que le form et soumis ? et valide ?
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            // Encoder le MDP 
            $password = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // J'enregistre en BDD 

        }

        // Je passe le formulaire en variable à ma template
        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
