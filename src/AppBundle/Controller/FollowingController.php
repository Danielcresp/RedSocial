<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; //para el nick
use Symfony\Component\HttpFoundation\Session\Session;

use BackendBundle\Entity\Following;
use BackendBundle\Entity\User;


class FollowingController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }


    public function followAction(Request $request) {
		$user = $this->getUser();  /*obtine al usuario logeado*/
		$followed_id = $request->get('followed');/*el id del usuario a segir extraido*/

		$em = $this->getDoctrine()->getManager(); //para hacer la consulta en la DB
        $user_repo = $em->getRepository("BackendBundle:User"); //para hacer la consulta en la DB

		$followed = $user_repo->find($followed_id);/* comprueva si el usuario a seguir esta resgistrado*/

		$following = new Following(); /*se obtinen la tabla de following para introducir datos*/
		$following->setUser($user);
		$following->setFollowed($followed);

		$em->persist($following); /*se persiste los objetos en doctrine*/

		$flush = $em->flush(); /*se meten xd los datos a la DB*/

		if ($flush == null) { /*Si flush no debuelve ningun error*/
			$status = "Ahora estás siguiendo a este usuario !!";
		} else {
			$status = "No se ha podido seguir a este usuario !!";
		}

		return new Response($status); /*Se retorna la respueta a la peticion ajax*/
	}

	 public function unfollowAction(Request $request) {
		$user = $this->getUser();  /*obtine al usuario logeado*/
		$followed_id = $request->get('followed');/*el id del usuario a segir extraido*/

		$em = $this->getDoctrine()->getManager(); //para hacer la consulta en la DB

        $following_repo = $em->getRepository("BackendBundle:Following"); //para hacer la consulta en la DB
        $followed = $following_repo->findOneBy(array( /*Saca de la tabla following los datos que */
        	     "user"=>$user,  /*user sea igual a la variable obtenida*/
        	     "followed"=>$followed_id /*folloved sea igual a la variable obtenida*/
        ));

		$em->remove($followed); /*se remueve los objetos en la base de datos*/

		$flush = $em->flush(); /*se meten xd los datos a la DB*/

		if ($flush == null) { /*Si flush no debuelve ningun error*/
			$status = "Ahora dejado de siguir a este usuario !!";
		} else {
			$status = "No se ha podido dejar de seguir a este usuario !!";
		}

		return new Response($status); /*Se retorna la respueta a la peticion ajax*/
	}
}
