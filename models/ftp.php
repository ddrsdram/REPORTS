<?php
namespace models;

class ftp
{
    private $host,$port,$login,$pass;
    private $fileSource,$fileDestination;
    private $dirSource,$dirDestination;
    private $idConnect;
    private $fileRegistry;

    function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 21;
        $this->login = '';
        $this->pass = '';
        $this->dirSource = $_SERVER['DOCUMENT_ROOT'].'ImpExp';
        $this->dirDestination = 'download';
    }

    /**
     * @return mixed
     */
    public function getFileRegistry()
    {
        return $this->fileRegistry;
    }

    /**
     * @param $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }


    /**
     * @param $pass
     * @return $this
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * @param $fileDestination
     * @return $this
     */
    public function setFileDestination($fileDestination)
    {
        $this->fileDestination = $fileDestination;
        return $this;
    }

    /**
     * @param $fileSource
     * @return $this
     */
    public function setFileSource($fileSource)
    {
        $this->fileSource = $fileSource;
        return $this;
    }

    /**
     * @param $dirDestination
     * @return $this
     */
    public function setDirDestination($dirDestination)
    {
        $this->dirDestination = $dirDestination;
        return $this;
    }

    /**
     * @param $dirSource
     * @return $this
     */
    public function setDirSource($dirSource)
    {
        $this->dirSource = $dirSource;
        return $this;
    }


    public function connection()
    {

        $this->idConnect = ftp_connect($this->host,$this->port);
        if (ftp_login($this->idConnect, $this->login, $this->pass)) {
            ftp_pasv($this->idConnect, true);
        }

        return $this;
    }


    public function copy()
    {

        $this->delete();
        if (ftp_put($this->idConnect, "/" . $this->dirDestination."/".$this->fileDestination, $this->dirSource."/".$this->fileSource, FTP_BINARY)){
        }
        usleep(50);

    }

    public function download()
    {
        $this->fileRegistry = '';
        $this->ftp_download_dir($this->dirSource, $this->dirDestination);
    }

    private function ftp_download_dir($src, $drc)
    {
        if (ftp_chdir($this->idConnect, $src) === FALSE) {
            return;
        }
        if (!(is_dir($drc))) {
            mkdir($drc);
        }
        chdir($drc);

        $contents = ftp_nlist($this->idConnect, '.');
        foreach ($contents as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (@ftp_chdir($this->idConnect, $file)) {
                ftp_chdir($this->idConnect, "..");
                $this->ftp_download_dir($file, $drc . '/' . $file);
            } else {
                $this->fileRegistry .= " $file, </br>".chr(10).chr(13);
                ftp_get($this->idConnect, $file, $file, FTP_BINARY);
            }
        }

        ftp_chdir($this->idConnect, '..');
        chdir('..');
    }

    public function delete()
    {
        ftp_delete($this->idConnect, "/" . $this->dirDestination."/".$this->fileDestination);
        usleep(50);

    }


}