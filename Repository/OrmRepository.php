<?php

namespace PhpInk\Nami\CoreBundle\Repository;

use Symfony\Component\Validator\Constraints\DateTimeValidator;
use Symfony\Component\Validator\Constraints\DateValidator;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\NoResultException;
use PhpInk\Nami\CoreBundle\Model\Orm\Core\Entity;
use PhpInk\Nami\CoreBundle\Model\Orm\User;

/**
 * Base repository with utility methods
 *
 * @package PhpInk\Nami\CoreBundle\Repository\Core
 * @author  Geoffroy Pierret <geofrwa@yandex.com>
 */
abstract class OrmRepository extends EntityRepository
{
    use RepositoryTrait;

    /**
     * Constructor overridden to adjust field maps
     *
     * @param \Doctrine\ORM\EntityManager $em    The entity manager.
     * @param ClassMetadata               $class The class metadata.
     */
    public function __construct($em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->orderByFields = array_merge(
            $this->orderByFields,
            $this->orderByRelationFields
        );
        $this->filterByFields = $this->orderByFields;
    }


    /**
     * Save/Persist a entity instance
     *
     * @param Entity|int $entityOrId The entity or its id.
     *
     * @return void
     */
    public function removeModel($entityOrId)
    {
        $em = $this->getEntityManager();
        // If the param is not an Entity but an id
        if (is_int($entityOrId)) {
            // Retrieves it
            $entityOrId = $this->getModelById($entityOrId);
        }
        if ($entityOrId) {
            $em->remove($entityOrId);
            $em->flush();
        }
    }

    /**
     * Save/Persist a entity instance
     *
     * @param Entity $entity The entity to save.
     *
     * @return Entity
     */
    public function saveModel(Entity $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    /**
     * Build the items query (join, filters)
     *
     * @param QueryBuilder $query The doctrine query builder.
     * @param User         $user  The user who made the request.
     *
     * @return QueryBuilder
     */
    public function buildItemsQuery(QueryBuilder $query, User $user = null)
    {
        return $query;
    }

    /**
     * Applies the correct addOrderBy to a query
     * Search for ASC/DESC order with '-' Prefix
     *
     * @param QueryBuilder $query         The query builder to sort field on.
     * @param string       $orderByField  Field name.
     * @param string       $orderByValue  Sort value : 0,1 for ASC,DESC.
     * @param array        $allowedFields Checked fields.
     *
     * @return QueryBuilder
     */
    public function addOrderByClause(
        QueryBuilder $query, $orderByField,
        $orderByValue = null, $allowedFields = null
    ) {
        $allowedFields = (!is_array($allowedFields)) ?
            $this->orderByFields : $allowedFields;

        if (array_key_exists($orderByField, $allowedFields)) {
            $orderByOrder = intval($orderByValue) ? 'DESC' : 'ASC';
            $query->addOrderBy(
                $allowedFields[$orderByField], $orderByOrder
            );
        }
        return $query;
    }

    /**
     * Adds a where clause to a query
     *
     * @param QueryBuilder $query      The query builder for the where clause
     * @param string       $field      The field for the where clause.
     * @param mixed        $value      The value of the where clause.
     * @param string       $expression The expression of the where clause.
     *
     * @return QueryBuilder
     */
    public function addWhereClause(
        QueryBuilder $query, $field, $value = null, $expression = 'eq'
    ) {
        $datePattern = DateValidator::PATTERN;
        $dateTimePattern = DateTimeValidator::PATTERN;
        if (is_string($value)) {
            if (preg_match($datePattern, $value)
                || preg_match($dateTimePattern, $value)
            ) {
                $value = new \DateTime($value);
            }
        }
        if ($value) {
            $paramKey = '?'. strval($this->whereCount + 1);
            switch ($expression) {
            case 'eq':
            default:
                $whereClause = $query->expr()->eq($field, $paramKey);
                break;
            case 'gte':
                $whereClause = $query->expr()->gte($field, $paramKey);
                break;
            case 'lte':
                $whereClause = $query->expr()->lte($field, $paramKey);
                break;
            case 'neq':
                $whereClause = $query->expr()->neq($field, $paramKey);
                break;
            case 'like':
                $whereClause = $query->expr()->like($field, $paramKey);
                break;
            case 'isNull':
                $whereClause = $query->expr()->isNull($field);
                $value = null;
                break;
            case 'in':
                $whereClause = $query->expr()->in($field, $value);
                break;
            }
        } else {
            $whereClause = $field;
        }
        if ($this->whereCount) {
            $query->andWhere($whereClause);
        } else {
            $query->where($whereClause);
        }
        if ($expression !== 'isNull' && $expression !== 'in') {
            $paramKey = substr($paramKey, 1, strlen($paramKey));
            $query->setParameter($paramKey, $value);
        }

        $this->whereCount++;
        return $query;
    }

    /**
     * Item get_one returning an Entity checking its accessibility
     *
     * @param int  $id   The entity id.
     * @param User $user The user making the request.
     *
     * @return Entity
     */
    public function getItem($id, User $user = null)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, $user);
        $query
            ->where('this.id = :id')
            ->setParameter('id', intval($id));

        $entity = $this->fetchSingleResult($query);
        return $entity;
    }

    /**
     * Fetch a single AbstractQuery result
     * Catches NoResultException
     *
     * @param QueryBuilder $query The doctrine query builder.
     *
     * @return Entity|bool
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function fetchSingleResult(QueryBuilder $query)
    {
        $entity = false;
        try {
            $entity = $query->getQuery()->getSingleResult(
                AbstractQuery::HYDRATE_OBJECT
            );

        } catch (NoResultException $e) {

        }
        return $entity;
    }

    /**
     * Count the total get_all items
     *
     * @param User $user The user making the request.
     *
     * @return int
     */
    public function countItems(User $user = null)
    {
        $countQuery = $this
            ->createQueryBuilder('this')
            ->select('COUNT(this.id)');
        $countQuery = $this->applyRoleFiltering($countQuery, $user);
        return $countQuery
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Paginate a query
     *
     * @param QueryBuilder $query  The dquery builder to paginate.
     * @param int          $offset The pagination offset [optional].
     * @param int          $limit  The pagination limit [optional].
     *
     * @return Paginator
     */
    public function paginateQuery(QueryBuilder $query, $offset = null, $limit = null)
    {
        if (is_null($offset)) {
            $offset = 0;
        }
        if (is_null($limit)) {
            $limit = 10;
        }
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query;
    }

    /**
     * Check that an existing entity with a given value
     * for a given column name exists in the database
     *
     * @param mixed    $value  Value to check.
     * @param int|null $id     Entity ID to exclude.
     * @param string   $column Column name to check [optional].
     *
     * @return bool
     */
    public function checkValueExists($value, $id, $column = 'name')
    {
        $query = $this->createQueryBuilder('this')
            ->select('COUNT(this.id)')
            ->where('this.' . $column . ' = :value')
            ->setParameter('value', $value);
        if ($id) {
            $query->andWhere('this.id != :id');
            $query->setParameter('id', $id);
        }
        try
        {
            $resultCount = $query->getQuery()->getSingleScalarResult();
        }
        catch (NoResultException $e)
        {
            $resultCount = 0;
        }
        return $resultCount > 0;
    }
}
