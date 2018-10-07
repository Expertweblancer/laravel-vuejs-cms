<?php
namespace App\Repositories;

use App\Locale;
use Illuminate\Validation\ValidationException;

class LocaleRepository
{
    protected $locale;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get all locales.
     *
     * @return Locale
     */
    public function getAll()
    {
        return $this->locale->all();
    }

    /**
     * List all locales.
     *
     * @return Locale
     */
    public function list()
    {
        return $this->locale->all()->pluck('locale_with_name', 'locale')->all();
    }

    /**
     * Find locale with given id or throw an error.
     *
     * @param integer $id
     * @return Locale
     */
    public function findOrFail($id)
    {
        $locale = $this->locale->find($id);

        if (! $locale) {
            throw ValidationException::withMessages(['message' => trans('locale.could_not_find')]);
        }

        return $locale;
    }

    /**
     * Find by locale.
     *
     * @return Locale
     */
    public function findByLocale($locale)
    {
        return $this->locale->filterByLocale($locale)->first();
    }

    /**
     * Find by locale or fail.
     *
     * @return Locale
     */
    public function findByLocaleOrFail($locale)
    {
        $locale = $this->locale->filterByLocale($locale)->first();

        if (! $locale) {
            throw ValidationException::withMessages(['message' => trans('locale.could_not_find')]);
        }

        return $locale;
    }

    /**
     * Paginate all locales using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'name';
        $order       = isset($params['order']) ? $params['order'] : 'asc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->locale->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new locale.
     *
     * @param array $params
     * @return Locale
     */
    public function create($params)
    {
        $locale = $this->locale->forceCreate($this->formatParams($params));

        if ($locale->locale != 'en') {
            \File::copyDirectory(base_path('/resources/lang/en'), base_path('/resources/lang/'.$params['locale']));
        }

        return $locale;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'name' => isset($params['name']) ? $params['name'] : null
        ];

        if ($action === 'create') {
            $formatted['locale'] = isset($params['locale']) ? $params['locale'] : null;
        }

        return $formatted;
    }

    /**
     * Update given locale.
     *
     * @param Locale $locale
     * @param array $params
     *
     * @return Locale
     */
    public function update(Locale $locale, $params)
    {
        if ($locale->locale != $params['locale']) {
            throw ValidationException::withMessages(['locale' => trans('locale.locale_cannot_be_changed')]);
        }

        $locale->forceFill($this->formatParams($params, 'update'))->save();

        return $locale;
    }

    /**
     * Find locale & check it can be deleted or not.
     *
     * @param integer $id
     * @return Locale
     */
    public function deletable($id)
    {
        $locale = $this->findOrFail($id);

        if ($locale->locale === 'en') {
            throw ValidationException::withMessages(['message' => trans('locale.default_cannot_be_deleted')]);
        }
        
        return $locale;
    }

    /**
     * Delete locale.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Locale $locale)
    {
        \File::deleteDirectory(base_path('/resources/lang/'.$locale->locale));

        return $locale->delete();
    }

    /**
     * Delete multiple locales.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->locale->whereIn('id', $ids)->delete();
    }

    /**
     * Toggle given locale status.
     *
     * @param Locale $locale
     * @param array $params
     *
     * @return Locale
     */
    public function toggle(Locale $locale)
    {
        $locale->forceFill([
            'completed_at' => (! $locale->status) ? Carbon::now() : null,
            'status'       => ! $locale->status
        ])->save();

        return $locale;
    }

    /**
     * Get all locale modules.
     *
     * @return array
     */
    public function getModules()
    {
        $modules = array();
        foreach (\File::allFiles(base_path('/resources/lang/en')) as $file) {
            $modules[] = basename($file, '.php');
        }

        return $modules;
    }

    /**
     * Sort translations of all modules.
     *
     * @return null
     */
    public function sortWords()
    {
        $locale = 'en';
        foreach ($this->getModules() as $module) {
            $words = \File::getRequire($this->validateModule($locale, $module));
            ksort($words);
            $this->writeToFile('en', $module, $words);
        }
    }

    /**
     * Write to translation file.
     *
     * @param string $locale
     * @param string $module
     * @param array $words
     * @return null
     */
    public function writeToFile($locale = 'en', $module = null, $words = array())
    {
        if (! $module) {
            return;
        }

        ksort($words);
        
        $file = base_path('/resources/lang/'.$locale.'/'.$module.'.php');
        \File::put($file, var_export($words, true));
        \File::prepend($file, '<?php return ');
        \File::append($file, ';');
    }

    /**
     * Get all the words for given locale & module
     *
     * @param string $locale
     * @param string $module
     * @return array
     */
    public function getWords($locale = 'en', $module = null)
    {
        if (! $module) {
            return;
        }

        $words = \File::getRequire($this->validateModule($locale, $module));

        ksort($words);

        return $words;
    }

    /**
     * Validate local modules, if doesn't exist create new file for module.
     *
     * @param string $locale
     * @param string $module
     * @return string Locale file
     */
    public function validateModule($locale, $module)
    {
        $file = base_path('/resources/lang/'.$locale.'/'.$module.'.php');

        if (!\File::exists($file)) {
            \File::put($file, var_export([], true));
            \File::prepend($file, '<?php return ');
            \File::append($file, ';');
        }

        return $file;
    }
}
