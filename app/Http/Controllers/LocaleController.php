<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LocaleRequest;
use App\Repositories\LocaleRepository;
use App\Repositories\ActivityLogRepository;

class LocaleController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'locale';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, LocaleRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
        $this->middleware('feature.available:multilingual');
        $this->middleware('prohibited.test.mode')->only(['translate','addWord']);
    }

    /**
     * Used to get all Locales
     * @get ("/api/locale")
     * @return Response
     */
    public function index()
    {
        $locales = $this->repo->paginate($this->request->all());

        $modules = $this->repo->getModules();

        return $this->success(compact('locales', 'modules'));
    }

    /**
     * Used to store Locale
     * @post ("/api/locale")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Locale"),
     *      @Parameter("locale", type="string", required="true", description="Locale (Short Name) of Locale"),
     * })
     * @return Response
     */
    public function store(LocaleRequest $request)
    {
        $locale = $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $locale->locale,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('locale.added')]);
    }

    /**
     * Used to get Locale detail
     * @get ("/api/locale/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Locale to be shown"),
     * })
     * @return Response download
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Locale
     * @patch ("/api/locale")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Locale to be updated"),
     *      @Parameter("name", type="string", required="true", description="Name of Locale"),
     *      @Parameter("locale", type="string", required="true", description="Locale (Short Name) of Locale"),
     * })
     * @return Response
     */
    public function update(LocaleRequest $request, $id)
    {
        $locale = $this->repo->findOrFail($id);

        $locale = $this->repo->update($locale, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $locale->locale,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('locale.updated')]);
    }

    /**
     * Used to delete Locale
     * @delete ("/api/locale")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Locale to be deleted"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $locale = $this->repo->deletable($id);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $locale->locale,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($locale);

        return $this->success(['message' => trans('locale.deleted')]);
    }

    /**
     * Used to fetch Locale modules & words
     * @post ("/api/locale/fetch")
     * @param ({
     *      @Parameter("module", type="string", required="true", description="Name of Module"),
     * })
     * @return Response
     */
    public function fetch()
    {
        $locale = $this->repo->findByLocaleOrFail(request('locale'));
        
        $modules = $this->repo->getModules();

        $words = $this->repo->getWords(request('locale'), request('module'));

        return $this->success(compact('modules', 'words','locale'));
    }

    /**
     * Used to translate words
     * @post ("/api/locale/translate")
     * @param ({
     *      @Parameter("locale", type="string", required="true", description="Locale"),
     *      @Parameter("words", type="array", required="true", description="Words to be translated"),
     * })
     * @return Response
     */
    public function translate()
    {
        $this->repo->writeToFile(request('locale'), request('module'), request('words'));

        return $this->success(['message' => trans('locale.translation_updated')]);
    }

    /**
     * Used to add new word for translation
     * @post ("/api/locale/add-word")
     * @param ({
     *      @Parameter("word", type="string", required="true", description="Word to be added"),
     *      @Parameter("translation", type="string", required="true", description="Translation of the word to be added"),
     *      @Parameter("module", type="string", required="true", description="Name of Module"),
     * })
     * @return Response
     */
    public function addWord(LocaleRequest $request)
    {
        $module = request('module');

        $words = $this->repo->getWords('en', $module);

        $word = preg_replace('/[^A-Za-z0-9-]+/', '_', strtolower(request('word')));

        if (array_key_exists($word, $words)) {
            return $this->error(['word' => trans('locale.word_already_added')]);
        }

        foreach ($this->repo->getAll() as $locale) {
            $words = $this->repo->getWords($locale->locale, $module);
            
            $words[$word] = request('translation');

            $this->repo->writeToFile($locale->locale, $module, $words);
        }

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => 'word',
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('locale.word_added')]);
    }
}
