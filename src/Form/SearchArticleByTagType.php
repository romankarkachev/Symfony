<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchArticleByTagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET')
            ->add('tagsScreen', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => function(TagRepository $tag) {
                    return $tag->createQueryBuilder('t')
                        ->orderBy('t.title');
                },
                'label' => 'Тэг: ',
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
