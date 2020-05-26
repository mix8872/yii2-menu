<?php

namespace mix8872\menu\models;

/**
 * Class MenuMove
 * @var public integer $prev_id
 * @var public integer $next_id
 * @var public integer $parent_id
 */
class MenuMove extends \yii\base\Model
{
    public $prev_id;
    public $next_id;
    public $parent_id;

    /**
     * Validate vars
     * @return array of validation rules
     */
    public function rules()
    {
        return [
            [['prev_id', 'next_id', 'parent_id'], 'integer']
        ];
    }
}
