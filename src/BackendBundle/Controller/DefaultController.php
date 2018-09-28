<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();//accede a las entidades mediante doctrine
        $user_repo = $em->getRepository("BackendBundle:User");//respositorio que tiene un modelo de cosulta
        
        $user = $user_repo->find(1); //obteneel primer usuario
        echo"Bienvenido".$user->getName()." ".$user->getSurname(); //muestra en un strim el nombre con el apéllido de usuario obtenido
        var_dump($user);//para ver los que genera la cosulta
        die();//paar que synfony no pida una vista
        
        return $this->render('BackendBundle:Default:index.html.twig');
    }
}
