<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use BackendBundle\Entity\User;
use AppBundle\Form\RegisterType;

class UserController extends Controller
{
    public function loginAction (Request $request){
        
        return $this->render('AppBundle:User:login.html.twig',
                array("titulo"=>"Tenia que hacer una red social"));
    }
    
    
    public function registerAction(Request $request) {
        
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $em = $this->getDoctrine()->getManeger();
                #$user_repo = $em->getRepository("BackendBundle:User");
                
                $query = $em->createQuery('SELECT u FROM BackleBundle:User U WHERE u.mail = :email OR u.nick = :nick')
                        ->setParameter('email', $form->get("email")->getData())
                        ->getParameter('nick', $form->get("nick")->getData());
                
                $user_isset = $query->getResult();
                
                if(count($user_isset)== 0){
                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->econdePassword($from->get("password")->getData(), $user->getSalt());
                    
                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImage(null);
                    
                    $em->persist($user);
                    $flush = $em->flush();
                    
                    if($flush == null){
                        $status = "Te has resgitrado correctamente";
                        return $this->redirect("login");
                    }else{
                        $status = "No se ha registrado correctamente";
                    }
                    
                }else{
                    $status = "El usuario ya existe ¡¡";
                }
                
                
            }else{
                $status = "No te has registrado correctamente ¡¡";
            }
        }
        return $this->render('AppBundle:User:register.html.twig',
                array(
                    "form"=> $form->createView()
                ));
    }
}
