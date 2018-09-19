<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleTaggedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Заголовок: ',
                'attr' => ['placeholder' => 'Введите текст заголовка'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Текст статьи',
                'attr' => ['placeholder' => 'Введите текст статьи', 'rows' => 5],
            ])
            ->add('available_from', DateType::class, ['label' => 'Опубликована с'])
            ->add('tagsScreen', TextType::class, [
                'label' => 'Ключевые слова: ',
                'attr' => ['placeholder' => 'Введите ключевые слова', 'title' => 'Ключевые слова вводятся через запятую, принимается только три первых'],
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
