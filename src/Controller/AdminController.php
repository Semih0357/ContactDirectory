<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("app_contact");
        }
        
        $users = $manager->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'UsersController',
            'users' => $users,
        ]);
    }

    #[Route('/user/delete/{id}', name: 'app_user_delete')]
    public function deleteUser(int $id, EntityManagerInterface $manager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("app_contact");
        }
        
        $user = $manager->getRepository(User::class)->findOneBy(array('id' => $id));
        if($user == null) {
            return $this->redirectToRoute("app_admin", array('m' => "not_found"));
        }

        $manager->getRepository(User::class)->remove($user,true);
        return $this->redirectToRoute("app_admin", array('id' => $id));

    }

    #[Route('/user/{id}', name: 'app_user_edit')]
     public function edit(int $id, EntityManagerInterface $manager, Request $request): Response
     {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("app_contact");
        }
        
        $user = $manager->getRepository(User::class)->findOneBy(array('id' => $id));
        if($user == null) {
            return $this->redirectToRoute("app_admin", array('m' => "not_found"));
        }
 
         $form_user_update = $this->createForm(UserFormType::class, $user);
         $form_user_update->handleRequest($request);
 
         if($this->isGranted('ROLE_ADMIN')) {
             if($form_user_update->isSubmitted() && $form_user_update->isValid()) {
                 $manager->persist($user);
                 $manager->flush();
             }
 
         }
         return $this->render('registration/update.html.twig', [
             'controller_name' => 'ContactManagerController',
             'user' => $user,
             'form' => $form_user_update->createView(),
         ]);
     }

    #[Route('/user/setAdmin/{id}', name: 'app_user_setAdmin')]
    public function setAdmin(int $id, EntityManagerInterface $manager) : Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("app_contact");
        }

        $user = $manager->getRepository(User::class)->findOneBy(array('id' => $id));
        if($user == null) {
            return $this->redirectToRoute("app_admin");
        }

        $user->setRoles(array('ROLE_ADMIN'));

        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute("app_admin");
    }

    #[Route('/user/removeAdmin/{id}', name: 'app_user_removeAdmin')]
    public function removeAdmin(int $id, EntityManagerInterface $manager) : Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("app_contact");
        }
        
        $user = $manager->getRepository(User::class)->findOneBy(array('id' => $id));
        if($user == null) {
            return $this->redirectToRoute("app_admin");
        }
        
        if(!($this->getUser()=== $user)){
            $user->setRoles(array(''));
            $manager->persist($user);
            $manager->flush();
        }
        return $this->redirectToRoute("app_admin");
    }
}