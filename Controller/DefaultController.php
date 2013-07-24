<?php

namespace RedCode\InstagramFeedBundle\Controller;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as MVC;
use FOS\RestBundle\Controller\Annotations as REST;
use JMS\SecurityExtraBundle\Annotation as Security;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author maZahaca
 */ 
class DefaultController extends Controller
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $tagClass;

    /**
     * @var string
     */
    private $imageClass;

    /**
     * @var int
     */
    private $pageItems;

    /**
     * @DI\InjectParams({
     *     "doctrine"    = @DI\Inject("doctrine"),
     *     "tagClass"    = @DI\Inject("%redcode.instagram.tag.class%"),
     *     "imageClass"  = @DI\Inject("%redcode.instagram.image.class%"),
     *     "pageItems"   = @DI\Inject("%redcode.instagram.app.page_items%")
     * })
     */
    public function __construct(Registry $doctrine, $tagClass, $imageClass, $pageItems)
    {
        $this->em           = $doctrine->getManager();
        $this->tagClass     = $tagClass;
        $this->imageClass   = $imageClass;
        $this->pageItems    = $pageItems;
    }

    /**
     * @MVC\Route("/instagram/feed", name="redcode_instagram_feed")
     * @REST\View()
     */
    public function instagramFeedAction(Request $request)
    {
        $page = $request->get('page', 0);
        $pages = ceil($this->getPageCount() / $this->pageItems);

        $nextPage = $page >= $pages ? null : $page + 1;

        $query = $this->getQueryBuilder()
            ->orderBy('i.createdAt', 'DESC');

        $images = $query
            ->setMaxResults($this->pageItems)
            ->setFirstResult($this->pageItems * $page)
            ->getQuery()->getResult();

        if($request->get('async', false)) {
            return $this->render('RedCodeInstagramFeedBundle:Default:instagramFeedItems.html.twig', ['nextPage'=>$nextPage, 'images'=> $images ]);
        }
        return ['nextPage'=>$nextPage, 'images'=> $images ];
    }

    public function getPageCount()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('count(i.id) _cnt');
        $result = $qb->getQuery()->getOneOrNullResult();
        if($result && $result['_cnt']) {
            return $result['_cnt'];
        }
        return 0;
    }

    protected function getQueryBuilder()
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('i')
            ->from($this->imageClass, 'i')
            ->where('i.approved = true');
        return $qb;
    }
}
