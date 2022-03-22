<?php

declare(strict_types=1);

namespace app\models;

use Exception;
use Stringable;
use DateInterval;
use yii\base\Model;
use LogicException;
use DateTimeInterface;
use yii\base\InvalidConfigException;
use app\modelCollections\ModelCollectionInterface;

/**
 * @package app\modules\api\models
 */
abstract class AbstractModel extends Model implements ModelInterface
{
    private const GEN_ID_PREFIX       = 'spl_object_id';
    public const DATE_FORMAT          = 'Y-m-d';

    /**
     * @param array $fields
     * @param bool  $recursive
     * @return array
     */
    public function serializeFields(array $fields, bool $recursive = true): array
    {
        $rawData = [];

        foreach ($fields as $alias => $field) {
            $alias = is_int($alias) ? $field : $alias;

            if (!$this->propertyExists($field)) {
                throw new LogicException("property {$field} is unavailable");
            }

            $property = $this->{$field};

            if (
                is_object($property) &&
                ! (
                    $property instanceof ModelInterface ||
                    $property instanceof DateTimeInterface ||
                    $property instanceof ModelCollectionInterface ||
                    $property instanceof DateInterval ||
                    $property instanceof Stringable
                )
            ) {
                continue;
            }

            if ($property instanceof ModelInterface) {
                $rawData[$alias] = $recursive
                    ? $property->serializeFields($property->fields())
                    : $property->getKeyValue();
            } elseif ($property instanceof DateTimeInterface) {
                $rawData[$alias] = $property->format('Y-m-d H:i:s');
            } elseif ($property instanceof DateInterval) {
                $rawData[$alias] = serialize($property);
            } elseif ($property instanceof ModelCollectionInterface && !$recursive) {
                continue;
            } elseif ($property instanceof ModelCollectionInterface) {
                $rawData[$alias] = $property->serializeFields($property->fields());
            } elseif ($property instanceof Stringable) {
                $rawData[$alias] =(string) $property;
            } elseif (!is_object($property)) {
                $rawData[$alias] = $property;
            }
        }

        return $rawData;
    }


    abstract protected function getMandatoryFields(): array;

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return $this->serializeFields($this->fields());
    }

    protected function handleValidationFailed($field, $description): void
    {
        $this->addError($field, $description);

        throw new Exception($description);
    }

    public function propertyExists(string $property): bool
    {
        return property_exists($this, $property) || method_exists($this, 'get' . ucfirst($property));
    }


    protected function genId(): string
    {
        return self::GEN_ID_PREFIX . '_' . spl_object_id($this);
    }

    public function init(): void
    {
        foreach ($this->getMandatoryFields() as $field) {
            if (!isset($this->$field)) {
                $model = get_class($this);
                throw new InvalidConfigException("Field {$field} is mandatory in {$model} model");
            }
        }
    }
}
