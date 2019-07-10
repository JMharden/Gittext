<?php

/**
 * 封装PHPExcel，导出数组到Excel
 * User: liubiao02@baidu.com
 * Date: 14-6-16
 * Time: 下午12:11
 */

require('./#DFrame/Library/Vendor/Excel/phpexcel/Classes/PHPExcel.php');
require('./#DFrame/Library/Vendor/Excel/phpexcel/Classes/PHPExcel/Writer/Excel2007.php');
require('./#DFrame/Library/Vendor/Excel/phpexcel/Classes/PHPExcel/Style/NumberFormat.php');
require('./#DFrame/Library/Vendor/Excel/phpexcel/Classes/PHPExcel/Worksheet.php');
require('./#DFrame/Library/Vendor/Excel/phpexcel/Classes/PHPExcel/IOFactory.php');

class excel 
{
   

    public  function arr2ExcelDownload($aArr, $aHead = null, $sName = null)
    {
        if (empty($aHead)) {
            $aHead = array_keys($aArr[0]);
        }
        if ($sName==null) {
            $sName = date('YmdHis');
        }
        $PE = self::arrHead2Excel($aArr, $aHead, null, null);

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $sName . '.xlsx');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        ob_clean();
        $PE->save('php://output');
    }

    /** 数组导出到Excel，默认将数组第一个value的key作为head
     *
     * eg:
     * array(
     *  array('渠道号' => 111, '注册时间' => '2014-xx-xx'),
     *  array('渠道号' => 111, '注册时间' => '2014-xx-xx'),
     * )
     * @param array $aArr
     * @param string $sExcelName
     * @param string $sExcelPath
     */
    public  function arr2Excel(array $aArr, $sExcelName = "", $sExcelPath = "./") {
        $aHead = array_keys((array)current($aArr));
        self::arrHead2Excel($aArr, $aHead, $sExcelName, $sExcelPath);
    }

    /**
     * 数组导出到Excel，head单处作为参数
     *  eg:
     * array('渠道号', '注册时间')
     * array(
     *  array('111, '2014-xx-xx'),
     *  array(111, '2014-xx-xx'),
     * )
     *
     * @param array $aArr
     * @param array $aHead
     * @param string $sExcelName
     * @param string $sExcelPath
     *
     * @return \PHPExcel_Writer_Excel2007
     */
    public  function arrHead2Excel(array $aArr, array $aHead = array(), $sExcelName = "", $sExcelPath = "./") {
        $objPHPExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $iRow = 1;
        if ($aHead) {
            $iCol = 0;
            foreach ($aHead as $mValue) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($iCol, $iRow, $mValue);
                $iCol++;
            }
            $iRow++;
        }

        $objActSheet = $objPHPExcel->getActiveSheet();

        $aNumColumns = array();

        #长数字（>= 12）转成string,且设置单元格格式为text，避免科学计数法
        $converValue = function ($sValue, $iCol) use (&$aNumColumns, &$objActSheet) {
            if (is_numeric($sValue) && strlen($sValue) >= 12) {
                $sValue = " " . $sValue;
                if (empty($aNumColumns[$iCol])) {
                    $aNumColumns[$iCol] = true;
                    $objActSheet->getStyle(Excel::getExcelCol($iCol))->getNumberFormat()
                        ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                    $aNumColumns[$iCol] = true;
                }
            }
            return $sValue;
        };

        foreach ($aArr as $aValue) {
            $iCol=0;
            if (is_array($aValue)) foreach ($aValue as $sValue) {
                $objActSheet->setCellValueByColumnAndRow($iCol, $iRow, $converValue($sValue, $iCol));
                $iCol++;
            }
            $iRow++;
        }

        $sExcelName = preg_replace('/\.xls(x?)/i', '', $sExcelName?:date('Y-m-d'));
        if ($sExcelName!==null && $sExcelPath!==null) {
            $objWriter->save($sExcelPath . '/' .$sExcelName.'.xlsx');
        }

        return $objWriter;
    }


    /**
     * 多数组分多sheet导出
     * @param array $aArr
     * @param string $sExcelName
     * @param string $sExcelPath
     * @return \PHPExcel_Writer_Excel2007
     */
    public  function multiArr2Excel($sExcelName = "", $sExcelPath = "./", array $aArr) {
        $PHPExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel2007($PHPExcel);

        $i = 0;
        foreach($aArr as $sKey => $aData) {
            $PHPExcelSheet = new \PHPExcel_Worksheet($PHPExcel, "$sKey");
            $PHPExcel->addSheet($PHPExcelSheet, $i);

            $PHPExcel->setActiveSheetIndex($i);
            $objActSheet = $PHPExcel->getActiveSheet();
            $iRow = 1;
            $aHead = array_keys((array)current($aData));
            if ($aHead) {
                $iCol = 0;
                foreach ($aHead as $mValue) {
                    $objActSheet->setCellValueByColumnAndRow($iCol, $iRow, $mValue);
                    $iCol++;
                }
                $iRow++;
            }

            #长数字（>= 12）转成string,且设置单元格格式为text，避免科学计数法
            $aNumColumns = array();
            $converValue = function ($sValue, $iCol) use (&$aNumColumns, &$objActSheet) {
                if (is_numeric($sValue) && strlen($sValue) >= 12) {
                    $sValue = " " . $sValue;
                    if (empty($aNumColumns[$iCol])) {
                        $aNumColumns[$iCol] = true;
                        $objActSheet->getStyle(self::getExcelCol($iCol))->getNumberFormat()
                            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $aNumColumns[$iCol] = true;
                    }
                }
                return $sValue;
            };

            foreach ($aData as $aValue) {
                $iCol=0;
                if (is_array($aValue)) foreach ($aValue as $sValue) {
                    $objActSheet->setCellValueByColumnAndRow($iCol, $iRow, $converValue($sValue, $iCol));
                    $iCol++;
                }
                $iRow++;
            }
            $i++;
        }

        $sExcelName = preg_replace('/\.xls(x?)/i', '', $sExcelName?:date('Y-m-d'));
        if ($sExcelName!==null && $sExcelPath!==null) {
            $objWriter->save($sExcelPath . '/' .$sExcelName.'.xlsx');
        }
    }


    /**
     * excel导出到数组
     * @param $sFilePath
     * @param $bRemoveTitle
     * @param string $sFileType Excel2007|Excel5
     * @param int $iActiveSheetIndex
     * @return array
     */
    public  function excel2Arr($sFilePath, $bRemoveTitle = false, $sFileType = 'Excel2007', $iActiveSheetIndex = 0)
    {
        $Reader = \PHPExcel_IOFactory::createReader($sFileType);
        /** @var \PHPExcel $PHPExcel */
        $PHPExcel = $Reader->load($sFilePath);
        $PHPExcel->setActiveSheetIndex($iActiveSheetIndex);
        $Sheet = $PHPExcel->getActiveSheet();
        $return = $Sheet->toArray();
        if ($bRemoveTitle) {
            array_shift($return);
        }
        return $return;
    }

    /**
     * 返回Excel的列名
     * 0=A,1=B,.....
     * @param $iCol
     * @return string
     */
    public  function getExcelCol($iCol) {
        $sCol = '';

        $sTmp = base_convert($iCol, 10, 26);
        $aArr = range('A', 'Z');
        for ( $i = 0; $i < strlen($sTmp); $i++) {
            $sCol .= $aArr[
            is_numeric($sTmp[$i])
                ? $sTmp[$i]
                : (ord($sTmp[$i]) - ord('a') + 10)
            ];
        }
        return $sCol;
    }
}
