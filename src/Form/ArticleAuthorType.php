<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleAuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Заголовок: ',
                'attr' => ['placeholder' => 'Введите текст заголовка']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Текст статьи: ',
                'attr' => ['placeholder' => 'Введите текст статьи', 'rows' => 5],
            ])
            ->add('available_from', DateType::class, ['label' => 'Опубликована с: '])
            ->add('authorScreen', EntityType::class, [
                'class' => Author::class,
                'query_builder' => function(AuthorRepository $author) {
                    return $author->createQueryBuilder('a')
                        ->orderBy('a.name');
                },
                'label' => 'Автор: ',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
