<?php

namespace JfxNinja\CMSBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use JfxNinja\CMSBundle\Entity\User;
use JfxNinja\CMSBundle\Entity\Role;
use JfxNinja\CMSBundle\Entity\FieldType;
use JfxNinja\CMSBundle\Entity\FieldSetupOptions;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Version20140512174301 extends AbstractMigration implements ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $this->createBaseCMSFields($em);

        $em->flush();
    }

    private function createBaseCMSFields($em)
    {

        //SINGLE LINE TEXT////////////////////////////////////////
        $singleLineText = new FieldType();
        $singleLineText
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Single Line Text")
            ->setVariableName("text");
        $textmaxchrs = new FieldSetupOptions();
        $textmaxchrs
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Single line text max characters")
            ->setLabel("Max Charactersrs")
            ->setInputType("text")
            ->setVariableName("textmaxchrs")
            ->setFieldType($singleLineText);
        $texttrans = new FieldSetupOptions();
        $texttrans
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Single line text translateable")
            ->setLabel("Is the text translatable")
            ->setInputType("checkbox")
            ->setVariableName("texttrans")
            ->setFieldType($singleLineText);


        //CHOICE FIELD//////////////////////////////////////////////
        $choice = new FieldType();
        $choice
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("choice")
            ->setName("Choice");
        $choiceoptions = new FieldSetupOptions();
        $choiceoptions
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Choice options")
            ->setLabel("Options")
            ->setInputType("textarea")
            ->setVariableName("choiceoptions")
            ->setFieldType($choice);
        $choiceexp = new FieldSetupOptions();
        $choiceexp
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Choice Expanded")
            ->setLabel("Choice expanded?")
            ->setInputType("checkbox")
            ->setVariableName("choiceexp")
            ->setFieldType($choice);
        $choicemulti = new FieldSetupOptions();
        $choicemulti
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Choice Multiple")
            ->setLabel("Can choose multiple options?")
            ->setInputType("checkbox")
            ->setVariableName("choicemulti")
            ->setFieldType($choice);


        //HTML FILE//////////////////////////////////////////////////
        $HTMLFile = new FieldType();
        $HTMLFile
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("htmlfile")
            ->setName("HTML File");
        $htmlfilefolder = new FieldSetupOptions();
        $htmlfilefolder
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("HTML file upload folder")
            ->setLabel("HTML file upload folder")
            ->setInputType("text")
            ->setVariableName("htmlfilefolder")
            ->setFieldType($HTMLFile);

        //FLEXI CONTENT//////////////////////////////////////////////
        $flexiContent = new FieldType();
        $flexiContent
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("flexi")
            ->setName("Flexi Content");

        //FILE UPLOAD////////////////////////////////////////////////
        $fileUpload= new FieldType();
        $fileUpload
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("fileupload")
            ->setName("File Upload");
        $fileUploadfolder = new FieldSetupOptions();
        $fileUploadfolder
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("File upload folder")
            ->setLabel("Upload folder path")
            ->setInputType("text")
            ->setVariableName("fileuploadfolder")
            ->setFieldType($fileUpload);

        //Files Folder//////////////////////////////////////////////
        $filesFolder = new FieldType();
        $filesFolder
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("filesfolder")
            ->setName("Files Folder");


        //DATE/////////////////////////////////////////////////////
        $date = new FieldType();
        $date
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("date")
            ->setName("Date");
        $datestringformat = new FieldSetupOptions();
        $datestringformat
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Date format")
            ->setLabel("Date format")
            ->setInputType("text")
            ->setVariableName("datestringformat")
            ->setFieldType($date);


        //TEXTAREA/////////////////////////////////////////////
        $textarea = new FieldType();
        $textarea
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("textarea")
            ->setName("Textarea");
        $textareatrans= new FieldSetupOptions();
        $textareatrans
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Translatable textarea")
            ->setLabel("Is this field translatable")
            ->setInputType("checkbox")
            ->setVariableName("textareatrans")
            ->setFieldType($textarea);
        $textareaCols = new FieldSetupOptions();
        $textareaCols
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Textarea columns")
            ->setLabel("Textarea columns")
            ->setInputType("text")
            ->setVariableName("textareaCols")
            ->setFieldType($textarea);
        $textareaRows = new FieldSetupOptions();
        $textareaRows
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Textarea rows")
            ->setLabel("Textarea rows")
            ->setInputType("text")
            ->setVariableName("textareaRows")
            ->setFieldType($textarea);


        //EMBEDED////////////////////////////////////////////////////
        $embeded = new FieldType();
        $embeded
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("embedded_content_model")
            ->setName("Embeded Content Type");
        $contenttype = new FieldSetupOptions();
        $contenttype
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Content Type")
            ->setLabel("Content Type")
            ->setInputType("entity")
            ->setInputTypeVar("ContentType")
            ->setVariableName("contenttype")
            ->setFieldType($embeded);


        //RELATED CONTENT ////////////////////////////////////////////
        $relatedContent = new FieldType();
        $relatedContent
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("related_content")
            ->setName("Related Content");
        $relatedcontentType = new FieldSetupOptions();
        $relatedcontentType
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Related content")
            ->setLabel("Related content")
            ->setInputType("entity")
            ->setInputTypeVar("ContentType")
            ->setVariableName("related_content")
            ->setFieldType($relatedContent);


        //CHECKBOX////////////////////////////////////////////////////
        $checkbox = new FieldType();
        $checkbox
            ->setCreatedBy("migration")
            ->setVariableName("checkbox")
            ->setModifiedBy("migration")
            ->setName("Checkbox");


        //FORM////////////////////////////////////////////////////////
        $form = new FieldType();
        $form
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("form")
            ->setName("CMS Form");
        $multilanguageform = new FieldSetupOptions();
        $multilanguageform
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("Translatable form")
            ->setLabel("Is this form available in other languages")
            ->setInputType("checkbox")
            ->setVariableName("multilanguageform")
            ->setFieldType($form);


        //Persist fileds
        $em->persist($singleLineText);
        $em->persist($choice);
        $em->persist($HTMLFile);
        $em->persist($flexiContent);
        $em->persist($fileUpload);
        $em->persist($filesFolder);
        $em->persist($date);
        $em->persist($textarea);
        $em->persist($embeded);
        $em->persist($relatedContent);
        $em->persist($checkbox);
        $em->persist($form);


        //Persist field options
        $em->persist($texttrans);
        $em->persist($textmaxchrs);


        $em->persist($textareaCols);
        $em->persist($textareaRows);
        $em->persist($textareatrans);

        $em->persist($choiceoptions);
        $em->persist($choiceexp);
        $em->persist($choicemulti);

        $em->persist($htmlfilefolder);

        $em->persist($fileUploadfolder);

        $em->persist($datestringformat);

        $em->persist($contenttype);

        $em->persist($relatedcontentType);


        $em->persist($multilanguageform);



    }
}
