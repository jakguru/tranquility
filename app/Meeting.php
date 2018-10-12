<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use \App\Helpers\Loggable;
    use \App\Helpers\ElasticSearchable;
    use \App\Helpers\Permitable;
    use \App\Helpers\Ownable;

    protected $notLoggable = [
        'created_at', 'updated_at', 'owner_id',
    ];

    protected $searchable_columns = [
        'subject','description'
    ];

    protected $casts = [
        'email_participants' => 'array',
    ];

    public function getParticipants()
    {
        $full = [];
        $receivable = \App\Helpers\PermissionsHelper::getModelsWithTrait('Receivable');
        foreach ($receivable as $model) {
            $property = \App\Helpers\ModelListHelper::getPluralLabelForClass($model);
            foreach ($this->{$property} as $participant) {
                array_push($full, $participant);
            }
        }
        return collect($full);
    }

    public function users()
    {
        return $this->morphedByMany('App\User', 'participant', 'receivables');
    }
}
