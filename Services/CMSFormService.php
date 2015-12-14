<?php

namespace SSone\CMSBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use SSone\CMSBundle\Entity\Content;
use SSone\CMSBundle\Form\Type\ContentTYPEfrontend;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * NavigationService
 *
 */
class CMSFormService extends EntityRepository
{


    private $locale;
    private $defaultLocale ;
    private $localiser ;
    private $em;
    private $hf;
    private $cs;
    private $bs;
    protected $requestStack;
    protected $mailer;
    protected $twigEngine;


    public function __construct(Localiser $localiser,EntityManager $em, HelperFunctions $hf, ContentService $cs, BlockService $bs, RequestStack $requestStack, \Swift_Mailer $mailer, TwigEngine $twigEngine)
    {
        $this->locale = $localiser->locale;
        $this->defaultLocale = $localiser->defaultLocale;
        $this->localiser = $localiser;
        $this->em = $em;
        $this->hf = $hf;
        $this->cs = $cs;
        $this->bs = $bs;
        $this->requestStack = $requestStack;
        $this->mailer = $mailer;
        $this->twigEngine = $twigEngine;
    }



    public function findBySecurekey($securekey)
    {
        return $this->em
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:CMSForm c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }



    public function processForms($content,$frontController,$uri)
    {

        if(isset($content) && isset($content['FORMS']))
        {
            foreach($content['FORMS'] as $variableName)
            {
               $content[$variableName]  = $this->handleForm($content[$variableName],$frontController,$uri);

            }

        }

        return $content;

    }

    private function handleForm($formId,$frontController,$uri)
    {


        $CMSForm = $this->getCMSForm($formId,$this->localiser);

        $content = new Content();

        $contentType = $this->em->getRepository('SSoneCMSBundle:ContentType')->find($CMSForm['contentTypeId']);

        $content->setContentType($contentType);

        $this->bs->contentBlockManager($content);

        $fieldsRepository = $this->em->getRepository('SSoneCMSBundle:Field');


        if($this->locale == $this->localiser->defaultLocale)
        {
            $route = "ssone_cms_frontend_noloco_post";
        }
        else
        {
            $route = "ssone_cms_frontend_post";
        }

        $formsService = $this;

        $form = $frontController->createForm(
            new ContentTYPEfrontend(
                $CMSForm['buttonText'],
                $fieldsRepository,
                $this->cs,
                $formsService,
                $this->localiser->locale
            ),
            $content,
            array('action' => $frontController->generateUrl(
                    $route,
                    array(
                        '_locale'=>$this->locale,'uri'=>ltrim($uri,"/"),
                        'contentTypeId'=>$contentType->getSecurekey(),"redirect"=>$CMSForm['successURL']
                    )
                )
            )
        );

        if($this->requestStack->getCurrentRequest()->getMethod() == "POST")
        {
            //add a content type check to handle multiple forms
            $form->handleRequest($this->requestStack->getCurrentRequest());
            if ($form->isValid())
            {
                //save content
                $blocks = $this->cs->saveContent($content,$form);
                $blocks = $blocks[$this->locale];

                //send email
                if($CMSForm['sendAdminEmailOnSubmit'])
                {

                    $message = \Swift_Message::newInstance()
                        ->setSubject($CMSForm['formTitle'] . ' form submission')
                        ->setFrom($CMSForm['adminEmailFromAddress'])
                        ->setTo($CMSForm['adminEmailToAddress'])
                        ->setBody("hello")

                    ;
                    if($CMSForm['adminEmailHTML'])
                    {
                        $message->setBody(
                            $this->twigEngine->render(
                            'SSoneCMSThemeBundle:'.$CMSForm['adminEmailHTML'],
                            array("content"=>$blocks)
                            )
                        )->setContentType("text/html");
                    }
                    $this->mailer->send($message);

                }


                $frontController->redirect = $CMSForm['successURL'];

            }
        }

        $CMSForm['settings'] = $CMSForm;
        $CMSForm['view'] = $form->createView();


        return $CMSForm;

    }


    private function getCMSForm($formId,$localiser)
    {
        $form = $this->em->getRepository('SSoneCMSBundle:CMSForm')->getCMSFormByFormId($formId,$localiser);
        return $form;
    }


}
