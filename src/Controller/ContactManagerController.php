<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactManagerController extends AbstractController
{
    #[Route('/contact/{id}', name: 'app_contact_manager')]
    public function index(int $id, EntityManagerInterface $manager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $contact = $manager->getRepository(Contact::class)->findContactById($id);

        if($contact == null) {
            return $this->redirectToRoute("app_contact", array('m' => "not_found"));
        }

        if(!$this->isGranted('ROLE_ADMIN')) {
            $contacts = $manager->getRepository(Contact::class)->findByContactOf($this->getUser());
            if(!$contacts->contains($contact)) {
                return $this->redirectToRoute("app_contact", array('m' => "not_found"));
            }
        }

        $update = null;

        $form_contact_update = $this->createForm(ContactType::class, $contact);
        $form_contact_update->handleRequest($request);

        if($contact->getCreatedBy() === $this->getUser() || $this->isGranted('ROLE_ADMIN')) {
            $update = true;
            if($form_contact_update->isSubmitted() && $form_contact_update->isValid()) {
                $manager->persist($contact);
                $manager->flush();
            }

        }

        return $this->render('contact/update.html.twig', [
            'controller_name' => 'ContactManagerController',
            'contact' => $contact,
            'update' => $update,
            'form' => $form_contact_update->createView(),
        ]);
    }



    #[Route('/contact/delete/{id}', name: 'app_contact_delete')]
    public function deleteContact(int $id, EntityManagerInterface $manager, Request $requiest): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $contact = $manager->getRepository(Contact::class)->findContactById($id);

        if($contact == null) {
            return $this->redirectToRoute("app_contact", array('m' => "not_found"));
        }

        if(!$this->isGranted('ROLE_ADMIN')) {
            $contacts = $manager->getRepository(Contact::class)->findByContactOf($this->getUser());
            if(!$contacts->contains($contact)) {
                return $this->redirectToRoute("app_contact", array('m' => "not_found"));
            }
        }

        $manager->getRepository(Contact::class)->remove($contact,true);
        //$manager->persist($contact);
        //$manager->flush();


        return $this->redirectToRoute("app_contact", array('id' => $id));

    }

}
