<?php

namespace App\Form;

use App\Entity\Podcasts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PodcastFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class)
            ->add('descripcion', TextareaType::class)
            ->add('imagen', FileType::class, [
                'label' => 'Seleccione una imagen',
                'mapped' => false,
                'required' => false])
            ->add('audio', FileType::class, [
                'label' => 'Seleccione el audio',
                'mapped' => false,
                'required' => false])
            ->add('crear', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Podcasts::class,
        ]);
    }
}
