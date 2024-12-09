<?php
namespace Moowgly\Lib\Controller;

use Moowgly\Lib\Constant\LogConstant;
use Moowgly\Lib\Storage\Storage;
use Moowgly\Lib\Utils\Chrono;
use Moowgly\Lib\Utils\DomUtil;
use Moowgly\Lib\Utils\HttpPutParser;
use Moowgly\Lib\Utils\Logger;
use Moowgly\Lib\Utils\SolrUtil;
use Moowgly\Lib\Utils\StringUtil;
use Moowgly\Lib\Utils\XslUtil;


abstract class AbstractControllerSilo extends AbstractController
{

    protected $category = null;

    protected $modelClassname = null;

    /**
     * Constructor
     * @param string $category : Resource category
     * @param string $modelClassname : Resource model classname
     */
    public function __construct($category, $modelClassname)
    {
        parent::__construct();
        $this->category = $category;
        $this->modelClassname = $modelClassname;
    }

    public function get($req, $res)
    {

        $dbSelected = Storage::getInstance()->get($req->data, $this->category, false);
        if ($dbSelected === false) {
            $res->send(500);
            $errors[] = 'Error dbSelected with ' . $this->category;
        }

        $res->add($dbSelected);
        $res->send(200);
    }

    public function search($req, $res)
    {

        $dbSelected = Storage::getInstance()->get($req->data, $this->category, true);
        if ($dbSelected === false) {
            $res->send(500);
            $errors[] = 'Error dbSelected with ' . $this->category;
        }

        $res->add($dbSelected);
        $res->send(200);
    }

    public function post($req, $res)
    {
        $dbInserted = Storage::getInstance()->post($req->data, $this->category);
        if ($dbInserted === false) {
            Logger::getInstance()->debug('AbstractController | POST | ' . $this->category . " | xform | dbInserted with id: " . $id, $this->getLogContext());
            $res->send(500);
            $errors[] = 'Error dbInserted with id: ' . $id;
            return;
        }
        $res->send(200);
    }

    public function put($req, $res)
    {
        // Update resource in database
        $dbUpdated = Storage::getInstance()->put($req->data, $this->category);

        if ($dbUpdated === false) {
            Logger::getInstance()->debug('AbstractController | PUT | ' . $this->category . " | xform | dbUpdated with id: " . $id, $this->getLogContext());
            $res->send(500);
            $errors[] = 'Error dbUpdated with id: ' . $id;
            return;
        }
        $res->send(200);
    }

    public function delete($req, $res)
    {
        // Delete resource in database
        $entryDeleted = Storage::getInstance()->delete($req->data, $this->category);
        if ($entryDeleted === false) {
            Logger::getInstance()->error('AbstractController | DELETE | ' . $this->category . " | id: " . $id . " not updated in database", $this->getLogContext());
            $res->send(500);
            return;
        }
        $res->send(200);
    }

    private function getLogContext()
    {
        return array(
            Logger::MODULE_TYPE => LogConstant::MODULE_TYPE_SILO,
            Logger::MODULE_NAME => (new \ReflectionClass($this))->getShortName()
        );
    }

    protected function getRawBody()
    {
        return file_get_contents('php://input');
    }
}
