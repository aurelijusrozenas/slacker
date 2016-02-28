<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    const COOKIE_SETTINGS = 'slacker_settings';
    const COOKIE_PARAM_WARNING_CLOSED = 'warningClosed';

    /**
     * @Route("/", name="index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statusAction()
    {
        return $this->render(
            'preloader.html.twig',
            [
                'url' => $this->generateUrl('index_loader'),
                'breadcrumbs' => $this->getBreadcrumbs('Home'),
            ]
        );
    }

    /**
     * @Route("/load", name="index_loader")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statusLoaderAction(Request $request)
    {
        $channels = $this->get('app.slack')->getChannels();

        $totalCount = 0;
        foreach ($channels as $channel) {
            if ($channel->count) {
                $totalCount += $channel->count;
            }
        }

        $json = json_decode($request->cookies->get(self::COOKIE_SETTINGS), true);
        $warningClosed = isset($json[self::COOKIE_PARAM_WARNING_CLOSED]) ? true : false;

        return $this->render(
            'default/status.html.twig',
            [
                'lastUpdatedAt' => $this->get('app.local_storage')->getLastUpdatedAt(),
                'storageCacheValidFor' => $this->container->getParameter('storage_cache_valid_for'),
                'warningClosed' => $warningClosed,
                'channels' => $channels,
                'totalCount' => $totalCount,
                'messageLimit' => $this->container->getParameter('message_limit'),
                'breadcrumbs' => $this->getBreadcrumbs('Home'),
                'extendBase' => false,
            ]
        );
    }

    /**
     * @Route("/clearCache", name="clear_cache")
     *
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearCacheAction(Request $request)
    {
        $status = $this->get('app.local_storage')->clearStorage();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => $status]);
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/closeWarning", name="close_warning")
     *
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function closeWarningAction(Request $request)
    {
        $json = json_decode($request->cookies->get(self::COOKIE_SETTINGS, "[]"), true);
        $json[self::COOKIE_PARAM_WARNING_CLOSED] = '1';
        $cookie = new Cookie(self::COOKIE_SETTINGS, json_encode($json));

        $response = new JsonResponse();
        $response->headers->setCookie($cookie);

        return $response;
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
