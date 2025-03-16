<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ "Nom d'utilisateur" avec placeholder et sans label
            ->add('username', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Nom d\'utilisateur']
            ])
            // Champ "Adresse e-mail" avec placeholder et sans label
            ->add('email', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Adresse e-mail']
            ])
            // Champ "Mot de passe" en mode répété pour demander la saisie deux fois
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options'  => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Mot de passe',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Confirmer le mot de passe',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/[a-z]+/',
                        'message' => 'Le mot de passe doit contenir au moins une minuscule',
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]+/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule',
                    ]),
                    new Regex([
                        'pattern' => '/\d+/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre',
                    ]),
                    new Regex([
                        'pattern' => '/[^\w\d\s]+/',
                        'message' => 'Le mot de passe doit contenir au moins un caractère spécial',
                    ]),
                ],
            ])
            // Champ "agreeTerms" avec une étiquette HTML pour intégrer un lien vers les CGU
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les <a href="/conditions-generales">Conditions Générales d\'Utilisation</a>',
                'label_html' => true, // Permet d'interpréter le HTML dans le label (Symfony 5.2+)
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default'], // Exclut explicitement le groupe 'update'
        ]);
    }
}
