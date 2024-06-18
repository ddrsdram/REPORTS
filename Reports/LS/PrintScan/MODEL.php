<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\LS\PrintScan;


class MODEL extends \Reports\reportModel
{
    private $id_PassHead;


    public function saveFile($saveAsName)
    {
        $conn = new \DB\Connect();
        $headData = $this->getHeadArray();
        $this->id_PassHead = $headData['id_ScanDoc'];

        $image = new \models\Image();

        $listFiles = Array();
        $conn = new \DB\Connect();

        $head = $conn->table("scan_head")
            ->where("id",$headData['id_ScanDoc'])
            ->select()
            ->fetch();


        $scan_image = $conn->table("scan_image")
            ->where("id_head",$headData['id_ScanDoc'])
            ->select("id_head,id")
            ->orderBy('id')
            ->fetchAll();

        foreach ($scan_image as $key => $itemScan) {

            $nameImage = $head['ORG']."_".$head['id_LS']."_".$head['id_typeScan']."_".$head['id']."_". $itemScan['id'];
            $listFiles[] = $nameImage;

            $image->setName($nameImage);
            $image->Save($itemScan['id']);
        }

        $image->convertToPdf($saveAsName,$listFiles);

    }
}