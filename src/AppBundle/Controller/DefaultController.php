<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function statusAction()
    {
        $channels = $this->get('app.slack')->getChannels();

        $totalCount = 0;
        foreach ($channels as $channel) {
            if ($channel->count) {
                $totalCount += $channel->count;
            }
        }

        return $this->render(
            'default/status.html.twig',
            [
                'lastUpdatedAt' => $this->get('app.local_storage')->getLastUpdatedAt(),
                'channels' => $channels,
                'totalCount' => $totalCount,
                'messageLimit' => $this->container->getParameter('message_limit'),
                'breadcrumbs' => $this->getBreadcrumbs('Home'),
            ]
        );
    }

    protected function getBreadcrumbs($active)
    {
        $breadcrumbs = [
            'Home' => [
                'url' => '/',
            ],
        ];

        foreach ($breadcrumbs as $key => $breadcrumb) {
            $breadcrumbs[$key]['active'] = $key == $active;
        }

        return $breadcrumbs;
    }
}
