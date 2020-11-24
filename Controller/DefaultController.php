<?php

namespace MauticPlugin\RemoteLoginBundle\Controller;

use Guzzle\Batch\ExceptionBufferingBatch;
use Mautic\CoreBundle\Controller\CommonController;
use Mautic\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class DefaultController extends CommonController {


	private $key      = 'B-O%BADTCyN@#rY^QDiRi5B{4-L>|.X-l2cot@@lAhdCkqi-0+YT;9Jgbj@eLig';


	/**
	 * @return string
	 */
	protected function getKey () {
		return $this->key;
	}


	/**
	 * @param $key string
	 *
	 * @return bool
	 */
	private function checkKey( $key, $userMail ) {

		$secureKey = $this->generateKey( $userMail );

		return $secureKey == $key;

	}


	/**
	 * @param $userMail string
	 * @return string
	 */
	private function generateKey ( $userMail ) {

		$keyMD5 = md5( $this->getKey() );

		return sha1($keyMD5.$userMail);
	}



	/**
	 * @param User|null $user
	 *
	 * @void
	 */
	private function checkLogin( User $user = null ) {

		if( !$user || !$this->checkKey($this->request->get('secret'), $user->getEmail()) ){
			throw new BadRequestHttpException('Invalid user or secret');
		}

	}


	/**
	 * @param $email
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * @throws BadRequestHttpException
	 */
	public function loginAction ( $email ) {
		$userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);

		/**
		 * @var $user User|null
		 */
		$user = $userRepository->findOneBy([
			'email' => $email
		]);
		
		/**
		 * @throws BadRequestHttpException
		 */
		$this->checkLogin( $user );


		return $this->doLogin( $user );

	}

	/**
	 * @param User $user
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	private function doLogin( User $user ) {
		
		$token = new UsernamePasswordToken( $user, $user->getPassword(), "secured_area", $user->getRoles() );
		$this->get( "security.token_storage" )->setToken( $token );

		$event = new InteractiveLoginEvent( $this->request, $token );
		$this->get( "event_dispatcher" )->dispatch( "security.interactive_login", $event );

		$url = $this->generateUrl( 'mautic_dashboard_index' );

		return $this->redirect( $url );
	}


}