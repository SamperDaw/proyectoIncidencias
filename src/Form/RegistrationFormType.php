<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class RegistrationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('email', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Introduzca el correo.',
                                ]),
                    ],
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Ingrese Correo Electronico '
                    )
                ])
                ->add('Password', PasswordType::class, [
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'La contraseña es obligatoria.',
                                ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'La contraseña debe tener un mínimo de {{ limit }} carácteres.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                                ]),
                    ],
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Ingrese la Password '
                    )
                ])
                ->add("nombre", TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor, indique su nombre.',
                                ]),
                    ],
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Ingrese Nombre'
                    )
                ])
                ->add("apellidos", TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor, indique sus apellidos.',
                                ]),
                    ],
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Ingrese Apellidos ',
                    )
                ])
                ->add("telefono", TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor, indique su número de teléfono.',
                                ]),
                    ],
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Ingrese Teléfono '
                    )
                ])
                ->add("foto", FileType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor, elija una foto de perfil.',
                                ]),
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png'
                            ],
                            'mimeTypesMessage' => 'la imagen como maximo puede ser de 1mb.',
                                ])
                    ]
                ])
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'Debe aceptar los términos y condiciones.',
                                ]),
                    ],
                    'label' => " Aceptar Terminos y Condiciones ",
                ])
                ->add('Registrar', SubmitType::class, [
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }

}
