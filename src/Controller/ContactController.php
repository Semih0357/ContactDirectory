<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\ContactType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if($this->isGranted('ROLE_ADMIN')) {
            $contacts = $manager->getRepository(Contact::class)->findAll();
        } else {
            $contacts = $manager->getRepository(Contact::class)->findByContactOf($this->getUser());
        }        

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'contactController',
            'contacts' => $contacts
                ]);
    }

    #[Route('/contact/new', name: 'new_contact')]
    public function create(EntityManagerInterface $manager, Request $request): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $contact= new Contact();
        $form = $this->createForm(ContactType::class, $contact);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $contact->setCreatedBy($this->getUser());
            $contact->setUser($this->getUser());

            $manager->persist($contact);
            $manager->flush();

            return $this->redirectToRoute("app_contact", array('m' => "contact_created"));

        }
        return $this->render('contact/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
