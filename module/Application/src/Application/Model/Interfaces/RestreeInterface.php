<?php

namespace Application\Model\Interfaces;

interface RestreeInterface extends TimeLoggerInterface, DoctrineIdInterface {
	public function getEntityClass();
	public function setEntityClass($entityClass);
	public function getEntityId();
	public function setEntityId($entityId);
	public function getPath();
	public function setPath($path);
	public function getIsDeleted();
	public function setIsDeleted($isDeleted);
	public function getReadOnly();
	public function setReadOnly($readOnly);
}