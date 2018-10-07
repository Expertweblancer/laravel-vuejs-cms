<?php
namespace App\Repositories;

use App\Upload;
use Illuminate\Validation\ValidationException;

class UploadRepository
{
    protected $upload;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Get Attachment(s) for given module.
     *
     * @param string $module
     * @param integer $module_id
     * @param string $attachment_uuid
     * @return Upload
     */

    public function getAttachment($module, $module_id, $attachment_uuid = null)
    {
        $attachments = $this->upload->filterByModule($module)->filterByModuleId($module_id)->filterByStatus(1);

        if ($attachment_uuid) {
            $attachment = $attachments->filterByUuid($attachment_uuid)->first();

            if (! $attachment) {
                throw ValidationException::withMessages(['message' => trans('general.invalid_link')]);
            }

            return $attachment;
        }

        return $attachments->get();
    }

    /**
     * Store upload to given module.
     *
     * @param string $module
     * @param integer $module_id
     * @param string $upload_token
     * @return null
     */

    public function store($module, $module_id, $upload_token)
    {
        $this->upload->filterByModule($module)->filterByUploadToken($upload_token)->update(['status' => 1,'module_id' => $module_id]);
    }

    /**
     * Update upload to given module.
     *
     * @param string $module
     * @param integer $module_id
     * @param string $upload_token
     * @return null
     */

    public function update($module, $module_id, $upload_token)
    {
        $old_uploads = $this->upload->filterByModule($module)->filterByModuleId($module_id)->filterByIsTempDelete(1)->get();

        foreach ($old_uploads as $old_upload) {
            \Storage::delete($old_upload->filename);
        }

        $this->upload->filterByModule($module)->filterByModuleId($module_id)->filterByIsTempDelete(1)->delete();

        $this->upload->filterByModule($module)->filterByUploadToken($upload_token)->update(['status' => 1,'module_id' => $module_id]);
    }

    /**
     * Delete upload of given module.
     *
     * @param string $module
     * @param integer $module_id
     * @return null
     */

    public function delete($module, $module_id)
    {
        $uploads = $this->upload->filterByModule($module)->filterByModuleId($module_id)->get();

        foreach ($uploads as $upload) {
            \Storage::delete($upload->filename);
        }

        $this->upload->filterByModule($module)->filterByModuleId($module_id)->delete();
    }

    /**
     * Bulk delete upload of given module.
     *
     * @param string $module
     * @param array $ids
     * @return null
     */

    public function bulkDelete($module, $ids = array())
    {
        $uploads = $this->upload->filterByModule($module)->whereIn('module_id', $ids)->get();
        foreach ($uploads as $upload) {
            \Storage::delete($upload->filename);
        }

        $this->upload->filterByModule($module)->whereIn('module_id', $ids)->delete();
    }
}
