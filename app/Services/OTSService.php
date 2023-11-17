<?php

namespace App\Services;

use Aliyun\OTS\Consts\DefinedColumnTypeConst;
use Aliyun\OTS\Consts\PrimaryKeyTypeConst;
use Aliyun\OTS\Consts\RowExistenceExpectationConst;
use Aliyun\OTS\OTSClient;
use Aliyun\OTS\ProtoBuffer\Protocol\DefinedColumnType;
use Aliyun\OTS\ProtoBuffer\Protocol\PrimaryKeyType;

use function Hyperf\Support\env;

class OTSService
{
    protected OTSClient $client;

    public function __construct()
    {
        $this->client = new OTSClient([
            'EndPoint' => env('OTS_ENDPOINT'),
            'AccessKeyID' =>  env('OTS_ACCESS_KEY'),
            'AccessKeySecret' =>  env('OTS_ACCESS_SECRET'),
            'InstanceName' =>  env('OTS_INSTANCE')
        ]);      
    }
    public function generateUuid4() {
        $randomBytes = bin2hex(random_bytes(16));
        return sprintf("%s-%s-%s-%s-%s",
            substr($randomBytes, 0, 8),
            substr($randomBytes, 8, 4),
            substr($randomBytes, 12, 4),
            substr($randomBytes, 16, 4),
            substr($randomBytes, 20)
        );
    }
    public function setToken($token,$openid)
    {
        $request = [
            'table_name' => 'user_token',
            'condition' => RowExistenceExpectationConst::CONST_IGNORE, //设置期望原行不存在时，写入数据。
            'primary_key' => [ 
                ['token', $token],
            ],
            'attribute_columns' => [
                ['openid',$openid],
            ]
        ];
        $this->client->putRow($request);    
    }

    public function getToken($token)
    {
        $request = [
            'table_name' => 'user_token',
            'primary_key' => [ //设置主键。
                ['token', $token],
            ],
            'max_versions' => 1,
            'columns_to_get' => ['user_id','openid']
        ];
        $response = $this->client->getRow ($request);            
        return  $response['attribute_columns'][0][1] ?? null;
    }

    public function createRecord($question,$userId)
    {
        $id = $this->generateUuid4();
        $request = [
            'table_name' => 'record',
            'condition' => RowExistenceExpectationConst::CONST_IGNORE, //设置期望原行不存在时，写入数据。
            'primary_key' => [ //设置主键。
                ['id', $id],
            ],
            'attribute_columns' => [
                ['question',$question],
                ['user_id',$userId],
            ]
        ];
        $response = $this->client->putRow($request);   
        return $id;
    }

    public function updateRecord($id,$content)
    {
        $request = [
            'table_name' => 'record',
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => [ //设置主键。
                ['id', $id]
            ],
            'update_of_attribute_columns' => [
                'PUT' => [                       //更新一些列。
                    ['content', $content],
                ]
            ]
        ];
        $response = $this->client->updateRow($request);        
    }

    public function getUser($openid)
    {
        $request = [
            'table_name' => 'user',
            'primary_key' => [ //设置主键。
                ['openid', $openid],
            ],
            'max_versions' => 1,
            'columns_to_get' => ['user_id','openid']
        ];
        return $this->client->getRow($request);          
    }

    public function createUser($openid)
    {
        $request = [
            'table_name' => 'user',
            'condition' => RowExistenceExpectationConst::CONST_IGNORE, //设置期望原行不存在时，写入数据。
            'primary_key' => [ //设置主键。
                ['openid', $openid]
            ],
            'attribute_columns' => [
                ['created_at',time()],
            ]
        ];
        return $this->client->putRow($request);   
    } 


    public function createUserTable()
    {
        $result = $this->client->createTable([
            'table_meta' => [
                'table_name' => 'user', 
                'primary_key_schema' => [
                    ['openid', PrimaryKeyTypeConst::CONST_STRING], 
                    ['id', PrimaryKeyTypeConst::CONST_INTEGER,PrimaryKeyTypeConst::CONST_PK_AUTO_INCR], 
                    
                ],
                'defined_column' => [
                    ['created_at', DefinedColumnTypeConst::DCT_INTEGER],
                    
                ]
            ], 
            'reserved_throughput' => [
                'capacity_unit' => [
                    'read' => 0, 
                    'write' => 0
                ]
            ],
            'table_options' => [
                'time_to_live' => -1,   
                'max_versions' => 2,    
                'deviation_cell_version_in_sec' => 86400  
            ],
            'stream_spec' => [
                'enable_stream' => true,
                'expiration_time' => 24
            ]
        ]);       
    }
    public function createTemplateTable()
    {
        $result = $this->client->createTable([
            'table_meta' => [
                'table_name' => 'teamplate', 
                'primary_key_schema' => [
                    ['name', PrimaryKeyTypeConst::CONST_STRING], 
                ],
                'defined_column' => [
                    ['template_content', DefinedColumnTypeConst::DCT_STRING],
                    
                ]
            ], 
            'reserved_throughput' => [
                'capacity_unit' => [
                    'read' => 0, 
                    'write' => 0
                ]
            ],
            'table_options' => [
                'time_to_live' => -1,   
                'max_versions' => 2,    
                'deviation_cell_version_in_sec' => 86400  
            ],
            'stream_spec' => [
                'enable_stream' => true,
                'expiration_time' => 24
            ]
        ]);       
    }

    public function createRecordtable()
    {
        $result = $this->client->createTable([
            'table_meta' => [
                'table_name' => 'reocrd', 
                'primary_key_schema' => [
                    ['name', PrimaryKeyTypeConst::CONST_STRING],
                ],
                'defined_column' => [
                    ['template_content', DefinedColumnTypeConst::DCT_STRING],
                    
                ]
            ], 
            'reserved_throughput' => [
                'capacity_unit' => [
                    'read' => 0, 
                    'write' => 0
                ]
            ],
            'table_options' => [
                'time_to_live' => -1,   
                'max_versions' => 2,    
                'deviation_cell_version_in_sec' => 86400  
            ],
            'stream_spec' => [
                'enable_stream' => true,
                'expiration_time' => 24
            ]
        ]);       
    }
    
    public function getTempalteByName($name)
    {
        $request = [
            'table_name' => 'template',
            'primary_key' => [ //设置主键。
                ['name', $name],
            ],
            'max_versions' => 1,
            'columns_to_get' => ['prompts','template']
        ];
        $response = $this->client->getRow($request);           
        return [
            'prompts'=> $response['attribute_columns'][0][1],
            'template'=> $response['attribute_columns'][1][1]
        ];
    }
}
