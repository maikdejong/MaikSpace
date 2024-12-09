<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'data_class' => null,
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                    ]),
                ],
                'label' => 'Nieuwe afbeelding uploaden',
                'help' => 'Laat leeg om huidige afbeelding te behouden'
            ]);
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $post = $event->getData();
            $form = $event->getForm();

            if (empty($post->getContent()) && empty($form->get('image')->getData())) {
                $form->addError(new \Symfony\Component\Form\FormError('Please fill in either the content or upload an image.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'validation_groups' => function ($form) {
                return ['Default'];
            },
        ]);
    }
}