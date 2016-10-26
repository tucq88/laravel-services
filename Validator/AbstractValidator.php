<?php namespace App\Libraries\Validator;

use Exception;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Factory as LaravelValidator;
use Prettus\Validator\Exceptions\ValidatorException;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * Data to be validated
     *
     * @var array
     */
    protected $data = [];

    /**
     * Validator object
     *
     * @var LaravelValidator
     */
    protected $validator;

    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validation Rules Params - Use for complex rules like Unique or custom
     * @var array
     */
    protected $rulesParams = [];

    /**
     * Validation errors
     *
     * @var MessageBag
     */
    protected $errors;

    /**
     * AbstractValidator constructor.
     *
     * @param \Illuminate\Validation\Factory $validator
     */
    public function __construct(LaravelValidator $validator)
    {
        $this->validator = $validator;
        $this->rules = $this->rules();
    }

    /**
     * Declare validation rules from SubClass
     *
     * Notice: Should always be overwritten by SubClass
     */
    protected function rules() {
        return [];
    }

    /**
     * Add data to validation against
     *
     * @param array
     * @return AbstractValidator
     */
    public function with(array $data) {
        $this->data = $data;

        return $this;
    }

    /**
     * Pass the data and the rules to the validator
     *
     * @param string $action
     * @return boolean
     */
    public function passes($action = null) {

        $validator = $this->validator->make($this->data, $this->getRules($action));

        if ($validator->fails()) {
            $this->errors = $validator->messages();
            return false;
        }

        return true;
    }

    /**
     * Pass the data and the rules to the validator or throws ValidatorException
     *
     * @param string $action
     * @return bool
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function passesOrFail($action = null) {

        if (!$this->passes($action))
            throw new ValidatorException($this->errorsBag());

        return true;
    }

    /**
     * Retrive validation errors array
     *
     * @return array
     */
    public function errors() {
        return $this->errorsBag()->all();
    }

    /**
     * Retrive validation errors bag
     *
     * @return MessageBag
     */
    public function errorsBag() {
        return $this->errors;
    }

    /**
     * Manually apply rules for Validation
     *
     * @param array $rules
     * @return $this
     */
    public function applyRules(array $rules) {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Get configured rules for validation
     *
     * @param $action
     * @return array
     * @throws \Exception
     */
    public function getRules($action = null)
    {
        $rules = $this->rules;

        if (is_array($rules) && $action && isset($rules[ $action ])) {
            $rules = $rules[ $action ];
        } elseif (is_array($rules) && !$action) {

            foreach ($rules as $rule) {
                if (is_array($rule)) {
                    throw new Exception('exception.validation.action_required', 500);
                }
            }

        } else {
            throw new Exception('exception.validation.action_undefined.', 500);
        }

        return $rules;

    }

    /**
     * Set rules params for complex rules like unique or custom rules
     *
     * @param array $params
     * @return $this
     */
    public function setRulesParams(array $params) {
        $this->rulesParams = $params;
        $this->rules = $this->rules();

        return $this;
    }
}