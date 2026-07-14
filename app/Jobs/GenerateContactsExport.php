<?php

namespace App\Jobs;

use App\Models\Contato;
use App\Models\Export;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateContactsExport implements ShouldQueue
{
    use Queueable;

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

            $contacts = Contato::where(
                'user_id',
                $this->export->user_id
            )->get();

            $content = "Nome,E-mail,Telefone\n";

            foreach ($contacts as $contact) {

                $content .=
                    "{$contact->nome},"
                    . "{$contact->email},"
                    . "{$contact->telefone}\n";
            }

            $fileName = "contatos_" . time() . ".csv";

            Storage::put(
                "exports/" . $fileName,
                $content
            );

            $this->export->update([
                'status' => 'Concluído',
                'nome_arquivo' => $fileName,
                'caminho_arquivo' => "exports/" . $fileName
            ]);
        } catch (\Exception $e) {

            $this->export->update([
                'status' => 'Falhou'
            ]);
        }
    }
}
