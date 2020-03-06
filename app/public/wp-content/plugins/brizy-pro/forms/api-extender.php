<?php


class BrizyPro_Forms_ApiExtender {

	const AJAX_AUTHENTICATE_INTEGRATION = 'brizy_authenticate_integration';
	const AJAX_GET_LISTS = 'brizy_get_lists';
	const AJAX_GET_FIELDS = 'brizy_get_fields';
	const AJAX_GET_SERVICE_ACCOUNTS = 'brizy_service_accounts';
	const AJAX_DELETE_SERVICE_ACCOUNT = 'brizy_delete_service_account';
	const AJAX_CREATE_GROUP = 'brizy_create_group';
	const AJAX_GET_GROUP_PROPERTIES = 'brizy_get_group_properties';
	const AJAX_GET_ACCOUNT_PROPERTIES = 'brizy_get_account_properties';

	/**
	 * @var Brizy_Editor_Project
	 */
	private $project;

	/**
	 * BrizyPro_Forms_ApiExtender constructor.
	 */
	public function __construct() {

		add_filter( 'brizy_create_integration_from_json', array( $this, 'createIntegrationFromJson' ) );
		add_filter( 'brizy_add_integration_accounts', array( $this, 'addIntegrationAccounts' ), 10, 2 );

		add_filter( 'brizy_update_integration', array( $this, 'updateIntegrationAccounts' ), 10, 2 );
		add_action( 'brizy_submit_form', array( $this, 'formSubmit' ), 10, 4 );

		if ( Brizy_Editor::is_user_allowed() ) {
			add_action( 'wp_ajax_' . self::AJAX_AUTHENTICATE_INTEGRATION, array( $this, 'authenticateIntegration' ) );
			add_action( 'wp_ajax_' . self::AJAX_GET_LISTS, array( $this, 'getIntegrationLists' ) );
			add_action( 'wp_ajax_' . self::AJAX_GET_FIELDS, array( $this, 'getIntegrationFields' ) );
			add_action( 'wp_ajax_' . self::AJAX_GET_SERVICE_ACCOUNTS, array( $this, 'getServiceAccountList' ) );
			add_action( 'wp_ajax_' . self::AJAX_DELETE_SERVICE_ACCOUNT, array( $this, 'deleteServiceAccount' ) );
			add_action( 'wp_ajax_' . self::AJAX_CREATE_GROUP, array( $this, 'createGroup' ) );
			add_action( 'wp_ajax_' . self::AJAX_GET_GROUP_PROPERTIES, array( $this, 'getGroupProperties' ) );
			add_action( 'wp_ajax_' . self::AJAX_GET_ACCOUNT_PROPERTIES, array( $this, 'getAccountProperties' ) );
		}
	}


	/**
	 * @param Brizy_Editor_Forms_AbstractIntegration $integration
	 *
	 * @return \BrizyForms\Service\Service
	 * @throws \BrizyForms\Exception\ServiceException
	 */
	protected function getIntegrationService( Brizy_Editor_Forms_AbstractIntegration $integration ) {

		/**
		 * @var \BrizyForms\Service\Service $service ;
		 */
		$service = \BrizyForms\ServiceFactory::getInstance( $integration->getId() );

		$accountManager = new Brizy_Editor_Accounts_ServiceAccountManager( Brizy_Editor_Project::get() );

		if ( $usedAccountId = $integration->getUsedAccount() ) {
			$account = $accountManager->getAccount( $usedAccountId );

			// initialize an instance of AuthenticationData
			$authData = new \BrizyForms\Model\AuthenticationData( $account->convertToOptionValue() );
			$service->setAuthenticationData( $authData );
		}

		return $service;

	}

	/**
	 * @param \BrizyForms\Service\Service $service
	 * @param Brizy_Editor_Forms_Form $form
	 * @param $fields
	 * @param Brizy_Editor_Forms_AbstractIntegration $integration
	 *
	 * @throws \BrizyForms\Exception\AuthenticationDataException
	 * @throws \BrizyForms\Exception\FieldMapException
	 */
	public function formSubmit( \BrizyForms\Service\Service $service, Brizy_Editor_Forms_Form $form, $fields, Brizy_Editor_Forms_AbstractIntegration $integration ) {
		$accountManager = new Brizy_Editor_Accounts_ServiceAccountManager( Brizy_Editor_Project::get() );

		// initialize an instance of AuthenticationData
		$account = $accountManager->getAccount( $integration->getUsedAccount() );

		$authData = new \BrizyForms\Model\AuthenticationData( $account->convertToOptionValue() );
		$service->setAuthenticationData( $authData );

		$fieldMap = new \BrizyForms\FieldMap( array_map( function ( $obj ) {
			return get_object_vars( $obj );
		}, $integration->getFieldsMap() ) );

		$data = array_map( function ( $obj ) {
			return new \BrizyForms\Model\Data( $obj->name, $obj->value );
		}, $fields );

		$service->createMember( $fieldMap, $integration->getUsedList(), $data, $integration->isConfirmationNeeded() );
	}

	public function createIntegrationFromJson( $jsonObject ) {
		return Brizy_Editor_Forms_ServiceIntegration::createFromJson( $jsonObject );
	}

	/**
	 * @param Brizy_Editor_Forms_AbstractIntegration $integration
	 * @param Brizy_Editor_Forms_Form $form
	 *
	 * @return Brizy_Editor_Forms_AbstractIntegration
	 * @throws Exception
	 */
	public function addIntegrationAccounts( Brizy_Editor_Forms_AbstractIntegration $integration ) {
		if ( $integration instanceof Brizy_Editor_Forms_ServiceIntegration ) {
			$accountManager = new Brizy_Editor_Accounts_ServiceAccountManager( Brizy_Editor_Project::get() );
			$integration->setAccounts( $accountManager->getAccountsByService( $integration->getid() ) );
		}

		return $integration;
	}

	/**
	 * @param Brizy_Editor_Forms_AbstractIntegration $integration
	 * @param Brizy_Editor_Forms_Form $form
	 *
	 * @return Brizy_Editor_Forms_AbstractIntegration
	 * @throws Exception
	 */
	public function updateIntegrationAccounts( Brizy_Editor_Forms_AbstractIntegration $integration, Brizy_Editor_Forms_Form $form ) {
		if ( $integration instanceof Brizy_Editor_Forms_ServiceIntegration ) {
			$accountManager = new Brizy_Editor_Accounts_ServiceAccountManager( Brizy_Editor_Project::get() );
			// detect integration changes and reset the values when account or lists is changed
			$oldIntegration = $form->getIntegration( $integration->getid() );

			if ( ! $oldIntegration ) {
				$this->error( 404, "Integration not found" );
			}

			$integration->setAccounts( $accountManager->getAccountsByService( $integration->getId() ) );

			// reset fields and lists if the account is changed
			if ( $oldIntegration->getUsedAccount() != $integration->getUsedAccount() ) {
				$integration->setLists( array() );
				$integration->setFields( array() );
				$integration->setUsedList( null );
				$integration->setFieldsMap( array() );
				$integration->setCompleted( false );
				$integration->setConfirmationNeeded( false );
			}

			// reset fields and fieldmap if the used list is changed
			if ( $oldIntegration->getUsedList() != $integration->getUsedList() ) {
				$integration->setFields( array() );
				$integration->setFieldsMap( array() );
				$integration->setCompleted( false );
			}


			// check if there are autogenerate fields
			$hasAutogenerateFields = false;
			foreach ( $integration->getFieldsMap() as $field ) {
				if ( $field->target == '_auto_generate' ) {
					$hasAutogenerateFields = true;
					break;
				}
			}

			$manager = new Brizy_Editor_Forms_FormManager( Brizy_Editor_Project::get() );
			$service = $this->getIntegrationService( $integration );

			if ( $hasAutogenerateFields ) {


				$fieldMap = new \BrizyForms\FieldMap( array_map( function ( $obj ) {
					return get_object_vars( $obj );
				}, $integration->getFieldsMap() ) );

				try {
					$newFieldMap = $service->createFields( $fieldMap, $integration->getUsedList() );

					$fieldMap = array();

					/**
					 * @var \BrizyForms\Model\FieldLink $fieldLink ;
					 */
					foreach ( $newFieldMap->toArray() as $fieldLink ) {
						$fieldMap[] = (object) array(
							'sourceId'    => $fieldLink->getSourceId(),
							'sourceTitle' => $fieldLink->getSourceId(),
							'target'      => $fieldLink->getTarget(),
						);
					}

					$integration->setFieldsMap( $fieldMap );

					$list = new \BrizyForms\Model\Group();
					if ( $integration->getUsedList() ) {
						$userlist = $integration->getUsedListObject();
						$list     = new \BrizyForms\Model\Group( $userlist->getId(), $userlist->getName() );
					}

					$fields = $service->getFields( $list );

					$integration->setFields( array() );
					foreach ( $fields as $field ) {
						$integration->addField( new Brizy_Editor_Forms_Field( $field ) );
					}

				} catch ( Exception $e ) {
					$this->error( 400, $e->getMessage() );
				}
			}

			$integration->setHasConfirmation( $service->hasConfirmation() );

			// save groups in integration
			$form->updateIntegration( $integration );
			$manager->addForm( $form );

		}

		return $integration;
	}


	/**
	 * @throws \BrizyForms\Exception\AuthenticationDataException
	 * @throws \BrizyForms\Exception\ServiceException
	 */
	public function authenticateIntegration() {

		$manager        = new Brizy_Editor_Forms_FormManager( Brizy_Editor_Project::get() );
		$accountManager = new Brizy_Editor_Accounts_ServiceAccountManager( Brizy_Editor_Project::get() );

		$form = $manager->getForm( $_REQUEST['formId'] );
		if ( ! $form ) {
			$this->error( 404, "Form not found" );
		}

		$integrationId = $_REQUEST['integration'];

		if ( ! $integrationId ) {
			$this->error( 400, "Invalid form integration" );
		}

		$integration = $form->getIntegration( $integrationId );

		if ( ! $integration ) {
			$this->error( 404, "Integration not found" );
		}

		if ( $integration instanceof Brizy_Editor_Forms_WordpressIntegration ) {
			$this->error( 400, "Unsupported integration" );
		}

		/**
		 * @var \BrizyForms\Service\Service $service ;
		 */
		$service = $this->getIntegrationService( $integration );

		$data          = json_decode( file_get_contents( 'php://input' ) );
		$data->service = $integration->getId();
		$account       = new Brizy_Editor_Accounts_Account( get_object_vars( $data ) );

		if ( $accountManager->hasAccount( $account ) ) {
			$this->error( 400, "Duplicate account" );
		}

		$response = null;

		try {
			// initialize an instance of AuthenticationData
			$authData = new \BrizyForms\Model\AuthenticationData( $account->convertToAuthData() );
			$service->setAuthenticationData( $authData );

			$response = $service->authenticate();

		} catch ( Exception $e ) {
			$this->error( 401, $e->getMessage() );
		}

		if ( $response instanceof \BrizyForms\Model\Response ) {

			if ( $response->getCode() == 200 ) {

				$serviceAccount = $service->getAccount();

				if ( $serviceAccount instanceof \BrizyForms\Model\Account ) {
					$account->setName( $serviceAccount->getName() );
				} else {
					$this->error( 500, "Unable to obtain account data." );
				}

				$folders = $service->getFolders();
				if ( $folders ) {
					$folders = array_map( function ( $folder ) {
						return (object) $folder->jsonSerialize();
					}, $folders );

					$account->setFolders( $folders );
				}

				if ( $form->updateIntegration( $integration ) ) {
					$manager->addForm( $form );
					$accountManager->addAccount( $account );
					$this->success( $account );
				}
			} else {
				$this->error( 401, $response->getMessage() );
			}
		}

		$this->error( 500, 'Failed to authenticate service' );
	}


	/**
	 * @throws Exception
	 */
	public function getIntegrationLists() {

		$this->authorize();

		$manager = new Brizy_Editor_Forms_FormManager( Brizy_Editor_Project::get() );
		$form    = $manager->getForm( $_REQUEST['formId'] );
		if ( ! $form ) {
			$this->error( 400, "Invalid form id" );
		}
		$integrationId = $_REQUEST['integration'];
		if ( ! $integrationId ) {
			$this->error( 400, "Invalid form integration" );
		}

		/**
		 * @var Brizy_Editor_Forms_ServiceIntegration $integration ;
		 */
		$integration = $form->getIntegration( $integrationId );

		if ( $integration instanceof Brizy_Editor_Forms_WordpressIntegration ) {
			$this->error( 400, "Unsupported integration" );
		}

		if ( $integration instanceof Brizy_Editor_Forms_WordpressIntegration ) {
			$this->error( 400, "Unsupported integration" );
		}

		if ( ! $integration ) {
			$this->error( 400, "Invalid form integration" );
		}

		if ( ! $integration->getUsedAccount() ) {
			$this->error( 400, "Invalid integration account" );
		}

		try {
			/**
			 * @var \BrizyForms\Service\Service $service ;
			 */
			$service = $this->getIntegrationService( $integration );

			$folder = null;

			if ( $fid = $integration->getUsedFolder() ) {
				$folder = new \BrizyForms\Model\Folder( $fid );
			}

			$groups = $service->getGroups( $folder );
			$integration->setLists( array() );
			foreach ( $groups as $group ) {
				$integration->addList( new Brizy_Editor_Forms_Group( $group ) );
			}

			// save groups in integration
			$form->updateIntegration( $integration );
			$manager->addForm( $form );

			$this->success( $groups ? $groups : array() );
		} catch ( \BrizyForms\Exception\AccessDeniedException $e ) {
			$this->error( 403, "Forbidden" );
		} catch ( Exception $e ) {
			$this->error( 500, "Unable to initialize service" );
		}
	}

	/**
	 * @throws Exception
	 */
	public function getIntegrationFields() {

		$this->authorize();

		$manager = new Brizy_Editor_Forms_FormManager( Brizy_Editor_Project::get() );
		$form    = $manager->getForm( $_REQUEST['formId'] );
		if ( ! $form ) {
			$this->error( 400, "Invalid form id" );
		}
		$integrationId = $_REQUEST['integration'];
		if ( ! $integrationId ) {
			$this->error( 400, "Invalid form integration" );
		}

		$integration = $form->getIntegration( $integrationId );

		if ( ! $integration ) {
			$this->error( 400, "Invalid form integration" );
		}

		if ( $integration instanceof Brizy_Editor_Forms_WordpressIntegration ) {
			$this->error( 400, "Unsupported integration" );
		}

		if ( ! $integration->getUsedAccount() ) {
			$this->error( 400, "Invalid integration account" );
		}

		try {
			/**
			 * @var \BrizyForms\Service\Service $service ;
			 */
			$service = $this->getIntegrationService( $integration );


			$list = new \BrizyForms\Model\Group();
			if ( $integration->getUsedList() ) {
				$userlist = $integration->getUsedListObject();
				if ( $userlist ) {
					$list = new \BrizyForms\Model\Group( $userlist->getId(), $userlist->getName() );
				}
			}

			$fields = $service->getFields( $list );

			$integration->setFields( array() );
			foreach ( $fields as $field ) {
				$integration->addField( new Brizy_Editor_Forms_Field( $field ) );
			}

			// save groups in integration
			$form->updateIntegration( $integration );
			$manager->addForm( $form );

			$this->success( $fields ? $fields : array() );

		} catch ( Exception $e ) {
			$this->error( 500, "Unable to initialize service" );
		}
	}


	/**
	 * @throws Exception
	 */
	public function getServiceAccountList() {

		$this->authorize();

		$manager = new Brizy_Editor_Accounts_ServiceAccountManager( Brizy_Editor_Project::get() );

		$serviceId = $_REQUEST['service'];
		if ( ! $serviceId ) {
			$this->error( 400, "Invalid form service id" );
		}

		$accounts = $manager->getAccountsByService( $serviceId );

		$this->success( $accounts );
	}

	/**
	 * @throws Exception
	 */
	public function deleteServiceAccount() {

		$this->authorize();

		$manager = new Brizy_Editor_Accounts_ServiceAccountManager( Brizy_Editor_Project::get() );

		$serviceId = $_REQUEST['service'];
		if ( ! $serviceId ) {
			$this->error( 400, "Invalid form service id" );
		}

		$accountId = $_REQUEST['account'];
		if ( ! $accountId ) {
			$this->error( 400, "Invalid account id" );
		}

		$manager->deleteAccountById( $accountId );

		$this->success( null );
	}

	/**
	 * @throws Exception
	 */
	public function createGroup() {

		$this->authorize();

		$manager = new Brizy_Editor_Forms_FormManager( Brizy_Editor_Project::get() );

		$form = $manager->getForm( $_REQUEST['formId'] );
		if ( ! $form ) {
			$this->error( 400, "Invalid form id" );
		}
		$integrationId = $_REQUEST['integration'];
		if ( ! $integrationId ) {
			$this->error( 400, "Invalid form integration" );
		}

		$integration = $form->getIntegration( $integrationId );


		$object = json_decode( file_get_contents( 'php://input' ) );

		if ( ! is_object( $object ) ) {
			$this->error( 400, "Invalid group data" );
		}

		$groupData = new \BrizyForms\Model\GroupData( get_object_vars( $object ) );

		try {
			/**
			 * @var \BrizyForms\Service\Service $service ;
			 */
			$service = $this->getIntegrationService( $integration );

			if ( $service ) {
				try {
					$group = $service->createGroup( $groupData );
					$this->success( $group );
				} catch ( Exception $e ) {
					$this->error( 400, $e->getMessage() );
				}
			}
		} catch ( Exception $e ) {
			$this->error( 500, $e->getMessage() );
		}

		$this->error( 404, 'Integration not found' );
	}

//	public function createFields() {
//
//		$this->authorize();
//
//		$manager = new Brizy_Editor_Forms_FormManager( Brizy_Editor_Project::get() );
//
//		$form = $manager->getForm( $_REQUEST['formId'] );
//		if ( ! $form ) {
//			$this->error( 400, "Invalid form id" );
//		}
//
//		$integrationId = $_REQUEST['integration'];
//		if ( ! $integrationId ) {
//			$this->error( 400, "Invalid form integration" );
//		}
//
//		$integration = $form->getIntegration( $integrationId );
//
//
//		$fields = json_decode( file_get_contents( 'php://input' ) );
//
//		if ( ! is_array( $fields ) ) {
//			$this->error( 400, "Invalid group data" );
//		}
//
//		try {
//			/**
//			 * @var \BrizyForms\Service\Service $service ;
//			 */
//			$service = $this->getIntegrationService($integration);
//
//			if ( $service ) {
//				try {
//
//					$fieldMap = new \BrizyForms\FieldMap( array_map( function ( $obj ) {
//						return get_object_vars( $obj );
//					}, $fields ) );
//
//					$fields = $service->createFields( $fieldMap );
//
//					$integration->setFields( array() );
//					foreach ( $fields as $field ) {
//						$integration->addField( new Brizy_Editor_Forms_Field( $field ) );
//					}
//
//					// save groups in integration
//					$form->updateIntegration( $integration );
//					$manager->addForm( $form );
//
//					$this->success( $fields );
//				} catch ( Exception $e ) {
//					$this->error( 400, $e->getMessage() );
//				}
//			}
//		} catch ( Exception $e ) {
//			$this->error( 500, $e->getMessage() );
//		}
//
//		$this->error( 404, 'Integration not found' );
//	}

	public function getGroupProperties() {
		$this->authorize();

		$manager = new Brizy_Editor_Forms_FormManager( Brizy_Editor_Project::get() );

		$form = $manager->getForm( $_REQUEST['formId'] );
		if ( ! $form ) {
			$this->error( 400, "Invalid form id" );
		}

		$integrationId = $_REQUEST['integration'];
		if ( ! $integrationId ) {
			$this->error( 400, "Invalid form integration" );
		}

		$integration = $form->getIntegration( $integrationId );

		try {
			/**
			 * @var \BrizyForms\Service\Service $service ;
			 */
			$service = $this->getIntegrationService( $integration );

			$data = $service->getGroupProperties();

			$this->success( $data ? $data : array() );
		} catch ( Exception $e ) {
			$this->error( 500, $e->getMessage() );
		}
	}

	public function getAccountProperties() {
		$this->authorize();

		$manager = new Brizy_Editor_Forms_FormManager( Brizy_Editor_Project::get() );

		$form = $manager->getForm( $_REQUEST['formId'] );
		if ( ! $form ) {
			$this->error( 400, "Invalid form id" );
		}

		$integrationId = $_REQUEST['integration'];
		if ( ! $integrationId ) {
			$this->error( 400, "Invalid form integration" );
		}

		$integration = $form->getIntegration( $integrationId );

		try {
			/**
			 * @var \BrizyForms\Service\Service $service ;
			 */
			$service = $this->getIntegrationService( $integration );

			$data = $service->getAccountProperties();

			$this->success( $data ? $data : array() );
		} catch ( Exception $e ) {
			$this->error( 500, $e->getMessage() );
		}
	}

	private function authorize() {
		if ( ! wp_verify_nonce( $_REQUEST['hash'], Brizy_Editor_API::nonce ) ) {
			wp_send_json_error( array( 'code' => 400, 'message' => 'Bad request' ), 400 );
		}
	}

	protected function error( $code, $message ) {
		wp_send_json_error( array( 'code' => $code, 'message' => $message ), $code );
	}

	protected function success( $data ) {
		wp_send_json_success( $data );
	}

}