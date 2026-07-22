<?php

namespace App\Jobs;

use App\Mail\ExportReadyMail;
use App\Models\Contact;
use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf; // dompdf
use Illuminate\Support\Facades\Mail;

class GenerateContactsExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */
    public function __construct(
        public Export $export
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {

            $this->export->update([
                'status' => 'Processando'
            ]);

            $contacts = Contact::where(
                'user_id',
                $this->export->user_id
            )->get();

            $content = "name,E-mail,phone\n";

            foreach ($contacts as $contact) {

                $content .=
                    "{$contact->name},"
                    . "{$contact->email},"
                    . "{$contact->phone}\n";
            }

            $fileName = "Contacts_" . time() . "." . $this->export->formato;
            $path = "exports/" . $fileName;

            switch ($this->export->formato) {
                case 'csv':
                    Storage::put($path, $content);
                    break;
                case 'xlsx':
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->fromArray(['Name', 'E-mail', 'Phone'], null, 'A1');

                    $row = 2;
                    foreach ($contacts as $contact) {
                        $sheet->fromArray(
                            [$contact->name, $contact->email, $contact->phone],
                            null,
                            "A{$row}"
                        );
                        $row++;
                    }

                    $writer = new Xlsx($spreadsheet);
                    $tmpPath = storage_path("app/tmp_{$fileName}");
                    $writer->save($tmpPath);
                    Storage::put($path, file_get_contents($tmpPath));
                    unlink($tmpPath);
                    break;
                case 'pdf':
                    $pdf = Pdf::loadView('exports.contacts', ['contacts' => $contacts]);
                    Storage::put($path, $pdf->output());
                    break;

                default:
                    throw new \Exception("Formato de exportação inválido.");
            }

            $this->export->update([
                'status' => 'Concluído',
                'name_arquivo' => $fileName,
                'caminho_arquivo' => "exports/" . $fileName
            ]);

            Mail::to($this->export->user->email)->send(new ExportReadyMail($this->export));
        } catch (\Exception $e) {
            Log::error('Falha ao gerar exportação: ' . $e->getMessage(), [
                'user_email' => $this->export->user->email,
                'export_id' => $this->export->id,
                'trace' => $e->getTraceAsString(),
            ]);
            $this->export->update(['status' => 'Falhou']);
        }
    }
}
