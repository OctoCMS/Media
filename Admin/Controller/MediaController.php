<?php
namespace Octo\Media\Admin\Controller;

use b8\Form;
use b8\Image;
use b8\Http\Upload;
use Octo\AssetManager;
use Octo\Store;
use Octo\Admin\Controller;
use Octo\Admin\Form as FormElement;
use Octo\Admin\Menu;
use Octo\Event;
use Octo\Utilities\StringUtilities;
use Octo\File\Model\File;

class MediaController extends Controller
{
    /**
     * Return the menu nodes required for this controller
     *
     * @param Menu $menu
     * @return void
     * @author James Inman
     */
    public static function registerMenus(Menu $menu)
    {
        $media = $menu->getRoot('Media');
        $media->addChild(new Menu\Item('Upload', '/media/add'));

        $media->addChild(Menu\Item::create('Load Metadata', '/media/metadata', true));
        $media->addChild(Menu\Item::create('Search Images', '/media/autocomplete/images', true));

        $media->addChild(Menu\Item::create('Manage Images', '/media/manage/images', false));
        $media->addChild(Menu\Item::create('Edit Images', '/media/edit/images', true));
        $media->addChild(Menu\Item::create('Delete Images', '/media/delete/images', true));

        $media->addChild(Menu\Item::create('Search Files', '/media/autocomplete/files', true));
        $media->addChild(Menu\Item::create('Manage Files', '/media/manage/files', false));
        $media->addChild(Menu\Item::create('Edit Files', '/media/edit/files', true));
        $media->addChild(Menu\Item::create('Delete Files', '/media/delete/files', true));
    }

    /**
     * @var \Octo\File\Store\FileStore
     */
    protected $fileStore;

    /**
     * Setup initial menu
     *
     * @return void
     * @author James Inman
     */
    public function init()
    {
        $this->setTitle('Media');
        $this->addBreadcrumb('Media', '/media/add');

        $this->fileStore = Store::get('File');
    }

    /**
     * Upload files
     */
    public function add($scope = '')
    {
        $this->setTitle('Upload Media and Files');
        $this->addBreadcrumb('Upload');

        AssetManager::getInstance()->addThirdParty('js', 'Media', 'plupload/plupload.full.min.js');

        Event::trigger($scope . 'Upload', $this);

        if ($this->request->getMethod() == 'POST') {
            $upload = new Upload('file');
            $info = $upload->getFileInfo();

            $file = new File;
            $file->setId(md5(strtolower($info['hash'] . $scope)));
            $file->setFilename(strtolower($info['basename']));
            $file->setTitle(strtolower($info['basename']));
            $file->setExtension(strtolower($info['extension']));
            $file->setMimeType($info['type']);
            $file->setSize($info['size']);
            $file->setCreatedDate(new \DateTime);
            $file->setUpdatedDate(new \DateTime);
            $file->setUserId($this->currentUser->getId());
            $file->setMeta([]);

            switch ($info['type']) {
                case 'image/jpeg':
                    $file->setScope('images');
                    break;
                case 'image/png':
                    $file->setScope('images');
                    break;
                case 'image/gif':
                    $file->setScope('images');
                    break;
                case 'image/pjpeg':
                    $file->setScope('images');
                    break;
                default:
                    $file->setScope('files');
                    break;
            }

            try {
                Event::trigger($scope . 'BeforeUploadProcessed', $file);

                if ($foundFile = $this->fileStore->getById($file->getId())) {
                    $data = array_merge($foundFile->toArray(), array('url' => $foundFile->getUrl()));
                    return $this->json($data);
                }

                $file = $this->fileStore->insert($file);

                $fileInfo = [
                    'id' => $file->getId(),
                    'data' => $upload->getFileData(),
                    'extension' => $file->getExtension(),
                    'file' => $file,
                ];

                Event::trigger('BeforePutFile', $fileInfo);

                if (!Event::trigger('PutFile', $fileInfo)) {
                    $this->fileStore->delete($file);
                    return $this->json(['error' => true, 'message' => 'Upload failed']);
                }

                Event::trigger($scope . 'FileSaved', $file);

                $data = $file->toArray();
                return $this->json($data);
            } catch (\Exception $ex) {
                return $this->json(['error' => true, 'message' => $ex->getMessage()]);
            }
        }
        $this->view->scope = $scope;
    }

    public function edit($scope, $fileId)
    {
        $file = $this->fileStore->getById($fileId);
        $this->view->scope = $scope;
        $this->view->scope_name = StringUtilities::singularize(ucwords($scope));

        $this->setTitle($file->getTitle(), 'Manage ' . ucwords($scope));
        $this->addBreadcrumb(ucwords($scope), '/media/manage/' . $scope . '/');
        $this->addBreadcrumb($file->getTitle(), '/media/edit/' . $scope . '/' . $fileId);
        $this->view->file = $file;

        if ($this->request->getMethod() == 'POST') {

            if ($this->request->isAjax()) {
                $file->setMetaKey('focal_point', [$this->getParam('x', 0), $this->getParam('y', 0)]);
                $this->fileStore->save($file);
                return $this->raw('OK');
            }

            $params = $this->getParams();
            $meta = [];

            if (isset($params['meta'])) {
                $meta = $params['meta'];
                unset($params['meta']);
            }

            $values = array_merge($params, array('id' => $fileId));
            $form = $this->fileForm($values, $scope, 'edit');

            if ($form->validate()) {
                try {
                    foreach ($meta as $key => $value) {
                        $file->setMetaKey($key, $value);
                    }

                    $file->setValues($this->getParams());
                    $file = $this->fileStore->save($file);
                    Event::trigger($scope . 'MediaEditPostSave', $this);

                    return $this->redirect('/media/manage/' . $scope)
                                ->success($file->getTitle() . ' was edited successfully.');
                } catch (\Exception $e) {
                    $this->errorMessage('There was an error editing the file. Please try again.');
                }
            } else {
                $this->errorMessage('There was an error editing the file. Please try again.');
            }
        }

        $this->view->form = $this->fileForm($file->toArray(), $scope)->render();

        $imageFiles = ['jpg', 'jpeg', 'gif', 'png'];

        if (in_array($file->getExtension(), $imageFiles)) {
            $this->view->image = $file->getUrl();

            $focal = $file->getMetaKey('focal_point');

            if (is_null($focal) || !is_array($focal)) {
                $focal = [0, 0];
            }

            $this->view->focal = json_encode($focal);
        }

        Event::trigger($scope . 'MediaEditForm', $this);
    }

    protected function fileForm($values, $scope)
    {
        $form = new FormElement();
        $form->setMethod('POST');

        $form->setAction($this->config->get('site.full_admin_url') . '/media/edit/' . $scope . '/' . $values['id']);

        $form->setClass('smart-form');

        $fieldset = new Form\FieldSet('fieldset');
        $form->addField($fieldset);

        $field = new Form\Element\Text('title');
        $field->setRequired(true);
        $field->setLabel('Title');
        $fieldset->addField($field);

        $field = new Form\Element\Text('filename');
        $field->setRequired(true);
        $field->setLabel('File Name');
        $fieldset->addField($field);

        if ($scope != 'images') {
            $field = new Form\Element\Select('meta[thumbnail]');
            $field->setRequired(false);
            $field->setLabel('Thumbnail');
            $field->setClass('octo-image-picker');
            $fieldset->addField($field);
        }

        $data = [&$form, &$values];
        Event::trigger($scope . 'FileFormFields', $data);
        list($form, $values) = $data;

        $field = new Form\Element\Submit();
        $field->setValue('Save File');
        $field->setClass('btn-success');
        $fieldset->addField($field);

        $form->setValues($values);
        return $form;
    }

    /**
     * @param $scope Scope of files to view
     */
    public function manage($scope = '')
    {
        $scope_name = ucwords($scope);

        $this->setTitle('Manage ' . $scope_name, 'Media');
        $this->addBreadcrumb($scope_name, '/media/manage/' . $scope);

        $this->view->scope = $scope;
        $this->view->scope_name = $scope_name;
        $this->view->files = $this->fileStore->getAllForScope($scope);

        if ($scope == 'images') {
            $this->view->gallery = true;
        }

        Event::trigger($scope . 'MediaList', $this);
    }

    /**
     * @param $scope Scope of files to view
     */
    public function autocomplete($scope)
    {
        $query = $this->getParam('q', '')['term'];
        $files = $this->fileStore->search($scope, $query);

        $rtn = ['results' => [], 'more' => false];

        foreach ($files as $file) {
            $rtn['results'][] = [
                'id' => $file->getId(),
                'text' => $file->getTitle(),
                'filename' => $file->getFilename(),
            ];
        }

        return $this->json($rtn);
    }

    /**
     * @param $scope Scope of file to delete
     * @param $fileId ID of file to delete
     */
    public function delete($scope, $fileId)
    {
        $file = $this->fileStore->getById($fileId);
        @unlink($file->getPath());
        $this->fileStore->delete($file);

        Event::trigger($scope . 'DeleteFile', $this);
        return $this->redirect('/media/manage/'.$scope)->success($file->getTitle() . ' was deleted successfully.');
    }

    /**
     * @param $fileId
     * @param int $width
     * @param int $height
     */
    public function render($fileId, $width = 160, $height = 160)
    {
        $file = $this->fileStore->getById($fileId);

        Image::$sourcePath = APP_PATH . '/public/uploads/';
        $image = new Image($file->getId() . '.' . $file->getExtension());
        $output = $image->render($width, $height);

        $this->response->setHeader('Content-Type', 'image/jpeg');
        $this->response->setContent($output);
        $this->response->disableLayout();
        $this->response->flush();
        print $this->response->getContent();
        exit;
    }

    /**
     * Return an AJAX list of all images
     *
     * @param $scope
     * @return string JSON
     */
    public function ajax($scope)
    {
        $files = $this->fileStore->getAllForScope($scope);
        File::$sleepable = array('id', 'url', 'title');
        foreach ($files as &$item) {
            $imageData = getimagesize($item->getPath());
            $item = $item->toArray(1);
            $item['width'] = $imageData[0];
            $item['height'] = $imageData[1];
        }
        print json_encode($files);
        exit;
    }

    // Get meta information about a set of files described by Id.
    public function metadata()
    {
        $imageIds = json_decode($this->getParam('q', '[]'));
        $rtn = ['results' => [], 'more' => false];

        foreach ($imageIds as $imageId) {
            $image = $this->fileStore->getById($imageId);

            if ($image) {
                $rtn['results'][] = ['id' => $image->getId(), 'text' => $image->getTitle()];
            }
        }

        return $this->json($rtn);
    }
}
