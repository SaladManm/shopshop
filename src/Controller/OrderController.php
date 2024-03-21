<?php
declare(strict_types = 1);

namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use PDO;
use Twig\Environment;
use MyApp\Model\OrdersModel; 
class OrderController
{
    private $twig;
    private $pdo;
    private $orderModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->pdo = $dependencyContainer->get('PDO');
        $this->orderModel = $dependencyContainer->get('OrderModel'); 
    }

    public function orders()
    {
        $orders = $this->orderModel->getAllOrders();
        echo $this->twig->render('OrderController/order.html.twig', ['orders' => $orders]);
    }
}
