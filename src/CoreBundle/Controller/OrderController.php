<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 24/01/2017
 * Time: 06:40
 */

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class OrderController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreBundle:Order:index.html.twig');
    }
}