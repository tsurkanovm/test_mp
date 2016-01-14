<?php
namespace common\modules\parser\models;

use yii\base\ErrorException;
use yii\base\Model;
use yii\web\UploadedFile;
use Yii;
use common\components\CustomVarDamp;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadFileParsingForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    // chosen file
    public $file;
    public $show;


    public function rules()
    {

        return [
            ['show', 'in', 'range' => [10, 100, 'all'] ],
            ['file', 'required'],
            [['file'], 'file', 'extensions' => ['csv', 'xlsx', 'xml', 'xls', 'txt'], 'checkExtensionByMimeType' => false ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('app', 'Custom file'),
        ];
    }
}