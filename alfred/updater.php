<?php

namespace Alfred;

use Exception;

class Updater
{
    /**
     * Check interval
     * The interval in seconds between update checks, default 15 days
     *
     * @var int
     */
    private $check_interval = 86400 * 15;

    /**
     * Remote plist URL
     * the url to the remote plist, used to check for new versions
     *
     * @var string
     */
    private $remote_plist_url;

    /**
     * Remote workflow URL
     * URL to the .workflow file
     *
     * @var [type]
     */
    private $remote_workflow;

    /**
     * Force download
     * Force the download of the latest version
     *
     * @var bool
     */
    private $force_download;

    /**
     * Notifications
     *
     * @var array
     */
    private $alfred_notifications;


    /**
     * Construct
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->remote_plist_url = $this->getVar($config, 'plist_url', false);
        $this->remote_workflow = $this->getVar($config, 'workflow_url', false);
        $this->check_interval = $this->getVar($config, 'check_interval', $this->check_interval);
        $this->force_download = $this->getVar($config, 'force_download', false);
        $this->alfred_notifications = $this->getVar($config, 'alfred_notifications', false);
    }


    /**
     * Maybe check for updates
     */
    public function checkForUpdates($force_check = null, $custom_last_check = null)
    {
        if (!$this->remote_plist_url || !$this->remote_workflow) {
            throw new Exception("Configure the plist and workflow URL");
        }

        $updates_file = $this->updatesFile();
        $response = [
            'update_available' => false,
            'new_version' => '',
            'current_version' => getenv('alfred_workflow_version'),
            'performed_check' => false,
        ];

        if (!file_exists($updates_file) || $force_check || $this->force_download) {
            $remote_plist_content = $this->downloadRemotePlist();
            $should_update = $this->shouldUpdate($remote_plist_content);
            $response['performed_check'] = time();

            if ($should_update) {
                $response['update_available'] = true;
                $response['new_version'] = $should_update;
            }

            return $response;
        }

        $updated = $custom_last_check ? $custom_last_check : filemtime($updates_file);
        $time = time() - $updated;
        if ($time < 0 || $time > $this->check_interval) { // interval passed, check again
            return $this->checkForUpdates(true);
        }

        return $response;
    }


    /**
     * Download remote plist
     * to compare it's version with
     * the local
     */
    private function downloadRemotePlist()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->remote_plist_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $remote_plist_content = curl_exec($curl);

        curl_close($curl);

        if (!$remote_plist_content) {
            return false;
        }

        file_put_contents($this->remotePlistPath(), $remote_plist_content);
        file_put_contents($this->updatesFile(), time());

        return $remote_plist_content;
    }


    /**
     * shouldUpdate
     * compare remote version with local version
     */
    private function shouldUpdate($remote_plist_content = '')
    {
        if (empty($remote_plist_content)) {
            if (!file_exists($this->remotePlistPath())) {
                return false;
            }
            $remote_plist_content = file_get_contents($this->remotePlistPath());
        }

        $matches = [];
        preg_match_all('/<key>version<\/key>\s+<string>(.*)<\/string>/mx', $remote_plist_content, $matches);
        if (empty($matches) || count($matches) < 2 || !isset($matches[1][0])) { // remote version not found
            return false;
        }

        $remote_version = $matches[1][0];
        $local_version = getenv('alfred_workflow_version');
        if ($this->force_download || version_compare($remote_version, $local_version) > 0) {
            return $remote_version;
        }

        touch($this->updatesFile()); // Reset timer by touching local file
        return false;

        /*$remote_plist = $this->escapeEspaces($this->remotePlistPath());
        $command = "/usr/libexec/PlistBuddy -c 'print version' {$remote_plist}";
        $remote_version = shell_exec($command);*/
    }


    /**
     * Download updated workflow
     */
    public function downloadUpdate()
    {
        $title = getenv('alfred_workflow_name');
        $home = getenv('HOME');
        $name = $title;
        $url = $this->remote_workflow;
        $tmp_download = $this->filePath("{$name}.download");
        $tmp_workflow = "{$home}/Downloads/{$name}.alfredworkflow";

        if (file_exists($tmp_download)) { // pending download abort
            //return;
        }

        file_put_contents($tmp_download, time());

        $command = "curl --silent --location --output \"{$tmp_workflow}\" \"{$url}\"";
        $command .= ' && rm ' . $this->escapeEspaces($tmp_download);
        $command .= ' && rm ' . $this->escapeEspaces($this->remotePlistPath());
        $command .= ' && open ' . $this->escapeEspaces($tmp_workflow);

        exec("/usr/bin/nohup {$command} >/dev/null 2>&1");
        return true;
    }


    /**
     * Delete remote plist
     */
    public function deleteRemotePlist()
    {
        if (file_exists($this->remotePlistPath())) {
            unlink($this->remotePlistPath());
        }
    }


    /**
     * Display notification
     */
    public function notify($message = '', $title = '', $trigger = '')
    {
        $title = (!empty($title) ? $title : getenv('alfred_workflow_name'));
        $native_notifications = $this->alfred_notifications;

        if ($trigger) {
            $native_notifications = $trigger;
        }

        if ($native_notifications) {
            $trigger = is_string($native_notifications) ? $native_notifications : 'notifier';
            $title = htmlspecialchars($title, ENT_QUOTES);
            $message = htmlspecialchars($message, ENT_QUOTES);
            $bundleid = getenv('alfred_workflow_bundleid');
            $output = $title . '|' . $message;
            $script = 'tell application id "com.runningwithcrayons.Alfred" to run trigger "' . $trigger . '" in workflow "' . $bundleid . '" with argument "' . $output . '"';
            $command = "osascript -e '{$script}'";
        }

        if (!$native_notifications) {
            $title = htmlspecialchars($title, ENT_QUOTES);
            $message = htmlspecialchars($message, ENT_QUOTES);
            $command = "osascript -e 'display notification \"{$message}\" with title \"{$title}\"'";
        }

        shell_exec($command);
    }

    /**
     * Escape string spaces
     */
    private function escapeEspaces($str = '')
    {
        return str_replace(' ', '\ ', $str);
    }


    /**
     * Get workflow file path
     * the base path is the variable alfred_workflow_data
     * this is the path that Alfred uses to store
     * data about the workflow
     *
     * @param string $path
     * @return string
     */
    private function filePath($file)
    {
        $data_path = getenv('alfred_workflow_data');

        // Make sure workflow data folder exists
        if (!file_exists($data_path)) {
            mkdir($data_path, 0777, true);
        }

        $file_path = "{$data_path}/{$file}";

        return $file_path;
    }


    /**
     * Remote plist path
     * return the local path where
     * the remote plist is downloaded and
     * stored while performing checks
     *
     * @return string
     */
    private function remotePlistPath()
    {
        return $this->filePath('remote-plist.plist');
    }

    /**
     * Remote plist path
     * return the local path where
     * the remote plist is downloaded and
     * stored while performing checks
     *
     * @return string
     */
    private function updatesFile()
    {
        return $this->filePath('last-update-check.txt');
    }

    /**
     * Get var from array
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getVar($array, $key, $default = null)
    {
        if (is_array($array) && isset($array[$key]) && !empty($array[$key])) {
            return $array[$key];
        }
        if (!is_null($default)) {
            return $default;
        }

        return '';
    }
}
