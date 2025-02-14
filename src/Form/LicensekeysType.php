<?php

namespace App\Form;

use App\Entity\Customers;
use App\Entity\Licensekeys;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LicensekeysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('license_key')
            ->add('isActive')
            ->add('valid_until', null, [
                'widget' => 'single_text',
            ])
            ->add('customers', EntityType::class, [
                'class' => Customers::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Licensekeys::class,
        ]);
    }
}
