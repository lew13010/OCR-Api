<?php

namespace Api\AdvertBundle\Controller;

use Api\AdvertBundle\Entity\Advert;
use Api\AdvertBundle\Entity\Category;
use Api\AdvertBundle\Entity\City;
use Api\AdvertBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdverts();

        if(empty($adverts)){
            return new JsonResponse([
                'success'   => false,
                'code'      => 404,
                'message'   => 'Aucune annonce n\'a été trouvé',
            ]);
        }

        $array  = [];
        foreach ($adverts as $advert) {
            unset($images);
            $images = [];
            if(!is_null($advert->getImages())){
                foreach ($advert->getImages() as $image) {
                    if(!is_null($image)){
                        array_push($images, $image->getImage());

                    }
                }
            }

            array_push($array, [
                'id'            =>  $advert->getId(),
                'title'         =>  $advert->getTitle(),
                'categorie'     =>  $advert->getCategories()->getName(),
                'city'          =>  $advert->getCity()->getName(),
                'price'         =>  $advert->getPrice(),
                'description'   =>  $advert->getDescription(),
                'images'        =>  $images,
            ]);
        }

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   =>  'Les 5 dernieres Annonces',
            'annonces'  =>  $array,
            ]);
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('ApiAdvertBundle:Advert')->findOneBy(array('id' => $id));


        if(empty($advert)){
            return new JsonResponse([
                'success'   => false,
                'code'      => 404,
                'message'   => 'Aucune annonce n\'a été trouvé avec cet id',
            ]);
        }

            unset($images);
            $images = [];
            if(!is_null($advert->getImages())){
                foreach ($advert->getImages() as $image) {
                    if(!is_null($image)){
                        array_push($images, $image->getImage());
                    }
                }
            }
            $array = [
                'id'            =>  $advert->getId(),
                'title'         =>  $advert->getTitle(),
                'categorie'     =>  $advert->getCategories()->getName(),
                'city'          =>  $advert->getCity()->getName(),
                'price'         =>  $advert->getPrice(),
                'description'   =>  $advert->getDescription(),
                'images'        =>  $images,
            ];

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   =>  'Annonce id = ' .$id,
            'annonces'  =>  $array,
        ]);
    }

    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $title = ucfirst($request->query->get('title'));
        $category = $request->query->get('category');
        $city = $request->query->get('city');
        $price = (int)$request->query->get('price');
        $description = $request->query->get('description');

        $message = array();
        $error = 0;

        if(null == $title){ $error++; array_push($message, 'Veuillez saisir le parametre "title"');}

        if(null == $category){ $error++; array_push($message, 'Veuillez saisir le parametre "category"');}
        else{
            $verif = $em->getRepository('ApiAdvertBundle:Category')->findOneBy(array('slugCat' => $category));
            if(null == $verif){ $error++; array_push($message, "La categorie '$category' n'existe pas dans la BDD");}
            else{
                $category = $verif;
            }
        }

        if(null == $city){ $error++; array_push($message, 'Veuillez saisir le parametre "city"');}
        else{
            $verif = $em->getRepository('ApiAdvertBundle:City')->findOneBy(array('name' => $city));

            if(null == $verif){ $error++; array_push($message, "La ville '$city' n'existe pas dans la BDD");}
            else{
                $city = $verif;
            }
        }

        if(null != $price){
            if(!is_int($price)){ $error++; array_push($message, 'Veuillez saisir le parametre "price" en chiffre');}
        }

        if(null == $description){ $error++; array_push($message, 'Veuillez saisir le parametre "description"');}

        $advert = new Advert();
        $advert->setTitle($title);
        $advert->setCategories($category);
        $advert->setCity($city);
        $advert->setPrice($price);
        $advert->setDescription($description);
        $images = $request->files->all();
        $errorImg = 0;

        foreach ($images as $image) {
            $errorImg++;
            if(!$image->guessExtension() == ('jpg' || 'jpeg' || 'png')){
                $error++;
                array_push($message, 'Le format de fichier n°'.$errorImg.' n\'est pas accépté');
            }

            if($error > 0){
                return new JsonResponse([
                    'success'   =>  false,
                    'code'      =>  412,
                    'message'   =>  $message,
                ]);
            }

            $img = new Image();
            $img->setImage(uniqid() . '.' . $image->guessExtension());
            $advert->addImage($img);
            $image->move('../web/uploads/', $img->getImage());
            $img->setAdvert($advert);
            $em->persist($img);
        }

        if($error > 0){
            return new JsonResponse([
                'success'   =>  false,
                'code'      =>  412,
                'message'   =>  $message,
            ]);
        }

        $em->persist($advert);
        $em->flush();

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   => 'Annonce ajoutée avec succès',
            'id'        =>  $advert->getId(),
        ]);
    }

    public function editAction(Request $request, $id)
    {
        $id = (int)$id;
        $title = ucfirst($request->query->get('title'));
        $category = $request->query->get('category');
        $city = $request->query->get('city');
        $price = (int)$request->query->get('price');
        $description = $request->query->get('description');
        $images = $request->files->all();


        $error = 0;
        $errorImg = 0;
        $update = 0;
        $message = [];

        $em = $this->getDoctrine()->getManager();

        if($id == 0){
            $error++; array_push($message, "Veuillez saisir l'id de l'article");
        }
        else{
            $advert = $em->getRepository('ApiAdvertBundle:Advert')->findOneBy(array('id' => $id));
            if(empty($advert)){ $error++; array_push($message, "Aucune article n'a été trouvé avec cet 'id'");}
        }

        if(!empty($title)){ $update++; $advert->setTitle($title); }
        if(!empty($category)) {
            $verif = $em->getRepository('ApiAdvertBundle:Category')->findOneBy(array('slugCat' => $category));
            if (empty($verif)) {
                $error++;
                array_push($message, "La categorie '$category' n'existe pas pas dans la BDD");
            } else {
                $update++;
                $advert->setCategories($verif);
            }
        }

        if(!empty($city)) {
            $verif = $em->getRepository('ApiAdvertBundle:City')->findOneBy(array('name' => $city));
            if (empty($verif)) {
                $error++;
                array_push($message, "La ville '$city' n'existe pas pas dans la BDD");
            } else {
                $update++;
                $advert->setCity($verif);
            }
        }
        if(!empty($price)){ $update++; $advert->setPrice($price); }
        if(!empty($description)){ $update++; $advert->setDescription($description); }

        if(!empty($images)) {
            $update++;

            $img = $em->getRepository('ApiAdvertBundle:Image')->findOneBy(array('advert' => $id));
            foreach ($images as $image) {
                $errorImg++;
                if (!$image->guessExtension() == ('jpg' || 'jpeg' || 'png')) {
                    $error++;
                    array_push($message, 'Le format de fichier n°' . $errorImg . ' n\'est pas accépté');
                }

                if ($error > 0) {
                    return new JsonResponse([
                        'success' => false,
                        'code' => 412,
                        'message' => $message,
                    ]);
                }
                $image->move('../web/uploads/', $img->getImage());
                $em->persist($img);
            }
        }
        if($update == 0){ $error++; array_push($message, "Veuillez saisir au moins un parametre de modification");}

        if($error > 0){
            return new JsonResponse([
                'success'   =>  false,
                'code'      =>  412,
                'message'   =>  $message,
            ]);
        }

        $em->persist($advert);
        $em->flush();

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   => 'Annonce modifiée avec succès',
            'id'        =>  $advert->getId(),
        ]);


    }

    public function deleteAction($id)
    {
        $id = (int)$id;
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('ApiAdvertBundle:Advert')->findOneBy(array('id' => $id));

        if(empty($advert)) {
            return new JsonResponse([
                'success' => false,
                'code' => 412,
                'message' => 'Aucun article n\'a été trouvé avec cet id',
            ]);
        }

        $em->remove($advert);
        $em->flush($advert);
        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   => 'Annonce supprimé avec succès',
        ]);
    }

    public function newCatAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $name = ucfirst($request->query->get('name'));
        $error = 0;
        $message = [];

        if(null == $name){ $error++; array_push($message, 'Veuillez saisir le parametre "name"');}
        else{
            $verif = $em->getRepository('ApiAdvertBundle:Category')->findOneBy(array('name' => $name));
            if(null != $verif){
                { $error++; array_push($message, "La categorie '$name' existe déjà dans la BDD avec l'id : '".$verif->getId()."'");}
            }
        }

        if($error > 0){
            return new JsonResponse([
                'success'   =>  false,
                'code'      =>  412,
                'message'   =>  $message,
            ]);
        }

        $category = new Category();
        $category->setName($name);

        $em->persist($category);
        $em->flush($category);

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   => 'Categorie ajoutée avec succès',
            'id'        =>  $category->getId(),
        ]);

    }

    public function newCityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $name = ucfirst($request->query->get('name'));
        $error = 0;
        $message = [];

        $infoCity = $this->container->get('services.google_api')->verifCityFrance($name);
        if(!$infoCity){
            $error++;
            array_push($message, "La ville '$name' n'existe pas en France");
        }else{
            $verif = $em->getRepository('ApiAdvertBundle:City')->findOneBy(array('name' => $name));
            if(null != $verif){
                $error++;
                array_push($message, "La ville '$name' existe déjà dans la BDD avec l'id : '".$verif->getId()."'");
            }
        }

        if($error > 0){
            return new JsonResponse([
                'success'   =>  false,
                'code'      =>  412,
                'message'   =>  $message,
            ]);
        }

        $city = new City();
        $city->setName($name);
        $em->persist($city);
        $em->flush($city);

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   =>  'Ville ajoutée avec succès',
            'id'        =>  $city->getId(),
        ]);
    }

    public function advertCategoryAction($category, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $testInt = (int)$category;
        $error = 0;
        $message = [];
        $array  = [];
        $nbAdvertPerPage = 10;

        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCategory($category);
        $count = count($adverts);
        $totalPage = ceil($count/$nbAdvertPerPage);

        if($page <= 0 || $page > $totalPage){ $error++; array_push($message, "La page demandé n'existe pas");}
        if($testInt <> 0){ $error++; array_push($message, "Veuillez saisir le 'Slug' de la catagorie dans l'url");}
        if(empty($adverts)){ $error++; array_push($message, 'La categorie demandé est vide ou n\'existe pas dans la BDD');}

        if($error > 0){
            return new JsonResponse([
                'success'   =>  false,
                'code'      =>  412,
                'message'   =>  $message,
            ]);
        }

        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCategory($category, $page, $nbAdvertPerPage);

        foreach ($adverts as $advert) {
            unset($images);
            $images = [];
            if(!is_null($advert->getImages())){
                foreach ($advert->getImages() as $image) {
                    if(!is_null($image)){
                        array_push($images, $image->getImage());

                    }
                }
            }
            array_push($array, [
                'id'            =>  $advert->getId(),
                'title'         =>  $advert->getTitle(),
                'categorie'     =>  $advert->getCategories()->getName(),
                'city'          =>  $advert->getCity()->getName(),
                'price'         =>  $advert->getPrice(),
                'description'   =>  $advert->getDescription(),
                'images'        =>  $images,
            ]);
        }

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   =>  $count.' annonces trouvé dans la categorie : '.$category,
            'annonces'  =>  $array,
            'page'      =>  $page .'/'.$totalPage,
        ]);
    }

    public function advertCityAction($city, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $testInt = (int)$city;
        $error = 0;
        $message = [];
        $array  = [];
        $nbAdvertPerPage = 10;

        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCity($city);
        $count = count($adverts);
        $totalPage = ceil($count/$nbAdvertPerPage);

        if($page <= 0 || $page > $totalPage){ $error++; array_push($message, "La page demandé n'existe pas");}
        if($testInt <> 0){ $error++; array_push($message, "Veuillez saisir le 'nom' de la catagorie dans l'url");}
        if(empty($adverts)){ $error++; array_push($message, 'La ville demandé est vide ou n\'existe pas dans la BDD');}

        if($error > 0){
            return new JsonResponse([
                'success'   =>  false,
                'code'      =>  412,
                'message'   =>  $message,
            ]);
        }
        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCity($city, $page, $nbAdvertPerPage);

        foreach ($adverts as $advert) {
            unset($images);
            $images = [];
            if(!is_null($advert->getImages())){
                foreach ($advert->getImages() as $image) {
                    if(!is_null($image)){
                        array_push($images, $image->getImage());

                    }
                }
            }

            array_push($array, [
                'id'            =>  $advert->getId(),
                'title'         =>  $advert->getTitle(),
                'categorie'     =>  $advert->getCategories()->getName(),
                'city'          =>  $advert->getCity()->getName(),
                'price'         =>  $advert->getPrice(),
                'description'   =>  $advert->getDescription(),
                'images'        =>  $images,
            ]);
        }

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   =>  $count.' annonces trouvé dans la ville : '.$city,
            'annonces'  =>  $array,
            'page'      =>  $page .'/'.$totalPage,
        ]);
    }

    public function advertCityCategoryAction($city, $category, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $testIntCity = (int)$city;
        $testIntCategory = (int)$category;
        $error = 0;
        $message = [];
        $array  = [];
        $nbAdvertPerPage = 10;

        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCityCategory($city, $category);
        $count = count($adverts);
        $totalPage = ceil($count/$nbAdvertPerPage);

        if($page <= 0 || $page > $totalPage){ $error++; array_push($message, "La page demandé n'existe pas");}
        if($testIntCity <> 0){ $error++; array_push($message, "Veuillez saisir le 'nom' de la ville dans l'url");}
        if($testIntCategory <> 0){ $error++; array_push($message, "Veuillez saisir le 'nom' de la catagorie dans l'url");}
        if(empty($adverts)){ $error++; array_push($message, 'La "ville/categorie" demandé est vide ou n\'existe pas dans la BDD');}

        if($error > 0){
            return new JsonResponse([
                'success'   =>  false,
                'code'      =>  412,
                'message'   =>  $message,
            ]);
        }

        $adverts = $em->getRepository('ApiAdvertBundle:Advert')->findAdvertCity($city, $page, $nbAdvertPerPage);
        foreach ($adverts as $advert) {
            unset($images);
            $images = [];
            if(!is_null($advert->getImages())){
                foreach ($advert->getImages() as $image) {
                    if(!is_null($image)){
                        array_push($images, $image->getImage());

                    }
                }
            }

            array_push($array, [
                'id'            =>  $advert->getId(),
                'title'         =>  $advert->getTitle(),
                'categorie'     =>  $advert->getCategories()->getName(),
                'city'          =>  $advert->getCity()->getName(),
                'price'         =>  $advert->getPrice(),
                'description'   =>  $advert->getDescription(),
                'images'        =>  $images,
            ]);
        }

        return new JsonResponse([
            'success'   =>  true,
            'code'      =>  200,
            'message'   =>  $count.' annonces trouvé dans la ville : '.$city.' et dans la categorie: '.$category,
            'annonces'  =>  $array,
            'page'      =>  $page .'/'.$totalPage,
        ]);
    }
}
