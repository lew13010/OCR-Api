<?php

namespace Api\AdvertBundle\Form;

use Api\AdvertBundle\ApiAdvertBundle;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Api\AdvertBundle\Form\ImageType;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class, array('required' => false))
            ->add('categories', EntityType::class, array(
                'class'        => 'ApiAdvertBundle:Category',
                'choice_label' => 'name',
            ))
            ->add('city', EntityType::class, array(
                'class'         =>  'Api\AdvertBundle\Entity\City',
                'choice_label'  =>  'name',
                'multiple'      =>  false,
            ))
            ->add('images', CollectionType::class, array(
                'entry_type'    =>  ImageType::class,
                'allow_add'     =>  true,
                'allow_delete'  =>  true,
            ))
            ->add('Valider', SubmitType::class)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Api\AdvertBundle\Entity\Advert'
        ));
    }
}
