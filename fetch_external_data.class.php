<?php

class FetchExternalDataJob extends CronJob
{
    /**
     * Returns the name of the cronjob.
     */
    public static function getName()
    {
        return _('Externe JSON-Daten f�r die digitale Lagekarte (Plugin).');
    }

    /**
     * Returns the description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Ruft alle externen Ressourcen-URL auf und prozessiert die zur�ckbekommenen JSON-Objekte in den jeweiligen Veranstaltungen.');
    }

    /**
     * Setup method. Loads neccessary classes and checks environment. Will
     * bail out with an exception if environment does not match requirements.
     */
    public function setUp()
    {
    }

    /**
     * Return the paremeters for this cronjob.
     *
     * @return Array Parameters.
     */
    public static function getParameters()
    {
        return array();
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
