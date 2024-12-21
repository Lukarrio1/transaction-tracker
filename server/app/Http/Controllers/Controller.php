<?php

namespace App\Http\Controllers;

use App\Models\User;
use ReflectionClass;
use App\SendEmailTrait;
use App\Models\Node\Node;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use SendEmailTrait;

    protected $cache_ttl = 0;
    protected $search_skip_word;
    protected $data_interoperability;
    protected $admin_role;

    public function __construct()
    {
        $this->cache_ttl = \getSetting('cache_ttl');
        $this->search_skip_word = \getSetting('search_skip_word');
        $this->admin_role = Role::find((int)\getSetting('admin_role'));
    }

    public function auth_user()
    {
        return \auth()->user();
    }

    /**
     * exception_property_value_keys
     *
     * @var array
     */
    public $exception_property_value_keys = [
        'route_function',
        'node_audit_message',
        'node_endpoint_to_consume',
        'node_item_display_aid',
        'node_order_by_field',
        'node_order_by_type',
        'node_table_columns',
        'node_database',
        'html_value'
    ];


    /**
     * Method removeKeys
     *
     * @param $properties object [This refers to the properties of a route node]
     *
     * @return array
     */
    public function removeKeys($properties)
    {
        $keys = collect($properties)->keys();
        $object = collect([]);
        $keys->each(fn ($key) => !\in_array($key, $this->exception_property_value_keys) ? $object->put($key, $properties->$key) : null);
        return $object->toArray();
    }
    /**
     * Method clearCache
     *
     * @return void
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('optimize');
        Cache::set("is_cache_valid", Str::random(40));

    }

    /**
     * Method getCurrentRoute
     *
     * @return object
     */
    public function getCurrentRoute()
    {
        $currentRoute = join('::', explode('@', Route::currentRouteAction()));
        return Cache::get('routes')
            ->where('properties.value.route_function', $currentRoute)
            ->first();
    }

    public function getCurrentMethodCacheTtl()
    {
        return  \optional(\optional($this->getCurrentRoute())->properties['value'])->node_cache_ttl ?? $this->cache_ttl;
    }

    /**
     * Method getValidationRules
     *
     * @return array
     */
    public function getValidationRules()
    {
        $rules = ['required', 'integer', 'min:3', 'min:5', 'min:10', 'sometimes', 'present', 'max:3', 'max:5', 'max:10'];
        return $rules;
    }

    /**
     * Method backupDatabase
     *
     * @param $databaseName string [The database name]
     * @param $databaseUser string [The database user]
     * @param $databasePassword string [The database password]
     * @param $databaseHost string [The database host]
     * @param $databasePort string [The database port]
     *
     * @return void
     */
    public function backupDatabase($databaseName, $databaseUser, $databasePassword, $databaseHost, $databasePort = 3306)
    {
        $backupFileName = $databaseName . '-backup-' . date('Y-m-d-h-m-s') . '.sql';
        $backupFilePath = storage_path('app/backups/' . $databaseName . "/" . $backupFileName);

        // Ensure the backups directory exists
        if (!file_exists(dirname($backupFilePath))) {
            mkdir(dirname($backupFilePath), 0755, true);
        }

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%d %s > %s',
            escapeshellarg($databaseUser),
            escapeshellarg($databasePassword),
            escapeshellarg($databaseHost),
            (int)$databasePort,
            escapeshellarg($databaseName),
            escapeshellarg($backupFilePath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            return $backupFileName;
        } else {
            throw new \Exception('Error creating database backup.');
        }
    }

    /**
     * Method getHttpData
     *
     * @param $url string [This is url for the data end point]
     *
     * @return mixed
     */
    public function getHttpData($url)
    {
        return !empty($url) ? Http::get($url)->json() : [];
    }

    /**
     * Method handleJoins
     *
     * @param $currentRouteNode object
     *
     * @return array
     */
    public function handleJoins($currentRouteNode)
    {
        if (!isset($currentRouteNode->properties['value']->node_join_tables)) {
            return [];
        }
        $joinTables = collect(json_decode($currentRouteNode->properties['value']->node_join_tables));
        $properties = $currentRouteNode->properties['value'];

        $joinTableQueries = $joinTables->map(function ($item, $idx) use ($properties, $joinTables) {
            return [
                'first_table' => $idx == 0 ? $properties->{'node_table'} : $joinTables[$idx - 1],
                'first_value' => $idx == 0
                    ? $properties->node_join_column
                    : $properties->{'node_previous_' . $item . '_join_column'},
                'condition' => $properties->{'node_' . $item . '_join_by_condition'},
                'second_value' =>  $properties->{'node_' . $item . '_join_by_column'},
                'second_table' => $item,
                'one_or_many' => $properties->{"node_" . $item . "_object_or_array_or_count"} ?? 2,
                'columns' => collect($properties->{'node_' . $item . '_join_columns'})->map(function ($c) use ($item) {
                    return  $c;
                })->toArray(),
            ];
        });

        return $joinTableQueries;
    }


    /**
     * Method addNestedRelationship
     *
     * @param $items collection
     * @param $currentRouteNode object
     * @param $database string
     *
     * @return collection
     */
    public function addNestedRelationship($items, $currentRouteNode, $database)
    {
        $relationShips = $this->handleJoins($currentRouteNode);
        $database = DB::connection($database);
        if (count($relationShips) > 0) {
            $items = $items->map(function ($item) use ($relationShips, $database) {
                $this->processRelationships($item, $relationShips, $database);
                return $item;
            });
        }
        return $items;
    }

    /**
     * Method processRelationships
     *
     * @param &$item object [This is the related item]
     * @param $relationShips Object [This is the relationships]
     * @param $database mixed [This is the database connection]
     * @param $level integer [this is the current level at which the related item should be attached]
     *
     * @return void
     *
     *  This only scales vertically meaning, it only nest relations on an object or elements of an array (object).
     *  The next step is to make it scale horizontally in terms of adding relations to an item given the level of the related
     *  item(s) that should be directly related to it.
     */
    private function processRelationships(&$item, $relationShips, $database, $level = 0)
    {
        if ($level < $relationShips->count()) {
            $rel = $relationShips[$level];
            $allRelatedItems = collect();
            $database->table($rel['second_table'])
                ->select($rel['columns'])
                ->where($rel['second_value'], $rel['condition'], $item->{$rel['first_value']})
                ->orderBy($rel['second_value'])
                ->when($rel['one_or_many'] == 2, fn ($q) => $q->limit(1))
                ->chunk(500, function ($relatedItems) use (&$allRelatedItems, $relationShips, $database, $level, $rel) {
                    $allRelatedItems = $allRelatedItems->merge($relatedItems);
                    if ($rel['one_or_many'] == 3) {
                        return false;
                    }
                    $relatedItems->each(function ($relatedItem) use ($relationShips, $database, $level) {
                        $this->processRelationships($relatedItem, $relationShips, $database, $level + 1);
                    });
                });
            $item->{$rel['second_table']} = $rel['one_or_many'] == 2 ?
                $allRelatedItems->toArray() : ($rel['one_or_many'] == 3 ?
                    $allRelatedItems->count() : $allRelatedItems->first());
        }
    }

    protected function updateUser($user, $properties = [])
    {
        User::find($user->id)->update($properties);
        return User::find($user->id);

    }

    protected function processVerificationEmail($email, $email_token)
    {
        $client_app_url = \getSetting('client_app_url');
        $verification_front_end_link = \explode('/', \optional(optional(Node::where('uuid', 'yuUkEHFptRPqzkBdOosQPeU5yeKbycDcE2qPvmr8LhIb6OmlYE')->first()->properties)['value'])->node_route);
        $verification_front_end_link = $client_app_url . collect($verification_front_end_link)
            ->filter(fn ($_, $idx) => 1 + $idx != \count($verification_front_end_link))
            ->join('/') . '/' . $email_token;

        \defer(fn () => $this->sendEmail(
            $email,
            "Email Verification",
            "Click <a href='$verification_front_end_link'>here</a> to verify your email address."
        ));
        return true;
    }


    protected function processPasswordEmail($email, $token)
    {
        $password_reset_email_frontend_link = \explode('/', \optional(optional(Node::where('uuid', 'q9LGEnzL3R7m7NXdffIlVUoIy7rUU07PS0Z7C8AjqXKn5cp6Gb')->first()->properties)['value'])->node_route);
        $password_reset_email_frontend_link = getSetting('client_app_url') . collect($password_reset_email_frontend_link)
            ->filter(fn ($_, $idx) => 1 + $idx != \count($password_reset_email_frontend_link))
            ->join('/') . '/' . $token;
        \defer(fn () => $this->sendEmail($email, 'Password Email', "Click <a href='$password_reset_email_frontend_link'> here to reset your password.</a>"));
        return true;

    }
}
