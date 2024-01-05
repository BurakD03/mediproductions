<?php

namespace App\Form;

use App\Entity\Product\ProductVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;

class ProductVariantTypeExtensionType extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isRecurring', CheckboxType::class, [
                'label' => 'Is recurring ?',
            ])
            ->add('durationValue', IntegerType::class, [
                'label' => 'Durée',
            ])
            ->add('durationUnit', ChoiceType::class, [
                'choices' => [
                    'days' => 'days',
                    'months' => 'months',
                    'years' => 'years',
                ],
                'placeholder' => 'Sélectionnez l\'unité',
                'label' => 'Unité',
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductVariantType::class];
    }
}
