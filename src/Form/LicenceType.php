<?php

namespace App\Form;

use App\Entity\Order\Order;
use App\Entity\Licence\Licence;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LicenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('startedAt',  TextType::class, [
            //     'attr' => [
            //         'placeholder' => 'Start',
            //     ]
            // ])
            // ->add('endedAt',  TextType::class, [
            //     'attr' => [
            //         'placeholder' => 'End',
            //     ]
            // ])
            ->add('startedAt',  DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Start',
                    // 'type' => 'text'
                ]
            ])
            ->add('endedAt',  DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'End',
                ]
            ])
            ->add('platform',  ChoiceType::class, [
                'choices'  => [
                    'CRM' => 'CRM',
                    'Shop' => 'Shop',
                ],
            ])
            ->add('demo')
            ->add('state', ChoiceType::class, [
                'choices'  => [
                    'In progress' => 'In progress',
                    'Completed' => 'Completed',
                ],
            ])
            ->add('codeCrm')
            // ->add('createdAt')
            // ->add('updatedAt')
            ->add('syliusOrder', EntityType::class, [
                'class' => Order::class,
                'choice_label' => 'number',
                'by_reference' => true,
                'disabled' => true
            ])
            ->add('syliusProductVariant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Licence::class,
        ]);
    }
}
