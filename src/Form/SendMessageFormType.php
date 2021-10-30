<?php

namespace App\Form;

use App\Entity\Fact;
use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType
;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SendMessageFormType extends AbstractType
{
    public function getName()
    {
        return 'send_message_form';
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
                        'min' => 1,
                        'minMessage' => 'Не меньше {{ limit }} знаков',
                        'max' => 1000,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
