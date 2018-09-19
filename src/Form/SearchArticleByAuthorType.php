<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchArticleByAuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET')
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
