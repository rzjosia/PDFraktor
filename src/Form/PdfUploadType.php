<?php

namespace App\Form;

use App\Entity\PdfUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PdfUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('document', FileType::class, [
                
                'label' => "Selectionner votre PDF",
                
                'attr' => [
                    'placeholder' => "cliquer ou glisser ici",
                    'class' => "form-control",
                    'accept' => 'application/pdf, application/x-pdf'
                ],
                
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                
                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => true,
                
                // allow multiple file
                'multiple' => false,
                
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '50000k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true,
            'csrf_token_id' => 'pdf_upload'
        ]);
    }
}
