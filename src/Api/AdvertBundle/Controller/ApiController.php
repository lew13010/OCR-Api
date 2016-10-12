<?php

namespace Api\AdvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAll();

        if(empty($adverts)){
            return new JsonResponse([
                'success'   => false,
                'code'      => 404,
                'message'   => 'Aucune annonce n\'a été trouvé',
            ]);
        }

        $array = [];
        foreach ($adverts as $advert) {
            array_push($array, [
                'id'            =>  $advert->getId(),
                'title'         =>  $advert->getTitle(),
                'categorie'     =>  $advert->getCategories()->getName(),
                'city'          =>  $advert->getCity()->getName(),
                'price'         =>  $advert->getPrice(),
                'descritpion'   =>  $advert->getDescription(),
            ]);
        }

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   =>  'Toutes les annonces',
            'annonces'  =>  $array,
            ]);
    }

    public function showAction()
    {

    }

    public function newAction()
    {

    }

    public function editAction()
    {

    }

    public function deleteAction()
    {

    }

    public function newCatAction()
    {

    }

    public function newCityAction()
    {

    }

    public function advertCategoryAction()
    {

    }

    public function AdvertCityAction()
    {

    }

    public function advertCityCategoryAction()
    {

    }
}
