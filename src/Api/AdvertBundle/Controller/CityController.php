<?php

namespace Api\AdvertBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Api\AdvertBundle\Entity\City;
use Api\AdvertBundle\Form\CityType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * City controller.
 *
 */
class CityController extends Controller
{
    /**
     * Lists all City entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cities = $em->getRepository('ApiAdvertBundle:City')->findAll();

        return $this->render('ApiAdvertBundle:City:index.html.twig', array(
            'cities' => $cities,
        ));
    }

    /**
     * Creates a new City entity.
     *
     */
    public function newAction(Request $request)
    {
        $city = new City();
        $form = $this->createForm('Api\AdvertBundle\Form\CityType', $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $city->setName(ucfirst($_POST['city']['name']));
            $infoCity = $this->container->get('services.google_api')->verifCityFrance($city->getName());
            if ($infoCity) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($city);
                $em->flush();

                return $this->redirectToRoute('city_show', array('id' => $city->getId()));
            }
            else{
                $form->get('name')->addError(new FormError('La Ville n\'existe pas'));
            }
        }

        return $this->render('ApiAdvertBundle:City:new.html.twig', array(
            'city' => $city,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a City entity.
     *
     */
    public function showAction(City $city)
    {
        $deleteForm = $this->createDeleteForm($city);

        return $this->render('ApiAdvertBundle:City:show.html.twig', array(
            'city' => $city,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing City entity.
     *
     */
    public function editAction(Request $request, City $city)
    {
        $deleteForm = $this->createDeleteForm($city);
        $editForm = $this->createForm('Api\AdvertBundle\Form\CityType', $city);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();

            return $this->redirectToRoute('city_edit', array('id' => $city->getId()));
        }

        return $this->render('ApiAdvertBundle:City:edit.html.twig', array(
            'city' => $city,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a City entity.
     *
     */
    public function deleteAction(Request $request, City $city)
    {
        $form = $this->createDeleteForm($city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($city);
            $em->flush();
        }

        return $this->redirectToRoute('city_index');
    }

    /**
     * Creates a form to delete a City entity.
     *
     * @param City $city The City entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(City $city)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('city_delete', array('id' => $city->getId())))
            ->setMethod('DELETE')
            ->add('Supprimer', SubmitType::class)
            ->getForm()
        ;
    }
}
