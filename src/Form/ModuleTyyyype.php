<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Campain;
use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModuleeeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('semester')
            ->add('year', EntityType::class, [
                'class' => Campain::class,
                'choice_label' => 'year',
            ])
            ->add('teachers', EntityType::class, [
                // looks for choices from this entity
                'class' => Teacher::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'teachers',
                // 'mapped' => false,

                // used to render a select box, check boxes or radios
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}