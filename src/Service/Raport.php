<?php

namespace App\Service;

use App\Controller\RaportController;
use App\Repository\MeetingsRepository;
use Exception;
use phpDocumentor\Reflection\Types\This;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPMailer\PHPMailer\PHPMailer;

class Raport
{
    private $meetingsRepository;

    public function __construct(
        MeetingsRepository $meetingsRepository,
    ) {
        $this->meetingsRepository = $meetingsRepository;
    }

    private function getTime(\DateTimeInterface $dt): string
    {
        $dateTime = (new \DateTime())->setTimestamp($dt->getTimestamp());
        return $dateTime->format('H:i:s d-m-Y');
    }

    public function sendExcelByEmail(string $fileName, string $filePath): void{
        $email = new PHPMailer(true);

        try {
            $email->isSMTP();
            $email->SMTPDebug  = 2;
            $email->Host = 'smtp.google.com';
            $email->SMTPAuth = true;
            $email->Username = 'pimpmyzut@gmail.com';
            $email->Password = 'KochamZUT123';
            $email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $email->Port = 587;

            //KochamZUT123

            $email->setFrom('pimpmyzut@gmail.com', 'WI ZUT');

            $email->addAddress('pimpmyzut@gmail.com');

            $email->isHTML(true);
            $email->Subject = 'Message Subject';
            $email->Body    = 'Body of your message here...';
            $email->AltBody = 'Plain text version of your message';

            $file_to_attach = $filePath;
            $email->addAttachment($file_to_attach, $fileName);

            $email->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ' . $email->ErrorInfo;
        }
    }

    private function sendExcelFile(Spreadsheet $data): void
    {
        $writer = new Xlsx($data);
        $fileName = 'Course_report_' . date('d_m_Y') . '.xlsx';
        $filePath = '/' . $fileName;

        try {
            $writer->save($filePath);
        } catch (Exception $e) {
            return;
        }

        ob_start();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode(basename($fileName)) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        if (file_exists($filePath)) {
            header('Content-Length: ' . filesize($filePath));

            readfile($filePath);

            //$this->sendExcelByEmail($fileName, $filePath);

            unlink($filePath);
        } else {
        }
        ob_end_flush();
    }

    private function getColorForGrade($grade): string
    {
        switch ($grade) {
            case -2:
                return 'FFFF0000';
            case -1:
                return 'FFFFCCCC';
            case 1:
                return 'FFCCFFCC';
            case 2:
                return 'FF00FF00';
            default:
                return 'FFFFFFFF';
        }
    }

    private function generateExcelFile(array $data): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Arkusz "Raport"
        $reportSheet = $spreadsheet->getActiveSheet();
        $reportSheet->setTitle("Raport");
        $reportSheet->setCellValue('A1', 'Imię i nazwisko:');
        $reportSheet->setCellValue('B1', $data['name']);
        $reportSheet->setCellValue('A2', 'Przedmiot:');
        $reportSheet->setCellValue('B2', $data['subject']);
        $reportSheet->setCellValue('A3', 'Data i Godzina:');
        $reportSheet->setCellValue('B3', $data['datetime']);
        $reportSheet->setCellValue('A4', 'Σ punktów:');
        $reportSheet->setCellValue('B4', $data['total_points']);
        $reportSheet->setCellValue('A5', 'Zmiana punktowa:');
        $reportSheet->setCellValue('B5', $data['point_change']);

        // Arkusz "Oceny i opinie"
        $gradesSheet = $spreadsheet->createSheet();
        $gradesSheet->setTitle("Oceny i opinie");
        $grades = $data['grades'];
        $row = 1;
        $column = 'A';

        foreach ($grades as $index => $grade) {
            $gradesSheet->setCellValue($column . $row, $grade);
            $color = $this->getColorForGrade($grade);
            $gradesSheet->getStyle($column . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($color);

            if (($index + 1) % 10 == 0) {
                $row++;
                $column = 'A';
            } else {
                if ($column == 'J') {
                    $column = 'A';
                    $row++;
                } else {
                    $column++;
                }
            }
        }

        $row += 5;
        foreach ($data['opinions'] as $opinion) {
            $gradesSheet->setCellValue('A' . $row, $opinion);
            $row++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $this->sendExcelFile($spreadsheet);
    }

    public function generateReport(int $meetingId): void
    {
        $meeting = $this->meetingsRepository->find($meetingId);

        $professor = $meeting->getProf();
        $evaluations = $meeting->getMeetEvals();
        $scoreSum = 0;

        foreach ($evaluations as $eval) {
            $scoreSum += $eval->getScore();
        }

        $reportItem = [
            'datetime' => $this->getTime($meeting->getMeetingStart()),
            'name' => $professor->getTeacher(),
            'subject' => $meeting->getMeetingName(),
            'total_points' => $professor->getTotalScore(),
            'point_change' => $scoreSum,
            'grades' => array_map(function ($eval) {
                return (int)$eval->getScore();
            }, $evaluations->toArray()),
            'opinions' => array_filter(array_map(function ($eval) {
                return $eval->getInfo() !== null ? $eval->getInfo() : null;
            }, $evaluations->toArray())),
        ];

        $this->generateExcelFile($reportItem);
    }
}
