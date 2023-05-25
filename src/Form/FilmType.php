<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Categorie;
use App\Entity\Acteur;
use Symfony\Component\Form\AbstractType;
use App\Repository\ActeurRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Routing\RouterInterface;

class FilmType extends AbstractType
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 6], // nbr de lignes de la zone de texte
            ])
            ->add('duree')
            ->add('date_sortie', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de sortie',
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('video', null, [
                'empty_data' => 'https://www.youtube.com/embed/mkggXE5e2yk',
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('casting', EntityType::class, [
                'class' => Acteur::class,
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
