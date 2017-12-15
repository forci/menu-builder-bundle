<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\MenuBuilderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Forci\Bundle\MenuBuilderBundle\Filter\Route\RouteFilter;
use Forci\Bundle\MenuBuilderBundle\Form\Route\FilterType;

class RouteController extends Controller {

    public function listRoutesAction(Request $request) {
        $repo = $this->get('forci_menu_builder.repo.routes');
        $filter = new RouteFilter();
        $pagination = $filter->getPagination()->enable();
        $filterForm = $this->createForm(FilterType::class, $filter);
        $filter->load($request, $filterForm);
        $routes = $repo->filter($filter);
        $data = [
            'routes' => $routes,
            'filter' => $filter,
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView()
        ];

        return $this->render('@ForciMenuBuilder/Route/list/list.html.twig', $data);
    }

    public function refreshListRowAction($id) {
        $repo = $this->get('forci_menu_builder.repo.routes');
        $route = $repo->findOneById($id);

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
        $repo = $this->container->get('forci_menu_builder.repo.routes');
        $route = $repo->findOneById($id);

        if (!$route) {
            return $this->witter([
                'text' => 'Route not found'
            ]);
        }

        $route->setIsSystem($boolean);
        $repo->save($route);

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

        $repo = $this->container->get('forci_menu_builder.repo.routes');
        $route = $repo->findOneById($id);
        $route->setName($name);
        $repo->save($route);

        return new Response();
    }

    public function updateRouteParameterNameAction($id, Request $request) {
        $post = $request->request;
        // name, value, pk
        $name = $post->get('value', null);

        if (null === $name) {
            return new Response('Error - Empty value', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $repo = $this->container->get('forci_menu_builder.repo.routes_parameters');
        $parameter = $repo->findOneById($id);
        $parameter->setName($name);
        $repo->save($parameter);

        return new Response();
    }

    public function importAction(Request $request) {
        /** @var $router Router */
        $router = $this->container->get('router');

        $manager = $this->container->get('forci_menu_builder.manager.routes');

        $referer = $request->headers->get('referer');

        try {
            $manager->importRouter($router);

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
        } catch (\Exception $ex) {
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
