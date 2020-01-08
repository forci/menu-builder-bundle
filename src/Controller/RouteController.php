<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\MenuBuilder\Controller;

use Forci\Bundle\MenuBuilder\Filter\Route\RouteFilter;
use Forci\Bundle\MenuBuilder\Form\Route\FilterType;
use Forci\Bundle\MenuBuilder\Manager\RouteManager;
use Forci\Bundle\MenuBuilder\Repository\RouteParameterRepository;
use Forci\Bundle\MenuBuilder\Repository\RouteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class RouteController extends AbstractController {

    /** @var RouteManager */
    private $routeManager;

    /** @var RouteRepository */
    private $routeRepository;

    /** @var RouteParameterRepository */
    private $routeParameterRepository;

    /** @var RouterInterface */
    private $router;

    public function __construct(
        RouteManager $routeManager, RouteRepository $routeRepository,
        RouteParameterRepository $routeParameterRepository, RouterInterface $router
    ) {
        $this->routeManager = $routeManager;
        $this->routeRepository = $routeRepository;
        $this->routeParameterRepository = $routeParameterRepository;
        $this->router = $router;
    }

    public function listRoutesAction(Request $request) {
        $filter = new RouteFilter();
        $pagination = $filter->getPagination()->enable();
        $filterForm = $this->createForm(FilterType::class, $filter);
        $filter->load($request, $filterForm);
        $routes = $this->routeRepository->filter($filter);
        $data = [
            'routes' => $routes,
            'filter' => $filter,
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView()
        ];

        return $this->render('@ForciMenuBuilder/Route/list/list.html.twig', $data);
    }

    public function refreshListRowAction($id) {
        $route = $this->routeRepository->findOneById($id);

        $data = [
            'route' => $route
        ];

        return $this->render('@ForciMenuBuilder/Route/list/list_row.html.twig', $data);
    }

    public function makeSystemAction($id) {
        return $this->system($id, true);
    }

    public function makePublicAction($id) {
        return $this->system($id, false);
    }

    protected function system($id, $boolean) {
        $route = $this->routeRepository->findOneById($id);

        if (!$route) {
            return $this->json([
                'witter' => [
                    'text' => 'Route not found'
                ]
            ]);
        }

        $route->setIsSystem($boolean);
        $this->routeRepository->save($route);

        return $this->json([
            'success' => true,
            'refresh' => true
        ]);
    }

    public function updateRouteNameAction($id, Request $request) {
        $post = $request->request;
        // name, value, pk
        $name = $post->get('value', null);

        if (null === $name) {
            return new Response('Error - Empty value', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $route = $this->routeRepository->findOneById($id);
        $route->setName($name);
        $this->routeRepository->save($route);

        return new Response();
    }

    public function updateRouteParameterNameAction($id, Request $request) {
        $post = $request->request;
        // name, value, pk
        $name = $post->get('value', null);

        if (null === $name) {
            return new Response('Error - Empty value', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $parameter = $this->routeParameterRepository->findOneById($id);
        $parameter->setName($name);
        $this->routeParameterRepository->save($parameter);

        return new Response();
    }

    public function importAction(Request $request) {
        $referer = $request->headers->get('referer');

        try {
            $this->routeManager->importRouter($this->router);

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'witter' => [
                        'title' => 'Success',
                        'text' => 'Routes have been successfully imported'
                    ]
                ]);
            }

            if ($referer) {
                return $this->redirect($referer);
            }

            return $this->redirectToRoute('forci_menu_builder_dashboard');
        } catch (\Throwable $ex) {
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'witter' => [
                        'title' => 'Error',
                        'text' => 'There was an error'
                    ]
                ]);
            }

            if ($referer) {
                return $this->redirect($referer);
            }

            return $this->redirectToRoute('forci_menu_builder_dashboard');
        }
    }
}
