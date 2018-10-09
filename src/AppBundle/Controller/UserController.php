<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;//para el nick
use Symfony\Component\HttpFoundation\Session\Session;


use BackendBundle\Entity\User;
use AppBundle\Form\RegisterType;

class UserController extends Controller
{
    private $session;

    public function __construct() {
	$this->session = new Session();
   }
    
    public function loginAction (Request $request){

        if (is_object($this->getUser())) { /*si el objeto getUser debuelve un objeto (esta logeado)*/
            return $this->redirect('home'); /*que retorne a home*/
        }

        $authenticationUtils = $this->get('security.authentication_utils'); /*carga el servicio de autenticacion*/
        $error = $authenticationUtils->getLastAuthenticationError(); /*obtiene dato si sucede un error*/
        $lastUsername = $authenticationUtils->getLastUsername(); /*saca la informacion si falla si logea*/

        
        return $this->render('AppBundle:User:login.html.twig', array(
                    'last_username' => $lastUsername, /*enviar a la vista la autenticacion*/
                    'error' => $error /*envia los errores a la vista*/
        ));
    }
    
    
    public function registerAction(Request $request) {
        
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        
        $form->handleRequest($request);//Obtiene la request del formulario
        if($form->isSubmitted()){//Verifica si el fromulario se ha enviado 
            
            if($form->isValid()){ //verifica si el formulario es valido
                
                $em = $this->getDoctrine()->getManager(); //Cargar la entidad para hacer consultas en la DB
                
                #$user_repo = $em->getRepository("BackendBundle:User");
                
                //CONUSLTA CON QUERYBILD PARA VERIFICAS SI NO SE HA REGISTRADO ANTERIORMENTE
                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick')
						->setParameter('email', $form->get("email")->getData())//ESTABLECE LOS PARAMETROIS DE LA CONSULTA (saca el dato del formulario)
						->setParameter('nick', $form->get("nick")->getData());//saca el dato del formulario 
                
		$user_isset = $query->getResult();//verifica si el usuario ya esta registrado
                
                if (count($user_isset) == 0)  { //si el usuario es nuevo se ejecuta el codigo
                    $factory = $this->get("security.encoder_factory");//Sifrar la contraseña con escoder
                    $encoder = $factory->getEncoder($user);//obtiene el encoder de la clase Usuario
                    $password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());//Sifrando la Password
                                 
                    //SET POR DEFECTO CAMPOS DE LA DB
                    $user->setPassword($password); //setea la password
                    $user->setRole("ROLE_USER");//roll por defecto
                    $user->setImage(null);//la imagen por defecto sera null
                    
                    //GUARDAR LA INFORMACION OBTENIDA
                    $em->persist($user);//guarda el objeto y luego lo guarda el la base de datos(los persiste)
                    $flush = $em->flush();//pasa los obtetos persistidos a la base de datos 
                    
                    if($flush == null){//verificar si se guarado bien en la base de datos
                        $status = "Te has resgitrado correctamente";
                        $this->session->getFlashBag()->add("status", $status);
                        return $this->redirect("login");
                    }else{//si da error
                        $status = "No se ha registrado correctamente";
                    }
                    
                }else{
                    $status = "El usuario ya existe ¡¡";
                }
                
                
            }else{
                $status = "No te has registrado correctamente ¡¡";
            }
            
            $this->session->getFlashBag()->add("status", $status);
        }
        return $this->render('AppBundle:User:register.html.twig',
                array(
                    "form"=> $form->createView()
                ));
    }

    public function nickTestAction(Request $request) {
	$nick = $request->get("nick");//obtener del request en nick 

	$em = $this->getDoctrine()->getManager();//para hacer la consulta en la DB
	$user_repo = $em->getRepository("BackendBundle:User");//para hacer la consulta en la DB
	$user_isset = $user_repo->findOneBy(array("nick" => $nick));//consulta si nick es igual

	$result = "used";
	if (count($user_isset) >= 1 && is_object($user_isset)) {
		$result = "used";
	} else {
		$result = "unused";//respuesta si esta o no usado
	}

	return new Response($result);//manda el resultado si esta o no usado
    }

    public function editUserAction(Request $request) {
        return $this->render('AppBundle:User:edit_user.html.twig', array(
        ));
    }

}
