Laravel validation package
===================================

 Metrics | _
---|---
Version | [![PHP version](https://badge.fury.io/ph/fhteam%2Flaravel-validator.svg)](http://badge.fury.io/ph/fhteam%2Flaravel-validator)
Compatibility | [![Laravel compatibility](https://img.shields.io/badge/laravel-5-green.svg)](http://laravel.com/)
Quality | [![Code Climate](https://codeclimate.com/github/fhteam/laravel-validator/badges/gpa.svg)](https://codeclimate.com/github/fhteam/laravel-validator) [![Build Status](https://travis-ci.org/fhteam/laravel-validator.svg?branch=master)](https://travis-ci.org/fhteam/laravel-validator) [![Coverage Status](https://coveralls.io/repos/fhteam/laravel-validator/badge.svg?branch=master)](https://coveralls.io/r/fhteam/laravel-validator?branch=master)

Features:
-----------------------------------

 - Validation logic is completely separate from objects being validated
 - Validation rules and validation failure behaviour can be written in a declarative way in 90% of cases
 - Uses Laravel validator core and rules
 - Stateful model validation
 - Provides OOP way to write and register new validation rules
 - Serves as a replacer to a Input::get() to prevent unvalidated data from sneaking into your application
 - Rule-based data transformations of validated data before it reaches your application logic (in progress)
 - Provides unified way to write rules for controllers and models
 - Can validate and aggregate many types of input: $_GET/$_POST, $_FILES, HTTP headers, Session data, Cookies (both
   PHP native and Laravel's encrypted ones)
 - Two types of input validators: middleware (to be manually registered) and validate-when-resolved (just inject and 
 you are done)
 
Documentation
------------------------------------
 - Documentation is available [here|https://github.com/fhteam/laravel-validator/wiki]
 
Quick example:
-----------------------------------

```php
class OrderControllerValidator extends FrontendControllerValidatorWhenResolved
{
    protected $rules = [
        '*' => [
            'user_id' => 'required|numeric',
        ],
        'getCreate, getShow, getEdit' => [],
        'postOpen, postDelete, postAssignContractor, postCancelContractorAssignment' => [],
        'postCreate' => [
            'title' => 'required|max:100',
            'categoryId' => 'required|numeric',
            'description' => 'required|max:4096',
        ],
        'postEdit, postFileDelete, postFileUpload' => [
            'title' => 'required|max:100',
            'categoryId' => 'required|numeric',
            'description' => 'required|max:4096',
        ],
    ];

    protected $errorRedirects = [
        'getShow' => ['route' => 'home'],
        'postCreate' => ['route' => 'orders_create'],
        'postEdit, postFileUpload, postFileDelete' => ['route' => ['orders_edit', ['orderId' => '#orderId']]],
    ];
}

class OrderController extends Controller
{
    /**
     * @var OrderControllerValidator
     */
    protected $validator;
    
    /**
     * IoC invoked constructor
     */
    public function __construct(OrderControllerValidator $validator) {
        $this->validator = $validator;
    }
    
    public function getShowValidatedData() {
        return Response::make($this->validator->description);
    }
```