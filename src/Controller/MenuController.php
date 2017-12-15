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
use Forci\Bundle\MenuBuilderBundle\Entity\Menu;
use Forci\Bundle\MenuBuilderBundle\Filter\Menu\MenuFilter;
use Forci\Bundle\MenuBuilderBundle\Form\Menu\CreateType;
use Forci\Bundle\MenuBuilderBundle\Form\Menu\FilterType;

class MenuController extends Controller {

    public function listAction(Request $request) {
        $repo = $this->get('forci_menu_builder.repo.menus');
        $filter = new MenuFilter();
        $pagination = $filter->getPagination()->enable();
        $filterForm = $this->createForm(FilterType::class, $filter);
        $filter->load($request, $filterForm);
        $menus = $repo->filter($filter);
        $data = [
            'menus' => $menus,
            'filter' => $filter,
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView()
        ];

        return $this->render('@ForciMenuBuilder/Menu/list.html.twig', $data);
    }

    public function refreshListRowAction(Menu $menu) {
        $data = [
            'menu' => $menu
        ];

        return $this->render('@ForciMenuBuilder/Menu/list_row.html.twig', $data);
    }

    public function makeSystemAction($id) {
        return $this->system($id, true);
    }

    public function makePublicAction($id) {
        return $this->system($id, false);
    }

    protected function system($id, $boolean) {
        $repo = $this->container->get('forci_menu_builder.repo.menus');
        $menu = $repo->findOneById($id);

        if (!$menu) {
            return $this->witter([
                'text' => 'Menu not found'
            ]);
        }

        $menu->setIsSystem($boolean);
        $repo->save($menu);

        return $this->json([
            'success' => true,
            'refresh' => true
        ]);
    }

    public function previewAction(Menu $menu) {
        $data = [
            'menu' => $menu
        ];

        return $this->render('@ForciMenuBuilder/Menu/preview.html.twig', $data);
    }

    public function updateNameAction($id, Request $request) {
        $post = $request->request;
        // name, value, pk
        $name = $post->get('value', null);

        if (null === $name) {
            return new Response('Error - Empty value', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $repo = $this->container->get('forci_menu_builder.repo.menus');
        $menu = $repo->findOneById($id);
        $menu->setName($name);
        $repo->save($menu);

        return new Response();
    }

    public function createAction(Request $request) {
        $menu = new Menu();
        $form = $this->createForm(CreateType::class, $menu);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->container->get('forci_menu_builder.manager.menus');
            $manager->save($menu);

            return $this->redirectToRoute('forci_menu_builder_menu_edit', [
                'id' => $menu->getId()
            ]);
        }

        $data = [
            'form' => $form->createView(),
            'menu' => $menu
        ];

        return $this->render('@ForciMenuBuilder/Menu/create.html.twig', $data);
    }

    public function editAction($id, Request $request) {
        $repo = $this->get('forci_menu_builder.repo.menus');
        $menu = $repo->findOneById($id);

        $form = $this->createForm(CreateType::class, $menu);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->container->get('forci_menu_builder.manager.menus');
            $manager->save($menu);

            return $this->redirectToRoute('forci_menu_builder_menu_edit', [
                'id' => $menu->getId()
            ]);
        }

        $data = [
            'form' => $form->createView(),
            'menu' => $menu
        ];

        return $this->render('@ForciMenuBuilder/Menu/edit.html.twig', $data);
    }
}
