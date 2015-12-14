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
class ModuleService extends EntityRepository
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

    public $activeMenuItem;
    public $activeRoot;
    public $mappedActiveMenuItem;
    public $navigationMap;
    public $templateNavigationMap;
    public $domainTemplate;

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
                'SELECT c FROM SSoneCMSBundle:Module c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Field Types";
        $data['columns'] = array(
            "Name"=>"name",
            "URL Match"=>"urlMatchExpression",
            "Last Modified"=>"modifiedAt",
            "Last Modified By"=>"modifiedBy",
        );
        $data['items'] = $this->em
            ->createQuery(
                'SELECT
                c.name,
                c.id,
                c.securekey,
                c.urlMatchExpression,
                c.modifiedAt,
                c.modifiedBy
                FROM SSoneCMSBundle:Module c
                ORDER BY c.name ASC'
            )
            ->getResult();

        foreach($data['items'] as &$item)
        {
            $item = $this->localiser->setMultiLanguageFields($item,array('urlMatchExpression'),$this->defaultLocale);
        }

        return $data;
    }


    /**
     *
     * Modules array("A"=>array(modules),"B"=>array(modules) etc...)
     *
     * @param $url
     * @param $frontController
     * @return array
     */
    public function getURLModules($url,$frontController)
    {

        $url = "/".$url;

        $templateModules = array();

        $modules = $this->em
            ->createQuery(
                'SELECT
                m.name,
                m.title,
                m.description,
                m.sort,
                m.type,
                m.urlMatchExpression,
                m.resultsLimit,
                m.templatePosition,
                m.templatePath,
                ct.id AS contentTypeId,
                c.id AS contentId,
                f.id AS formId,
                m.contentFilterField,
                m.contentFilterValue,
                m.contentOrderByField,
                m.contentOrderByValue
                FROM SSoneCMSBundle:Module m
                LEFT JOIN m.contentType ct
                LEFT JOIN m.form f
                LEFT JOIN m.singleContentItem c'
            )

            ->getResult();

        foreach($modules as $k=>$m)
        {
            $match = $this->urlMatch($url,$m['urlMatchExpression'],$this->localiser->locale);
            if($match)
            {
                $module = $this->buildModule($m,$frontController,$url);

                $templateModules[$module['settings']['templatePosition']][$k] = $module;

            }
        }

        return $templateModules;

    }



    private function urlMatch($url,$pattern,$locale)
    {

        if(!isset($pattern[$locale])) return false;
        $pattern = $pattern[$locale];

        $pattern = str_replace("*","(.*)",$pattern);
        $pattern = str_replace("/","\\/",$pattern);


        $match = preg_match("/".$pattern."/",$url);

        return $match;

    }

    private function buildModule($m,$frontController,$url)
    {

        $module = array();

        $module['settings'] = $this->getModuleSettings($m);


        if($m['type'] == "content")
        {
            $module['content'] = $this->getModuleContent($m);
        }
        elseif($m['type'] == "form")
        {

            $module['form'] = $this->getModuleForm($m,$frontController,$url);

            if(!$module['settings']['templatePath'])
                $module['settings']['templatePath'] = $module['form']['settings']['template'];

        }




        return $module;
    }



    private function getModuleSettings($m)
    {

        $m=$this->localiser->setMultiLanguageFields($m,array("title","description"),$this->localiser->locale);

        return $m;

    }


    private function getModuleContent($m)
    {


        //if single content
        if($m['contentId'])
        {
            $moduleContent = $this->cs->getSingleItem($m['contentId']);
        }
        //if filtered list
        else
        {
            $moduleContent = $this->cs->getModuleListContent($m['contentTypeId'],$m['contentFilterField'],$m['contentFilterValue'],$m['contentOrderByField'],$m['contentOrderByValue']);
        }


        return $moduleContent;

    }

    private function getModuleForm($m,$frontController,$url)
    {

        $CMSForm = $this->getCMSForm($m['formId'],$this->localiser);

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

        $form = $frontController->createForm(
            new ContentTYPEfrontend(
                "edit",
                $fieldsRepository,
                $this->cs,
                $this->localiser->locale
            ),
            $content,
            array('action' => $frontController->generateUrl(
                    $route,
                    array(
                        '_locale'=>$this->locale,'uri'=>ltrim($url,"/"),
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
                            'SSoneCMSBundle:AppData:'.$CMSForm['adminEmailHTML'],
                            array("content"=>$blocks)
                            )
                        );
                    }
                    $this->mailer->send($message);

                }


                $frontController->redirect = $CMSForm['successURL'];

            }
        }


        $module['form'] = $form->createView();

        $module['settings'] = $CMSForm;



        return $module;

    }


    private function getCMSForm($formId,$localiser)
    {
        $form = $this->em->getRepository('SSoneCMSBundle:CMSForm')->getCMSFormByFormId($formId,$localiser);
        return $form;
    }


}
