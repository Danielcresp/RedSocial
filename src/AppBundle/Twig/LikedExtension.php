<?php
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;

class LikedExtension extends \Twig_Extension {
	protected $doctrine;
	
	public function __construct(RegistryInterface $doctrine) {
		$this->doctrine = $doctrine;
	}
	
	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('liked', array($this, 'likedFilter'))
		);
	}
	
	public function likedFilter($user, $publication){
		$like_repo = $this->doctrine->getRepository('BackendBundle:Like'); 
		$publication_liked = $like_repo->findOneBy(array( /*buscar el la Bd si ya se dio like*/
			"user" => $user,                  /*usurio*/
			"publication" => $publication     /*con publication si son iguales*/
		));
		
		if(!empty($publication_liked) && is_object($publication_liked)){   /*si ya esta el registro del like*/
			$result = true;
		}else{
			$result = false;
		}
		
		return $result;
	}
	
	public function getName() {
		return 'liked_extension';
	}
}
