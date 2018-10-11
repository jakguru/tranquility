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

    public function participants()
    {
        return $this->morphTo('meeting_participants');
    }
}
