<?php

namespace AEvent;

class Event
{      
    public $caption = "";
    /**
     * breadCrumbs
     *
     * @var array
     */
    public $breadCrumbs = [];
    /**
     * contexts to show (object, array)
     *
     * @var array
     */
    public $contexts = [];
    
    /**
     * Additional to show (any)
     *
     * @var any
     */
    public $variables = [];
    
    /**
     * Event Type
     *
     * @var string
     */
    public $type = Logger::TYPE_DEFAULT;

    public $level = Logger::LEVEL_INFO;

    public $webinarID = null;

    public $fingerprint = null;

    public $scheduleID = null;

    public $integrationID = null;

    public $tags = [];

    public $user = [
        "username" => "AEvent",
        "email" => "AEvent"
    ];

    public function __construct()
    {
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    public function addContext($name, $variable)
    {
        $this->contexts[$name] = $variable;
        return $this;
    }

    public function setFingerprint($uuid) {
        $this->fingerprint = $uuid;
        return $this;
    }

    public function addData($name, $variable)
    {
        $this->variables[$name] = $variable;
        return $this;
    }

    public function addTag($name, $tag)
    {
        $this->tags[$name] = $tag;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    public function setWebinarID($webinarID) {
        $this->webinarID = $webinarID;
        return $this;
    }

    public function setUser($dbname)
    {
        $this->user = [
            "username" => $dbname,
            "email" => $dbname
        ];
        return $this;
    }

    public function setScheduleID($scheduleID) {
        $this->scheduleID = $scheduleID;
    }

    public function setIntegrationID($integrationID) {
        $this->integrationID = $integrationID;
    }

    public function initBreadCrumb($level, $type, $category, $description) {
        $breadCrumb = [];
        $breadCrumb["level"] = $level;
        $breadCrumb["type"] = $type;
        $breadCrumb["category"] = $category;
        $breadCrumb["description"] = $description;
        return  $breadCrumb;
    }

    public function updateBreadCrumbs()
    {
        $this->breadCrumbs = [];
        $this->breadCrumbs[] = $this->initBreadCrumb(Logger::LEVEL_INFO, \Sentry\Breadcrumb::TYPE_USER, "From", $this->user["username"]);
        switch ($this->type) {
            case Logger::TYPE_DEFAULT:
                $this->breadCrumbs[] = $this->initBreadCrumb(Logger::LEVEL_INFO, \Sentry\Breadcrumb::TYPE_NAVIGATION, "Type", "Info");
            break;
            case Logger::TYPE_UPLOAD:
                $this->breadCrumbs[] = $this->initBreadCrumb(Logger::LEVEL_INFO, \Sentry\Breadcrumb::TYPE_NAVIGATION, "Type", "Upload");
            break;
            case Logger::TYPE_WEBINAR:
                $this->breadCrumbs[] = $this->initBreadCrumb(Logger::LEVEL_INFO, \Sentry\Breadcrumb::TYPE_NAVIGATION, "Type", "Webinar");
            break;
            case Logger::TYPE_ALERT:
                $this->breadCrumbs[] = $this->initBreadCrumb(Logger::LEVEL_INFO, \Sentry\Breadcrumb::TYPE_NAVIGATION, "Type", "Alert");
            break;
            default:
                $this->breadCrumbs[] = $this->initBreadCrumb(Logger::LEVEL_INFO, \Sentry\Breadcrumb::TYPE_NAVIGATION, "Type", $this->type);
        }
        if ($this->webinarID) {
            $this->breadCrumbs[] = $this->initBreadCrumb(Logger::LEVEL_INFO, \Sentry\Breadcrumb::TYPE_DEFAULT, "Webinar", $this->webinarID);
        }
        // $this->breadCrumbs[] = $this->initBreadCrumb($this->level, \Sentry\Breadcrumb::TYPE_DEFAULT, "Message", $this->caption);
        return $this;
    }

    public function save() {
        $logger = new Logger();
        $logger->save($this);
    }
}
