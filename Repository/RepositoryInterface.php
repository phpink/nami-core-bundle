<?php

namespace PhpInk\Nami\CoreBundle\Repository;

use PhpInk\Nami\CoreBundle\Model\ModelInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Repository interface (ODM/ORM)
 *
 * @package PhpInk\Nami\CoreBundle\Repository\Repository
 * @author  Geoffroy Pierret <geofrwa@yandex.com>
 */
interface RepositoryInterface
{
    /**
     * Save/Persist a entity instance
     *
     * @param ModelInterface|int|string $modelOrId The model or its id.
     *
     * @return void
     */
    public function removeModel($modelOrId);

    /**
     * Save/Persist a model instance
     *
     * @param ModelInterface $model The entity to save.
     *
     * @return ModelInterface
     */
    public function saveModel(ModelInterface $model);

    /**
     * Build the items query (join, filters)
     *
     * @param mixed         $query The doctrine query builder.
     * @param UserInterface $user  The user who made the request.
     *
     * @return mixed
     */
    public function buildItemsQuery($query, UserInterface $user = null);

    /**
     * Applies the correct addOrderBy to a query
     * Search for ASC/DESC order with '-' Prefix
     *
     * @param mixed  $query         The query builder to sort field on.
     * @param string $orderByField  Field name.
     * @param string $orderByValue  Sort value : 0,1 for ASC,DESC.
     * @param array  $allowedFields Checked fields.
     *
     * @return mixed
     */
    public function addOrderByClause(
        $query, $orderByField,
        $orderByValue = null, $allowedFields = null
    );

    /**
     * Adds a where clause to a query
     *
     * @param mixed  $query      The query builder for the where clause
     * @param string $field      The field for the where clause.
     * @param mixed  $value      The value of the where clause.
     * @param string $expression The expression of the where clause.
     *
     * @return mixed
     */
    public function addWhereClause(
        $query, $field, $value = null, $expression = 'eq'
    );

    /**
     * Item get_one returning an model, checking access rights
     *
     * @param int|string    $id   The model id.
     * @param UserInterface $user The user making the request.
     *
     * @return ModelInterface
     */
    public function getItem($id, UserInterface $user = null);

    /**
     * Fetch a single AbstractQuery result.
     * Catches NoResultException
     *
     * @param mixed $query The doctrine query builder.
     *
     * @return ModelInterface|bool
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function fetchSingleResult($query);

    /**
     * Count the total get_all items
     *
     * @param UserInterface $user The user making the request.
     *
     * @return int
     */
    public function countItems(UserInterface $user = null);

    /**
     * Paginate a query
     *
     * @param mixed $query  The dquery builder to paginate.
     * @param int   $offset The pagination offset [optional].
     * @param int   $limit  The pagination limit [optional].
     *
     * @return mixed Paginated query
     */
    public function paginateQuery($query, $offset = null, $limit = null);

    /**
     * Check that an existing entity with a given value
     * for a given column name exists in the database
     *
     * @param mixed    $value  Value to check.
     * @param int|null $id     Model Id to exclude.
     * @param string   $column Column name to check [optional].
     *
     * @return bool
     */
    public function checkValueExists($value, $id, $column = 'name');



    /**
     * Create an empty model instance
     *
     * Takes any given params [optional]
     *
     * @return ModelInterface
     */
    public function createModel();

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
    );

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
    );


    /**
     * Sorting of getItemsQuery

     * @param mixed $query         The query builder.
     * @param array $orderBy       Field names.
     * @param array $allowedFields Checked fields.
     *
     * @return mixed Query builder object
     */
    public function getItemsQueryOrderBy($query, $orderBy, $allowedFields = null);

    /**
     * Filtering of getItemsQuery
     *
     * @param mixed $query         The query builder.
     * @param array $filterBy      Filters.
     * @param array $allowedFields Allowed fields.
     *
     * @return mixed Query builder object
     */
    public function getItemsQueryFilterBy($query, $filterBy, $allowedFields = null);

    /**
     * Applies the correct Where condition to a query
     *
     * @param mixed  $query         Query to sort field on.
     * @param string $filterField   Field name.
     * @param string $filterValue   Value filtered.
     * @param array  $allowedFields Checked fields.
     *
     * @return mixed The query builder object.
     */
    public function addFilterByClause(
        $query, $filterField, $filterValue, $allowedFields = null
    );

    /**
     * Add the 'public' condition for users without ROLE_MANAGER
     *
     * @param mixed         $query The query builder.
     * @param UserInterface $user  The logged user.
     *
     * @return mixed Query builder object
     */
    public function applyRoleFiltering($query, UserInterface $user = null);

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
    );

    /**
     * Item put_all
     *
     * @param array $fields The fields & values to update.
     * @param array $ids    The ids to update.
     *
     * @return integer The number of fields affected.
     */
    public function putItems($fields, $ids);

    /**
     * Item delete_all
     *
     * @param array $ids The ids to delete.
     *
     * @return integer The number of fields deleted.
     */
    public function deleteItems($ids);

    /**
     * Get the entity class name
     * mapped by the current Repository
     *
     * @return string
     */
    public function getModelClass();

    /**
     * Get the value of filterByFields.
     *
     * @return array
     */
    public function getFilterByFields();

    /**
     * Get the value of orderByFields.
     *
     * @return array
     */
    public function getOrderByFields();

    /**
     * Get the value of orderByRelationFields.
     *
     * @return array
     */
    public function getOrderByRelationFields();
}
