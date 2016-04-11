<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\UserORM;
use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Application\Utilities\NumberPlay;
use Application\Form\UserAdd;
use Zend\Json\Json;
use Application\Form\UserUpdate;
use Application\Exception\AuthenticationRequired;

class AdminController extends AbstractActionController {
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
	protected $lengthIndexedArray;
	/**
	 *
	 * @var Container
	 */
	protected $userInfoContainer;
	private function findChildsV1($parentPath) {
		$trueStepLength = strlen ( $parentPath ) + STEP_LENGTH + 1;
		$childs = array ();
		if (! empty ( $this->lengthIndexedArray [$trueStepLength] )) {
			$possibleChilds = $this->lengthIndexedArray [$trueStepLength];
			foreach ( $possibleChilds as $childId => $childPath ) {
				$isChild = strpos ( $childPath, $parentPath );
				if ($isChild !== false) {
					$childs [$childId] = $childPath;
				}
			}
		}
		return $childs;
	}
	public function onDispatch(MvcEvent $e) {
		$this->userInfoContainer = new Container ( USER_INFO_CONTAINER_NAME );
		$this->modelAccessor = $this->getServiceLocator ()->get ( 'UserModel' );
		$empAuthServiceService = $this->getServiceLocator ()->get ( 'EmpAuthService' );
		$this->empAuthServiceService = $empAuthServiceService;
		return parent::onDispatch ( $e );
	}
	public function indexAction() {
		return new ViewModel ();
	}
	private function getUserList() {
		$existingUsers = $this->modelAccessor->fetchAll ( null, null, null, array (
				'personalEmailId',
				'id',
				'employeeCode' 
		) );
		$userList = array ();
		if (! empty ( $existingUsers )) {
			foreach ( $existingUsers as $key => $info ) {
				if (empty ( $info ['employeeCode'] )) {
					$employeeCode = 'Employee Code Not Specified';
				} else {
					$employeeCode = $info ['employeeCode'];
				}
				$userList [$info ['id']] = $info ['personalEmailId'] . '(' . $employeeCode . ')';
			}
			natsort ( $userList );
		}
		return $userList;
	}
	public function adduserAction() {
		$form = new UserAdd ( $this->getUserList () );
		$errors = false;
		if ($this->getRequest ()->isPost ()) {
			$data = $this->getRequest ()->getPost ();
			$data = NumberPlay::cleaner ( $data, array (
					'password',
					'passwordConfirm' 
			) );
			
			$form->setData ( $data );
			if ($form->isValid ()) {
				$formData = $form->getData ();
				$password = $formData ['password'];
				$confirm = $formData ['passwordConfirm'];
				//
				$birthDay = $formData ['birthDay'];
				$birthMonth = $formData ['birthMonth'];
				$birthYear = $formData ['birthYear'];
				//
				if (! checkdate ( $birthMonth, $birthDay, $birthYear )) {
					$message = 'The date is invalid';
					$form->get ( 'birthDay' )->setMessages ( array (
							$message 
					) );
					$errors = true;
				} else {
					$formData ['dateOfBirth'] = mktime ( 0, 0, 0, $birthMonth, $birthDay, $birthYear );
				}
				$formData ['accessLevel'] = 1;
				$employeeCode = $formData ['employeeCode'];
				$personalEmailId = $formData ['personalEmailId'];
				$userIdParent = $formData ['userIdParent'];
				$seniorInfo = $this->modelAccessor->fetchAll ( array (
						'userId' => $userIdParent 
				) ) [0];
				$seniorpath = $seniorInfo ['selfPath'];
				$existingUserByEmail = $this->modelAccessor->fetchAll ( array (
						'personalEmailId' => $personalEmailId 
				) ) [0];
				$existingUserByEmpCode = $this->modelAccessor->fetchAll ( array (
						'employeeCode' => $employeeCode 
				) ) [0];
				if (! empty ( $existingUserByEmpCode )) {
					$form->get ( 'employeeCode' )->setMessages ( array (
							'Employee Code is already registered' 
					) );
					$errors = true;
				}
				if (! empty ( $existingUserByEmail )) {
					$form->get ( 'personalEmailId' )->setMessages ( array (
							'Email id is already registered' 
					) );
					$errors = true;
				}
				$formData ['reportingPath'] = $seniorpath;
				$formData ['seniorId'] = $seniorInfo ['id'];
				//
				$loginId = $formData ['loginId'];
				$existingUserByLoginId = $this->modelAccessor->fetchAll ( array (
						'loginId' => $loginId 
				) ) [0];
				if (! empty ( $existingUserByLoginId )) {
					$form->get ( 'loginId' )->setMessages ( array (
							'Login id is already taken' 
					) );
					$errors = true;
				}
				//
				$mobile = $formData ['mobile'];
				$existingUserBymobile = $this->modelAccessor->fetchAll ( array (
						'mobile' => $mobile 
				) ) [0];
				if (! empty ( $existingUserBymobile )) {
					$form->get ( 'mobile' )->setMessages ( array (
							'Phone Number Already Registered' 
					) );
					$errors = true;
				}
				
				if ($password !== $confirm) {
					$errors = true;
					$form->get ( 'passwordConfirm' )->setMessages ( array (
							'Passwords do not match' 
					) );
				}
				if (! $errors) {
					$statuses = $this->modelAccessor->registeruser ( $formData );
					if (is_array ( $statuses )) {
						$form->setMessages ( $statuses );
					} else {
						return $this->redirect ()->toRoute ( 'admin/listusers' );
					}
				} else {
					if (ENABLE_DEBUG_MODE) {
						var_dump ( $formData );
						var_dump ( $form->getMessages () );
					}
				}
			} else {
				if (ENABLE_DEBUG_MODE) {
					echo "form is invalid";
					var_dump ( $form->getMessages () );
				}
			}
		}
		$view = new ViewModel ( array (
				'form' => $form 
		) );
		return $view;
	}
	public function removeuserAction() {
		return new ViewModel ();
	}
	public function traverseRecArray(&$givenArray, $totalNodes) {
		foreach ( $givenArray as $nId => $nodeInfo ) {
			$childs = $nodeInfo ['children'];
			$parentId = $nodeInfo ['parentId'];
			$path = $nodeInfo ['path'];
			$parentPath = $nodeInfo ['parentPath'];
			$nodeId = $nodeInfo ['id'];
			foreach ( $childs as $childId => $childPath ) {
				$givenArray [$childId] = array (
						'id' => $childId,
						'parentId' => $nodeId,
						'parentPath' => $path,
						'path' => $childPath,
						'children' => $this->findChildsV1 ( $childPath ) 
				);
			}
		}
		$currentCount = count ( $givenArray );
		if ($currentCount == $totalNodes) {
			return $givenArray;
		}
		$this->traverseRecArray ( $givenArray, $totalNodes );
	}
	public function updateuserAction() {
		$router = $this->serviceLocator->get ( 'Router' );
		$routeMatch = $router->match ( $this->request );
		$routeMatchParams = $routeMatch->getParams ();
		$userId = $routeMatchParams ['userId'];
		$event = $this->getEvent ();
		$application = $event->getApplication ();
		$existingUser = $this->modelAccessor->fetchAll ( array (
				'userId' => $userId 
		) ) [0];
		if (empty ( $existingUser )) {
			$errorMessage = 'Forbidden Resource';
			$event->setError ( ERROR_NEED_AUTHENTICATED_USER );
			$event->setParam ( 'exception', new AuthenticationRequired ( $errorMessage ) );
			$event->setParam ( 'redirectUri', '/user/dashboard' );
			return $application->getEventManager ()->trigger ( MvcEvent::EVENT_DISPATCH_ERROR, $event );
		}
		if ($existingUser ['accessLevel'] == 7) {
			$errorMessage = 'Forbidden Resource';
			$event->setError ( ERROR_NEED_AUTHENTICATED_USER );
			$event->setParam ( 'exception', new AuthenticationRequired ( $errorMessage ) );
			$event->setParam ( 'redirectUri', '/user/dashboard' );
			return $application->getEventManager ()->trigger ( MvcEvent::EVENT_DISPATCH_ERROR, $event );
		}
		unset ( $existingUser ['password'] );
		$dob = $existingUser ['dateOfBirth'];
		$existingUser ['birthDay'] = date ( 'j', $dob );
		$existingUser ['birthMonth'] = date ( 'n', $dob );
		$existingUser ['birthYear'] = date ( 'Y', $dob );
		if (! empty ( $existingUser ['reportingPath'] )) {
			$snrId = $this->modelAccessor->fetchAll ( array (
					'selfPath' => $existingUser ['reportingPath'] 
			), null, null, array (
					'id' 
			) ) [0] ['id'];
			$existingUser ['userIdParent'] = $snrId;
		}
		$oldEmpCode = $existingUser ['employeeCode'];
		$oldLoginId = $existingUser ['loginId'];
		$oldMobile = $existingUser ['mobile'];
		$oldPersonalEmailId = $existingUser ['personalEmailId'];
		//
		$form = new UserUpdate ( $this->getUserList () );
		$form->setData ( $existingUser );
		$errors = false;
		if ($this->getRequest ()->isPost ()) {
			$data = $this->getRequest ()->getPost ();
			$data = NumberPlay::cleaner ( $data, array (
					'password',
					'passwordConfirm' 
			) );
			
			$form->setData ( $data );
			if ($form->isValid ()) {
				$formData = $form->getData ();
				if (! empty ( $formData ['password'] ) && ! empty ( $formData ['passwordConfirm'] )) {
					$password = $formData ['password'];
					$confirm = $formData ['passwordConfirm'];
					if ($password !== $confirm) {
						$errors = true;
						$form->get ( 'passwordConfirm' )->setMessages ( array (
								'Passwords do not match' 
						) );
					}
				}
				$employeeCode = $formData ['employeeCode'];
				$personalEmailId = $formData ['personalEmailId'];
				$userIdParent = $formData ['userIdParent'];
				//
				$birthDay = $formData ['birthDay'];
				$birthMonth = $formData ['birthMonth'];
				$birthYear = $formData ['birthYear'];
				//
				if (! checkdate ( $birthMonth, $birthDay, $birthYear )) {
					$message = 'The date is invalid';
					$form->get ( 'birthDay' )->setMessages ( array (
							$message 
					) );
					$errors = true;
				} else {
					$formData ['dateOfBirth'] = mktime ( 0, 0, 0, $birthMonth, $birthDay, $birthYear );
				}
				$formData ['accessLevel'] = 1;
				
				$seniorInfo = $this->modelAccessor->fetchAll ( array (
						'userId' => $userIdParent 
				) ) [0];
				$seniorpath = $seniorInfo ['selfPath'];
				$formData ['reportingPath'] = $seniorpath;
				$formData ['seniorId'] = $seniorInfo ['id'];
				if ($oldPersonalEmailId !== $personalEmailId) {
					$existingUserByEmail = $this->modelAccessor->fetchAll ( array (
							'personalEmailId' => $personalEmailId 
					) ) [0];
					if (! empty ( $existingUserByEmail )) {
						$form->get ( 'personalEmailId' )->setMessages ( array (
								'Email id is already registered' 
						) );
						$errors = true;
					}
				}
				if ($oldEmpCode !== $employeeCode) {
					$existingUserByEmpCode = $this->modelAccessor->fetchAll ( array (
							'employeeCode' => $employeeCode 
					) ) [0];
					if (! empty ( $existingUserByEmpCode )) {
						$form->get ( 'employeeCode' )->setMessages ( array (
								'Employee Code is already registered' 
						) );
						$errors = true;
					}
				}
				
				$loginId = $formData ['loginId'];
				//
				
				if ($oldLoginId !== $loginId) {
					$existingUserByLoginId = $this->modelAccessor->fetchAll ( array (
							'loginId' => $loginId 
					) ) [0];
					if (! empty ( $existingUserByLoginId )) {
						$form->get ( 'loginId' )->setMessages ( array (
								'Login id is already taken' 
						) );
						$errors = true;
					}
				}
				//
				$mobile = $formData ['mobile'];
				if ($oldMobile !== $mobile) {
					$existingUserBymobile = $this->modelAccessor->fetchAll ( array (
							'mobile' => $mobile 
					) ) [0];
					if (! empty ( $existingUserBymobile )) {
						$form->get ( 'mobile' )->setMessages ( array (
								'Phone Number Already Registered' 
						) );
						$errors = true;
					}
				}
				
				if (! $errors) {
					$statuses = $this->modelAccessor->updateUser ( $userId, $formData );
					if (is_array ( $statuses )) {
						$form->setMessages ( $statuses );
					} else {
						return $this->redirect ()->toRoute ( 'admin/listusers' );
					}
				} else {
					if (ENABLE_DEBUG_MODE) {
						var_dump ( $formData );
						var_dump ( $form->getMessages () );
					}
				}
			} else {
				if (ENABLE_DEBUG_MODE) {
					echo "form is invalid";
					var_dump ( $form->getMessages () );
				}
			}
		}
		$view = new ViewModel ( array (
				'form' => $form 
		) );
		$view->setTemplate ( '/application/admin/adduser' );
		return $view;
	}
	public function listusersAction() {
		ini_set ( 'xdebug.var_display_max_depth', - 1 );
		ini_set ( 'xdebug.var_display_max_children', - 1 );
		ini_set ( 'xdebug.var_display_max_data', - 1 );
		$idKeysValuesPath = array ();
		$pathKeysIdsPath = array ();
		$allChilds = array ();
		$lengths = array ();
		$existingUsers = $this->modelAccessor->fetchAll ( null );
		$totalNodes = 0;
		if (! empty ( $existingUsers )) {
			foreach ( $existingUsers as $info ) {
				$totalNodes += 1;
				$indexLength = strlen ( $info ['selfPath'] );
				$idKeysValuesPath [$info ['id']] = $info ['selfPath'];
				$pathKeysIdsPath [$info ['selfPath']] = $info ['id'];
				if (! isset ( $this->lengthIndexedArray [$indexLength] ) || ! is_array ( $this->lengthIndexedArray [$indexLength] )) {
					$this->lengthIndexedArray [$indexLength] = array ();
				}
				$this->lengthIndexedArray [$indexLength] [$info ['id']] = $info ['selfPath'];
				$lengths [] = $indexLength;
			}
			foreach ( $idKeysValuesPath as $pth ) {
				$allChilds [$pth] = $this->findChildsV1 ( $pth );
			}
			natsort ( $idKeysValuesPath );
			natsort ( $lengths );
			$lengths = array_unique ( $lengths );
			$minLength = array_shift ( $lengths );
			$maxLength = array_pop ( $lengths );
			$roots = $this->lengthIndexedArray [$minLength];
			$treeInfo = array ();
			
			foreach ( $roots as $rId => $rPath ) {
				$treeInfo [$rId] = array (
						'id' => $rId,
						'parentId' => null,
						'parentPath' => null,
						'path' => $rPath,
						'children' => $this->findChildsV1 ( $rPath ) 
				);
			}
			
			$this->traverseRecArray ( $treeInfo, $totalNodes );
		}
		
		$allUsers = array ();
		$existingUsers = $this->modelAccessor->fetchAll ( null );
		if (! empty ( $existingUsers )) {
			foreach ( $existingUsers as $info ) {
				$allUsers [$info ['id']] = $info;
			}
		}
		$data = array (
				'allUsers' => $allUsers,
				'lengthIndexedArray' => $this->lengthIndexedArray,
				'maxLength' => $maxLength,
				'minLength' => $minLength,
				'idNdHash' => $idKeysValuesPath,
				'idNdHashReverse' => $pathKeysIdsPath,
				'treeInfo' => $treeInfo 
		);
		$vm = new ViewModel ( array (
				'data' => $data 
		) );
		return $vm;
	}
	public function updateusersAction() {
		$request = $this->getRequest ();
		$data = $request->getPost ();
		$data = ( array ) $data;
		if (empty ( $data ['userId'] ) || empty ( $data ['status'] )) {
			return $this->getResponse ()->setContent ( Json::encode ( 'Invalid Data' ) );
		}
		$userId = $data ['userId'];
		switch ($data ['status']) {
			case 800 :
				$status = 0;
				break;
			case 900 :
				$status = 1;
				break;
			default :
				;
				break;
		}
		$this->modelAccessor->updateUser ( $userId, array (
				'accessLevel' => $status 
		) );
		return $this->getResponse ()->setContent ( 'Done' );
	}
}
