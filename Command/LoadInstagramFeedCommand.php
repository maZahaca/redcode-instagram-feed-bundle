<?php

namespace RedCode\InstagramFeedBundle\Command;

use Doctrine\ORM\EntityManager;
use RedCode\InstagramFeedBundle\Service\ApproveRule\ApproveRuleManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Wedding\CoreBundle\Entity\InstagramImage;

/**
 * @author maZahaca
 */
class LoadInstagramFeedCommand extends ContainerAwareCommand {

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
                ->setName('redcode:instagram:feed:load')
                ->setDescription('Load instagram feed by specified tags')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startFrom = $this->getStartLoadDate();
        $tsStart = $startFrom->getTimestamp();

        $tags = $this->getEm()->getRepository($this->getContainer()->getParameter('redcode.instagram.tag.class'))->findAll();
        $output->writeln("Loading images for tags: " . implode(', ', array_map(function ($tag) { return '#' . (string)$tag; }, $tags)));

        $config = array(
            'client_id' => $this->getContainer()->getParameter('redcode.instagram.app.client_id')
        );
        $api = \Instaphp\Instaphp::Instance(null, $config);

        /** @var ApproveRuleManager $approveRuleChecker */
        $approveRuleChecker = $this->getContainer()->get('redcode.instagram.rule.approver');

        foreach($tags as $tag) {
            $params = [];
            $exit = 0;
            $countToExit = 10;
            /** @var \Instaphp\Response $response */
            while($exit < $countToExit && ($response = $api->Tags->Recent($tag, $params)) && !$response->error) {
                $exit = 0;
                foreach($response->data as $item) {
                    if($tsStart > $item->created_time) {
                        $exit++;
                        continue;
                    }

                    $image = new InstagramImage();
                    $image->setTag($tag);
                    $image->setCreatedAt(date_create()->setTimestamp($item->created_time));
                    $image->setApproved($approveRuleChecker->check($item));
                    $image->setImageSrc($item->images->standard_resolution->url);
                    $image->setImageThumbnail($item->images->thumbnail->url);
                    $image->setUsername($item->user->username);
                    $image->setUserProfilePicture($item->user->profile_picture);
                    $this->getEm()->persist($image);
                }

                if($response->pagination) {
                    $params['max_tag_id'] = $response->pagination->next_max_tag_id;
                }
            }
            $this->getEm()->flush();
        }
    }

    /**
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function getStartLoadDate()
    {
        $startFrom = new \DateTime($this->getContainer()->getParameter('redcode.instagram.app.load.start_from'));

        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('i.createdAt _date')
            ->from($this->getContainer()->getParameter('redcode.instagram.image.class'), 'i')
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults(1)
        ;
        $result = $qb->getQuery()->getOneOrNullResult();
        if($result && $result['_date'] && $result['_date'] > $startFrom) {
            $startFrom = $result['_date'];
        }
        return $startFrom;
    }
}
