<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; //para el nick
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\User;
use AppBundle\Form\RegisterType;
use AppBundle\Form\UserType;

class UserController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function loginAction(Request $request) {

        if (is_object($this->getUser())) { /* si el objeto getUser debuelve un objeto (esta logeado) */
            return $this->redirect('home'); /* que retorne a home */
        }

        $authenticationUtils = $this->get('security.authentication_utils'); /* carga el servicio de autenticacion */
        $error = $authenticationUtils->getLastAuthenticationError(); /* obtiene dato si sucede un error */
        $lastUsername = $authenticationUtils->getLastUsername(); /* saca la informacion si falla si logea */


        return $this->render('AppBundle:User:login.html.twig', array(
                    'last_username' => $lastUsername, /* enviar a la vista la autenticacion */
                    'error' => $error /* envia los errores a la vista */
        ));
    }

    public function registerAction(Request $request) {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request); //Obtiene la request del formulario
        if ($form->isSubmitted()) {//Verifica si el fromulario se ha enviado 
            if ($form->isValid()) { //verifica si el formulario es valido
                $em = $this->getDoctrine()->getManager(); //Cargar la entidad para hacer consultas en la DB
                #$user_repo = $em->getRepository("BackendBundle:User");
                //CONUSLTA CON QUERYBILD PARA VERIFICAS SI NO SE HA REGISTRADO ANTERIORMENTE
                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick')
                        ->setParameter('email', $form->get("email")->getData())//ESTABLECE LOS PARAMETROIS DE LA CONSULTA (saca el dato del formulario)
                        ->setParameter('nick', $form->get("nick")->getData()); //saca el dato del formulario 

                $user_isset = $query->getResult(); //verifica si el usuario ya esta registrado

                if (count($user_isset) == 0) { //si el usuario es nuevo se ejecuta el codigo
                    $factory = $this->get("security.encoder_factory"); //Sifrar la contraseña con escoder
                    $encoder = $factory->getEncoder($user); //obtiene el encoder de la clase Usuario
                    $password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt()); //Sifrando la Password
                    //SET POR DEFECTO CAMPOS DE LA DB
                    $user->setPassword($password); //setea la password
                    $user->setRole("ROLE_USER"); //roll por defecto
                    $user->setImage(null); //la imagen por defecto sera null
                    //GUARDAR LA INFORMACION OBTENIDA
                    $em->persist($user); //guarda el objeto y luego lo guarda el la base de datos(los persiste)
                    $flush = $em->flush(); //pasa los obtetos persistidos a la base de datos 

                    if ($flush == null) {//verificar si se guarado bien en la base de datos
                        $status = "Te has resgistrado correctamente";
                        $this->session->getFlashBag()->add("status", $status);
                        return $this->redirect("login");
                    } else {//si da error
                        $status = "No se ha registrado correctamente";
                    }
                } else {
                    $status = "El usuario ya existe ¡¡";
                }
            } else {
                $status = "No te has registrado correctamente ¡¡";
            }

            $this->session->getFlashBag()->add("status", $status);
        }
        return $this->render('AppBundle:User:register.html.twig', array(
                    "form" => $form->createView()
        ));
    }

    public function nickTestAction(Request $request) {
        $nick = $request->get("nick"); //obtener del request en nick 

        $em = $this->getDoctrine()->getManager(); //para hacer la consulta en la DB
        $user_repo = $em->getRepository("BackendBundle:User"); //para hacer la consulta en la DB
        $user_isset = $user_repo->findOneBy(array("nick" => $nick)); //consulta si nick es igual

        $result = "used";
        if (count($user_isset) >= 1 && is_object($user_isset)) {
            $result = "used";
        } else {
            $result = "unused"; //respuesta si esta o no usado
        }

        return new Response($result); //manda el resultado si esta o no usado
    }

    public function editUserAction(Request $request) {

        $user = $this->getUser(); /* obtine el objeto del usuario ya logeado */
        $user_image = $user->getImage(); /* obtine la imagen que tiene el usuario */
        $form = $this->createForm(UserType::class, $user); /* se obtinen los datos del usuario y se meten al formulario */

        $form->handleRequest($request); //Obtiene la request del formulario
        if ($form->isSubmitted()) {//Verifica si el fromulario se ha enviado 
            if ($form->isValid()) { //verifica si el formulario es valido
                $em = $this->getDoctrine()->getManager(); //Cargar la entidad para hacer consultas en la DB
                #$user_repo = $em->getRepository("BackendBundle:User");
                //CONUSLTA CON QUERYBILD PARA VERIFICAS SI NO SE HA REGISTRADO ANTERIORMENTE
                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick')
                        ->setParameter('email', $form->get("email")->getData())//ESTABLECE LOS PARAMETROIS DE LA CONSULTA (saca el dato del formulario)
                        ->setParameter('nick', $form->get("nick")->getData()); //saca el dato del formulario 

                $user_isset = $query->getResult(); //verifica si el usuario ya esta registrado

                if (count($user_isset) == 0 || ($user->getEmail() == $user_isset[0]->getEmail() && $user->getNick() == $user_isset[0]->getNick())) { //si el usuario es nuevo se ejecuta el codigo y si son iguales el email el nick al usuario en la base de datos que perimita modificarlos

                    /* Actualiza la imagen */
                    $file = $form["image"]->getData(); /* captura la imagen del fromulario */

    
                    if (!empty($file) && $file != null) { /* si no esta vacia y file no esta null */
                        $ext = $file->guessExtension(); /* obtener el tipo de extencion de la imagen */


                        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
                            $file_name = $user->getId() . time() . "." . $ext; /* el nombre del fichero con el id del user y tipo de extencion */
                            $file->move("uploads/users", $file_name); /* en  que carpeta se gurdara la imagen */

                            $user->getImagen($file_name);
                        }
                    } else {
                        $user->setImage($user_image);
                    }

                    //GUARDAR LA INFORMACION OBTENIDA
                    $em->persist($user); //guarda el objeto y luego lo guarda el la base de datos(los persiste)(OBTINE LOS DATOS)
                    $flush = $em->flush(); //pasa los obtetos persistidos a la base de datos(LOS GUARADA EN LA DB)

                    if ($flush == null) {//verificar si se guarado bien en la base de datos
                        $status = "Has modificado correctamente tus datos";
                    } else {//si da error
                        $status = "No se ha modificado correctamente tus datos";
                    }
                } else {
                    $status = "El usuario ya existe ¡¡";
                }
            } else {
                $status = "No se han actualizado correctamente tus datos ¡¡";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirect('my-data');
        }
        return $this->render('AppBundle:User:edit_user.html.twig', array(
                    "form" => $form->createView() /* se genera el formulario en la vista */
        ));
    }

    public function usersAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT u FROM BackendBundle:User u ORDER BY u.id ASC";/* consulta a la DB con dql*/
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator'); /*servicio de knp_paginator*/
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5 /*5 registros por pagina*/
        );

        return $this->render('AppBundle:User:users.html.twig', array(
                    'pagination' => $pagination
        ));
    }
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $search = trim($request->query->get("search", null));

        if ($search == null) {
            return $this->redirect($this->generateURL('home_publications'));
        }

        $dql = "SELECT u FROM BackendBundle:User u "
                . "WHERE u.name LIKE :search OR u.surname LIKE :search "
                . "OR u.nick LIKE :search ORDER BY u.id ASC"; /*la busqueda*/
        $query = $em->createQuery($dql)->setParameter('search', "%$search%"); /*une el contedido de la busqueda*/

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );
        return $this->render('AppBundle:User:users.html.twig', array(
                    'pagination' => $pagination
        ));
    }
    

}
