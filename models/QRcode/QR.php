<?php
namespace models\QRcode;

//пример исполнения
//QR "Привет 012345ASDF zxcvb asfashggf 123!!#$%$%^%", "UNI", "M", 3

class QR extends QR_constant
{
    Const Shablon = "1101";
    public $QR_ver = 0;
    public $QR_size = 0;
    public $bVySh = false;
    public $bVersQR = 0;
    public $strVersQR = 0;
    public $UrKorr = 0;
    public $nBlocks = 0;
    public $nByte_Korr = 0;
    public $nLenBlockData = 0;
    public $nLenBlD_Min = 0;
    public $nCntOST_BlD = 0;

    public $arrVyravShabl = Array();
    public $arr_LenBlD = Array();
    public $arr_GenMnKorr = Array(Array());
    public $arrValTest = Array(Array());
    public $arr_DataMask = Array(Array());// Маска данных
    public $arr_RangeData = Array(Array());// Область данных
    public $arr_Pixels = Array(Array(Array()));// Пиксели
    public $arr_Data = Array(Array()); // Данные
    public $arrVisible = Array(Array()); //Массив для вывода
    private $pixelInToPoint = 1;
    private $QR_final;


    public $bfType = "BM";  // Тип файла
    public $biCompression = 0; // Тип сжатия для сжатых изображений
    public $biXPelsPerMeter = 0; // Горизонтальное разрешение в пикселях на метр для целевого устройства.
    public $biYPelsPerMeter = 0; // Вертикальное разрешение в пикселях на метр для целевого устройства.

    public $biClrUsed = 0; // Количество используемых цветовых индексов в палитре.
    public $biClrImportant = 0; // Количество элементов палитры, необходимых для отображения изображения.
    public $pnHandle; //дескриптор файла
    public $biPlanes = 1; // Количество цветовых плоскостей и в формате BMP содержит единицу.
    public $biBitCount = 1; // Количество бит на пиксель.
    public $biSize = 40; // Размер данной структуры в байтах
    public $bfOffBits = 62; // Смещение
    public $biWidth = 0;// Ширина изображения в пикселях.
    public $biHeight = 0;// Длина изображения в пикселях.
    public $nByteLine = 0;
    public $biSizeImage = 0;// Размер изображения в байтах.
    public $bfSize = 0;// Размер файла

    private $path = '/download';
    private $streem = 'TXT';
    private $sizeInPixels = 100;

    function __construct()
    {
        $this->GetQRTable();
    }


    /**
     * @param $Stroka
     * @param string $TypeField
     * @param int $Level_
     * @param int $typeOutput
     * @return bool|null
     */
    public function QR($Stroka, $TypeField = QR_TypeField::UNICODE, $Level_ = QR_Level::QRLevel_H, $typeOutput = 1)
    {
        If (gettype($TypeField) == "string") {
            Switch ($TypeField) {
                Case "D":
                    $Type_field = 1;
                    break;
                Case "SD":
                    $Type_field = 2;
                    break;
                Case "STR":
                    $Type_field = 3;
                    break;
                Case "UNI":
                    $Type_field = 3;
                    //$Stroka = iconv("cp1251", "UTF-8", $Stroka);
                    break;
                default:
                    return null;
            }
        } Else {
            $Type_field = 3;
            $Stroka = (string)$Stroka;
        }
        $UrKorr = $Level_;
        $arrDataByte = $this->P_DataToByte($Stroka, $Type_field, $UrKorr); // строка, тип поле, уровень коррекции
        $cntData = count($arrDataByte);
        $nBlocks = self::ArrBlocks[$UrKorr][$this->QR_ver];
        $nByte_Korr = self::ArrByteInBlock[$UrKorr][$this->QR_ver];
        $nLenBlockData = Ceil($cntData / $nBlocks);
        $nLenBlD_Min = intdiv($cntData, $nBlocks);
        $nCntOST_BlD = $cntData % $nBlocks;

        //Определение длины блока
        $arr_LenBlD = array_fill(1, $nBlocks, $nLenBlD_Min); //Массив длины блоков.
        If ($nCntOST_BlD > 0) {
            For ($j = $nBlocks - $nCntOST_BlD + 1; $j <= $nBlocks; $j++) {
                $arr_LenBlD[$j] = $nLenBlockData;
            }
        }

        // Заполнение массива блоков данных
        $ix = 0;
        $arr_DataByte = array_fill(1, $nBlocks, array_fill(1, $nLenBlockData, Null));
        For ($i_block = 1; $i_block <= $nBlocks; $i_block++) {
            For ($j = 1; $j <= $arr_LenBlD[$i_block]; $j++) {
                $arr_DataByte[$i_block][$j] = $arrDataByte[$ix];
                $ix++;
            }
        }

        // Заполнение массива генирирующего многочлена.
        $arr_GenMnKorr = array_fill(1, $nByte_Korr, 0); //Массив генирирующего многочлена.
        For ($i = 1; $i <= $nByte_Korr; $i++) {
            $arr_GenMnKorr[$i] = self::ArrGenMnKorr[$nByte_Korr][$i];
        }

        // Заполнение массива корректировки данных
        $arr_DataKorr = array_fill(1, $nBlocks, array_fill(1, $nByte_Korr, 0));
        For ($i_block = 1; $i_block <= $nBlocks; $i_block++) { // для каждого блока
            $cntData = $arr_LenBlD[$i_block];
            $cnt_Data = (int)Max($cntData, $nByte_Korr);
            $arrData = array_fill(1, $cnt_Data, 0);

            //запись из массива данных во временный массив исправления ошибок
            For ($i = 1; $i <= $cntData; $i++) {
                $arrData[$i] = $arr_DataByte[$i_block][$i];
            }

            //расчет массива исправления ошибок
            For ($n = 1; $n <= $cntData; $n++) {
                $arr_B = array_fill(1, $cnt_Data, 0);
                For ($i = 1; $i <= $nByte_Korr; $i++) {
                    If ($arrData[1] == 0) {
                        break;
                    }
                    $nInvGalua = $arrData[1];
                    $Sdvg = ($arr_GenMnKorr[$i] + $this->Arr_InvGalua[$nInvGalua]) % 255;
                    $arr_B[$i] = self::Arr_Galua[$Sdvg];
                }
                For ($i = 1; $i < $cnt_Data; $i++) {
                    $arrData[$i] = ($arr_B[$i] ^ $arrData[$i + 1]);
                }
                 $arrData[$cnt_Data] = $arr_B[$cnt_Data];
             }

            //Заполнение массива исправления ошибок
            For ($i = 1; $i <= $nByte_Korr; $i++) {
                $arr_DataKorr[$i_block][$i] = $arrData[$i];
            }
         }

        $cDataKorr = $this->print_arr_of_num1($arr_DataKorr, $nBlocks, $nByte_Korr);
        $cDataByte = $this->print_arr_of_num1($arr_DataByte, $nBlocks, $nLenBlockData);
        $cData = $this->P_str($cDataByte, $cDataKorr);
        $this->P_Maket($this->QR_ver);

        //Создание массивов слоев информации QR кода
        $Atrib_Pixel = array("V" => false, "T" => 0);
        $this->arr_RangeData = array_fill(1, $this->QR_size, array_fill(1, $this->QR_size, False)); // Область данных
        $this->arr_Pixels = array_fill(1, $this->QR_size, array_fill(1, $this->QR_size, $Atrib_Pixel)); // Пиксели
        $this->arr_Data = array_fill(1, $this->QR_size, array_fill(1, $this->QR_size, False)); // Данные

        //Формирование изображения QR кода
        $this->P_ShablonPoiska();
        If ($this->bVySh === true) {
            $this->P_ShablonNapravl($this->arrVyravShabl);
        }
        $this->P_ShablonSinchron(9, $this->QR_size - 8, 1, 2);
        $this->P_ShablonSinchron(9, $this->QR_size - 8, 2, 2);


        //***********************
        $aa = str_repeat("0", 15);
        $this->P_INF_Format($aa);
        If ($this->bVersQR === true) {
            $this->P_INFVersion($this->strVersQR);
        }

        $this->P_GetRangeData();
        $this->P_InsertData($cData);
        $cntBalls_0 = 0;
        $optim_mask=0;
        For ($id_mask = 0; $id_mask <= 7; $id_mask++) {
            $this->P_DataMaska($id_mask);
            $this->P_AddMaska();
            If ($id_mask == 0) {
                $cntBalls_0 = $this->P_TestMaski();
            } Else {
                $cntBalls = $this->P_TestMaski();
                If ($cntBalls_0 > $cntBalls) {
                    $cntBalls_0 = $cntBalls;
                    $optim_mask = $id_mask;
                }
            }
        }

        //$optim_mask=8; для отладки
        $this->P_DataMaska($optim_mask);
        $this->P_AddMaska();
        $strFormQR = $this->BitRead(self::ArrKodMask[$UrKorr][$optim_mask + 1], 15); // Код маски и уровня коррекции
        $this->P_INF_Format($strFormQR);

        // определение вывода QR-Кода
        Switch ($typeOutput) {
            Case 1:
                $Str_1 = "ver=" . (string)$this->QR_ver . ";";
                $Str_1 .= "LevelKorr=" . (string)$Level_ . ";";
                $Str_1 .= "Size=" . (string)$this->QR_size . ":" . Chr(13);
                For ($Y = 1; $Y <= $this->QR_size; $Y++) {
                    $Str_1 .= "{";
                    For ($X = 1; $X <= $this->QR_size; $X++) {
                        $Str_1 .= ($this->P_TestPixel($X, $Y) == 1) ? "1" : "0";
                    }
                    $Str_1 .= "}" . Chr(13);
                }
                $this->QR_final = $Str_1 ;
                return true;
                break;
            Case 2:
                $BMP_size = 2 * self::border_size + $this->QR_size;
                $arrNegotiv = array_fill(1, $BMP_size, array_fill(1, $BMP_size, True));
                For ($X = 1; $X <= $this->QR_size; $X++) {
                    For ($Y = 1; $Y <= $this->QR_size; $Y++) {
                        $arrNegotiv[self::border_size + $X][self::border_size + $Y] = ($this->P_TestPixel($X, $Y) == 1) ? False : True;
                    }
                }

                //выгрузка в массив
                $this->QR_final  = array(1 => $BMP_size, 2 => $arrNegotiv);
                return true;
                break;
            case 3:
                //Визуализация QR кода по частям в режиме отладки
                echo '|QR_ver||',$this->QR_ver,"|<br>";
                echo '|nBlocks||',$nBlocks,"|<br>";
                $this->arrVisible= array_fill(1, $this->QR_size, array_fill(1, $this->QR_size, true));

                $this->P_VisibleRange($this->arr_Pixels,true);
                $this->P_VisibleRange($this->arr_Data);
                //$this->P_VisibleRange($this->arr_DataMask);

                $this->QR_final = array(1 => $this->QR_size, 2 => $this->arrVisible);
                return true;
                break;
            default:
                return null;
        }
    }

    //===================================================================================
    //
    //   ФУНКЦИИ
    //
    //===================================================================================
    //Добавление строки корректировки к строке данных и преобразование в бинарную строку.
    /**
     * @param $cData
     * @param $cDataKorr
     * @return string
     */
    private function P_str($cData, $cDataKorr)
    {
        $Str_DATA = $cData . "," . $cDataKorr;
        $arr_Byte = explode(self::cSeparatorStr, $Str_DATA);
        $nBite_all = Count($arr_Byte);
        $Str_ = "";
        For ($i = 0; $i < $nBite_all; $i++) {
            $nBite = $arr_Byte[$i];
            $Str_ .= $this->BitRead($nBite, 8);
        }
        return $Str_;
    }


    //Наложение маски на данные
    /**
     *
     */
    private function P_AddMaska()
    {
        $this->arr_Data = array_fill(1, $this->QR_size, array_fill(1, $this->QR_size, False));
        For ($i = 1; $i <= $this->QR_size; $i++) {
            For ($j = 1; $j <= $this->QR_size; $j++) {
                If ($this->arr_DataMask[$i][$j]) {
                    $this->arr_Data[$i][$j] = !$this->arrValTest[$i][$j];
                } Else {
                    $this->arr_Data[$i][$j] = $this->arrValTest[$i][$j];
                }
            }
        }
    }


    //Запись данных
    /**
     * @param $cData
     */
    private function P_InsertData($cData)
    {
        $this->arrValTest = array_fill(1, $this->QR_size, array_fill(1, $this->QR_size, False)); // Данные
        $nLenData = strlen($cData);
        $INX = 0;
        $Step_Y = -1;
        $poz_Y = $this->QR_size;
        $poz_0X = $this->QR_size;
        $poz_dX = 0;
        While ($INX < $nLenData) {
            $poz_X = $poz_0X - $poz_dX;
            If ($poz_dX === 2) {
                $poz_dX = 0;
                If (($poz_Y === 1 && $Step_Y === -1) || ($poz_Y === $this->QR_size && $Step_Y === 1)) {
                    $Step_Y = -$Step_Y;
                    $poz_0X = $poz_0X - 2;
                    If ($poz_0X === 7) {$poz_0X--;}
                } Else {
                    $poz_Y = $poz_Y + $Step_Y;
                }
            } Else {
                If ($this->arr_RangeData[$poz_X][$poz_Y]) {
                    $bSign = (SUBSTR($cData, $INX, 1) === "1") ? True : False;
                    $this->arrValTest[$poz_X][$poz_Y] = $bSign;
                    $INX++;
                }
                $poz_dX++;
            }
        }
    }


    //Построение маски
    /**
     * @param $nMask
     */
    private function P_DataMaska($nMask)
    {
        $this->arr_DataMask = array_fill(1, $this->QR_size, array_fill(1, $this->QR_size, False));
        $znac = 0;
        For ($i = 0; $i < $this->QR_size ; $i++) {
            For ($j = 0; $j < $this->QR_size; $j++) {
                Switch ($nMask) {
                    Case 0:
                        $znac = ($i + $j) % 2;
                        break;
                    Case 1:
                        $znac = $j % 2;
                        break;
                    Case 2:
                        $znac = $i % 3;
                        break;
                    Case 3:
                        $znac = ($i + $j) % 3;
                        break;
                    Case 4:
                        $znac = (intdiv($i, 3) + intdiv($j, 2)) % 2;
                        break;
                    Case 5:
                        $znac = (($i * $j) % 2) + (($i * $j) % 3);
                        break;
                    Case 6:
                        $znac = (($i * $j) % 2 + ($i * $j) % 3) % 2;
                        break;
                    Case 7:
                        $znac = (($i * $j) % 3 + ($i + $j) % 2) % 2;
                        break;
                }
                If (($znac == 0) And ($this->arr_RangeData[$i + 1][$j + 1])) {
                    $this->arr_DataMask[$i + 1][$j + 1] = True;
                }
            }
        }

    }


    //Визуализация заданной облости
    /**
     * @param $ArrayForVisible
     * @param bool $param
     */
    private function P_VisibleRange($ArrayForVisible, $param=false){

        For ($i = 1; $i <= $this->QR_size ; $i++) {
            For ($j = 1; $j <= $this->QR_size; $j++) {
                if (!$param) {
                    if ($ArrayForVisible[$i][$j ] === true) {$this->arrVisible[$i][$j] = false; }
                }else{
                    if ($ArrayForVisible[$i][$j]["V"] === true) {$this->arrVisible[$i][$j] = false; }
                }
            }
        }
        return ;
    }


    //Определение облости значений
    /**
     *
     */
    private function P_GetRangeData()
    {
        For ($i = 1; $i <= $this->QR_size; $i++) {
            For ($j = 1; $j <= $this->QR_size; $j++) {
                $this->arr_RangeData[$i][$j] = ($this->arr_Pixels[$i][$j]["T"] == 0) ? True : False;
            }
        }
    }


    //Информация о Formate
    /**
     * @param $Str_vr
     */
    private function P_INF_Format($Str_vr)
    {
        $arrVal=array();
        For ($i = 0;$i< 15; $i++) {
            $arrVal[$i+1] = (SUBSTR($Str_vr, $i, 1) === "1") ? True : False;
        }
        //Первое зеркало
        $INX = 0;
        For ($i = 1; $i < 7; $i++) {
            $this->SetPixel(9, $i, $arrVal[++$INX], 5);
        }
        $this->SetPixel(9, 8, $arrVal[++$INX], 5);
        $this->SetPixel(9, 9, $arrVal[++$INX], 5);
        $this->SetPixel(8, 9, $arrVal[++$INX], 5);
        For ($i = 6; $i >= 1; $i--) { $this->SetPixel($i, 9, $arrVal[++$INX], 5); }
        //Второе зеркало
        $INX = 0;
        For ($i = 0; $i < 8; $i++) {$this->SetPixel($this->QR_size - $i, 9, $arrVal[++$INX], 5); }
        $this->SetPixel(9, $this->QR_size - 7, True, 5);
        For ($i = 6; $i >= 0; $i--) { $this->SetPixel(9, $this->QR_size - $i, $arrVal[++$INX], 5); }

    }


    //Информация о версии
    /**
     * @param $Str_vr
     */
    private function P_INFVersion($Str_vr)
    {
        $arrVal=array_fill(1, 18,false );
        For ($i = 0; $i < 18; $i++) {
            $arrVal[18 - $i] = (SUBSTR($Str_vr, $i, 1) == "1") ? True : False;
        }
        For ($i = 0; $i < 6; $i++) {
            For ($j = 0; $j <= 2; $j++) {
                $this->SetPixel($i + 1, $j + $this->QR_size - 10, $arrVal[$i * 3 + $j + 1], 4);
                $this->SetPixel($j + $this->QR_size - 10, $i + 1, $arrVal[$i * 3 + $j + 1], 4);
            }
        }
    }


    //Направляющий шаблон
    /**
     * @param $arr_poz
     */
    private function P_ShablonNapravl($arr_poz)
    {
        $cnt_poz_setki = Count($arr_poz);
        For ($i1 = 1; $i1 <= $cnt_poz_setki; $i1++) {
            For ($j1 = 1; $j1 <= $cnt_poz_setki; $j1++) {
                If ($this->P_TestKalabsa($arr_poz[$i1], $arr_poz[$j1], $arr_poz[$i1] + 4, $arr_poz[$j1] + 4)) {
                    $this->P_Shablon($arr_poz[$i1] - 2, $arr_poz[$j1] - 2, "101", 3);
                }
            }
        }
    }


    //Шаблон синхронихации
    /**
     * @param $X1
     * @param $X2
     * @param $bPoza
     * @param $nType
     */
    private function P_ShablonSinchron($X1, $X2, $bPoza, $nType)
    {
        $bSignal = True;
        For ($i = $X1;$i<= $X2; $i++) {
            If ($bPoza == 1) {
                $this->SetPixel($i, 7, $bSignal, $nType);
            } Else {
                $this->SetPixel(7, $i, $bSignal, $nType);
            }
            $bSignal = !$bSignal;
        }
    }

    //Проверка на наложение шаблонов
    /**
     * @param $X1
     * @param $Y1
     * @param $X2
     * @param $Y2
     * @return bool
     */
    private function P_TestKalabsa($X1, $Y1, $X2, $Y2)
    {
        For ($i = $X1; $i <= $X2; $i++) {
            For ($j = $Y1; $j <= $Y2; $j++) {
                If ($this->arr_Pixels[$i][$j]["V"] === true) {
                    return False;
                }
            }
        }
        return True;
    }

    //Шаблон поиска
    /**
     *
     */
    private function P_ShablonPoiska()
    {
        $this->P_Shablon(0, 0, self::Shablon, 1);
        $this->P_LineRazd(8, 1, 8, 7, 1);
        $this->P_LineRazd(1, 8, 8, 8, 1);

        $this->P_Shablon($this->QR_size - 7, 0, self::Shablon, 1);
        $this->P_LineRazd($this->QR_size - 7, 1, $this->QR_size - 7, 7, 1);
        $this->P_LineRazd($this->QR_size - 7, 8, $this->QR_size, 8, 1);

        $this->P_Shablon(0, $this->QR_size - 7, self::Shablon, 1);
        $this->P_LineRazd(8, $this->QR_size - 7, 8, $this->QR_size, 1);
        $this->P_LineRazd(1, $this->QR_size - 7, 8, $this->QR_size - 7, 1);
    }



    //Установка пикселя
    /**
     * @param $ix
     * @param $Jx
     * @param $bValue
     * @param $nType
     */
    Function SetPixel($ix, $Jx, $bValue, $nType)
    {
        $this->arr_Pixels[$ix][$Jx] = array("V"=> $bValue, "T" => $nType);
    }


    //Шаблон
    /**
     * @param $X0
     * @param $Y0
     * @param $sh_maska
     * @param $nType
     */
    private function P_Shablon($X0, $Y0, $sh_maska, $nType)
    {
        $len_Shablona = strlen($sh_maska);
        $Sd = $len_Shablona;
        $X = $X0;
        $Y = $Y0;
        For ($K = 0; $K < $len_Shablona; $K++) {
            $bSign = (SUBSTR($sh_maska, $K, 1) == "1") ? True : False;
            $N_2 = $K * 2 ;
            $K_2 = $K - 1;
            If ($K === 0) {
                $this->SetPixel($X + $len_Shablona, $Y + $len_Shablona, $bSign, $nType);
            } Else {
                For ($N = 0; $N < $N_2; $N++) {
                    $this->SetPixel($X + $Sd - $K, $Y + $Sd + $K_2 - $N, $bSign, $nType);
                    $this->SetPixel($X + $Sd - $K_2 + $N, $Y + $Sd - $K, $bSign, $nType);
                    $this->SetPixel($X + $Sd + $K, $Y + $Sd - $K_2 + $N, $bSign, $nType);
                    $this->SetPixel($X + $Sd + $K_2 - $N, $Y + $Sd + $K, $bSign, $nType);
                }
            }
        }
    }


    //Разделительная линия шаблона поиска
    /**
     * @param $X1
     * @param $Y1
     * @param $X2
     * @param $Y2
     * @param $nType
     */
    private function P_LineRazd($X1, $Y1, $X2, $Y2, $nType)
    {
        For ($i = $X1; $i <= $X2; $i++) {
            For ($j = $Y1; $j <= $Y2; $j++) {
                $this->SetPixel($i, $j, False, $nType);
            }
        }
    }


    //Четение массива макет
    /**
     * @param $nQRVer
     */
    private function P_Maket($nQRVer){
        $this->QR_size =        $this->ArrMaketVer[$nQRVer][1];
        $this->bVySh =          $this->ArrMaketVer[$nQRVer][2];
        $this->arrVyravShabl =  $this->ArrMaketVer[$nQRVer][3];
        $this->bVersQR =        $this->ArrMaketVer[$nQRVer][4];
        $this->strVersQR =      $this->ArrMaketVer[$nQRVer][5];
    }


    //Подсчет штрафов наложения маски
    /**
     * @return int
     */
    private function P_TestMaski(){
        $cnt_BalyG = 0;
        $znac_line = 0;
        $bPixel_old = false;
        // Подсчет линий по горизонтали
        For ($i = 1; $i <= $this->QR_size; $i++) {
            For ($j = 1; $j <= $this->QR_size; $j++) {
                $bPixel = $this->P_TestPixel($i, $j);
                If ($j === 1) {
                    $znac_line = 1;
                    $bPixel_old = $bPixel;
                } ElseIf (($j === $this->QR_size) And ($znac_line > 4)) {
                    $cnt_BalyG = $cnt_BalyG + $this->P_TestLine($znac_line);
                } ElseIf ($bPixel_old !== $bPixel) {
                    $cnt_BalyG = $cnt_BalyG + $this->P_TestLine($znac_line);
                    $znac_line = 1;
                    $bPixel_old = $bPixel;
                } Else {
                    $znac_line = ($bPixel_old == $bPixel) ? $znac_line + 1 : $znac_line;
                }
            }
        }

        // Подсчет линий по вертикали
        $cnt_BalyV = 0;
        For ($j = 1; $j <= $this->QR_size; $j++) {
            For ($i = 1; $i <= $this->QR_size; $i++) {
                $bPixel = $this->P_TestPixel($i, $j);
                If ($i === 1) {
                    $znac_line = 1;
                    $bPixel_old = $bPixel;
                } ElseIf (($i === $this->QR_size) And ($znac_line > 4)) {
                    $cnt_BalyV += $this->P_TestLine($znac_line);
                } ElseIf ($bPixel_old !== $bPixel) {
                    $cnt_BalyV += $this->P_TestLine($znac_line);
                    $znac_line = 1;
                    $bPixel_old = $bPixel;
                } Else {
                    $znac_line = ($bPixel_old === $bPixel) ? $znac_line + 1 : $znac_line;
                }
            }
        }

        // Подсчет квадратов
        $cnt_BalyK = 0;
        For ($i = 1; $i <= $this->QR_size - 1; $i++) {
            For ($j = 1; $j <= $this->QR_size - 1; $j++) {
                $cntPixel = Abs($this->P_TestPixel($i, $j)
                    + $this->P_TestPixel($i + 1, $j)
                    + $this->P_TestPixel($i, $j + 1)
                    + $this->P_TestPixel($i + 1, $j + 1));
                If ($cntPixel === 4) $cnt_BalyK += 3;
            }
        }

        // Подсчет четных и нечетных модулей
        $cnt_1 = 0;
        For ($i = 1; $i <= $this->QR_size; $i++) {
            For ($j = 1; $j <= $this->QR_size; $j++) {
                If ($this->P_TestPixel($i, $j) === 1) $cnt_1++;
            }
        }
        $cnt_Baly4 = Abs(intdiv($cnt_1 * 100 / ($this->QR_size ** 2) - 50, 1)) * 2;

        // Подсчет схемы "ЧБЧЧЧБЧ"
        $cnt_1 = 0;
        For ($i = 1; $i <= $this->QR_size; $i++) {
            $Str_search = "";
            For ($j = 1; $j <= $this->QR_size; $j++) { $Str_search .=($this->P_TestPixel($i, $j) === 1)? "1":"0";}
            $cnt_1 += substr_count($Str_search, self::cModul_1 . self::cModul_0);
            $cnt_1 += substr_count($Str_search, self::cModul_0 . self::cModul_1);
            $cnt_1 += substr_count($Str_search, self::cModul_0 . self::cModul_1 . self::cModul_0);
        };
        For ($j = 1; $j <= $this->QR_size; $j++) {
            $Str_search = "";
            For ($i = 1; $i <= $this->QR_size; $i++) { $Str_search .=($this->P_TestPixel($i, $j) === 1)? "1":"0";}
            $cnt_1 += substr_count($Str_search, self::cModul_1 . self::cModul_0);
            $cnt_1 += substr_count($Str_search, self::cModul_0 . self::cModul_1);
            $cnt_1 += substr_count($Str_search, self::cModul_0 . self::cModul_1 . self::cModul_0);
        }
        $cnt_Baly5 = $cnt_1 * 40;
        $rez=$cnt_BalyG + $cnt_BalyV + $cnt_BalyK + $cnt_Baly4 + $cnt_Baly5;
        return $rez;
    }


    //Возвращает состояние пикселя
    /**
     * @param $X
     * @param $Y
     * @return int
     */
    private function P_TestPixel($X, $Y)
    {
        $v1=(boolean)$this->arr_Data[$X][$Y];
        $v2=(boolean)$this->arr_Pixels[$X][$Y]["V"];
        return (($v1 Or $v2) ? 1 : -1);
    }


    //Возвращает ошибку линии
    /**
     * @param $znac_line
     * @return int
     */
    private function P_TestLine($znac_line)
    {
        Return ($znac_line > 4) ? $znac_line - 2 : 0;
    }


    //Заполнение поля данных
    /**
     * @param $Straka
     * @param $nTypeData
     * @param $LvlKorr
     * @return array
     */
    private function P_DataToByte($Straka, $nTypeData, $LvlKorr)
    {
        $aa = "";
        $LenData = strlen($Straka);
        $bTypeData = "0000";
        Switch ($nTypeData) {
            Case 1:  // цифровое поле
                $aa = $this->P_sNumToBin($Straka);
                $bTypeData = "0001"; // цифровое поле
                break;
            Case 2:  // Буквенно-цифровое поле
                $aa = $this->P_sLN_ToBin($Straka);
                $bTypeData = "0010"; // Буквенно-цифровое поле
                break;
            Case 3:  // Байтовое поле
                $aa = $this->P_sByte_ToBin($Straka);
                $bTypeData = "0100"; // Байтовое поле
                break;
            Case 4:  // Кандзи
                //$aa = $this->P_sKandzi_ToBin($Straka);
                $bTypeData = "1000"; // Кандзи
                break;
            Case 5:  // ECI
                //$aa = $this->P_sECI_ToBin($Straka);
                $bTypeData = "0111"; // ECI
        }
        $aa .= "000";
        $LenBins = strlen($aa) + 4;
        $cntDataLen = 0;
        $axCntInf = 0;

        For ($i = 1; $i <= self::cntVerQR; $i++) {
            $this->QR_ver = $i;
            $axCntInf = self::ArrMaxCntInf[$LvlKorr][$i]; // Максимальное количества данных
            $cntDataLen = $this->ArrCntDataLen[$i][$nTypeData]; // Длина поля количества данных
            $CntInf = Ceil($LenBins + $cntDataLen);
            If ($CntInf < $axCntInf) {
                break;
            }
        }

        $сCntData = $this->BitRead($LenData, $cntDataLen); //Поля количества данных
        $bb = $bTypeData . $сCntData . $aa;
        $OST = strlen($bb) % 8;
        If ($OST > 0) { $bb .= str_repeat("0", 8 - $OST);} // Выравнивание строки до байта
        $byteZap = ($axCntInf - strlen($bb)) / 8;
        For ($i = 1; $i <= $byteZap; $i++) { $bb .= (($i % 2) == 0) ? self::cBalast1 : self::cBalast0;}
        $cDataByte = (strlen($bb) === $axCntInf)? $this->P_BytesToNumStr($bb):"";
        $arr_Byte= explode( self::cSeparatorStr ,$cDataByte);
         return $arr_Byte;
    }


    /**
     * @param int $pixelInToPoint
     * @return $this
     */
    public function setPixelInToPoint(int $pixelInToPoint)
    {
        $this->pixelInToPoint = $pixelInToPoint;
        return $this;
    }


    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->path = $path;
        return $this;
    }


    /**
     * @param int $sizeInPixels
     * @return $this
     */
    public function setSizeInPixels(int $sizeInPixels)
    {
        $this->sizeInPixels = $sizeInPixels;
        return $this;
    }

    /**
     * @param $filename
     */
    public function saveToFile($filename)
    {
        $QRtoBMP = new \models\QRcode\QRtoBMP();
        //$QRtoBMP->writeQRtoBMP($this->QR_final, $this->pixelInToPoint,'\telefone.bmp');

        $fileNameFull = $_SERVER['DOCUMENT_ROOT'].$this->path. $filename;
        $this->open($fileNameFull);
        $this->createBodyFile();
        $this->close();
    }





    /**
     * @param $nQR_Size
     * @return bool
     */
    public function init($nQR_Size)
    {
        $this->biWidth = $nQR_Size * $this->pixelInToPoint; // Ширина изображения в пикселях.
        $this->biHeight = $nQR_Size * $this->pixelInToPoint;
        $this->nByteLine = ceil($this->biWidth / 8 / 4) * 4;
        $this->biSizeImage = $this->nByteLine * $this->biHeight;
        $this->bfSize = $this->biSizeImage + $this->bfOffBits;
        return false;
    }



    /**
     * @return null|string
     */
    private function createBodyImage()
    {
        $nQR_Size=$this->QR_final[1];
        $ArrBit=$this->QR_final[2];
        
        $this->init($nQR_Size);
        $BMPData = null;
        for ($j = 1; $j <= $nQR_Size; $j++) {
            $BMPData1 = null;
            $nByte = 0;
            $N = 0;
            for ($i = 1; $i <= $nQR_Size; $i++) {
                for ($pix = 1; $pix <= $this->pixelInToPoint; $pix++) {
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
            for ($pix = 1; $pix <= $this->pixelInToPoint; $pix++) {
                $BMPData = $BMPData1 . $BMPData;
            }
        }
        return $BMPData;
    }

    private function fWrite($body)
    {
        fwrite($this->pnHandle,$body);
    }
    /**
     * @param $fileNameFull2
     */
    private function open($fileNameFull2){
        $this->streem = 'File';
        $this->pnHandle =  fopen($fileNameFull2,"w");
        return;
    }

    /**
     *
     */
    private function close(){
        fclose($this->pnHandle);
    }

    /**
     * @param $Variable_
     * @param $cnBit
     * @param $typ_
     * @return bool|int
     */
    private function SetProp($Variable_,$cnBit,$typ_){
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

    /**
     * @param $pnHandle
     * @param $hex1
     * @param $cnBit
     * @return bool|int
     */
    private function my_write($pnHandle, $hex1,$cnBit)
    {
        // $pnHandle аналдиз
        if ($this->streem == 'File'){
            return fwrite($pnHandle, $hex1,$cnBit);
        }else{
            $this->pnHandle .= substr($hex1,0,$cnBit);
            return true;
        }
   }

    /**
     *
     */
    private function createBodyFile()
    {
        $BMPData = $this->createBodyImage();

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

    }
    /**
     * @return string
     */
    public function getQRCodeImage($filename = false)
    {
        $this->streem = 'TXT';
        $this->pnHandle = "";
        $this->createBodyFile();

        $im = new \Imagick();
        $im->setResolution(200, 200);
        $im->readImageBlob($this->pnHandle);
        $im->setImageUnits(1); //Declare the units for resolution.
        $im->setImageFormat('jpeg');
        $im->setImageCompression(8);// \Imagick::COMPRESSION_JPEG
        $im->setImageCompressionQuality(80);
        $im->setImageUnits(1); //Declare the units for resolution.
        $im->setImageFormat('jpeg');
        $im->setImageCompression(8);// \Imagick::COMPRESSION_JPEG
        $im->setImageCompressionQuality(80);

        $im->resizeImage($this->sizeInPixels, $this->sizeInPixels,\imagick::COLOR_BLACK,0.275,false);

        $imageTmp = $im->getImageBlob();
        $im->destroy();
        if ($filename !== false){
            $fileNameFull = $_SERVER['DOCUMENT_ROOT'].$this->path."/".$filename;
            $this->open($fileNameFull);
            $this->fWrite($imageTmp);
            $this->close();
        }
        return $imageTmp;
    }
}