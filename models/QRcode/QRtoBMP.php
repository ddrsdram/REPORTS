<?php
namespace models\QRcode;

class QRtoBMP{
    public $bfType="BM";  // Тип файла
    public $biCompression=0; // Тип сжатия для сжатых изображений
    public $biXPelsPerMeter=0; // Горизонтальное разрешение в пикселях на метр для целевого устройства.
    public $biYPelsPerMeter=0; // Вертикальное разрешение в пикселях на метр для целевого устройства.

    public $biClrUsed=0; // Количество используемых цветовых индексов в палитре.
    public $biClrImportant=0; // Количество элементов палитры, необходимых для отображения изображения.
    public $pnHandle; //дескриптор файла
    public $biPlanes = 1; // Количество цветовых плоскостей и в формате BMP содержит единицу.
    public $biBitCount = 1; // Количество бит на пиксель.
    public $biSize = 40; // Размер данной структуры в байтах
    public $bfOffBits = 62; // Смещение
    public $biWidth=0;// Ширина изображения в пикселях.
    public $biHeight =0;// Длина изображения в пикселях.
    public $nByteLine =0;
    public $biSizeImage =0;// Размер изображения в байтах.
    public $bfSize=0;// Размер файла

    function init($nQR_Size,$nPixelSize){
        $this->biWidth = $nQR_Size * $nPixelSize; // Ширина изображения в пикселях.
        $this->biHeight = $nQR_Size * $nPixelSize;
        $this->nByteLine = ceil($this->biWidth / 8 / 4) * 4;
        $this->biSizeImage = $this->nByteLine * $this->biHeight;
        $this->bfSize = $this->biSizeImage + $this->bfOffBits;
        return;
    }


    // Запись монохромного BMP файла
    function writeQRtoBMP($ArrQR,$nPixelSize=1, $fileNameFull="C:\\Temp\\test1.bmp")
    {
        $fileNameFull = $_SERVER['DOCUMENT_ROOT']. $fileNameFull;

        $nQR_Size=$ArrQR[1];
        $ArrBit=$ArrQR[2];

        $this->init($nQR_Size,$nPixelSize);
        $BMPData = null;
        for ($j = 1; $j <= $nQR_Size; $j++) {
            $BMPData1 = null;
            $nByte = 0;
            $N = 0;
            for ($i = 1; $i <= $nQR_Size; $i++) {
                for ($pix = 1; $pix <= $nPixelSize; $pix++) {
                    if ($ArrBit[$i][$j]) {
                         $nByte ^= (1 << (7-$N));
                    }
                    $N = ($N + 1)% 8;
                    if ($N === 0) {
                        $BMPData1 .= pack("C",$nByte);
                        $nByte=0;
                    }
                }
            }
            if ($N > 0) {
                $BMPData1 .= pack("C",$nByte);
            }
            $BMPData1 =str_pad ($BMPData1, $this->nByteLine, pack("C",0));
            for ($pix = 1; $pix <= $nPixelSize; $pix++) {
                $BMPData = $BMPData1 . $BMPData;
            }
        }

        $this->open($fileNameFull);
        //echo '--|$fileNameFull2||',$fileNameFull2,"|<br>";

        $this->SetProp($this->bfType, 2, "H"); // Тип файла
        $this->SetProp($this->bfSize, 4, "N"); // Размер файла
        $this->SetProp(0, 2, "N"); // Резерв1
        $this->SetProp(0, 2, "N"); // Резерв2
        $this->SetProp($this->bfOffBits, 4, "N"); // Смещение
        $this->SetProp($this->biSize, 4, "N"); // Размер данной структуры в байтах
        $this->SetProp($this->biWidth, 4, "N"); // Ширина изображения в пикселях.
        $this->SetProp($this->biHeight, 4, "N"); // Длина изображения в пикселях.
        $this->SetProp($this->biPlanes, 2, "N"); // Количество цветовых плоскостей и в формате BMP содержит единицу.
        $this->SetProp($this->biBitCount, 2, "N"); // Количество бит на пиксель.
        $this->SetProp($this->biCompression, 4, "N"); // Тип сжатия для сжатых изображений
        $this->SetProp($this->biSizeImage, 4, "N"); // Размер изображения в байтах.
        $this->SetProp($this->biXPelsPerMeter, 4, "N"); // Горизонтальное разрешение в пикселях на метр для целевого устройства.
        $this->SetProp($this->biYPelsPerMeter, 4, "N"); // Вертикальное разрешение в пикселях на метр для целевого устройства.
        $this->SetProp($this->biClrUsed, 4, "N"); // Количество используемых цветовых индексов в палитре.
        $this->SetProp($this->biClrImportant, 4, "N"); // Количество элементов палитры, необходимых для отображения изображения.
        $this->SetProp("00000000ffffff00", 8, "C");

        $this->SetProp($BMPData, strlen($BMPData), "H");
        $this->close();
    }

    function open($fileNameFull2){
        $this->pnHandle =  fopen($fileNameFull2,"w");
        return;
    }
    function close(){
        fclose($this->pnHandle);
    }

    function SetProp($Variable_,$cnBit,$typ_){
        $val_=0;
        Switch ($typ_) {
            Case "N":
                Switch ($cnBit) {
                    Case 4:
                        $hex1= pack("Q*", $Variable_);
                        break;
                    case 2:
                        $hex1= pack("L*", $Variable_);
                        break;
                    default:
                        $hex1=null;
                }
                $val_ = $this->my_write($this->pnHandle, $hex1,$cnBit);
                break;
            Case  "C":
                $val_ = $this->my_write($this->pnHandle, pack("H*",$Variable_), $cnBit);
                break;
            Case  "H":
                $val_ = $this->my_write($this->pnHandle, $Variable_, $cnBit);
                break;
        }
        return $val_;
    }

    private function my_write($pnHandle, $hex1,$cnBit){
        // $pnHandle аналдиз
        return fwrite($pnHandle, $hex1,$cnBit);
    }
}

