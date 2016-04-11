<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Model\Interfaces\RestreeInterface;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="eval_res_tree")
 */
class Restree implements RestreeInterface {
	
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="bigint", options={"unsigned"=true})
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string",nullable = false)
	 */
	protected $entityClass;
	/**
	 * @ORM\Column(type="bigint",nullable = false)
	 */
	protected $entityId;
	/**
	 * @ORM\Column(type="boolean",nullable=false)
	 */
	protected $isDeleted;
	
	/**
	 * @ORM\Column(type="boolean",nullable=false)
	 */
	protected $readOnly;
	
	/**
	 * @ORM\Column(type="string",unique=true, nullable = false)
	 */
	protected $path;
	
	/**
	 * @ORM\Column(type="bigint", options={"unsigned"=true})
	 */
	protected $updatedOn;
	
	/**
	 * @ORM\Column(type="bigint", options={"unsigned"=true})
	 */
	protected $createdOn;
	
	/**
	 * @ORM\PrePersist
	 */
	public function logDatesOnCreate() {
		$currentTimestamp = time ();
		$this->updatedOn = $currentTimestamp;
		$this->createdOn = $currentTimestamp;
	}
	
	/**
	 * @ORM\PreUpdate
	 */
	public function logDatesOnUpdate() {
		$currentTimestamp = time ();
		$this->updatedOn = $currentTimestamp;
	}
	public function getId() {
		return $this->id;
	}
	
	/**
	 *
	 * @return the $entityClass
	 */
	public function getEntityClass() {
		return $this->entityClass;
	}
	
	/**
	 *
	 * @param number $entityClass        	
	 */
	public function setEntityClass($entityClass) {
		$this->entityClass = $entityClass;
	}
	
	/**
	 *
	 * @return the $entityId
	 */
	public function getEntityId() {
		return $this->entityId;
	}
	
	/**
	 *
	 * @param number $entityId        	
	 */
	public function setEntityId($entityId) {
		$this->entityId = $entityId;
	}
	/**
	 *
	 * @return the $path
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 *
	 * @param number $path        	
	 */
	public function setPath($path) {
		$this->path = $path;
	}
	/**
	 *
	 * @return the $isDeleted
	 */
	public function getIsDeleted() {
		return $this->isDeleted;
	}
	
	/**
	 *
	 * @param field_type $isDeleted        	
	 */
	public function setIsDeleted($isDeleted) {
		$this->isDeleted = $isDeleted;
	}
	
	/**
	 *
	 * @return the $readOnly
	 */
	public function getReadOnly() {
		return $this->readOnly;
	}
	
	/**
	 *
	 * @param field_type $readOnly        	
	 */
	public function setReadOnly($readOnly) {
		$this->readOnly = $readOnly;
	}
	/**
	 *
	 * @return the $updatedOn
	 */
	public function getUpdatedOn() {
		return $this->updatedOn;
	}
	
	/**
	 *
	 * @param number $updatedOn        	
	 */
	public function setUpdatedOn($updatedOn) {
		$this->updatedOn = $updatedOn;
	}
	
	/**
	 *
	 * @return the $createdOn
	 */
	public function getCreatedOn() {
		return $this->createdOn;
	}
	
	/**
	 *
	 * @param number $createdOn        	
	 */
	public function setCreatedOn($createdOn) {
		$this->createdOn = $createdOn;
	}
}