<?php

class FetchExternalDataJob extends CronJob
{
    /**
     * Returns the name of the cronjob.
     */
    public static function getName()
    {
        return _('Externe JSON-Daten für die digitale Lagekarte (Plugin).');
    }

    /**
     * Returns the description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Ruft alle externen Ressourcen-URL auf und prozessiert die zurückbekommenen JSON-Objekte in den jeweiligen Veranstaltungen.');
    }

    /**
     * Setup method. Loads neccessary classes and checks environment. Will
     * bail out with an exception if environment does not match requirements.
     */
    public function setUp()
    {
        require_once 'lib/language.inc.php';
        require_once 'lib/functions.php';
        require_once 'lib/deputies_functions.inc.php';
        require_once 'lib/classes/StudipMail.class.php';
        require_once 'lib/classes/ModulesNotification.class.php';

        if (!Config::get()->MAIL_NOTIFICATION_ENABLE) {
            throw new Exception('Mail notifications are disabled in this Stud.IP installation.');
        }
        if (empty($GLOBALS['ABSOLUTE_URI_STUDIP'])) {
            throw new Exception('To use mail notifications you MUST set correct values for $ABSOLUTE_URI_STUDIP in config_local.inc.php!');
        }
    }

    /**
     * Return the paremeters for this cronjob.
     *
     * @return Array Parameters.
     */
    public static function getParameters()
    {
        return array(
            'verbose' => array(
                'type'        => 'boolean',
                'default'     => false,
                'status'      => 'optional',
                'description' => _('Sollen Ausgaben erzeugt werden (sind später im Log des Cronjobs sichtbar)'),
            ),
        );
    }

    /**
     * Executes the cronjob.
     *
     * @param mixed $last_result What the last execution of this cronjob
     *                           returned.
     * @param Array $parameters Parameters for this cronjob instance which
     *                          were defined during scheduling.
     *                          Only valid parameter at the moment is
     *                          "verbose" which toggles verbose output while
     *                          purging the cache.
     */
    public function execute($last_result, $parameters = array())
    {
        $urls = ExternalDataURL::findBySQL("active = 1");
        foreach ($urls as $url) {
            $url->fetch();
        }
    }
}
