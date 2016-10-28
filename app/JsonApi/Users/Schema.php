<?php


namespace App\JsonApi\Users;


use App\User;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\LaravelJsonApi\Schema\EloquentSchema;
use Illuminate\Database\Eloquent\Model;

class Schema extends EloquentSchema
{

    /**
     * The json-api resource type of the User model.
     */
    const RESOURCE_TYPE = 'users';

    /**
     * @inheritdoc
     */
    protected $attributes = [
        'email',
        'sex',
        'first_name',
        'last_name',
        'date_of_birth',
        'active'
    ];

    /**
     * @inheritdoc
     */
    public function getResourceType()
    {
        return self::RESOURCE_TYPE;
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        /** @var User $resource */

        if (!$resource instanceof User) {
            throw new RuntimeException('Expected a user model.');
        }

        return [
            'groups-admin' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['groups-admin']) ? true : false,
                self::DATA => $resource->groupsAdmin,
            ],
            'groups-member' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['groups-member']) ? true : false,
                self::DATA => $resource->groupsMember,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    protected function serializeAttribute($value, Model $model, $modelKey)
    {
        if ($modelKey == 'date_of_birth') {
            /** @var \DateTime $value */
            return $value->format('Y-m-d');
        }

        return parent::serializeAttribute(
            $value,
            $model,
            $modelKey
        );
    }


}