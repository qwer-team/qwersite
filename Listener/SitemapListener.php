<?php

namespace Main\SiteBundle\Listener;

use Symfony\Component\Routing\RouterInterface;

use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapListener implements SitemapListenerInterface
{
    private $router;
    private $em;

    public function __construct(RouterInterface $router, $em)
    {
        $this->router = $router;
        $this->em = $em;
    }
    
    public function setEm($em)
    {
        $this->em = $em;
    }
    
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();
        if (is_null($section) || $section == 'default') {
            //get absolute homepage url
            $url = $this->router->generate('index', array(), true);

            //add homepage url to the urlset named default
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $url,
                    new \DateTime(),
                    UrlConcrete::CHANGEFREQ_HOURLY,
                    1
                ),
                'default'
            );
            $repo = $this->em->getRepository("ItcAdminBundle:Menu\Menu");
            $menus = $repo->findBy(array("visible" => 1));
           // echo $menus->getId();
    
            foreach($menus as $menuItem ){
                $translit = $menuItem->translate("en")->getTranslit();
                if(is_null($translit)){
                    return;
                }
                $url = $this->router->generate('other', array("translit" => $menuItem->translate("en")->getTranslit(), "_locale"=>'en'), true);
                $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $url,
                    new \DateTime(),
                    UrlConcrete::CHANGEFREQ_HOURLY,
                    1
                ),
                'default'
            );
                
                 
           /* array_push($list, $sites->getId() );
            $list_keywords = array();
            foreach($menu_keywords as $val){
                if ( $val['menu_id'] == $sites->getId() )
                    array_push($list_keywords, $val['keyword_id']);
            }
            $entities_keywords[$sites->getId()] = implode(',', $list_keywords);*/
        }
          // var_dump($menus);
          //  echo count($menus);
        }
    }
}