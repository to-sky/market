<?php

namespace MarketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class)
                ->add('body', TextareaType::class, array(
                    'attr' => [
                        'rows' => 8
                    ]
                ))
                ->add('imageFile', VichImageType::class, array(
                    'label' => 'Thumbnail',
                    'required' => false,
                    'download_link' => false
                ))
                ->add('price', MoneyType::class);
                // ->add('publishedAt', DateTimeType::class, [
                //     'placeholder' => array(
                //         'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                //         'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                //     )
                // ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MarketBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'marketbundle_product';
    }


}
