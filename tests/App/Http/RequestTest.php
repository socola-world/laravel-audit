<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Http;

use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;
use function PHPUnit\Framework\assertCount;

class RequestTest extends TestCase
{
//    /**
//     * A basic feature test example.
//     *
//     * @return void
//     */
//    public function test_example()
//    {
//        $response = $this->get('/');
//
//        $response->assertStatus(200);
//    }
//
//    /**
//     * @test
//     */
//    public function it_can_x()
//    {
////        dd('aaa');
////        $this->assertTrue(true);
//    }

    /**
     * @dataProvider requests
     */
    public function test_rule_mising_attributes($request)
    {
            $requestClassName = trim($request->getName(), "\\");
            $className = str_replace('\\', '__', $requestClassName);

            $class = sprintf('class %s extends %s {
                protected function failedValidation($validator)
                {

                }

                public function authorize()
                {
                    return true;
                }
            }', $className, $requestClassName);

            if (class_exists($className) === false) {
                eval($class);
            }

            /**
             * @type \Illuminate\Foundation\Http\FormRequest $request1
             */
            $request1 = new $className();
//            $request1 = app($className);
////            $request1 = app($className);
//
            $rules = $request1->rules();
            $attributes = $request1->attributes();
            $missingAttributes = array_diff(array_keys($rules), array_keys($attributes));

            $this->assertCount(
                0,
                $missingAttributes,
                $this->echo($requestClassName, "missing attributes", $missingAttributes)
            );

////            $this->assertEquals('', 's');
//        }
//
////        $this->assertCount(10, $requestClasses->toArray());
    }

//    public function test_rules_has_attributes()
//    {
//        auth('store_manager')->attempt(['email' => 'admin@gmail.com', 'password' => 'hblab@12345']);
//        $requestClasses = $this->getClassRequests();
//
//        foreach ($requestClasses as $requestClass) {
//            $requestClassName = trim($requestClass->getName(), "\\");
////            Log::debug($requestClass);
//            $className = str_replace('\\', '__', $requestClassName);
//
//            $class = sprintf('class %s extends %s {
//                protected function failedValidation($validator)
//                {
//
//                }
//
//                public function authorize()
//                {
//                    return true;
//                }
//            }', $className, $requestClassName);
//
//            Log::debug($class);
//
//            if (class_exists($className) === false) {
//                eval($class);
//            }
//
////            /**
////             * @type \Illuminate\Foundation\Http\FormRequest $request1
////             */
////            $request1 = new $className();
////            $request1 = app($className);
////            $request1 = app($className);
//
////            $rules = $request1->rules();
////            $attributes = $request1->attributes();
////            $missingAttributes = array_diff(array_keys($rules), array_keys($attributes));
////
////            $this->assertCount(
////                0,
////                $missingAttributes,
////                "$requestClass missing attributes [". implode(', ', $missingAttributes) . "]"
////            );
//
////            $this->assertEquals('', 's');
//        }
//
////        $this->assertCount(10, $requestClasses->toArray());
//    }
}
