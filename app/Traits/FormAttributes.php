<?php

namespace App\Traits;

trait FormAttributes
{
    protected function validationAttributes(): array
    {
        $ruleNames = [];

        if (isset($this->form) && method_exists($this->form::class, 'rules')) {
            foreach ($this->form->rules() as $key => $val) {
                $ruleNames['form.'.$key] = $key;
            }
        }

        if (isset($this->form) && method_exists($this->form::class, 'attributes')) {
            foreach ($this->form->attributes() as $key => $val) {
                $ruleNames['form.'.$key] = $val;
            }
        }

        return $ruleNames;
    }
}
