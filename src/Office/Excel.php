<?php

namespace Wmud\HyperfLib\Office;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Excel
{
    /**
     * 导出excel
     * value mixed 单元格值
     * rowSpan int 合并行
     * colSpan int 合并列
     * width int 宽度
     * height int 高度
     * bold bool 是否加粗
     * vertical string 垂直对其方式 bottom,top,center,justify,distributed
     * horizontal string 水平对其对其方式 general,left,right,center,centerContinuous,justify,fill,distributed
     * @param array $data 数据源 例: [['value' => 'test', width => 16], ['value' => 'test', width => 16]]
     * @param string $excelPath 保存文件地址
     * @param int $columnNum 列数
     * @return string
     * @throws WriterException
     * @throws Exception
     */
    public function exportXlsx(array $data, string $excelPath, int $columnNum = 0): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if (!$columnNum) {
            $columnNum = count($data[1] ?? []);
        }
        foreach ($data as $key => $item) {
            $index = 0;
            for ($i = 65; $i < $columnNum + 65; $i++) {
                $cellValue = $item[$index]['value'] ?? ''; // 单元格值
                $column = $this->getColumn($i, $index); // 当前列
                $row = ($key + 1); // 当前行
                $cell = $column . $row; // 当前单元格
                $sheet->setCellValue($cell, $cellValue);
                // 设置宽度
                if (isset($item[$index]['width'])) {
                    $sheet->getColumnDimension($column)->setWidth($item[$index]['width']);
                } else {
                    $sheet->getColumnDimension($column)->setWidth(18);
                }
                // 设置高度
                if (isset($item[$index]['height'])) {
                    $sheet->getRowDimension($row)->setRowHeight($item[$index]['height']);
                } else {
                    $sheet->getRowDimension($row)->setRowHeight(20);
                }
                // 设置加粗
                if (isset($item[$index]['bold']) && $item[$index]['bold']) {
                    $sheet->getStyle($cell)->getFont()->setBold(true);
                }
                $styleData = [
                    'alignment' => [
                        'vertical' => $item[$index]['vertical'] ?? Alignment::VERTICAL_CENTER,
                        'horizontal' => $item[$index]['horizontal'] ?? Alignment::HORIZONTAL_CENTER,
                    ]
                ];
                $sheet->getStyle($cell)->applyFromArray($styleData);
                // 合并行
                if (isset($item[$index]['rowSpan'])) {
                    $rowNum = $key + $item[$index]['rowSpan'];
                    $sheet->mergeCells("$cell:$column$rowNum");
                }
                // 合并列
                if (isset($item[$index]['colSpan'])) {
                    $colSpan = $item[$index]['colSpan'];
                    $endCol = $this->getColumn($i + $colSpan, $index);
                    $sheet->mergeCells("$cell:$endCol$row");
                    $i += $item[$index]['colSpan'];
                }
                $index++;
            }
        }
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($excelPath);
        return $excelPath;
    }

    /**
     * 获取当前列
     * @param int $columnNun
     * @param int $index
     * @return string
     */
    protected function getColumn(int $columnNun, int $index): string
    {
        if ($index < 26 && $columnNun < 91) {
            return strtoupper(chr($columnNun));
        } else {
            $i = (int)floor(($columnNun - 65) / 26);
            $beforeColumn = strtoupper(chr(65 + $i - 1));
            $afterColumn = strtoupper(chr($columnNun - ($i * 26)));
            return $beforeColumn . $afterColumn;
        }
    }
}
