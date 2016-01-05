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

    protected static $data;

    public static function setUpBeforeClass()
    {
        $options =
            ['csv' =>
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

        $file_path = Yii::getAlias('@tests') . '\template.csv';

        $ph = new YiiParserHandler();
        $ph->setConfiguration( $options );
        $csv_parser = Stub::make( new YiiMultiparser(), ['parserHandler' => $ph] );

        if (!$csv_parser)
            self::markTestSkipped('Parser handler do not initialized.');

        self::$data = $csv_parser->parse( $file_path, ['mode' => 'template'] );
    }



    public function testOnEmptyResult( ){

        $this->assertNotEmpty( self::$data , 'Output array is empty' );

    }

    /**
     * @depends testOnEmptyResult
     */
    public function testOnAssocResult( ){

        $this->assertArrayHasKey( 'Article', self::$data[0], 'Output array does`t have key - Article'  );
        $this->assertArrayHasKey( 'Count', self::$data[1], 'Output array does`t have key - Count'  );
        $this->assertArrayHasKey( 'Description', self::$data[2], 'Output array does`t have key - Description'  );
        $this->assertArrayHasKey( 'Price', self::$data[3], 'Output array does`t have key - Price'  );
        $this->assertArrayHasKey( 'Price', self::$data[13], 'Output array does`t have key - Brand'  );

    }

    /**
     * @depends testOnEmptyResult
     */
    public function testOnFullResult( ){

        $this->assertEquals( 16, count( self::$data ), 'Output array does`t have 16 rows'  );

    }






}