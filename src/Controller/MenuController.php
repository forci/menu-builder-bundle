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

use Forci\Bundle\MenuBuilder\Filter\Menu\MenuFilter;
use Forci\Bundle\MenuBuilder\Form\Menu\CreateType;
use Forci\Bundle\MenuBuilder\Form\Menu\FilterType;
use Forci\Bundle\MenuBuilder\Manager\MenuManager;
use Forci\Bundle\MenuBuilder\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController {
    
    /** @var MenuManager */
    private $menuManager;
    
    /** @var MenuRepository */
    private $menuRepository;

    public function __construct(
        MenuManager $menuManager, MenuRepository $menuRepository
    ) {
        $this->menuManager = $menuManager;
        $this->menuRepository = $menuRepository;
    }

    public function listAction(Request $request) {
        $filter = new MenuFilter();
        $pagination = $filter->getPagination()->enable();
        $filterForm = $this->createForm(FilterType::class, $filter);
        $filter->load($request, $filterForm);
        $menus = $this->menuRepository->filter($filter);
        $data = [
            'menus' => $menus,
            'filter' => $filter,
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView()
        ];

        return $this->render('@ForciMenuBuilder/Menu/list.html.twig', $data);
    }

    public function refreshListRowAction($id) {
        $menu = $this->menuRepository->findOneById($id);

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
        $menu = $this->menuManager->findOneById($id);

        if (!$menu) {
            return $this->json([
                'witter' => [
                    'text' => 'Menu not found'
                ]
            ]);
        }

        $menu->setIsSystem($boolean);
        $this->menuManager->save($menu);

        return $this->json([
            'success' => true,
            'refresh' => true
        ]);
    }

    public function makeApiVisibleAction($id) {
        return $this->apiVisible($id, true);
    }

    public function makeApiInvisibleAction($id) {
        return $this->apiVisible($id, false);
    }

    protected function apiVisible($id, $boolean) {
        $menu = $this->menuManager->findOneById($id);

        if (!$menu) {
            return $this->json([
                'witter' => [
                    'text' => 'Menu not found'
                ]
            ]);
        }

        $menu->setIsApiVisible($boolean);
        $this->menuManager->save($menu);

        return $this->json([
            'success' => true,
            'refresh' => true
        ]);
    }

    public function previewAction($id) {
        $menu = $this->menuRepository->findOneById($id);

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

        $menu = $this->menuManager->findOneById($id);
        $menu->setName($name);
        $this->menuManager->save($menu);

        return new Response();
    }

    public function createAction(Request $request) {
        $menu = $this->menuManager->create();
        $form = $this->createForm(CreateType::class, $menu);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->menuManager->save($menu);

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
        $menu = $this->menuRepository->findOneById($id);

        $form = $this->createForm(CreateType::class, $menu);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->menuManager->save($menu);

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
