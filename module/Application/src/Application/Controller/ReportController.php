<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Model\UserORM;
use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Application\Form\EvaluationForm;
use Application\Exception\AuthenticationRequired;
use Application\Exception\AccessDenied;

class ReportController extends AbstractActionController {
	/**
	 *
	 * @var AuthenticationService
	 */
	protected $empAuthServiceService;
	/**
	 *
	 * @var UserORM
	 */
	protected $modelAccessor;
	/**
	 *
	 * @var Container
	 */
	protected $userInfoContainer;
	public function onDispatch(MvcEvent $e) {
		$this->userInfoContainer = new Container ( USER_INFO_CONTAINER_NAME );
		$this->modelAccessor = $this->getServiceLocator ()->get ( 'UserModel' );
		$empAuthServiceService = $this->getServiceLocator ()->get ( 'EmpAuthService' );
		$this->empAuthServiceService = $empAuthServiceService;
		return parent::onDispatch ( $e );
	}
	public function dashboardAction() {
	}
	public function evaluationformAction() {
		$router = $this->serviceLocator->get ( 'Router' );
		$routeMatch = $router->match ( $this->request );
		$routeMatchParams = $routeMatch->getParams ();
		$userId = $routeMatchParams ['userId'];
		$event = $this->getEvent ();
		$authorised = true;
		$application = $event->getApplication ();
		$requestedUser = $this->modelAccessor->fetchAll ( array (
				'userId' => $userId 
		), null, null, array (
				'selfPath',
				'reportingPath' 
		) ) [0];
		if (empty ( $requestedUser )) {
			$errorMessage = 'Unidentified User';
			$event->setError ( ERROR_NEED_AUTHENTICATED_USER );
			$event->setParam ( 'exception', new AuthenticationRequired ( $errorMessage ) );
			$event->setParam ( 'redirectUri', '/user/dashboard' );
			return $application->getEventManager ()->trigger ( MvcEvent::EVENT_DISPATCH_ERROR, $event );
		} else {
			$reqUsP = $requestedUser ['selfPath'];
			$reqUsPRepP = $requestedUser ['reportingPath'];
		}
		$allInfo = $this->userInfoContainer->allInfo;
		$thisUsP = $allInfo ['selfPath'];
		if ($reqUsP == $thisUsP) {
			$authorised = false;
		}
		if ($reqUsPRepP !== $thisUsP) {
			$authorised = false;
		}
		if (! $this->resourceAllowed ( $reqUsP, $thisUsP )) {
			$authorised = false;
		}
		if (! $authorised) {
			$errorMessage = 'Forbidden Resource';
			$event->setError ( RESTRICTED_ACCESS_ERROR );
			$event->setParam ( 'exception', new AccessDenied ( $errorMessage ) );
			return $application->getEventManager ()->trigger ( MvcEvent::EVENT_DISPATCH_ERROR, $event );
		}
		
		$fields = array (
				'Performance in terms of Target' => 'targetScore',
				'Performance in terms of Response' => 'responseScore',
				'Performance in terms of JD' => 'jdScore',
				'Performance in terms of Reliability' => 'reliabilityScore',
				'Performance in terms of Team Spirit' => 'teamSpiritScore',
				'Performance in terms of Attendance' => 'attendanceScore',
				'Performance in terms of Attitude' => 'attitudeScore',
				'Performance in terms of Rules' => 'rulesScore',
				'Performance in terms of Team Lead\'s Feedback' => 'tlFeedBackScore',
				'Performance in terms of Peer\'s Feedback' => 'peerScore' 
		);
		
		$form = new EvaluationForm ( $fields );
		if ($this->getRequest ()->isPost ()) {
			$data = $this->getRequest ()->getPost ();
			$form->setData ( $data );
			if ($form->isValid ()) {
				$formData = $form->getData ();
				var_dump ( $formData );
				die ();
			} else {
				if (ENABLE_DEBUG_MODE) {
					echo "form is invalid";
					var_dump ( $form->getMessages () );
				}
			}
		}
		$view = new ViewModel ( array (
				'form' => $form,
				'fields' => $fields 
		) );
		return $view;
	}
	private function resourceAllowed($reqPath, $requestByPath) {
		$subOrdinateLength = strlen ( $reqPath );
		$superiorLenght = strlen ( $requestByPath );
		if (($subOrdinateLength - $superiorLenght) > STEP_LENGTH) {
			return false;
		}
		$isChild = strpos ( $reqPath, $requestByPath );
		if ($isChild !== false) {
			return true;
		}
		return false;
	}
}
