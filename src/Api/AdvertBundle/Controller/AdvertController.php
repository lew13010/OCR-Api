<?php

namespace Api\AdvertBundle\Controller;

use Api\AdvertBundle\Entity\Category;
use Api\AdvertBundle\Entity\City;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Api\AdvertBundle\Entity\Advert;

/**
 * Advert controller.
 *
 */
class AdvertController extends Controller
{
    /**
     * Lists all Advert entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAll();

        return $this->render('ApiAdvertBundle:Advert:index.html.twig', array(
            'adverts' => $adverts,
        ));
    }

    public function advertCategoryAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCategory($category);

        return $this->render('ApiAdvertBundle:Advert:index.html.twig', array(
            'adverts' => $adverts,
        ));
    }

    public function advertCityAction(Request $request, City $city)
    {
        $em = $this->getDoctrine()->getManager();
        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCity($city);

        return $this->render('ApiAdvertBundle:Advert:index.html.twig', array(
            'adverts' => $adverts,
        ));
    }

    public function advertCityCategoryAction(Request $request, City $city, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCityCategory($city, $category);

        return $this->render('ApiAdvertBundle:Advert:index.html.twig', array(
            'adverts' => $adverts,
        ));
    }

    /**
     * Creates a new Advert entity.
     *
     */
    public function newAction(Request $request)
    {
        $advert = new Advert();
        $form = $this->createForm('Api\AdvertBundle\Form\AdvertType', $advert);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($advert->getImages() as $image) {
                $image->setAdvert($advert);
            }
            //die(var_dump('lol'));
            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('advert_show', array('slug' => $advert->getSlug()));
        }

        return $this->render('ApiAdvertBundle:Advert:new.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Advert entity.
     *
     */
    public function showAction(Advert $advert)
    {
        $deleteForm = $this->createDeleteForm($advert);

        return $this->render('ApiAdvertBundle:Advert:show.html.twig', array(
            'advert' => $advert,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Advert entity.
     *
     */
    public function editAction(Request $request, Advert $advert)
    {
        $deleteForm = $this->createDeleteForm($advert);
        $editForm = $this->createForm('Api\AdvertBundle\Form\AdvertType', $advert);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($advert->getImages() as $image) {
                $image->setAdvert($advert);
            }
            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('advert_edit', array('id' => $advert->getId()));
        }

        return $this->render('ApiAdvertBundle:Advert:edit.html.twig', array(
            'advert' => $advert,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Advert entity.
     *
     */
    public function deleteAction(Request $request, Advert $advert)
    {
        $form = $this->createDeleteForm($advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($advert);
            $em->flush();
        }

        return $this->redirectToRoute('advert_index');
    }

    /**
     * Creates a form to delete a Advert entity.
     *
     * @param Advert $advert The Advert entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Advert $advert)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('advert_delete', array('id' => $advert->getId())))
            ->setMethod('DELETE')
            ->add('Supprimer', SubmitType::class)
            ->getForm()
        ;
    }
}
