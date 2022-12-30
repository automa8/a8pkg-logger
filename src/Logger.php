<?php

namespace AEvent;

class Logger
{
    const TYPE_DEFAULT = "default";
    const TYPE_WEBINAR = "webinar";
    const TYPE_UPLOAD = "upload";
    const TYPE_ALERT = "alert";

    const LEVEL_DEBUG = "debug";
    const LEVEL_INFO = "info";
    const LEVEL_WARNING = "warning";
    const LEVEL_ERROR = "error";
    const LEVEL_FATAL = "fatal";

    public function __construct()
    {
    }

    public static function log($fingerprint = null, $caption = "", $user = null, $webinarID = null, $type = null, $level = null, $contexts = null, $variables = null, $scheduleID = null) {
        $event = new Event();
        $event->setCaption($caption);
        if ($user) {
            $event->setUser($user);
        }
        if ($webinarID) {
            $event->setWebinarID($webinarID);
        }
        if ($type) {
            $event->setType($type);
        }
        if ($fingerprint) {
            $event->setFingerprint($fingerprint);
        }
        if ($level) {
            $event->setLevel($level);
        }
        if ($contexts) {
            foreach ($contexts as $name => $context) {
                $event->addContext($name, $context);
            }
        }
        if ($variables) {
            foreach ($variables as $name => $variable) {
                $event->addData($name, $variable);
            }
        }
        if ($scheduleID) {
            $event->setScheduleID($scheduleID);
        }
        $event->updateBreadCrumbs();
        return $event->save();
    }


    public function save(Event $event)
    {
        \Sentry\withScope(function (\Sentry\State\Scope $scope) use($event) : void {
            $fingerPrints = [
                $event->user["username"],
                $event->type
            ];
            foreach ($event->contexts as $name => $variable) {
                $scope->setContext($name, $variable);
            }
            foreach ($event->variables as $name => $variable) {
                $scope->setExtra($name, $variable);
            }
            $scope->setTag('type', $event->type);
            if ($event->fingerprint) {
                $scope->setTag('fingerprint', $event->fingerprint);
                $fingerPrints[] = $event->fingerprint;
            }
            if ($event->webinarID) {
                $scope->setTag('webinarID', $event->webinarID);
                $fingerPrints[] = $event->webinarID;
            }
            if ($event->scheduleID) {
                $scope->setTag('scheduleID', $event->scheduleID);
                $fingerPrints[] = $event->scheduleID;
            }
            $scope->setTag('logger', 'custom');
            $scope->setUser($event->user);
            $scope->setFingerprint($fingerPrints);
            foreach ($event->breadCrumbs as $breadCrumb) {
                $scope->addBreadcrumb(new \Sentry\Breadcrumb(
                    $breadCrumb["level"],
                    $breadCrumb["type"],
                    $breadCrumb["category"],
                    $breadCrumb["description"]
                ));
            }
            $scope->setLevel(new \Sentry\Severity($event->level));
            \Sentry\captureMessage($event->caption);
        });
    }
}