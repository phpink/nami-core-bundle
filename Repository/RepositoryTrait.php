<?php

namespace PhpInk\Nami\CoreBundle\Repository;

use PhpInk\Nami\CoreBundle\Model\UserInterface;
use PhpInk\Nami\CoreBundle\Util\PaginatedCollection;

/**
 * RepositoryTrait
 * Common ORM/ODM repository methods
 */
trait RepositoryTrait
{
    /**
     * Query field maps
     *
     * @var array
     */
    protected $orderByFields = array();

    protected $orderByRelationFields = array();

    protected $filterByFields = array();

    /**
     * Where clauses count for "where/addWhere"
     * (assuming one request per controller/repository instantiation)
     *
     * @var int
     */
    protected $whereCount = 0;


    /**
     * Create the entity object
     * when POSTing new Entities
     *
     * Takes any given params [optional]
     *
     * @return Entity
     */
    public function createModel()
    {
        $_reflection = new \ReflectionClass(
            $this->getModelClass()
        );
        return $_reflection->newInstanceArgs(
            func_get_args()
        );
    }

    /**
     * Item get_all query
     *
     * @param UserInterface $user     The user making the request.
     * @param array         $orderBy  Fields to order [optional].
     * @param array         $filterBy Fields to filter [optional].
     *
     * @return mixed Query builder object.
     */
    public function getItemsQuery(
        UserInterface $user = null,
        $orderBy = array(), $filterBy = array()
    ) {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, $user);
        $query = $this->applyRoleFiltering($query, $user);
        $query = $this->getItemsQueryOrderBy($query, $orderBy);
        $query = $this->getItemsQueryFilterBy($query, $filterBy);
        return $query;
    }

    /**
     * Item get_all query with a where clause added
     *
     * @param string        $paramName  The where clause field.
     * @param integer       $paramValue The where clause value.
     * @param UserInterface $user       The user making the request.
     * @param integer       $offset     The pagination offset.
     * @param integer       $limit      The pagination limit.
     * @param array         $orderBy    Fields to order [optional].
     * @param array         $filterBy   Fields to filter [optional].
     *
     * @return mixed Query builder object
     */
    public function getItemsQueryWhere(
        $paramName, $paramValue, UserInterface $user = null,
        $offset = null, $limit = null, $orderBy = array(), $filterBy = array()
    )
    {
        $query = $this->getItemsQuery($user, $orderBy, $filterBy);
        $query = $this->addWhereClause($query, $paramName, $paramValue);
        return $this->paginateQuery($query, $offset, $limit);
    }


    /**
     * Sorting of getItemsQuery

     * @param mixed $query         The query builder.
     * @param array $orderBy       Field names.
     * @param array $allowedFields Checked fields.
     *
     * @return mixed Query builder object
     */
    public function getItemsQueryOrderBy($query, $orderBy, $allowedFields = null)
    {
        // Order by from paramFetcher $orderBy if not empty
        if (is_array($orderBy) && count($orderBy)) {
            foreach ($orderBy as $orderByField => $orderByValue) {
                if (is_string($orderByField) && strlen($orderByField)) {
                    $query = $this->addOrderByClause(
                        $query, $orderByField,
                        $orderByValue, $allowedFields
                    );
                }
            }
            // Default orderBy on the first self::$orderByFields element
        } else {
            $orderByClause = reset($this->orderByFields);
            if (is_array($orderByClause)) {
                foreach ($orderByClause as $orderByClauseItem) {
                    $query = $this->addOrderByClause(
                        $query, $orderByClauseItem,
                        null, $allowedFields
                    );
                }
            }
        }
        return $query;
    }

    /**
     * Filtering of getItemsQuery
     *
     * @param mixed $query         The query builder.
     * @param array $filterBy      Filters.
     * @param array $allowedFields Allowed fields.
     *
     * @return mixed Query builder object
     */
    public function getItemsQueryFilterBy($query, $filterBy, $allowedFields = null)
    {
        // Order by from paramFetcher $orderBy if not empty
        if (is_array($filterBy) && count($filterBy)) {
            foreach ($filterBy as $filterField => $filterValue) {
                if (is_string($filterField)
                    && strlen($filterField)
                    && !empty($filterValue)
                ) {
                    $query = $this->addFilterByClause(
                        $query, $filterField,
                        $filterValue, $allowedFields
                    );
                }
            }
        }
        return $query;
    }

    /**
     * Applies the correct Where condition to a query
     *
     * @param mixed  $query         Query to sort field on.
     * @param string $filterField   Field name.
     * @param string $filterValue   Value filtered.
     * @param array  $allowedFields Checked fields.
     *
     * @return mixed The QueryBuilder object.
     */
    public function addFilterByClause(
        $query, $filterField, $filterValue, $allowedFields = null
    ) {
        $whereExpression = 'eq';
        if ($filterValue[0] === '>') {
            $whereExpression = 'gte';
        } elseif ($filterValue[0] === '<') {
            $whereExpression = 'lte';
        } elseif ($filterValue[0] === '!') {
            $whereExpression = 'neq';
        }
        if ($whereExpression !== 'eq') {
            $filterValue = substr($filterValue, 1);
        }
        $filterValueLastIndex = strlen($filterValue) - 1;
        if ($filterValueLastIndex
            && ($filterValue[0] === '%'
            ||  $filterValue[$filterValueLastIndex] === '%')
        ) {
            $whereExpression = 'like';
        }
        if ($filterValue === 'null') {
            $whereExpression = 'isNull';
            $filterValue = null;
        }

        $allowedFields = (!is_array($allowedFields)) ?
            $this->filterByFields : $allowedFields;

        if (array_key_exists($filterField, $allowedFields)) {
            $this->addWhereClause(
                $query, $allowedFields[$filterField],
                $filterValue, $whereExpression
            );
        }
        return $query;
    }

    /**
     * Add the 'public' condition for users without ROLE_MANAGER
     *
     * @param mixed         $query The query builder.
     * @param UserInterface $user  The logged user.
     *
     * @return mixed Query builder object
     */
    public function applyRoleFiltering($query, UserInterface $user = null)
    {
        if (!$user || !$user->isAdmin()) {
            /*
             * Apply some filtering here depending on user
             */
        }
        return $query;
    }

    /**
     * Item get_all returning a collection
     *
     * @param UserInterface $user     The user making the request.
     * @param integer       $offset   The pagination offset.
     * @param integer       $limit    The pagination limit.
     * @param array         $orderBy  The fields to order.
     * @param array         $filterBy The fields to filter.
     *
     * @return mixed The query builder paginated.
     */
    public function getItems(
        UserInterface $user = null, $offset = null,
        $limit = null, $orderBy = array(), $filterBy = array()
    ) {
        $query = $this->paginateQuery(
            $this->getItemsQuery(
                $user, $orderBy, $filterBy
            ),
            $offset, $limit
        );
        return $query;
    }

    /**
     * Item put_all
     *
     * @param array $fields The fields & values to update.
     * @param array $ids    The ids to update.
     *
     * @return integer The number of fields affected.
     */
    public function putItems($fields, $ids)
    {
        $updated = 0;
        if (is_array($ids) && count($ids)) {
            // Filter by the given IDs
            $update = $this->createQueryBuilder('this')->update();
            $update = $this->addWhereClause(
                $update, $this->filterByFields['id'],
                $ids, 'in'
            );
            // Update given fields
            foreach ($fields as $field => $value) {
                if (!is_null($value) && !is_array($value)
                    && array_key_exists($field, $this->filterByFields)
                ) {
                    $update->set(
                        $this->filterByFields[$field],
                        ':'. $field
                    )->setParameter(':'. $field, $value);
                }
            }
            $updated = $update->getQuery()->execute();

        }
        return $updated;
    }

    /**
     * Item delete_all
     *
     * @param array $ids The ids to delete.
     *
     * @return integer The number of fields deleted.
     */
    public function deleteItems($ids)
    {
        $deleted = 0;
        // Filter by the given IDs
        if (is_array($ids) && count($ids)) {
            $itemsSelect = $this->createQueryBuilder('this')->select();
            $itemsSelect = $this->addWhereClause(
                $itemsSelect,
                $this->filterByFields['id'],
                $ids, 'in'
            );
            $items = $itemsSelect->getQuery()->execute();
            if (is_array($items)) {
                foreach ($items as $item) {
                    $this->removeModel($item);
                    $deleted++;
                }
            }

        }
        return $deleted;
    }

    /**
     * Get the entity class name
     * mapped by the current Repository
     *
     * @return string
     */
    public function getModelClass()
    {
        return $this->getClassMetadata()->getName();
    }

    /**
     * Get the value of filterByFields.
     *
     * @return array
     */
    public function getFilterByFields()
    {
        return $this->filterByFields;
    }

    /**
     * Get the value of orderByFields.
     *
     * @return array
     */
    public function getOrderByFields()
    {
        return $this->orderByFields;
    }

    /**
     * Get the value of orderByRelationFields.
     *
     * @return array
     */
    public function getOrderByRelationFields()
    {
        return $this->orderByRelationFields;
    }
}
