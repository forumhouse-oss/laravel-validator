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
     *
     * @param OrderControllerValidator $validator
     * @param OrderServiceInterface    $orderService
     * @param CategoryServiceInterface $categoryService
     * @param FileServiceInterface     $files
     * @param LocationServiceInterface $locations
     *
     * @throws Exception
     */
    public function __construct(OrderControllerValidator $validator) {
        $this->validator = $validator;
    }
    
    public function getShowValidatedData() {
        return Response::make($this->validator->description);
    }
```

Documentation
-----------------------------------

### Controllers - creating validator class

To create a controller validator just make an empty class in any namespace and inherit it from either:

 - `\FHTeam\LaravelValidator\Validator\Input\AbstractInputValidator` - this one is for manual validation. You need to
 create an instance of it manually via App::make() call, passing your descendant's class name or
 [inject](http://laravel.com/docs/5.0/controllers#dependency-injection-and-controllers) it into controller. Validation
 will not be done automatically. You will have to manually call `isThisValid(null)` method to run it.
 - `FHTeam\LaravelValidator\Validator\Input\ValidatesWhenResolved\FrontendControllerValidatorWhenResolved` - when using
 this one as a base, you need just to [inject](http://laravel.com/docs/5.0/controllers#dependency-injection-and-controllers) 
 it into your controller's constructor or method. Validation will work auto-magically
 - `FHTeam\LaravelValidator\Validator\Input\RoutingMiddleware\FrontendControllerValidatorMiddleware` - this one should
 be used as a [routing middleware](http://laravel.com/docs/5.0/controllers#controller-middleware)
 
Controller validator classes contain special `$inputTypes` property, which is a bitmask of what data will be imported
into validator for validation. By default it is only `AbstractInputValidator::VALIDATE_INPUT` which means 
`Input::all()` is validated. But you can validate many other types of input as well.
 
### Validation rules

Now declare `protected $rules` array field. This field will contain rules for each controller method called. Rule array
can contain the following keys:

 - `*` this key means that rules, listed under it will be used when any controller method was executed
 - `controllerMethod` - this key means, that rules listed under it will be used when only `controllerMethod` is executed
 - `controllerMethod1, controllerMethod2` - this key means, that rules listed under it will be used when either
   `controllerMethod1` or `controllerMethod2` is executed
   
Rules are merged before validation. If there are two rules for the same attribute - last declared one wins. Rule syntax
used is Laravel standard one.

### Redirects

If validation fails, a redirect is issued. Redirects are declared in almost the same way as rules. Redirect data for
controller method can contain the following members (check example above):

```php
        protected $redirects = [
            'postLogin' => [
                'route' => 'login',
                'templateVar' => 'login_errors', //if empty, withErrors($validator) will be used
                'withInput' => true, //by default, can be omitted
            ],
            'postRegister' => [
                 route => ['register', ['param1' => 'test',
                                        'param2' => ':currentInputParam2'
                                        'param3' => '#currentRouteParam3'
                                      ] - params prefixed with ":" taken from Input::get() by provided name
            ],
        ];
```

 - `route` is the name of the route to redirect to in case of error. It can be either string (if you are
 redirecting just to the route) or an array. In the latter case array should contain parameters for the route you are
 redirecting user to. You can use just some value or tell validator to fetch existing input data or route parameter.
 - `action` works just like `route`, but redirects to controller action, not to a route
 - `withInput` is true by default means, that all the input is passed with the redirection and will be available as 
 `Input::old()`
 - Laravel's error array is passed with the redirect data by default (`withErrors()`), but you can assign all errors 
 to some variable by using `templateVar`
 - You can use `formName` to make validator call `withErrors($messageBag, $formName)` if you need to
 
 
### Models - creating validator class

 - Create validator class inheriting from `\FHTeam\LaravelValidator\Validator\Eloquent\EloquentModelValidator` and 
  declare `$rules` just as you did with the controller. Default naming convention of validator for a model `\Acme\Model`
  is `\Acme\ModelValidator`
 - If your model uses states (for example, `Order` can be `open` or `closed` and in the open state field 
 `contractor_id` can be null, but in `closed` - it cannot) you can override `getValidationGroup()` method to return
 `$this->state` for example.
 - Now declare rules for your model. If you don't use state, validation rules for fields can be just put into `$rules`
 array. If you use state, consider it to be the same as controller method name and declare rules likewise.

 - `use` `\FHTeam\LaravelValidator\Validator\Eloquent\EloquentValidatingTrait` in your model. 
 
 - If you use custom validator class name, override `createValidator()` method provided by the trait
 
### Validation rules - adding
You may add new validation rules to the system in an OOP style by creating a rule class implementing 
`\FHTeam\LaravelValidator\Rule\ValidationRuleInterface` interface. You can actually inherit it from
`\FHTeam\LaravelValidator\Rule\AbstractValidationRule` class to provide default Laravel's replacer 
functionality. After that the rule should be registered via 
`\FHTeam\LaravelValidator\Rule\ValidationRuleRegistry::register` call. Rule class instance will be created using
IoC container so you can inject whatever you want into rule's constructor

### Converters

Rule set for a field can contain special *converter* rules. These rules declare that after successful validation
field value should be passed via converter with the specified name like `trim` for example.