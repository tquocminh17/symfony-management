<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class ContactType
 */
class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, [
                'attr' => [
                    'hidden' => true,
                ],
                'required' => false,
                'mapped' => false,
                'label' => false,
            ])
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('zip', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('country', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('phone', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('birthday', DateType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'years' => range(date('Y') - 1, date('Y') - 100),
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^.+@.+\..+$/',
                        'match' => true,
                        'message' => 'Please input a correct email',
                    ])
                ],
                'required' => true,
            ])
            ->add('picture', FileType::class, [
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload .png or .jpeg file',
                    ]),
                ],
                'required' => false,
                'attr' => [
                    'accept' => 'image/jpeg, image/png',
                ],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
