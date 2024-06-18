<?php


namespace models;


class Image
{
    private $name;

    private $root_dir;

    private $dir;

    private $remainingHeight;

    private $numSheet;

    private $listSheets = Array();
    /**
     * @var \Imagick
     */
    private $a4Object;

    private $saveAsName;


    public function __construct()
    {
        $this->root_dir = $_SERVER['DOCUMENT_ROOT'];
        $this->dir = 'ImpExp';
    }

    /**
     * @param mixed $dir
     */
    public function setDir($dir): void
    {
        $this->dir = $dir;
    }

    /**
     * @param mixed $root_dir
     */
    public function setRootDir($root_dir): void
    {
        $this->root_dir = $root_dir;
    }


    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }


    public function Save($id)
    {
        $conn = new \DB\Connect();
        $body = $conn->table("scan_image")
            ->where("id",$id)
            ->select("body")
            ->fetchField("body");

        $image = new \Imagick();
        $image->readImageBlob($body);
        $d = $image->getImageGeometry();

        if ($d["width"] > $d["height"])
            $image->rotateImage(new \ImagickPixel('none'), -90);

        $nameFile = $this->root_dir . "/" . $this->dir ."/". $this->name  . '.jpg';
        $image->setImageFormat('jpg');
        $image->writeImage($nameFile);
/*
        $hf = fopen($nameFile,"w+");
        fwrite($hf,$body);
        fclose($hf);
*/
    }



    public function convertToPdf($saveAsName,$listFiles)
    {
        $this->saveAsName = $saveAsName;

        $PDF = $this->root_dir .  $this->dir ."/". $saveAsName .".pdf";

        $JPG = '';
        foreach ($listFiles as $key => $name) {
            $JPG .= ' ' . $this->root_dir .  $this->dir ."/". $name . '.jpg';
        }
        $cmd = "convert  -page A4  -density 300  $JPG  pdf $PDF ";
        shell_exec($cmd);
        foreach ($listFiles as $key => $name) {
            unlink ($this->root_dir .  $this->dir ."/". $name . '.jpg');
        }

    }

}
