<?php

namespace App\Models\Node;

use App\TenantTrait;
use ReflectionClass;
use ReflectionMethod;
use App\Models\Setting;
use App\Models\BaseModel;
use App\HasCustomPagination;
use App\Models\Tenant\Tenant;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Node extends BaseModel
{
    use HasFactory;
    use HasCustomPagination;
    use TenantTrait;

    public function __construct()
    {
        // $this->initializeTenancy();
    }
    protected $guarded = ['id'];

    public const Authentication_Levels = [
        1 => 'AUTHENTICATED',
        0 => 'UNAUTHENTICATED',
        2 => 'PUBLIC',
    ];

    public $object_array_count = [
        1 => 'Object',
        2 => 'Array',
        3 => 'Count'
    ];
    public const NODE_STATUS = [1 => 'ENABLED', 0 => 'DISABLED'];

    public const NODE_TYPE = [1 => 'ROUTE', 2 => 'LINK', 3 => 'PAGE', 4 => 'COMPONENT', 5 => 'LAYOUT'];

    public function getAllControllerClasses()
    {
        // Path to the Controllers directory
        $controllersPath = app_path('Http/Controllers');

        // Get all PHP files in the Controllers directory
        $controllerFiles = File::allFiles($controllersPath);

        // Base namespace for controllers
        $baseNamespace = 'App\\Http\\Controllers\\';

        // Use a set ( associative array ) for unique controller classes
        $controllerClasses = [];

        // Loop through the files and extract class names
        foreach ($controllerFiles as $file) {
            // Remove base directory to get relative path
            $relativePath = ltrim(str_replace($controllersPath, '', $file->getPathname()), '/');

            // Convert to namespace structure by replacing '/' with '\\'
            $className = $baseNamespace . str_replace(['/', '.php'], ['\\', ''], $relativePath);

            // Avoid duplicates by using associative array keys
            if (!isset($controllerClasses[$className])) {
                $controllerClasses[$className] = true;
            }
        }

        // Return the keys of the associative array, which are the unique class names
        return array_keys($controllerClasses);
    }

    public function getControllerMethods()
    {
        $excluded_methods = ["__call"];
        $controllerClasses = $this->getAllControllerClasses();
        $controllerMethods = [];

        foreach ($controllerClasses as $controllerClass) {
            if (class_exists($controllerClass)) {
                $reflectionClass = new ReflectionClass($controllerClass);

                // Get all public methods ( excluding inherited ones like __construct )
                $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

                foreach ($methods as $method) {
                    // Skip inherited methods ( like those from parent classes or traits )
                    if ($method->getDeclaringClass()->getName() === $controllerClass && !in_array($method->getName(), $excluded_methods)) {
                        $controllerMethods[$controllerClass][] = $method->getName();
                    }
                }
            }
        }

        return $controllerMethods;
    }

    public function getAllModels()
    {
        $modelsPath = app_path('Models');
        $modelFiles = File::allFiles($modelsPath);
        $modelClasses = array_map(
            function ($file) {
                $relativePath = str_replace(app_path(), '', $file->getRealPath());
                $className = 'App' . str_replace(['/', '.php'], ['\\', ''], $relativePath);
                return $className;
            },
            $modelFiles
        );
        return $modelClasses;
    }

    public function getAuthenticationLevelAttribute($value)
    {
        return ['value' => $value, 'human_value' => self::Authentication_Levels[$value]];
    }

    public function getNodeTypeAttribute($value)
    {
        return ['value' => $value, 'human_value' => self::NODE_TYPE[$value]];
    }

    public function getPropertiesAttribute($value)
    {
        return [
            'value' => $this->addAppUrlToNodeRoute(\json_decode($value)),
            'html_value' => "<ul class='list-group list-group-flush'>" . \collect($this->addAppUrlToNodeRoute(json_decode($value)))->map(
                function ($value, $key) {
                    $value = gettype($value) == 'array' ? \json_encode($value) : (\count(\explode('_object_or_array_or_count', $key)) > 1 ? $this->object_array_count[$value] : $value);
                    return "<li class='list-group-item'>" . collect(\explode('_', $key))
                        ->map(fn ($word) => \ucfirst($word))->join(' ') . "<strong>:</strong> $value </li>";
                }
            )->join('') . '</ul>'
        ];
    }

    public function addAppUrlToNodeRoute($value)
    {
        $value = \collect($value);
        if (\in_array($this->node_type['value'], [1])) {
            $app_url = \collect(Cache::get('settings'))->where('key', 'app_url')->pluck('properties')->first();
            $seg = $this->node_type['value'] == 2 ? '/' : '/api/';
            $value = $value->put('node_route', $app_url . $seg . $value->get('node_route'));
        }

        return \json_decode(\json_encode($value));
    }

    public function getNodeStatusAttribute($value)
    {
        return ['value' => $value, 'human_value' => self::NODE_STATUS[$value]];
    }


    public function getVerbiageAttribute($value)
    {
        $verbiage = collect([]);
        collect(\explode('||', $value))->map(function ($item) use ($verbiage) {
            $segments = collect(\explode(':', $item));
            $verbiage->put($segments->first(), \str_replace('"', '', $segments->filter(fn ($_, $idx) => $idx > 0)->join(':')));
        });
        return ['value' => $value, 'human_value' => $verbiage];
    }

    public function updatePageLink()
    {
        if (\optional($this->node_type)['value'] == 2 && !empty(\optional(optional($this->properties)['value'])->node_page)) {
            $page = Node::find((int) $this->properties['value']->node_page);
            $page->update([
                'properties' => \json_encode([
                    'page_link' => $this->name,
                    'actual_component' => $page->properties['value']->actual_component,
                    'layout_id' => !empty(\optional(optional($page->properties)['value'])->layout_id) ? $page->properties['value']->layout_id : '',
                    'layout_name' => !empty(\optional(optional($page->properties)['value'])->layout_name) ? \optional(optional($page->properties)['value'])->layout_name : ''
                ]),
            ]);
        }
        return $this;
    }

    public function updatePageLayoutName()
    {
        if (\optional($this->node_type)['value'] == 3) {
            $layout = !empty(\optional(optional($this->properties)['value'])->layout_id) ?
                Node::find((int) $this->properties['value']->layout_id) : null;
            $this->update([
                'properties' => \json_encode([
                    'page_link'
                    => \optional($this->properties['value'])->page_link,
                    'actual_component' => \optional($this->properties['value'])->actual_component,
                    'layout_id' => !empty(\optional(optional($this->properties)['value'])->layout_id) ? $this->properties['value']->layout_id : '',
                    'layout_name' => !empty(\optional(optional($this->properties)['value'])->layout_id) ? $layout->name : ''
                ]),
            ]);
        }
        return $this;
    }
    public function permission()
    {
        return $this->hasOne(Permission::class, 'id', 'permission_id');
    }

    public function scopeEnabled($query)
    {
        return $query->where('node_status', 1);
    }


    public function scopeNodeType($query, $type)
    {
        return $query->where('node_type', $type);
    }

    public function scopeRoutes($query)
    {
        return $query->nodeType(1);
    }

    public function scopeLinks($query)
    {
        return $query->nodeType(2);
    }

    public function scopePages($query)
    {
        return $query->nodeType(3);
    }

    public function scopeComponents($query)
    {
        return $query->nodeType(4);
    }

    public function scopeLayouts($query)
    {
        return $query->nodeType(4);
    }
}
