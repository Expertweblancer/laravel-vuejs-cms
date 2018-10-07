<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Upload;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class UploadController extends Controller
{
    protected $upload;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    protected $module = 'upload';

    public function getAllowedExtension()
    {
        $upload_variables = getVar('upload');

        return isset($upload_variables[request('module')]['allowed_file_extensions']) ? $upload_variables[request('module')]['allowed_file_extensions'] : ['jpg','png','jpeg','pdf','doc','docx','xls','xlsx','txt'];
    }

    /**
     * Used to upload Files
     * @post ("/api/upload")
     * @param ({
     *      @Parameter("module", type="string", required="true", description="Name of module"),
     *      @Parameter("token", type="string", required="true", description="Upload Token from Form"),
     *      @Parameter("file", type="file", required="true", description="File to be uploaded"),
     * })
     * @return Response
     */
    public function upload()
    {
        $module = request('module');
        $token = request('token');
        $upload_variables = getVar('upload');
        $module_upload_variables = isset($upload_variables[$module]) ? $upload_variables[$module] : [];

        $auth_required = isset($module_upload_variables['auth_required']) ? $module_upload_variables['auth_required'] : 1;
        $max_file_size = isset($module_upload_variables['max_file_size']) ? $module_upload_variables['max_file_size'] : 10000;
        $allowed_file_extensions = isset($module_upload_variables['allowed_file_extensions']) ? $module_upload_variables['allowed_file_extensions'] : ['jpg','png','jpeg','pdf','doc','docx','xls','xlsx'];
        $max_no_of_files = isset($module_upload_variables['max_no_of_files']) ? $module_upload_variables['max_no_of_files'] : 5;

        if (!$module || !$token) {
            return $this->error(['message' => trans('general.invalid_action')]);
        }

        if ($this->upload->whereUploadToken(request('token'))->where('module', '!=', $module)->count()) {
            return $this->error(['message' => trans('general.invalid_action')]);
        }

        $user = (\Auth::check()) ? \Auth::user() : null;

        if ($auth_required && !isset($user)) {
            return $this->error(['message' => trans('upload.authentication_require_before_upload')]);
        }

        $size = request()->file('file')->getSize();

        if ($size > $max_file_size*1024*1024) {
            return $this->error(['message' => trans('upload.file_size_exceeds')]);
        }

        $extension = request()->file('file')->extension();

        if (!in_array($extension, $allowed_file_extensions)) {
            return $this->error(['message' => trans('upload.invalid_extension', ['extension' => $extension])]);
        }

        $existing_upload = $this->upload->filterByModule($module)->filterByUploadToken($token)->filterByIsTempDelete(0)->count();

        if ($existing_upload >= $max_no_of_files) {
            return $this->error(['message' => trans('upload.max_file_limit_crossed', ['number' => $max_no_of_files])]);
        }

        $upload = $this->upload;
        $upload->module = $module;
        $upload->module_id = request('module_id') ? request('module_id') : null;
        $upload->upload_token = $token;
        $upload->user_filename = request()->file('file')->getClientOriginalName();
        $upload->filename = request()->file('file')->store('uploads/'.$module);
        $upload->uuid = Str::uuid();
        $upload->user_id = isset($user) ? $user->id : null;
        $upload->save();

        return $this->success(['message' => trans('upload.file_uploaded'),'upload' => $upload]);
    }

    /**
     * Used to fetch Uploaded Files
     * @post ("/api/upload")
     * @param ({
     *      @Parameter("module", type="string", required="true", description="Name of module"),
     *      @Parameter("module_id", type="integer", required="true", description="Id of Module"),
     * })
     * @return Response
     */
    public function fetch()
    {
        $this->upload->filterByModule(request('module'))->filterByModuleId(request('module_id'))->update(['is_temp_delete' => 0]);
        return $this->ok($this->upload->filterByModule(request('module'))->filterByModuleId(request('module_id'))->filterByStatus(1)->get());
    }

    /**
     * Used to upload Image in Summernote
     * @post ("/api/upload/image")
     * @param ({
     *      @Parameter("file", type="file", required="true", description="Image file to be uploaded"),
     * })
     * @return Response
     */
    public function uploadImage()
    {
        $upload_path = 'uploads/images';

        request()->validate([
            'file' => [
                'required',
                'image',
                'mimes:jpeg,bmp,png,svg,gif'
            ],
        ], [], [
            'file' => trans('general.file')
        ]);

        $extension = request()->file('file')->getClientOriginalExtension();
        $filename = uniqid();
        $file = request()->file('file')->move($upload_path, $filename.".".$extension);
        $image_url = '/'.$upload_path.'/'.$filename.'.'.$extension;

        return $this->success(compact('image_url'));
    }

    /**
     * Used to delete Upload File
     * @post ("/api/upload/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Uploaded File"),
     *      @Parameter("token", type="string", required="true", description="Upload Token from Form"),
     *      @Parameter("module_id", type="integer", required="optional", description="Id of Module"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $upload = $this->upload->find($id);

        if (!$upload || $upload->upload_token != request('token')) {
            return $this->error(['message' => 'Invalid action!']);
        }

        if (request('module_id') && $upload->status) {
            $this->upload->filterById($id)->update(['is_temp_delete' => 1]);
        } else {
            \Storage::delete($upload->filename);
            $this->upload->filterById($id)->delete();
        }

        return $this->success(['message' => trans('upload.file_deleted')]);
    }
}
