<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Main\SiteBundle\Tools\ControllerHelper;
/**
 * AllNews controller.
 * Routing registered in routing.yml
 */
class AllNewsController extends Controller //Controller
{
    private $menu = array( 
        'ItcAdminBundle:Menu\Menu',
        'ItcAdminBundle:Menu\MenuTranslation'
    );
    /**
     * @Route("/allnew", name="allnews")
     * @Template()
     */
    public function indexAction($translit)
    {
        $locale =  $this->getRequest()->getLocale();
//echo( $locale );
 $wheres[] = "M.translit = :translit ";
        $parameters["translit"] =$translit;
        $orderby = array( "M.date_create", "DESC" );
        
        $repo = $this->getDoctrine()->getManager()->getRepository('ItcAdminBundle:Menu\Menu');
        
        list($entity, $entities, $children) = $repo->getMenuLevel2($translit);
        return array( 
            'entities' => $entities,
            'entity' => $entity,
            'news'   => $children,
            'locale' => $locale
        );
        //return array();
    }
 
}
