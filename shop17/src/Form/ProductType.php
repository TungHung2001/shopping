<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder
        //     ->add('Name')
        //     ->add('Category')
        //     ->add('Description')
        //     ->add('Price')
        // ;
        $builder
            ->add('Name', TextType::class,[
                'required' => true
            ])
           
            ->add('Description', TextType::class,[
                'required' => true
            ])
            ->add('Price', TextType::class,[
                'required' => true
            ])
            ->add('Image', FileType::class,[
                'label' => 'Product Image',
                'data_class' => null,
                'required' => is_null($builder->getData()->getImage())
            ])
            ->add('Category', EntityType::class, 
            [
                'class' => Category::class,
                'choice_label'=>'name',
                'multiple' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
