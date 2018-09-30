<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Helpers\PermissionsHelper;
use App\Helpers\Validators;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use Notifiable;
    use \App\Helpers\Loggable;
    use \App\Helpers\ElasticSearchable;
    use \App\Helpers\Listable;

    public static $list_columns = [
        'email' => [
            'type' => 'email',
            'label' =>'Email Address',
        ],
        'name' => [
            'type' => 'text',
            'label' =>'Name',
        ],
        'active' => [
            'type' => 'boolean',
            'label' =>'Active',
        ],
        'last_login_ip' => [
            'type' => 'ip',
            'label' =>'Last Login IP',
        ],
        'last_login_at' => [
            'type' => 'datetime',
            'label' =>'Last Login',
        ],
        'created_at' => [
            'type' => 'datetime',
            'label' =>'Created',
        ],
        'updated_at' => [
            'type' => 'datetime',
            'label' =>'Updated',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fName', 'lName', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'name', 'role_id', 'owner_ids', 'peer_ids', 'parent_ids'
    ];

    protected $notLoggable = [
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'owner_ids' => 'array',
        'peer_ids' => 'array',
        'parent_ids' => 'array',
    ];

    protected $searchable_columns = [
        'fName','lName','email', 'name',
    ];

    public function groups()
    {
        return $this->belongsToMany('App\Group', 'group_user');
    }

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function ownActivities()
    {
        return $this->hasMany('App\Activity', 'user_id');
    }

    public function isSudo()
    {
        foreach ($this->groups as $group) {
            if (true == $group->sudo) {
                return true;
            }
        }
        return false;
    }

    public function getPermissionForVerb($model, $verb = 'list', $allowSudo = true)
    {
        if (is_object($model)) {
            $model = PermissionsHelper::getClassName($model);
        }
        $verb = strtolower($verb);
        $field = sprintf('can_%s_%s', $verb, strtolower($model));
        $fields = PermissionsHelper::getPermissionFieldsForModel($model);
        if (!in_array($field, $fields)) {
            return 'none';
        }
        if (true == $allowSudo && true == $this->isSudo()) {
            return 'all';
        }
        $highest = 'none';
        foreach ($this->groups as $group) {
            $val = $group->{$field};
            if ('none' == $highest && 'none' !== $val) {
                $highest = $val;
            }
            if (in_array($highest, ['none', 'own']) && 'all' == $val) {
                $highest = $val;
            }
        }
        if (strlen($highest) == 0) {
            $highest = 'none';
        }
        return $highest;
    }

    public function getOwnerIds($recache = false)
    {
        $ids = $this->owner_ids;
        if (!is_array($ids) || true == $recache) {
            $ids = [$this->id];
            $roles = [];
            self::getChildrenRoles($this->role, $roles);
            $children_ids = User::whereIn('role_id', $roles)->pluck('id');
            foreach ($children_ids as $id) {
                array_push($ids, $id);
            }
            $ids = array_unique($ids);
            $ids = array_filter($ids, function ($var) {
                return !is_null($var);
            });
            sort($ids, SORT_NUMERIC);
            $this->owner_ids = $ids;
            $this->save();
        }
        return $ids;
    }

    public function getPeerIds($recache = false)
    {
        $ids = $this->peer_ids;
        if (!is_array($ids) || true == $recache) {
            $ids = [];
            $peer_ids = User::where(['role_id' => $this->role_id])->pluck('id');
            foreach ($peer_ids as $id) {
                if ($id !== $this->id) {
                    array_push($ids, $id);
                }
            }
            $ids = array_unique($ids);
            $ids = array_filter($ids, function ($var) {
                return !is_null($var);
            });
            sort($ids, SORT_NUMERIC);
            $this->peer_ids = $ids;
            $this->save();
        }
        return $ids;
    }

    public function getParentIds($recache = false)
    {
        $ids = $this->parent_ids;
        if (!is_array($ids) || true == $recache) {
            $ids = [];
            $roles = [];
            self::getParentRoles($this->role, $roles);
            $parent_ids = User::whereIn('role_id', $roles)->pluck('id');
            foreach ($parent_ids as $id) {
                if ($id !== $this->id) {
                    array_push($ids, $id);
                }
            }
            $ids = array_unique($ids);
            $ids = array_filter($ids, function ($var) {
                return !is_null($var);
            });
            sort($ids, SORT_NUMERIC);
            $this->parent_ids = $ids;
            $this->save();
        }
        return $ids;
    }

    public function canUseIp($ip, $recache = false)
    {
        $ipList = session('ipList', null);
        if (!is_array($ipList) || $recache == true) {
            $group_ip_whitelists = $this->groups()->pluck('ip_whitelist')->toArray();
            $ip_whitelist = [];
            foreach ($group_ip_whitelists as $whitelist) {
                $list = explode("\n", $whitelist);
                $list = array_map('trim', $list);
                $list = array_filter($list, 'strlen');
                $ip_whitelist = array_merge($ip_whitelist, $list);
            }
            $ipList = array_unique($ip_whitelist);
            session(['ipList' => $ipList]);
        }
        $canUse = false;
        foreach ($ipList as $smt) {
            if (true == $canUse) {
                continue;
            }
            if (strtolower($smt) == 'any' || strtolower($smt) == 'all') {
                $canUse = true;
                continue;
            } elseif (Validators::is_cidr($smt)) {
                $canUse = Validators::in_cidr($ip, $smt);
                if (true == $canUse) {
                    continue;
                }
            } elseif (Validators::is_ip($smt)) {
                $canUse = Validators::ips_match($ip, $smt);
                if (true == $canUse) {
                    continue;
                }
            }
        }
        return $canUse;
    }

    public function getAvatarUrl($size = 200, $default = 'mp', $rating = 'r', $forceDefault = false)
    {
        return route('get-model-avatar', [
            'model' => self::class,
            'id' => $this->id,
            's' => $size,
            'd' => $default,
            'r' => $rating,
        ]);
    }

    public function getBackgroundMap()
    {
        return 'https://via.placeholder.com/1024x500';
    }

    public function formatDateTime($datetime, $type = 'datetime')
    {
        if (!is_a($datetime, 'Illuminate\Support\Carbon')) {
            if (is_null($datetime)) {
                return null;
            }
            $datetime = new Carbon($datetime);
        }
        $formatfield = sprintf('%sformat', $type);
        $format = $this->{$formatfield};
        $timezone = $this->timezone;
        if (is_null($format) || 0 == strlen($format)) {
            $configkey = sprintf('app.%sformat', $type);
            $format = config($configkey);
        }
        if (is_null($timezone) || 0 == strlen($timezone)) {
            $timezone = config('app.timezone');
        }
        $datetime->setTimezone($timezone);
        $formatted = $datetime->format($format);
        return $formatted;
    }

    public function getTimeZone()
    {
        $timezone = $this->timezone;
        if (is_null($timezone) || 0 == strlen($timezone)) {
            $timezone = config('app.timezone');
        }
        return $timezone;
    }

    public function getMomentDateTimeFormat($type = 'datetime')
    {
        $formatfield = sprintf('%sformat', $type);
        $format = $this->{$formatfield};
        if (is_null($format) || 0 == strlen($format)) {
            $configkey = sprintf('app.%sformat', $type);
            $format = config($configkey);
        }
        return self::convertPHPToMomentFormat($format);
    }

    public function getDateTimeAsUserTimezone($datetime)
    {
        if (0 == strlen($datetime)) {
            return null;
        }
        $timezone = $this->timezone;
        if (is_null($timezone) || 0 == strlen($timezone)) {
            $timezone = config('app.timezone');
        }
        $datetime = new Carbon($datetime, $timezone);
        return $datetime;
    }

    private static function getChildrenRoles($role, &$results = array())
    {
        if (!is_null($role)) {
            $children = \App\Role::where(['role_id' => $role->id])->get();
            foreach ($children as $child_role) {
                array_push($results, $child_role->id);
                self::getChildrenRoles($child_role, $results);
            }
        }
    }

    private static function getParentRoles($role, &$results = array())
    {
        if (!is_null($role) && !is_null($role->role_id)) {
            $parent = \App\Role::find($role->role_id);
            array_push($results, $parent->id);
            self::getParentRoles($parent, $results);
        }
    }

    protected static function convertPHPToMomentFormat($format)
    {
        $replacements = [
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'o',
            'w' => 'e',
            'z' => 'DDD',
            'W' => 'W',
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '', // no equivalent
            'L' => '', // no equivalent
            'o' => 'YYYY',
            'Y' => 'YYYY',
            'y' => 'YY',
            'a' => 'a',
            'A' => 'A',
            'B' => '', // no equivalent
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => 'SSS',
            'e' => 'zz', // deprecated since version 1.6.0 of moment.js
            'I' => '', // no equivalent
            'O' => '', // no equivalent
            'P' => '', // no equivalent
            'T' => '', // no equivalent
            'Z' => '', // no equivalent
            'c' => '', // no equivalent
            'r' => '', // no equivalent
            'U' => 'X',
        ];
        $momentFormat = strtr($format, $replacements);
        return trim($momentFormat);
    }
}
