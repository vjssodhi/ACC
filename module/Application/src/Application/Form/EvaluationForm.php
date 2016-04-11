<?php

namespace Application\Form;

use Zend\InputFilter;
use Zend\Form\Form;

class EvaluationForm extends Form {
	protected $fields;
	public function __construct($fields, $name = null, $options = array()) {
		$this->fields = $fields;
		parent::__construct ( $name, $options );
		$this->addElements ();
		$this->setInputFilter ( $this->createInputFilter () );
	}
	public function addElements() {
		foreach ( $this->fields as $label => $fieldName ) {
			$this->add ( array (
					'type' => 'Zend\Form\Element\Select',
					'name' => $fieldName,
					'options' => array (
							'empty_option' => $label,
							'label' => $label,
							'value_options' => array (
									'0.5' => 'Poor',
									'1.0' => 'Average',
									'1.5' => 'Good',
									'2.0' => 'Very Good',
									'2.5' => 'Outstanding' 
							) 
					),
					'attributes' => array (
							'required' => 'required',
							'class' => 'form-control',
							'id' => $fieldName . 'Sel' 
					) 
			) );
		}
		
		$this->add ( array (
				'name' => 'mcsrf',
				'type' => 'Zend\Form\Element\Csrf',
				'options' => array (
						'csrf_options' => array (
								'timeout' => CSRF_TIMEOUT_SECONDS 
						) 
				) 
		) );
	}
	public function createInputFilter() {
		$inputFilter = new InputFilter\InputFilter ();
		foreach ( $this->fields as $label => $fieldName ) {
			$inputFilter->add ( array (
					'name' => $fieldName,
					'required' => true 
			) );
		}
		
		return $inputFilter;
	}
}

