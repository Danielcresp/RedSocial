<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use BackendBundle\Entity\Publication;
use AppBundle\Form\PublicationType;

class PublicationController extends Controller{

    public function indexAction (Request $request){

        $em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		
		$publication = new Publication();
		$form = $this->createForm(PublicationType::class, $publication);
        
        return $this->render('AppBundle:Publication:home.html.twig',array(
			'form' => $form->createView(),
		));
    }
}
