<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "details_test".
 *
 * @property integer $id
 * @property string $articul
 * @property string $brand
 * @property double $price
 * @property integer $quantity
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 */
class DetailsTest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'details_test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           // [['articul', 'brand', 'created_at', 'updated_at'], 'required'],
            [['articul', 'brand'], 'required'],
            [['price'], 'number'],
            [['quantity', 'created_at', 'updated_at'], 'integer'],
            [['articul'], 'string', 'max' => 255],
            [['brand'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 200],
            [['articul'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'articul' => 'Articul',
            'brand' => 'Brand',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
