<?php

namespace PhpInk\Nami\CoreBundle\Repository;

use Symfony\Component\Validator\Constraints\DateTimeValidator;
use Symfony\Component\Validator\Constraints\DateValidator;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Doctrine\ORM\NoResultException;
use PhpInk\Nami\CoreBundle\Model\Odm\Core\Document;
use PhpInk\Nami\CoreBundle\Model\Odm\User;

/**
 * Base repository with utility methods
 *
 * @package PhpInk\Nami\CoreBundle\Repository\Core
 */
abstract class OdmRepository extends DocumentRepository
{
    use RepositoryTrait;

    /**
     * Repository constructor overridden to reuse field maps
     * Initializes a new <tt>DocumentRepository</tt>.
     *
     * @param DocumentManager $dm            The DocumentManager to use.
     * @param UnitOfWork      $uow           The UnitOfWork to use.
     * @param ClassMetadata   $classMetadata The class descriptor.
     */
    public function __construct(
        DocumentManager $dm, UnitOfWork $uow, ClassMetadata $classMetadata
    ) {
        parent::__construct($dm, $uow, $classMetadata);
        $this->orderByFields = array_merge(
            $this->orderByFields,
            $this->orderByRelationFields
        );
        $this->filterByFields = $this->orderByFields;
    }

    /**
     * Save/Persist an entity instance
     *
     * @param Document|int $entityOrId The ID or document to remove.
     *
     * @return void
     */
    public function removeModel($entityOrId)
    {
        $em = $this->getDocumentManager();
        // If the param is not an Document but an id
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
     * @param Document $entity The document to save.
     *
     * @return Document
     */
    public function saveModel(Document $entity)
    {
        $em = $this->getDocumentManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    /**
     * Build the items query (join, filters)
     *
     * @param QueryBuilder $query The doctrine query builder.
     * @param User         $user  The user making the request.
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
     * @param QueryBuilder $query         The query to sort field on.
     * @param string       $orderByField  The field name to sort.
     * @param string       $orderByValue  The sort value : 0,1 for ASC,DESC.
     * @param array        $allowedFields The checked fields.
     *
     * @return QueryBuilder
     */
    public function addOrderByClause(
        QueryBuilder $query, $orderByField, $orderByValue = null, $allowedFields = null
    ) {
        $allowedFields = (!is_array($allowedFields)) ?
            $this->orderByFields : $allowedFields;

        if (!is_array($orderByField)) {
            if (array_key_exists($orderByField, $allowedFields)) {
                $orderByOrder = intval($orderByValue) ? 'desc' : 'asc';
                $query->sort(
                    $allowedFields[$orderByField], $orderByOrder
                );
            }
        } else {
            foreach ($orderByField as $field => $order) {
                $orderByField[$field] = intval($order) ? 'desc' : 'asc';
            }
            $query->sort($orderByField);
        }
        return $query;
    }

    /**
     * Adds a where clause to a query
     *
     * @param QueryBuilder $query      The doctrine query builder.
     * @param string       $field      The field name for the clause.
     * @param mixed        $value      The field value for the clause.
     * @param string       $expression The clause expression.
     *
     * @return QueryBuilder
     */
    public function addWhereClause(
        QueryBuilder $query, $field, $value = null, $expression = 'eq'
    ) {
        $datePattern = DateValidator::PATTERN;
        $dateTimePattern = DateTimeValidator::PATTERN;
        if (preg_match($datePattern, $value)
            || preg_match($dateTimePattern, $value)
        ) {
            $value = new \DateTime($value);
        }
        switch ($expression) {
        case 'eq':
        default:
            $query->field($field)->equals($value);
            break;
        case 'gte':
            $query->field($field)->gte($value);
            break;
        case 'lte':
            $query->field($field)->lte($value);
            break;
        case 'neq':
            $query->field($field)->notEqual($value);
            break;
        case 'like':
            $likeStart = $likeEnd = false;
            if ($value[0] === '%') {
                $value = substr($value, 1);
                $likeStart = true;
            }
            $lastIndex = strlen($value) - 1;
            if ($value[$lastIndex] === '%') {
                $value = substr($value, 0, $lastIndex - 1);
                $likeEnd = true;
            }
            if ($likeStart) {
                $value = '/.*' . $value .
                    ($likeEnd ? '.*/' : '$/');
            }
            if ($likeEnd && !$likeStart) {
                $value = '/^' . $value . '.*/';
            }
            $query->field($field)->equals(
                new \MongoRegex($value . 'i')
            );
            break;
        case 'isNull':
            $query->expr()->equals(null);
            break;
        }
        $this->whereCount++;
        return $query;
    }

    /**
     * Paginate a query
     *
     * @param QueryBuilder $query  The doctrine query builder.
     * @param int          $offset The pagination offset.
     * @param int          $limit  The pagination limit.
     *
     * @return QueryBuilder
     */
    public function paginateQuery(QueryBuilder $query, $offset = null, $limit = null)
    {
        if (is_null($offset)) {
            $offset = 0;
        }
        if (is_null($limit)) {
            $limit = 10;
        }
        $query->skip($offset);
        $query->limit($limit);
        return $query;
    }

    /**
     * Item get_one returning an Document checking its accessibility
     *
     * @param integer $id   The document ID.
     * @param User    $user The user making the request.
     *
     * @return Document
     */
    public function getItem($id, User $user = null)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, $user);
        $query->field('id')->equals($id);

        $entity = $this->fetchSingleResult($query);
        return $entity;
    }

    /**
     * Fetch a single AbstractQuery result
     * Catches NoResultException
     *
     * @param QueryBuilder $query The doctrine query builder.
     *
     * @return Document|bool
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function fetchSingleResult(QueryBuilder $query)
    {
        $entity = false;
        try {
            $entity = $query->getQuery()->getSingleResult();

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
            ->count();
        $countQuery = $this->applyRoleFiltering($countQuery, $user);
        return $countQuery
            ->getQuery()
            ->execute();
    }

    /**
     * Check that an existing entity with a given value
     * for a given column name exists in the database
     *
     * @param mixed    $value  The value to check.
     * @param int|null $id     A document ID to exclude.
     * @param string   $column The column name to check [optional].
     *
     * @return boolean
     */
    public function checkValueExists($value, $id, $column = 'name')
    {
        $query = $this->createQueryBuilder('this')
            ->select('COUNT(id)')
            ->field('' . $column)->equals($value);
        if ($id) {
            $query->field('id')->notEqual($id);
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
