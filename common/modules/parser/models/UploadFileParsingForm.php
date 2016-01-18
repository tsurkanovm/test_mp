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
    // how many rows of readed array we showed to user
    public $read_line_end;
    // first row to write in DB
    public $write_line_begin;
    // last row to write in DB
    public $write_line_end;

    // attribute for parser extensions
    protected $extensions;
    // parser configuration, use for define parser extensions
    public $parser_config;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->extensions = array_keys( $this->parser_config );
    }


    public function rules()
    {
        return [
            ['read_line_end', 'in', 'range' => [10, 100, 'все'] ],
            ['file', 'required'],
            [['file'], 'file', 'extensions' =>  $this->extensions, 'checkExtensionByMimeType' => false ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('app', 'Custom file'),
        ];
    }
}