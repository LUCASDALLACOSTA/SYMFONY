<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Intervenant;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('debut', DateTimeType::class, [
                'label' => 'Date et heure de début',
                'widget' => 'single_text',
            ])
            ->add('fin', DateTimeType::class, [
                'label' => 'Date et heure de fin',
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer le cours'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
