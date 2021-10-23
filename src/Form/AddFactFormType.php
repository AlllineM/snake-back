<?php

namespace App\Form;

use App\Entity\Fact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddFactFormType extends AbstractType
{
    public function getName()
    {
        return 'add_fact_form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('body', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, заполните поле',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Не меньше {{ limit }} знаков',
                        'max' => 200,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fact::class,
        ]);
    }
}
