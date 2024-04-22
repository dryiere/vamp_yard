<?php

namespace App\Controller\Admin;

use App\Controller\LoginController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Post;
use App\Entity\Reply;
use Symfony\Component\Routing\Generator\UrlGenerator;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index(); own commented

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        if(!$this->getUser()) {
            return $this->redirect('/login');
        }
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Vamp Yard');
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Main Site', 'fa fa-home', 'app_topic');
        yield MenuItem::linkToCrud('Admin', 'fa fa-kiwi-bird', User::class);
        yield MenuItem::linkToCrud('Topic', 'fa fa-inbox', Topic::class);
        yield MenuItem::linkToCrud('Post', 'fa fa-hurricane', Post::class);
        yield MenuItem::linkToCrud('Reply', 'fa fa-skull', Reply::class);
        
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
