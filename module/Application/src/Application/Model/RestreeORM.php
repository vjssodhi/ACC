<?php

namespace Application\Model;

use Doctrine\ORM\EntityManager;
use Application\Entity\Restree;
use Application\Utilities\NumberPlay;
use Doctrine\DBAL\Connection;

class RestreeORM {
	CONST RESTREE_ENTITY = 'Application\Entity\Restree';
	
	/**
	 *
	 * @var EntityManager
	 */
	protected $ormEntityMgr;
	/**
	 *
	 * @var Connection
	 */
	public $connection;
	/**
	 *
	 * @return the $connection
	 */
	public function getConnection() {
		return $this->connection;
	}
	
	/**
	 *
	 * @param \Doctrine\DBAL\Connection $connection        	
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
	}
	public function getOrmEntityMgr() {
		return $this->ormEntityMgr;
	}
	public function setOrmEntityMgr($ormEntityMgr) {
		$this->ormEntityMgr = $ormEntityMgr;
	}
	public function findPath($entityId, $entityClass) {
		if (empty ( $this->ormEntityMgr )) {
			throw new \Exception ( 'Entity Manager not set in Resource Tree orm model' );
		}
		$params = array ();
		$params [':eType'] = $entityClass;
		$params [':eId'] = ( int ) $entityId;
		$om = $this->getOrmEntityMgr ();
		$qbs = $om->createQueryBuilder ();
		$qbs->select ( array (
				'r.path' 
		) )->from ( static::RESTREE_ENTITY, 'r' )->where ( 'r.entityClass = :eType' )->andWhere ( 'r.entityId = :eId' );
		$qbs->setParameters ( $params );
		$queryq = $qbs->getQuery ();
		$results = $queryq->getResult ();
		
		if (empty ( $results [0] )) {
			return false;
		}
		return $results [0] ['path'];
	}
	public static function encodeId($entityClass, $entityId) {
		$base36EncodedId = NumberPlay::conver10ToBase36 ( strval ( $entityId ), STEP_LENGTH );
		$classCode = null;
		
		switch ($entityClass) {
			case 'Application\Entity\User' :
				$classCode = 'A';
				break;
			default :
				throw new \Exception ( 'Invalid class provided' );
				break;
		}
		return $classCode . $base36EncodedId;
	}
	public function changeNodeParent($childId, $childClass, $parentId, $parentClass) {
		$om = $this->getOrmEntityMgr ();
		$this->getConnection ()->beginTransaction ();
		try {
			$params = array ();
			$params [':cType'] = $childClass;
			$params [':cId'] = ( int ) $childId;
			$om = $this->getOrmEntityMgr ();
			$qbs = $om->createQueryBuilder ();
			$qbs->select ( array (
					'r' 
			) )->from ( static::RESTREE_ENTITY, 'r' )->where ( 'r.entityClass = :cType' )->andWhere ( 'r.entityId = :cId' );
			$qbs->setParameters ( $params );
			$queryq = $qbs->getQuery ();
			$results = $queryq->getResult ();
			if (empty ( $results [0] )) {
				throw new \Exception ( sprintf ( 'No entity by name: %s and id: %s exists', $childClass, $childId ) );
			}
			$parentPath = $this->findPath ( $parentId, $parentClass );
			if (empty ( $parentPath )) {
				throw new \Exception ( sprintf ( 'No entity by name: %s and id: %s exists', $parentClass, $parentId ) );
			}
			$encodedChildId = static::encodeId ( $childClass, $childId );
			$newPath = $parentPath . $encodedChildId;
			$child = $results [0];
			$child->setPath ( $newPath );
			$om->flush ( $child );
			$this->getConnection ()->commit ();
			return $newPath;
		} catch ( \Exception $e ) {
			$this->getConnection ()->rollBack ();
			$om->close ();
			throw $e;
		}
		return true;
	}
	public function save($entityClass, $entityId, $parentClass = null, $parentId = null) {
		// var_dump(func_get_args());
		if (empty ( $this->ormEntityMgr ) || empty ( $this->connection )) {
			throw new \Exception ( 'Entity Manager or Connection not set in Resource Tree orm model' );
		}
		$encodedEntityId = static::encodeId ( $entityClass, $entityId );
		$rootNodeId = str_pad ( APPLICATION_CODE, STEP_LENGTH + 1, '0', STR_PAD_LEFT );
		$om = $this->getOrmEntityMgr ();
		$this->getConnection ()->beginTransaction ();
		try {
			
			if (null == $parentId && null == $parentClass) {
				$path = $rootNodeId . $encodedEntityId;
			} elseif (null !== $parentId && null !== $parentClass) {
				$parentPath = $this->findPath ( $parentId, $parentClass );
				if (empty ( $parentPath )) {
					throw new \Exception ( sprintf ( 'No entity by name: %s and id: %s exists', $parentClass, $parentId ) );
				}
				$path = $parentPath . $encodedEntityId;
			} else {
				throw new \Exception ( 'Invalid parameter combination' );
			}
			$thisEntityPath = $this->findPath ( $entityId, $entityClass );
			
			if (empty ( $thisEntityPath )) {
				$resourceTree = new Restree ();
				// Object Information
				$resourceTree->setEntityClass ( $entityClass );
				$resourceTree->setEntityId ( $entityId );
				// Accessibility flags
				$resourceTree->setIsDeleted ( false );
				$resourceTree->setReadOnly ( false );
				// Path
				$resourceTree->setPath ( $path );
				$om->persist ( $resourceTree );
				
				$om->flush ();
				$this->getConnection ()->commit ();
				return $path;
			}
		} catch ( \Exception $e ) {
			$this->getConnection ()->rollBack ();
			$om->close ();
			throw $e;
		}
		return true;
	}
}

   
