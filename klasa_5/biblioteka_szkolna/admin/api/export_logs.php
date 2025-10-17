<?php
session_start();
require_once '../../database/db_connect.php';
require_once '../../vendor/autoload.php'; // Wymaga zainstalowania biblioteki TCPDF

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$format = $_GET['format'] ?? 'csv';
$adminId = isset($_GET['adminId']) ? (int)$_GET['adminId'] : null;
$dateFrom = $conn->real_escape_string($_GET['dateFrom']);
$dateTo = $conn->real_escape_string($_GET['dateTo']);
$search = $conn->real_escape_string($_GET['search']);

// Budowanie zapytania
$sql = "select l.*, a.login 
        from logi_admin l 
        join administrator a on l.admin_id = a.id 
        where 1=1";

if ($adminId) {
    $sql .= " and l.admin_id = $adminId";
}

if ($dateFrom) {
    $sql .= " and date(l.data_operacji) >= '$dateFrom'";
}

if ($dateTo) {
    $sql .= " and date(l.data_operacji) <= '$dateTo'";
}

if ($search) {
    $sql .= " and l.akcja like '%$search%'";
}

$sql .= " order by l.data_operacji desc";
$result = $conn->query($sql);

if ($format === 'csv') {
    // Export do CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=logi_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM dla UTF-8
    
    // Nagłówki
    fputcsv($output, ['ID', 'Administrator', 'Akcja', 'Data operacji']);
    
    // Dane
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['login'],
            $row['akcja'],
            $row['data_operacji']
        ]);
    }
    
    fclose($output);
} else if ($format === 'pdf') {
    // Export do PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Ustawienia dokumentu
    $pdf->SetCreator('Biblioteka Szkolna');
    $pdf->SetAuthor('System biblioteczny');
    $pdf->SetTitle('Logi administracyjne');
    
    // Ustawienia strony
    $pdf->setHeaderFont(['helvetica', '', 10]);
    $pdf->setFooterFont(['helvetica', '', 8]);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Dodanie strony
    $pdf->AddPage();
    
    // Nagłówek
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Logi administracyjne', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Nagłówki tabeli
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(20, 7, 'ID', 1);
    $pdf->Cell(40, 7, 'Administrator', 1);
    $pdf->Cell(90, 7, 'Akcja', 1);
    $pdf->Cell(40, 7, 'Data operacji', 1);
    $pdf->Ln();
    
    // Dane
    $pdf->SetFont('helvetica', '', 10);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 6, $row['id'], 1);
        $pdf->Cell(40, 6, $row['login'], 1);
        $pdf->Cell(90, 6, $row['akcja'], 1);
        $pdf->Cell(40, 6, $row['data_operacji'], 1);
        $pdf->Ln();
    }
    
    // Wysłanie PDF do przeglądarki
    $pdf->Output('logi_' . date('Y-m-d') . '.pdf', 'D');
}