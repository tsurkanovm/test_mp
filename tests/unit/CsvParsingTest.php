<?php
namespace tests\unit;

use Codeception\Util\Stub;
use Yii;
use yii\multiparser\YiiMultiparser;
use yii\multiparser\YiiParserHandler;

class CsvParsingTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    private $options;
    private $file_path;
    protected $csv_parser;
    protected $data;

    public function _before()
    {
        $this->options =  ['csv' =>
            ['template' =>
                ['class' => 'yii\multiparser\CsvParser',
                    'keys' => [
                        0 => 'Description',
                        1 => 'Article',
                        2 => 'Price',
                        3 => 'Brand',
                        4 => 'Count',
                    ],
                    'converter_conf' => [
                        'class' => 'yii\multiparser\Converter',
                        'configuration' => ["encode" => 'Description',
                            "string" => ['Description', 'Brand'],
                            "float" => 'Price',
                            "integer" => 'Count'
                        ],
                    ],
                ],
            ],
        ];

        $this->file_path = Yii::getAlias('@tests') . '\template.csv';

        $ph = new YiiParserHandler();
        $ph->setConfiguration( $this->options );
        $this->csv_parser = Stub::make( new YiiMultiparser(), ['parserHandler' => $ph] );

        if (!$this->csv_parser)
            $this->markTestSkipped('Parser handler do not initialized.');

        $this->data = $this->csv_parser->parse( $this->file_path );
    }


    public function dataProvider(){

        return $this->data;
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEmptyCsv( $data ){

        $this->assertNotEmpty( $data, 'Output array is empty' );

    }
    /**
     * @depends testEmptyCsv
     * @dataProvider dataProvider
     */
    public function testKeysCsv( $data ){

        $this->assertArrayHasKey( 'Article', $data, 'Output array don`t have key - Article'  );

        $this->assertTrue(true);
   }

}