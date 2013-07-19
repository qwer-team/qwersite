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
 * News controller.
 * Routing registered in routing.yml
 */
class NewsController extends ControllerHelper //Controller
{
    private $menu = array( 
        'ItcAdminBundle:Menu\Menu',
        'ItcAdminBundle:Menu\MenuTranslation'
    );//@Route("/{page}", name="news", requirements={"page" = "\d+"}, defaults={page"=1})
    /**
     * 
     * @Route("/", name="news")
     * @Template()
     */
    public function indexAction($translit, $entity, $page)
    {
        $coulonpage = 2;
        $locale =  $this->getRequest()->getLocale();
        
        $repo = $this->getDoctrine()->getManager()->getRepository('ItcAdminBundle:Menu\Menu');
        
        $qb = $repo->getChildrenQb($entity);
        $paginator = $this->get('knp_paginator');
        $pageNumber = $this->get('request')->query->get('page', $page);
        $children = $paginator->paginate(
            $qb,
            $pageNumber/*page number*/,
            $coulonpage/*limit per page*/ //array('distinct' => false)
        );
        
        return array( 
            'entity' => $entity,
            'news'   => $children,
            'locale' => $locale,
            'page'      => $page,
            'coulonpage' => $coulonpage,
        );
    }

    /**
     * @Route("/{translit}", name="onenews")
     * @Template()
     */
    public function onenewsAction( $translit )
    {

        $locale =  LanguageHelper::getLocale();

        $entity = $this->getEntityTranslit( $this->menu, $translit )
                       ->getOneOrNullResult();

        return array( 
            'entity' => $entity,
            'locale' => $locale
        );
    }
    
 
    
    /**
     * @Route("/newstag/{keyword}",  name="newstag")
     * @Template()
     */
    public function tagAction($keyword)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = LanguageHelper::getLocale();
        $deflocale = LanguageHelper::getDefaultLocale();
        if ($locale == $deflocale) {
            $entity = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                            ->createQueryBuilder('M')
                            ->select('M')
                            ->where("M.translit = :translit ")
                            ->setParameter('translit', $keyword)
                            ->getQuery()->getOneOrNullResult();
        } else {
            $entity = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                            ->createQueryBuilder('M')
                            ->select('M, T')
                            ->leftJoin('M.translations', 'T', 'WITH', "T.locale = :locale")
                            ->setParameter('locale', $locale)
                            ->where("T.property='translit'")
                            ->getQuery()->getOneOrNullResult();
        }
        if (!$entity) {
            throw $this->createNotFoundException('The keyword does not exist');
        }
        $ent = $em->getRepository('ItcAdminBundle:Menu\Menu')->findOneByRouting('allnews');
        $entities = $entity->getMenus();
        return array('entity' => $ent,
            'locale' => $locale,
            'entities' => $entities
        );
    }
}
