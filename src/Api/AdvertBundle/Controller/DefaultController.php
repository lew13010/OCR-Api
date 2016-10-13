<?php

namespace Api\AdvertBundle\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdverts();
        $categories = $em->getRepository('ApiAdvertBundle:Category')->findAll();
        $cities = $em->getRepository('ApiAdvertBundle:City')->findAll();

        $form = $this->createFormBuilder()
            ->add('category', EntityType::class, array(
                'class'         => 'Api\AdvertBundle\Entity\Category',
                'choice_label'  => 'name',
                'choice_value'  => 'slugCat',
            ))
            ->add('city', EntityType::class, array(
                'class'         => 'Api\AdvertBundle\Entity\City',
                'choice_label'  => 'name',
                'choice_value'  => 'name',
            ))
            ->add('chercher', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $city = $_POST['form']['city'];
            $category = $_POST['form']['category'];
            return $this->redirectToRoute('advert_city_category', array('city' => $city, 'category' => $category));
        }


        return $this->render('ApiAdvertBundle:Default:index.html.twig',array(
            'adverts'       => $adverts,
            'categories'    => $categories,
            'cities'        => $cities,
            'form'          => $form->createView()
        ));
    }
}
