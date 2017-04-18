<?php
namespace JfxNinja\CMSBundle\Listener;

use JfxNinja\CMSBundle\Services\Navigation;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class ExceptionListener
{
    protected $templating;
    protected $kernel;
    protected $requestStack;
    protected $container;

    public function __construct(EngineInterface $templating, $kernel, RequestStack $requestStack, Container $container)
    {
        $this->templating = $templating;
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        if ('prod' == $this->kernel->getEnvironment())
        {

            // exception object
            $exception = $event->getException();

            $navService = $this->container->get('jfxninja.cms.navigation');
            $logger = $this->container->get('logger');

            $logger->error($exception->getMessage());

            $host = $navService->host;

            // new Response object
            $response = new Response();

            // set response content
            $response->setContent(

                $this->templating->render(
                    'jfxninjaCMSThemeBundle:'.$navService->domainTemplate,
                    array(
                        'navigation'=>$navService->templateNavigationMap,
                        'pageClass'=>"notfound404",
                        'pageTitle'=>"Not found 404",
                        'metaDescription'=>$navService->metaDescription,
                        'content'=>array("attributes"=>array("template"=>"404/".$host.".html.twig")),
                        'modules'=>"",
                        'multiLanguageLinks'=>"",
                        'exception' => $exception
                    )

                )
            );



            // HttpExceptionInterface is a special type of exception
            // that holds status code and header details
            if ($exception instanceof HttpExceptionInterface)
            {
                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());

            }
            else
            {
                $response->setStatusCode(500);
            }

            // set the new $response object to the $event
            $event->setResponse($response);
        }
    }
}