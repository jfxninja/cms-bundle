<?php

namespace SSone\CMSBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * NavigationService
 *
 */
class Navigation extends EntityRepository
{


    private $locale;
    private $defaultLocale ;
    private $localiser ;
    private $domainMenusItems ;
    private $drawCategories;
    private $em;
    private $hf;
    private $cs;

    public $host;
    public $activeMenuItem;
    public $activeRoot;
    public $mappedActiveMenuItem;
    public $navigationMap;
    public $templateNavigationMap;
    public $domainTemplate;
    public $pageClass;
    public $pageTitle;
    public $metaDescription;

    public function __construct(Localiser $localiser,EntityManager $em, HelperFunctions $hf, ContentService $cs)
    {
        $this->locale = $localiser->locale;
        $this->defaultLocale = $localiser->defaultLocale;
        $this->localiser = $localiser;
        $this->drawCategories = true;
        $this->em = $em;
        $this->hf = $hf;
        $this->cs = $cs;
    }



    public function findBySecurekey($securekey)
    {
        return $this->em
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:Menu c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }

    /**
     * @param $host
     * @param $stopwatch
     * @param $uri
     */
    public function handleURL($host,$uri,$stopwatch)
    {

        $this->host = $host;

        if($stopwatch)
        {
            $stopwatch->start('SSone::handleURL::getDomainMenuItems');
            $this->domainMenusItems = $this->getDomainMenuItems($host);
            $stopwatch->stop('SSone::handleURL::getDomainMenuItems');

            $stopwatch->start('SSone::handleURL::findActiveMenuItem');
            $this->activeMenuItem = $this->findActiveMenuItem($this->domainMenusItems,$uri,$host);
            $stopwatch->stop('SSone::handleURL::findActiveMenuItem');

            $stopwatch->start('SSone::handleURL::buildNestedNavigationArray');
            $this->navigationMap = $this->buildNestedNavigationArray($host,$this->activeMenuItem);
            $stopwatch->stop('SSone::handleURL::buildNestedNavigationArray');

            $stopwatch->start('SSone::handleURL::formatNavigationForTemplate');
            $this->templateNavigationMap = $this->formatNavigationForTemplate($this->navigationMap);
            $stopwatch->stop('SSone::handleURL::formatNavigationForTemplate');

            $stopwatch->start('SSone::handleURL::getDomainTemplate');
            $this->domainTemplate = $this->getDomainTemplate($host,$this->activeMenuItem);
            $stopwatch->stop('SSone::handleURL::getDomainTemplate');

        }
        else
        {

            $this->domainMenusItems = $this->getDomainMenuItems($host);
            $this->activeMenuItem = $this->findActiveMenuItem($this->domainMenusItems,$uri,$host);
            $this->navigationMap = $this->buildNestedNavigationArray($host,$this->activeMenuItem);
            $this->templateNavigationMap = $this->formatNavigationForTemplate($this->navigationMap);
            $this->domainTemplate = $this->getDomainTemplate($host,$this->activeMenuItem);

        }


        return true;


    }


    /**
     * This function is responsible for matching teh request url to a menu item.
     *
     * @param $menuItems
     * @param $uri
     * @param $host
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */

    private function findActiveMenuItem($menuItems,$uri,$host)
    {

        $urlParts = explode("/",$uri);

        if($this->activeMenuItemHunt($menuItems,$urlParts,0))
        {
            $activeMenuItem = $this->activeMenuItemHunt($menuItems,$urlParts,0);
        }
        else
        {
            $this->navigationMap = $this->buildNestedNavigationArray($host);
            $this->templateNavigationMap = $this->formatNavigationForTemplate($this->navigationMap);

            throw new NotFoundHttpException("Page not found: ".$uri);
        }

        $this->mappedActiveMenuItem = $activeMenuItem;

        $this->activeRoot = $activeMenuItem['rootID'];
        $this->pageClass = $activeMenuItem['pageClass'];
        $this->pageTitle = $activeMenuItem['pageTitle'];
        if($activeMenuItem['metaDescription']) $this->metaDescription = $activeMenuItem['metaDescription'];

        return $activeMenuItem;

    }


    /**
     *
     * Menu constructions
     * URL: /Projects/Design[/optional]
     * Single Content = /menu slug/menu slug
     * List List =      /Menu slug/menu slug
     * List Item =      /menu slug/menu slug/content slug
     * Category Menu =  /menu slug/category slug
     *
     *
     * @param $menusItems
     * @param $urlParts
     * @param $urlPartCounter
     * @param null $parentId
     * @return mixed
     *
     *
     */
    private function activeMenuItemHunt($menusItems,$urlParts,$urlPartCounter,$parentId = null)
    {

        //TODO:JW This could be adapted to alert to multiple url matches, currently matches the first

        $countURLPartsRemaining = count($urlParts) - ($urlPartCounter +1);

        foreach($menusItems as $mi)
        {

            $mi['urlFilter1'] = "";
            $mi['urlFilter2'] = "";

            //handle home page requests
            if($urlParts[0] == "")
            {
                //TODO:JW Handle multiple menu groups
                //The first menu item without a parent should be the home page
                if(!$mi['parentId']) return $mi;
                continue;
            }

            if($parentId && $parentId != $mi['parentId']) continue; //filter menus not on this branch

            if($mi['slug'] == $urlParts[$urlPartCounter])
            {

                if($countURLPartsRemaining == 0)
                {
                    //print("we found an active menu (list or single)\r\n");
                    //we found an active menu (list or single)
                    return $mi;

                }

                //Check for active children - if yes follow down to next level
                foreach($menusItems as $childMenuItem)
                {

                    if($childMenuItem['parentId'] == $mi['id']) //if it is a child
                    {

                        if($childMenuItem['slug'] == $urlParts[$urlPartCounter+1]) //if the child matches the next url part
                        {

                            //run the function on the next url part there is a potential match in the children
                            $mi = $this->activeMenuItemHunt($menusItems,$urlParts,$urlPartCounter+1,$childMenuItem['parentId']);
                            if($mi) return $mi;

                        }

                    }

                }

                //No children remain on this branch - ergo we have a match?


                if($mi['ctc1FieldId'] && $mi['ctc2FieldId'])
                    //Both category fields are set
                {
                    switch($countURLPartsRemaining)
                    {
                        case 3:
                            $mi['mode'] = "listitem";
                            break;

                        case 2:
                            //Draws the list page
                            break;

                        case 1:
                            //Draws the list page but automatically selects the next level
                            break;

                        case 0:
                            //Draws the list page but automatically selects the next 2 levels
                            break;
                    }

                }
                elseif($mi['ctc1FieldId'] || $mi['ctc2FieldId'])
                    //Only one category field is set
                {
                    switch($countURLPartsRemaining)
                    {
                        case 2:
                            $mi['mode'] = "listitem";
                            break;

                        case 1:
                            //Draws the list page
                            break;

                        case 0:
                            //Draws the list page but automatically selects the next level

                    }

                }
                else
                    //No category fields are set
                {
                    switch($countURLPartsRemaining)
                    {
                        case 1:
                            //if the url part is submitted
                            //else
                            if($mi['mode'] != "place-holder")
                            {
                                $mi['mode'] = "listitem";
                            }

                            break;

                        case 0:
                            //Draws the list page
                            break;
                    }

                }

                $partsToRemove = count($urlParts) - $countURLPartsRemaining;
                $remainingURLparts = array_splice($urlParts, $partsToRemove );
                $mi['urlParts'] = $remainingURLparts;

                //Set url filters
                $mi['urlFilter1'] = (isset($remainingURLparts[0]))?$remainingURLparts[0]:"";
                $mi['urlFilter2'] = (isset($remainingURLparts[1]))?$remainingURLparts[1]:"";

                //Update menu item slug to refelect active category menu (used to assert correct list item item link)
                $mi['slug'] = (isset($remainingURLparts[0]))?$remainingURLparts[0]:$mi['slug'];
                $mi['slug'] = (isset($remainingURLparts[1]))?$remainingURLparts[1]:$mi['slug'];


                $mi['countURLPartsRemaining'] = $countURLPartsRemaining;

                return $mi;

            }
        }


    }

    /**
     * Returns menuitems translated for this domain
     *
     * @param $host
     *
     * @return array $menuItemId
     */
    public function getDomainMenuItems($host)
    {

        $domain = $this->em
            ->createQuery(
                'SELECT
                    d.id,
                    d.metaDescription
                FROM SSoneCMSBundle:Domain d
                WHERE d.domain LIKE :host'
            )
            ->setParameter('host', '%'.$host.'%')
            ->getSingleResult();

        $domain = $this->localiser->setMultiLanguageFields($domain,array("metaDescription"),$this->locale);

        $this->metaDescription = $domain['metaDescription'];

        $native = true;

        if($native)
        {

            $sql = "SELECT
                  s0_.id AS id,
                  s0_.name AS name,
                  s0_.sort AS sort,
                  s0_.slug AS slug,
                  s0_.slug AS mlSlug,
                  s0_.pageTitle,
                  s0_.pageClass,
                  s0_.metaDescription,
                  s0_.domain_template_override,
                  s0_.hide,
                  s0_.mode AS mode,
                  s0_.grandChildrenRelativePosition AS grandChildrenRelativePosition,
                  s0_.drawAllGrandChildren AS drawAllGrandChildren,
                  s0_.grandChildrenTemplatePosition AS grandChildrenTemplatePosition,
                  s0_.drawListItemsAsMenuItems AS drawListItemsAsMenuItems,
                  s0_.hideEmptyCategories AS hideEmptyCategories,
                  s1_.id AS ctc1FieldId,
                  s2_.id AS ctc2FieldId,
                  s3_.id AS ctcrFieldId,
                  s4_.slug AS cSlug,
                  s4_.id AS contentId,
                  s5_.id AS contentTypeId,
                  s5_.slug AS ctSlug,
                  s6_.id AS parentId,
                  s7_.id AS rootID
                FROM
                  ssone_menuItems s0_
                  LEFT JOIN ssone_menuItems s6_ ON s0_.fk_parentMenuItem_id = s6_.id
                  LEFT JOIN ssone_menus s7_ ON s0_.fk_rootMenu_id = s7_.id
                  LEFT JOIN ssone_domains s8_ ON s7_.fk_domain_id = s8_.id
                  LEFT JOIN ssone_content s4_ ON s0_.fk_content_id = s4_.id
                  LEFT JOIN ssone_contentTypes s5_ ON s0_.fk_contentType_id = s5_.id
                  LEFT JOIN ssone_fields s1_ ON s0_.fk_field_id_category1 = s1_.id
                  LEFT JOIN ssone_fields s2_ ON s0_.fk_field_id_category2 = s2_.id
                  LEFT JOIN ssone_fields s3_ ON s0_.fk_field_id_categoryRelationship = s3_.id
                WHERE
                  s8_.id = '{$domain['id']}'
                ORDER BY
                  s0_.sort ASC";




            $connection = $this->em->getConnection();
            $statement = $connection->prepare($sql);
            $statement->execute();

            $results = $statement->fetchAll();


            foreach($results as &$r)
            {

                $r = $this->hf->deserializeArray($r,array("name","slug","mlSlug","cSlug","ctSlug","contentId","pageTitle","metaDescription"));
                $r = $this->localiser->setMultiLanguageFields($r,array("name","slug","pageTitle","metaDescription"),$this->locale);

            }

            return $results;

        }
        else
        {
            $menuItems = $this->em
                ->createQuery(
                    'SELECT
                        mi.id,
                        mi.name,
                        mi.sort,
                        mi.pageClass,
                        mi.pageTitle,
                        mi.metaDescription,
                        mi.slug,
                        mi.slug AS mlSlug,
                        mi.mode,
                        mi.domain_template_override,
                        mi.hide,
                        mi.grandChildrenRelativePosition,
                        mi.drawAllGrandChildren,
                        mi.grandChildrenTemplatePosition,
                        mi.drawListItemsAsMenuItems,
                        mi.hideEmptyCategories,
                        ctc1.id AS ctc1FieldId,
                        ctc2.id AS ctc2FieldId,
                        ctcr.id AS ctcrFieldId,
                        c.slug AS cSlug,
                        c.id AS contentId,
                        ct.id AS contentTypeId,
                        ct.slug AS ctSlug,
                        p.id AS parentId,
                        r.id AS rootID
                    FROM SSoneCMSBundle:MenuItem mi INDEX BY mi.id
                    LEFT JOIN mi.parent p
                    LEFT JOIN mi.root r
                    LEFT JOIN r.domain d
                    LEFT JOIN mi.content c
                    LEFT JOIN mi.contentType ct
                    LEFT JOIN mi.contentCategory1 ctc1
                    LEFT JOIN mi.contentCategory2 ctc2
                    LEFT JOIN mi.contentCategoryRelationship ctcr
                    WHERE d.id = :id
                    ORDER BY mi.sort'
                )
                ->setParameter('id', $domain['id'])
                ->getResult();

            //translate routes for searching
            foreach($menuItems as &$m)
            {
                //Translate slug
                $m = $this->localiser->setMultiLanguageFields($m,array("name","slug","pageTitle","metaDescription"),$this->locale);
            }

            return $menuItems;

        }



    }

    /**
     * @param null $host
     * @return mixed $menus
     */
    private function getMenus($host = null)
    {
        if($host)
        {
            $query ='SELECT m.name, m.id, m.sort, m.menuTemplate, m.menuTemplatePosition, m.grandChildrenRelativePosition, m.drawAllGrandChildren, m.grandChildrenTemplatePosition, d.domain AS domain
                    FROM SSoneCMSBundle:Menu m
                    LEFT JOIN m.domain d
                    WHERE d.domain LIKE :host
                    ORDER BY m.sort';

            $menus = $this->em
                ->createQuery($query)
                ->setParameter('host', "%".$host."%");
        }
        else
        {
            $query ='SELECT m.name, m.id, m.sort, m.menuTemplate, m.menuTemplatePosition, m.grandChildrenRelativePosition, m.drawAllGrandChildren, m.grandChildrenTemplatePosition
                    FROM SSoneCMSBundle:Menu m
                    ORDER BY m.sort';

            $menus = $this->em
                ->createQuery($query);

        }

        return $menus->getResult();


    }

    /**
     * @return mixed $domains
     */
    private function getDomains()
    {

        return $this->em
                ->createQuery(
                    'SELECT d.domain
                    FROM SSoneCMSBundle:Menu m
                    LEFT JOIN m.domain d
                    ORDER BY d.id')
                ->getResult();

    }


    /**
     * Nested navigation array builder - the root function for building any list of navigation
     * @param $host
     * @param $activeMenuItem
     * @return array()
     *
     * 1) Select Root menus
     * 2) Loop through each Root to build navigation tree
     *  2a) Select Child Menu Items
     *  2b) if requested highlight the active and active branch
     *  2c) Loop through child items building level 1 navigation
     *  2d) Recursively loop through navigation array foreach child item remaining
     *      assigning it when a parent is found until navigation tree is built (Solitaire style!)
     *      (uses nestedAddToParent to walk array)
     */
    public function buildNestedNavigationArray($host = null, $activeMenuItem = null)
    {

        $menus = $this->getMenus($host);

        $nn = array(); //nested navigation

        if(!isset($this->domainMenusItems))
        {
            $this->domainMenusItems = $this->getDomainMenuItems($host);
        }

        $groupedDomainMenuItems = array();

        foreach($this->domainMenusItems as $mi)
        {

            $groupedDomainMenuItems[$mi['rootID']][$mi['id']] = $mi;

            unset($mi);
        }

        foreach($menus as $m)
        {
            if(isset($groupedDomainMenuItems[$m['id']]))
            {
                $menuItems = $groupedDomainMenuItems[$m['id']];
            }
            else
            {
                $menuItems = array();
            }

            $activeBranch = $this->highlightActiveBranch($menuItems,$activeMenuItem['id']);

            //set array pointer for this root menu
            $nn[$m['id']]['attributes']['name'] = $m['name'];
            $nn[$m['id']]['attributes']['id'] = $m['id'];
            $nn[$m['id']]['attributes']['menuTemplate'] = $m['menuTemplate'];
            $nn[$m['id']]['attributes']['menuPos'] = $m['menuTemplatePosition'];
            $nn[$m['id']]['attributes']['gcRelPos'] = $m['grandChildrenRelativePosition'];
            $nn[$m['id']]['attributes']['gcDrawAll'] = $m['drawAllGrandChildren'];
            $nn[$m['id']]['attributes']['gcTemplPos'] = $m['grandChildrenTemplatePosition'];
            $nn[$m['id']]['attributes']['highlight'] = $activeBranch;

            

            $first = true;
            while(count($menuItems) > 0)
            {
                foreach($menuItems as $mk=>$mi)
                {

                    //if parent ID is blank this is a top level menuItem so add it to the first level
                    if($mi['parentId'] == "")
                    {

                        //if this is the default menu and menu item set a blank URL
                        if($first)
                        {
                            foreach($mi['mlSlug'] as $lc=>$uri)
                            {
                                $mi['mlSlug'][$lc] = "";
                                $mi['slug'] = "";
                            }
                            $first = false;
                        }

                        $parent['attributes']['name'] = "root";
                        $parent['attributes']['menuTemplate'] = $m['menuTemplate'];
                        $parent['attributes']['gcRelPos'] = $m['grandChildrenRelativePosition'];
                        $parent['attributes']['gcDrawAll'] = $m['drawAllGrandChildren'];
                        $parent['attributes']['gcTemplPos'] = $m['grandChildrenTemplatePosition'];

                        $nn[$m['id']][$mi['id']] = $this->createNavigationNode($mi,$parent);

                        $nn[$m['id']][$mi['id']] = $this->addCategoryMenus($mi['ctc1FieldId'],$mi['ctc2FieldId'],$nn[$m['id']][$mi['id']],$mi,$activeMenuItem);


                        unset($menuItems[$mk]);

                        continue;
                    }

                    //Tests that at least once branch (top level menu) has been set to walk
                    //TODO:JW This could be optimised to also not follow non-highlighted routes if draw children is not set
                    if(is_array($nn[$m['id']]))
                    {
                        $this->nestedAddToParent($nn[$m['id']], $mi, $mk, $menuItems,$activeMenuItem);
                    }

                }
            }

        }

        return $nn;

    }

    /**
     * Handles recursive array walk for nested navigation builder
     *
     * @param $nn - current nested navigation array
     * @param $mi - Menu item
     * @param $mk - Menu item key
     * @param $menuItems - Menu items container list
     * @param $activeMenuItem - active menu
     */
    private function nestedAddToParent(&$nn,$mi,$mk,&$menuItems,$activeMenuItem)
    {

        //Loop through the current menu map
        foreach($nn as $k=>$menu)
        {
            //test skips attributes
            if(is_int($k))
            {
                //If the parent ID of the menu item to be placed matched the loop menu item...
                if($mi['parentId'] == $k)
                {
                    //Found a menu to attache so pop from menuItems
                    unset($menuItems[$mk]);
                    //and add to nested menus
                    $nn[$k][$mi['id']] = $this->createNavigationNode($mi,$nn[$k]);

                    //Check if menu item categories are set to add dynamic menus
                    $nn[$k][$mi['id']] = $this->addCategoryMenus($mi['ctc1FieldId'],$mi['ctc2FieldId'],$nn[$k][$mi['id']],$mi,$activeMenuItem);

                    return;
                }

                //Prevents walking menuItems with no children
                if($nn[$k])
                {
                    $this->nestedAddToParent($nn[$k],$mi,$mk,$menuItems,$activeMenuItem);
                }

            }
        }
        //If we reach here, the menu items parent has not been added to the tree yet, move on to the next item.
    }

    /**
     * Add category menus
     */
    private function addCategoryMenus($c1fId,$c2fId,$parentNode,$parentMenuItem,$activeMenuItem)
    {


        //If the filter field is not set no need to check category menus.
        if(!$c1fId || !$this->drawCategories) return $parentNode;

        $categoryMenus = $this->getCategoryMenus($c1fId,$c2fId,$parentNode,$parentMenuItem,$activeMenuItem);

        //set urls to first child
        if(count($categoryMenus))
        {
            $firstCategory = true;
            foreach($categoryMenus as $cm)
            {
                $parentNode[$cm['attributes']['id']] = $cm;
                if($firstCategory)
                {
                    //$parentNode['attributes']['url'] = $cm['attributes']['url'];
                    $firstCategory = false;
                }
            }
        }

        if($activeMenuItem['urlFilter1']) $parentNode['attributes']['active'] = false;

        return $parentNode;
    }


    /**
     * @param $c1fId
     * @param $c2fId
     * @param $parentMenuItem
     * @param $parentNode
     * @param $activeMenuItem
     * @return array
     */
    private function getCategoryMenus($c1fId,$c2fId,$parentNode,$parentMenuItem,$activeMenuItem)
    {

        $categoryMenus = array();
        $hideEmptyCategories = $parentMenuItem['hideEmptyCategories'];
        $drawContentAsMenus = $parentMenuItem['drawListItemsAsMenuItems'];

        $categories1 = $this->buildCategoryOptions($c1fId,$activeMenuItem['urlFilter1'],$parentNode);
        $categories2 = $this->buildCategoryOptions($c2fId,$activeMenuItem['urlFilter2'],$parentNode,$parentNode['attributes']['contentTypeCategoryRelatedField']);

        //is category 1 set
        if(count($categories1))
        {
            if($hideEmptyCategories || $drawContentAsMenus)
            {
                $content = $this->getContentForCategoryMenus($c1fId,$c2fId);
            }


            $routeFound = false;
            foreach($categories1 as $k=>$c)
            {


                if($hideEmptyCategories)
                {
                    //Filter the content for this category
                    $remainingContentItems = $this->filterCategoryByContent($content,$c,"filter1");

                    //There is no content in this category so skip to the next.
                    if(!$remainingContentItems) continue;

                }

                //Create the navigation node
                $categoryMenus[$k] = $this->createNavigationNode($c,$parentNode);

                //Set the active category
                if(
                    !$activeMenuItem['urlFilter2']
                    && $categoryMenus[$k]['attributes']['cSlug'] == $activeMenuItem['urlFilter1']
                )
                {
                    $categoryMenus[$k]['attributes']['active'] = true;
                    $categoryMenus[$k]['attributes']['highlight'] = false;
                }

                //If this is highlighted route set the mapped active menu item
                if($c['highlight'])
                {
                    $routeFound = true;
                    $this->mappedActiveMenuItem = $categoryMenus[$k]['attributes'];
                }

                if(count($categories2))
                {
                    if($c['highlight'])
                    {
                        $routeFound = false;
                    }

                    $firstCategory = true;
                    foreach($categories2 as $c2)
                    {

                        if($hideEmptyCategories)
                        {

                            //Filter the content for this category
                            $remainingSubContentItems = $this->filterCategoryByContent($remainingContentItems,$c2,"filter2");

                            //There is no content in this category so skip to the next.
                            if(!$remainingSubContentItems) continue;
                        }

                        if($parentNode['attributes']['contentTypeCategoryRelatedField'])
                        {
                            if($this->filterRelatedCategory($parentNode['attributes']['contentTypeCategoryRelatedField'],$c2,$c))
                            {
                                continue;
                            }

                        }

                        //unset the highlighted child for groups not highlighted
                        if(!$c['highlight']) $c2['highlight'] = false;

                        //check we had a match
                        if($c['highlight'] && $c2['highlight']) $routeFound = true;

                        //Set the parent slug
                        if($firstCategory)
                        {
                            $categoryMenus[$k]['attributes']['url'] .= "/" . $c2['slug'];
                            $firstCategory = false;
                        }

                        $c2['id'] = $c2['id'] + $k;

                        $categoryMenus[$k]["{$c2['id']}"] = $this->createNavigationNode($c2,$categoryMenus[$k]);

                        //If this is highlighted route set the mapped active menu item
                        if($c['highlight'] && $c2['highlight']) $this->mappedActiveMenuItem = $categoryMenus[$k]["{$c2['id']}"]['attributes'];

                        //add content menu items

                    }

                }
                else
                {
                    //add content menu items
                }


            }

            //If this is the highlighted route but we didnt find
            if($parentNode['attributes']['active'] && !$routeFound)
            {
                //throw new NotFoundHttpException("Page not found");
            }
        }
        return $categoryMenus;

    }


    /**
     * Compare an array of content to a category retuning content that matches the category
     *
     * @param $content
     * @param $category
     * @param $filterKey
     * @return array
     */

    private function filterCategoryByContent($content,$category,$filterKey)
    {

        $remainingContent = array();

        foreach($content as $ck=>$c)
        {
            $matched = false;
            foreach($c[$filterKey] as $fk=>$filter)
            {

                if($filter == $category['contentId']) $matched = true;
            }
            if($matched) $remainingContent[$ck] = $c;

        }

        return $remainingContent;

    }

    /**
     * Get category menu options for the given content fields that have been selected
     * to categorise the content by.
     *
     * @param $c1fId
     * @param $c2fId
     * @return array
     */

    private function getContentForCategoryMenus($c1fId,$c2fId)
    {

        $content = array();

        //get the category field
        $filter1 = $this->em
            ->createQuery(
                'SELECT
                  b.id,
                  bf.fieldContent AS filter1,
                  c.id AS contentId,
                  c.slug
                  FROM SSoneCMSBundle:Block b
                  LEFT JOIN b.blockFields bf
                  LEFT JOIN b.field f
                  LEFT JOIN b.content c
                  WHERE f.id = :fid'
            )
            ->setParameter('fid', $c1fId)
            ->getResult();

        $filter2 = array();
        if($c2fId)
        {
            $filter2 = $this->em
                ->createQuery(
                    'SELECT
                      b.id,
                      bf.fieldContent AS filter2,
                      c.id AS contentId,
                      c.slug
                      FROM SSoneCMSBundle:Block b
                      LEFT JOIN b.blockFields bf
                      LEFT JOIN b.field f
                      LEFT JOIN b.content c
                      WHERE f.id = :fid'
                )
                ->setParameter('fid', $c2fId)
                ->getResult();

        }

        foreach($filter1 as $k=>$f1)
        {
            $content[$k] = $this->localiser->setMultiLanguageFields($f1,array("slug"),$this->locale);

            foreach($filter2 as $f2)
            {
                if($f1['contentId'] == $f2['contentId'])
                {
                    $content[$k]['filter2'] = $f2['filter2'];
                }
            }
        }

        return $content;

    }


    /**
     * Compare the categories parentIds to the parent
     *
     * @param $fieldId
     * @param $category
     * @param $parentCategory
     * @return bool
     */
    private function filterRelatedCategory($fieldId,$category,$parentCategory)
    {

        foreach($category['parentCategories'] as $categories)
        {
            foreach($categories as $k=>$v)
            {
                if($v == $parentCategory['contentId']) return false;
            }
        }

        return true;

    }


    /**
     * Category menus are retrieved from the database for filters
     *
     * @param $fieldId
     * @param $highlightSlug
     * @param $parent
     * @param $relatedCategoryFieldId
     * @return array
     */
    private function buildCategoryOptions($fieldId,$highlightSlug,$parent,$relatedCategoryFieldId=NULL)
    {

        if(!$fieldId) return array();

        //get the category field
        $field = $this->em
            ->createQuery(
                'SELECT
                  f.id,
                  f.variableName,
                  f.fieldTypeSettings,
                  ft.variableName as fieldType
                  FROM SSoneCMSBundle:Field f
                  LEFT JOIN f.fieldType ft
                  WHERE f.id = :id'
            )
            ->setParameter('id', $fieldId)
            ->getSingleResult();

        //get teh categories
        //print_r($field['fieldTypeSettings']);
        if($field['fieldType'] == "choice")
        {
            $options = $this->cs->convertChoiceOptionsStringToArray($field['fieldTypeSettings']['choice']['choiceoptions']);
            $index = 1;
            foreach($options as $k=>$v)
            {
                $slug = $this->hf->urlSafe($v);
                //add option ensureing unique key substr(microtime(true) * 1000, 9).$index
                $options[$k] = array("slug"=>array($this->defaultLocale=>$k),"name"=>$v,"id"=>substr(microtime(true) * 1000, 9).$index,"contentId"=>"");
                $index++;
            }

        }
        elseif($field['fieldType'] == "relatedcontent")
        {
            $options
                = $this->em
                ->createQuery(
                    'SELECT
                      c.id,
                      c.slug,
                      c.name
                      FROM SSoneCMSBundle:Content c
                      LEFT JOIN c.contentType ct
                      WHERE ct.id = :id'
                )
                ->setParameter('id', $field['fieldTypeSettings']['relatedcontent']['relatedcontent'])
                ->getResult();

            foreach($options as $k=>$o)
            {

                $o['contentId'] = $o['id'];
                $o['id'] = $parent['attributes']['id'].".".$o['id'];
                $options[$k] = $o;
            }
        }

        $blockFields = array();

        //If this is a related category
        if($relatedCategoryFieldId)
        {
            //get the category field
            $blockFields = $this->em
                ->createQuery(
                    'SELECT
                      b.id,
                      bf.fieldContent,
                      c.id AS contentId
                      FROM SSoneCMSBundle:Block b
                      LEFT JOIN b.blockFields bf
                      LEFT JOIN b.field f
                      LEFT JOIN b.content c
                      WHERE f.id = :fid'
                )
                ->setParameter('fid', $relatedCategoryFieldId)
                ->getResult();


        }


        $menuItems = array();

        foreach($options as $o)
        {

            $o['parentCategories'] = "";
            //if we have blockfields add these to the item
            foreach($blockFields as $bf)
            {
                if($bf['contentId'] == $o['contentId']) $o['parentCategories'][$bf['id']] = $bf['fieldContent'];

            }

            //set slug to new pointer to store in multi language format
            $o['mlSlug'] = $o['slug'];

            //Translate the slug
            $o = $this->localiser->setMultiLanguageFields($o,array("slug"),$this->locale);

            //Check if this is a matched route
            $highlight = ($highlightSlug == $o['slug'])?true:false;

            $menuItems[] = array(
                "name"=>$o['name'],
                "cSlug"=>$o['slug'],
                "slug"=>$o['slug'],
                "mlSlug"=>$o['mlSlug'],
                "ctSlug"=>"",
                "id"=>$o['id'],
                //TODO:JW Build dynamic page class
                "pageClass"=>'',
                "contentId"=>$o['contentId'],
                "highlight"=>$highlight,
                "grandChildrenRelativePosition"=>"",
                "drawAllGrandChildren"=>"",
                "grandChildrenTemplatePosition"=>"",
                "mode"=>"list",
                "ctcrFieldId"=>"",
                "parentCategories"=>$o['parentCategories'],

            );
        }

        return $menuItems;
    }


    /**
     * @param $menuItem
     * @param null $parent
     * @return array
     */
    private function createNavigationNode($menuItem, $parent)
    {

        $concatName = ($parent['attributes']['name'] == "root") ? "/".$menuItem['name'] : $parent['attributes']['concatname']."->".$menuItem['name'];

        $active = (isset($menuItem['active']) && $menuItem['active'] == true) ? true : false;

        $parentId = ($parent['attributes']['name'] == "root") ? "" : $parent['attributes']['id'];

        $buildURL = $this->buildMenuItemURL($menuItem,$parent);

        $highlight = (isset($menuItem['highlight']) && $menuItem['highlight'] == true) ? true : false;

        $contentId = (isset($menuItem['contentId']))? $menuItem['contentId'] : "";

        //TODO:JW template overrides
        return array(
                "attributes"=>
                    array(
                        "name"=>$menuItem['name'],
                        "id"=>$menuItem['id'],
                        'parentId' => $parentId,
                        "pageClass"=>$menuItem['pageClass'],
                        "hide"=>$menuItem['hide'],
                        "cSlug"=>$menuItem['cSlug'],
                        "ctSlug"=>$menuItem['ctSlug'],
                        "mlSlug"=>$menuItem['mlSlug'],
                        "trail"=>$buildURL['trail'],
                        "url"=>$buildURL['url'],
                        'gcRelPos' => $menuItem['grandChildrenRelativePosition'],
                        'gcDrawAll' => $menuItem['drawAllGrandChildren'],
                        'gcTemplPos' => $menuItem['grandChildrenTemplatePosition'],
                        'cRelPos' => $parent['attributes']['gcRelPos'],
                        'cDrawAll' => $parent['attributes']['gcDrawAll'],
                        'cTemplPos' => $parent['attributes']['gcTemplPos'],
                        'menuTemplate' => $parent['attributes']['menuTemplate'],
                        "active"=>$active,
                        "highlight"=>$highlight,
                        "contentTypeCategoryRelatedField"=>$menuItem['ctcrFieldId'],
                        "contentId"=>$contentId,
                        "concatname"=>$concatName));

    }



    private function buildMenuItemURL($menuItem, $parent)
    {

        $trail = ($parent['attributes']['name'] == "root" || $parent['attributes']['trail'] == "/" ) ? "" : $parent['attributes']['trail'];
        $lp = ($this->locale != $this->defaultLocale)? "/" . $this->locale : "";

        if($menuItem['mode'] == "single")
        {
            $url =      $lp . $trail . "/" . $menuItem['slug'];
            $trail =    $trail . "/" . $menuItem['slug'];
        }
        elseif($menuItem['mode'] == "list")
        {
            $url =      $lp . $trail . "/" . $menuItem['slug']; //removed trailing list
            $trail =    $trail . "/" . $menuItem['slug'];
        }
        elseif($menuItem['mode'] == "place-holder")
        {
            $childSlug = $this->getFirstChildURL($menuItem['id']);
            $url =      $lp . $trail . "/" . $menuItem['slug'] . "/" . $childSlug;
            $trail =    $trail . "/" . $menuItem['slug'];
        }
        else
        {
            $url =      $lp . $trail . "/" . $menuItem['slug'];
            $trail =    $trail . "/" . $menuItem['slug'];
        }

        return array("trail"=>$trail,"url"=>$url);

    }


    private function getFirstChildURL($menuItemId)
    {
        $children = $this->em
            ->createQuery(
                'SELECT
                    mi.id,
                    mi.name,
                    c.mode,
                    c.id AS childId,
                    c.sort,
                    c.slug
                FROM SSoneCMSBundle:MenuItem mi
                LEFT JOIN mi.children c
                WHERE mi.id = :menuItemId
                ORDER BY mi.sort'
            )
            ->setParameter('menuItemId', $menuItemId)
            ->getResult();


        

        foreach($children as $c)
        {
            //get the url or the first child
            if(!$c['childId']) return "place-holder-missing-child";
            $c = $this->localiser->setMultiLanguageFields($c,array("slug"),$this->locale);
            $slug = $c['slug'];
            break;
        }

        if($c['mode'] == "place-holder")
        {
            $slug = $slug . "/" . $this->getFirstChildURL($c['childId']);
        }
        return $slug;

    }


    /**
     * @param $menuItems
     * @param $highlightId
     * @return bool
     */
    private function highlightActiveBranch(&$menuItems, $highlightId)
    {
        $found = false;
        if(!$highlightId) return $found;

         if(isset($menuItems[$highlightId]))
         {
             $menuItems[$highlightId]['active'] = true;
             $found = true;
             $this->highlightBranch($menuItems,$menuItems[$highlightId]);

         }

        return $found;
    }


    /**
     * @param $menuItems
     * @param $menuItem
     */
    private function highlightBranch(&$menuItems, $menuItem)
    {

        if(isset($menuItems[$menuItem['parentId']])) //does it have a parent menu
        {
            $menuItems[$menuItem['parentId']]['highlight'] = true;
            $this->highlightBranch($menuItems, $menuItems[$menuItem['parentId']]);
        }

        return;

    }


    /**
     * Flat navigation builder, takes nested navigation array and returns flattened array for
     * parent menu selections
     *
     * Recursively walks nested array (flatten array function) foreach root navigation tree
     * building new array flat array;
     *
     * @param null $excludeId - optional parameter to exclude a given menu item, ie when selecting a parent menu
     * @return array
     */
    public function buildFlatNavigationArray($excludeId = null)
    {
        $fn = array();

        $domains = $this->getDomains();

        $this->drawCategories = false;

        foreach($domains as $d)
        {
            $nn = $this->buildNestedNavigationArray($d['domain']);

            foreach($nn as $k=>$rootNavigationTree)
            {

                $flattened = array();
                $this->formatNavigationForDropdown($rootNavigationTree,$flattened,$k,$excludeId);

                $fn[$nn[$k]['attributes']['name']." ".$d['domain']] = array($k."_root"=>"Root") + $flattened;
            }

        }



        return $fn;

    }



    /**
     * @param $nn - Nested navigation array
     * @param $flattened - back referenced flattened array
     * @param $rootID - ID of the current root navigation iteration
     * @param null $excludeId
     */
    public function formatNavigationForDropdown($nn,&$flattened,$rootID,$excludeId)
    {

        foreach($nn as $k=>$menu)
        {


            if(is_numeric($k) && $k != $excludeId)
            {

                $flattened[$rootID."_".$k] = $menu['attributes']['concatname'];
            }

            $hasChildren = false;
            foreach($menu as $key=>$child)
            {
                if(is_numeric($key)) $hasChildren = true;
            }
            if($hasChildren)
            {
                $this->formatNavigationForDropdown($menu,$flattened,$rootID,$excludeId);
            }



        }
        return $flattened;

    }



    /**
     * Processes the navigation based on inherent display settings.
     *
     * @param $navigation
     * @return array
     */

    private function formatNavigationForTemplate($navigation)
    {
        $navigationBlocks = array();

        foreach($navigation as $menu)
        {
            $navigationBlocks[$menu['attributes']['menuPos']]["root_".$menu['attributes']['id']]['attributes']['name'] = $menu['attributes']['name'];
            $navigationBlocks[$menu['attributes']['menuPos']]["root_".$menu['attributes']['id']]['attributes']['menuTemplate'] = $menu['attributes']['menuTemplate'];

            foreach($menu as $k=>$menuItem)
            {
                if(!is_numeric($k)) continue;
                $navigationBlocks
                [$menu['attributes']['menuPos']]
                ["root_".$menu['attributes']['id']]
                ['children']
                [$k]
                ['attributes'] =  $menuItem['attributes'];

                $this->cascadeDrawNavigation(
                    $navigationBlocks,
                    $navigationBlocks[$menu['attributes']['menuPos']]["root_".$menu['attributes']['id']]['children'][$k],
                    $menuItem
                );
            }

        }

        return  $navigationBlocks;

    }

    /**
     * @param $navigationBlocks
     * @param $parentBranch
     * @param $menuItem
     */
    private function cascadeDrawNavigation(&$navigationBlocks,&$parentBranch,$menuItem)
    {
        foreach($menuItem as $k=>$child)
        {
            if(!is_numeric($k)) continue;
            if($parentBranch['attributes']['highlight'] || $parentBranch['attributes']['active'] || $parentBranch['attributes']['cDrawAll']) //parent is highlighted
            {
                //if draw children inline append to this array
                if($parentBranch['attributes']['cRelPos'] == "inline")
                {
                    $parentBranch['children'][$k]['attributes'] = $child['attributes'];
                    //parent is highlighted and same branch
                    $this->cascadeDrawNavigation($navigationBlocks,$parentBranch['children'][$k],$child);

                }
                //elseif draw children separate append to tempalate block as new tree
                elseif($parentBranch['attributes']['cRelPos'] == "separate")
                {
                    //start new tree
                    $navigationBlocks
                    [$parentBranch['attributes']['cTemplPos']]
                    ['menu'.$parentBranch['attributes']['id']]
                    ['attributes']
                    ['name'] = $parentBranch['attributes']['name'];

                    $navigationBlocks
                    [$parentBranch['attributes']['cTemplPos']]
                    ['menu'.$parentBranch['attributes']['id']]
                    ['attributes']
                    ['menuTemplate'] = $parentBranch['attributes']['menuTemplate'];



                    //add navigation item to the new tree
                    $navigationBlocks
                    [$parentBranch['attributes']['cTemplPos']]
                    ['menu'.$parentBranch['attributes']['id']]
                    ['children']
                    ["{$child['attributes']['id']}"]['attributes'] = $child['attributes'];


                    //parent is highlighted follow new tree
                    $this->cascadeDrawNavigation(
                        $navigationBlocks,
                        $navigationBlocks[$parentBranch['attributes']['cTemplPos']]['menu'.$parentBranch['attributes']['id']]['children'][$k],
                        $child);
                }
                else
                {
                    print("error: children relative position not specified");
                }


            }



        }
        return;
    }


    /**
     * @param $nn - Nested navigation array
     * @param $flattened - back referenced flattened array
     * @param $rootID - ID of the current root navigation iteration
     * @param null $excludeId
     */
    public function formatNavigationForMultiLanguage($nn,&$flattened,$rootID,$excludeId)
    {

        foreach($nn as $k=>$menu)
        {


            if(is_numeric($k) && $k != $excludeId)
            {

                $flattened[$k] = $menu['attributes'];
            }

            $hasChildren = false;
            foreach($menu as $key=>$child)
            {
                if(is_numeric($key)) $hasChildren = true;
            }
            if($hasChildren)
            {
                $this->formatNavigationForMultiLanguage($menu,$flattened,$rootID,$excludeId);
            }



        }
        return $flattened;

    }


    //consider setting content slug in menu hunt
    public function getAlternateLanguageURIs($contentSlug = "")
    {

        if($this->activeMenuItem['mode'] != "listitem")
        {
            $contentSlug = "";
        }

        $languages = $this->em
            ->createQuery(
                'SELECT l.languageCode
                FROM SSoneCMSBundle:Language l'
            )
            ->getResult();

        $menuItems = array();


        $this->formatNavigationForMultiLanguage($this->navigationMap[$this->activeRoot],$menuItems,$this->activeRoot,"");



        $urls = array();
        foreach($languages as $l)
        {
            $lc = $l['languageCode'];
            $urls[$lc] = $this->getMenuItemURI($this->mappedActiveMenuItem,$menuItems,$lc);
            if($contentSlug)
            {
                $urls[$lc] = $urls[$lc] . "/" . $this->localiser->translateMultiLanguageField($contentSlug, $lc);
            }
            if($lc != $this->defaultLocale) $urls[$lc] = "/" . $lc . $urls[$lc];
        }


        return $urls;

    }



    public function getMenuItemURI($menuItem,$menusItems,$lc)
    {

        $menuItem = $this->localiser->setMultiLanguageFields($menuItem,array("mlSlug"),$lc);

        $miProcessed = $this->localiser->setMultiLanguageFields($menusItems["{$menuItem['id']}"],array('mlSlug'),$lc);

        if($menuItem['parentId'])
        {
            $uri = $this->getMenuItemURI($menusItems[$menuItem['parentId']],$menusItems,$lc) . "/" . $miProcessed['mlSlug'];
            return $uri;
        }
        else
        {
            return  "/" . $miProcessed['mlSlug'];
        }

    }

    /**
     * function getDomainTemplate
     * @param $host
     * @param $activeMenuItem
     * @return string
     */
    private function getDomainTemplate($host,$activeMenuItem)
    {
        if($activeMenuItem['domain_template_override'])
        {
            return $activeMenuItem['domain_template_override'];
        }
        else{
            $domain = $this->em
                ->getRepository('SSoneCMSBundle:Domain')->getDomain($host);
            if($domain->getThemeBundleName() && $domain->getDomainHTMLTemplate()) {
                return $domain->getThemeBundleName() . ':' . $domain->getDomainHTMLTemplate();
            }
        }

    }


}
