<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\Type\ContactType;
use AppBundle\Service\FileManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactController
 * @package AppBundle\Controller
 */
class ContactController extends Controller
{
    /**
     * @Route("/contacts", name="contact_show")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $list = $this->getDoctrine()->getRepository(Contact::class)->findAll();
        return $this->render('contact/list.html.twig', ['contacts' => $list]);
    }

    /**
     * @Route("/contacts/create", name="contact_create")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        return $this->upsert($request);
    }

    /**
     * @Route("/contacts/update/{slug}", name="contact_update")
     *
     * @param Request $request
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, string $slug)
    {
        return $this->upsert($request, $slug);
    }

    /**
     * @Route("/contacts/delete/{slug}", name="contact_delete")
     *
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(string $slug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var Contact $contact */
        $contact = $entityManager->getRepository(Contact::class)->find($slug);

        try {
            if ($contact->getPicture()) {
                $this->container->get(FileManager::class)
                    ->deleteFile($this->getPictureDestination() . '/' . $contact->getPicture());
            }

            $entityManager->remove($contact);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                sprintf('The contact (id=%s) has been deleted successfully!', $slug)
            );
        } catch (\Exception $exception) {
            $this->addFlash(
                'error',
                sprintf('The contact (id=%s) has been deleted failed!', $slug)
            );
        }

        return $this->redirectToRoute('contact_show');
    }

    /**
     * @Route("/contacts/upsert", name="contact_upsert", )
     *
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function upsert(Request $request, $id = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $id ? $entityManager->getRepository(Contact::class)->find($id) : new Contact();
        $originalPicture = $contact->getPicture();
        $form = $this->getForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var Contact $contact */
                $contact = $form->getData();
                /** @var UploadedFile $picture */
                $picture = $form['picture']->getData();

                if ($picture) {
                    $contact->setPicture($this->uploadPicture($picture));

                    if ($originalPicture) {
                        $this->container->get(FileManager::class)
                            ->deleteFile($this->getPictureDestination() . '/' . $originalPicture);
                    }
                }

                $entityManager->persist($contact);
                $entityManager->flush();
                $this->addFlash(
                    'notice',
                    $id
                        ? sprintf('The contact (id=%s) has been updated successfully!', $id)
                        : 'The contact has been created successfully!'
                );

                return $this->redirectToRoute('contact_show');
            } else {
                $this->addFlash(
                    'error',
                    'The submitted information was incorrect!'
                );
            }
        }

        return $this->render('contact/form.html.twig', [ 'form' => $form->createView(), 'contact' => $contact]);
    }

    /**
     * @param UploadedFile $picture
     * @return string|null
     */
    private function uploadPicture(UploadedFile $picture)
    {
        /** @var FileManager $fileManager */
        $fileManager = $this->container->get(FileManager::class);
        try {
            return $fileManager->uploadFile($picture, $this->getPictureDestination());
        } catch (FileException $e) {
            $this->addFlash(
                'error',
                'The file uploaded failed!'
            );
        }

        return null;
    }

    /**
     * @param Contact $contact
     * @return \Symfony\Component\Form\FormInterface
     */
    private function getForm(Contact $contact)
    {
        return $this->createForm(ContactType::class, $contact, [
            'method' => 'PUT',
            'action' => $contact->getId()
                ? $this->generateUrl('contact_update', ['slug' => $contact->getId()])
                : $this->generateUrl('contact_create'),
        ]);
    }

    /**
     * @return string
     */
    private function getPictureDestination()
    {
        return realpath($this->container->getParameter('kernel.root_dir').'/../web/')
            . '/' . $this->getParameter('app.uploaded_picture_directory');
    }
}
