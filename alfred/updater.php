<?php

namespace Alfred;

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
     * Force check
     * Bypass the cache and check again the remote plist
     *
     * @var bool
     */
    private $force_check;

    /**
     * Download type
     * async/sync
     *
     * @var string
     */
    private $download_type;


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
        $this->force_check = $this->getVar($config, 'force_check', false);
        $this->force_download = $this->getVar($config, 'force_download', false);
        $this->download_type = $this->getVar($config, 'download_type', 'async');
    }


    /**
     * Maybe check for updates
     */
    public function checkUpdates()
    {
        if (!$this->remote_plist_url || !$this->remote_workflow) {
            throw new Exception("Configure the plist and workflow URL");
        }

        $updates_file = $this->updatesFile();

        if (!file_exists($updates_file) || $this->force_check || $this->force_download) {
            return $this->getRemotePlist();
        }

        $updated = filemtime($updates_file);
        $time = time() - $updated;

        if ($time > $this->check_interval) { // interval passed, check again
            $this->deleteRemotePlist();
            $this->getRemotePlist();
            return;
        }

        return false;
    }


    /**
     * Download remote plist
     */
    private function getRemotePlist()
    {
        $tmp_download = $this->filePath('remote_plist.download');

        if (file_exists($tmp_download)) { // pending download abort
            return;
        }

        file_put_contents($tmp_download, time());

        if (file_put_contents($this->remotePlistPath(), file_get_contents($this->remote_plist_url))) {
            unlink($tmp_download);
            file_put_contents($this->updatesFile(), time());
            return $this->shouldUpdate();
        }
    }


    /**
     * shouldUpdate
     * compare remote version with local version
     */
    private function shouldUpdate()
    {
        $remote_plist = $this->escapeEspaces($this->remotePlistPath());
        $command = "/usr/libexec/PlistBuddy -c 'print version' {$remote_plist}";
        $remote_version = shell_exec($command);
        $local_version = getenv('alfred_workflow_version');

        if ($this->force_download || version_compare($remote_version, $local_version) > 0) {
            $this->notifyUpdate();
            $this->downloadWorkflow();

            return true;
        } else {
            $this->deleteRemotePlist();
            touch($this->updatesFile()); // Reset timer by touching local file
            return false;
        }
    }


    /**
     * Download updated workflow
     */
    private function downloadWorkflow()
    {
        $title = getenv('alfred_workflow_name');
        $home = getenv('HOME');
        $name = $title;
        $url = $this->remote_workflow;
        $tmp_download = $this->filePath("{$name}.download");
        $tmp_workflow = "{$home}/Downloads/{$name}.alfredworkflow";

        if (file_exists($tmp_download)) { // pending download abort
            return;
        }

        file_put_contents($tmp_download, time());

        $command = "curl --silent --location --output \"{$tmp_workflow}\" \"{$url}\"";
        $command .= ' && rm ' . $this->escapeEspaces($tmp_download);
        $command .= ' && rm ' . $this->escapeEspaces($this->remotePlistPath());
        $command .= ' && open ' . $this->escapeEspaces($tmp_workflow);
        $command .= " && osascript -e 'display notification \"Download Completed\" with title \"{$title}\"'";

        if ($this->download_type ==  'async') {
            shell_exec("/usr/bin/nohup {$command} >/dev/null 2>&1 &");
        }
        if ($this->download_type ==  'sync') {
            shell_exec("/usr/bin/nohup {$command}");
        }
    }


    /**
     * Delete remote plist
     */
    private function deleteRemotePlist()
    {
        unlink($this->remotePlistPath());
    }


    /**
     * Show a notification about the update
     */
    private function notifyUpdate()
    {
        $this->notify('New version available. Downloading and installing…');
    }


    /**
     * Display notification
     */
    private function notify($message = '', $title = '')
    {
        $title = (!empty($title) ? $title : getenv('alfred_workflow_name'));
        $command = "osascript -e 'display notification \"{$message}\" with title \"{$title}\"'";
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
            return trim($array[$key]);
        }
        if (!is_null($default)) {
            return $default;
        }

        return '';
    }
}
