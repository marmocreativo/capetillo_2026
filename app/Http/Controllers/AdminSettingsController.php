<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    /**
     * Lista blanca de comandos permitidos desde el panel.
     * La clave es lo que se recibe del formulario; nunca se ejecuta un comando libre.
     */
    protected function allowedCommands(): array
    {
        return [
            'migrate' => [
                'label' => 'Ejecutar migraciones (php artisan migrate)',
                'signature' => 'migrate',
                'params' => ['--force' => true],
            ],
            'optimize' => [
                'label' => 'Optimizar aplicación (php artisan optimize)',
                'signature' => 'optimize',
                'params' => [],
            ],
            'cache-clear' => [
                'label' => 'Limpiar caché de aplicación (php artisan cache:clear)',
                'signature' => 'cache:clear',
                'params' => [],
            ],
            'view-clear' => [
                'label' => 'Limpiar caché de vistas (php artisan view:clear)',
                'signature' => 'view:clear',
                'params' => [],
            ],
        ];
    }

    public function index()
    {
        $commands = $this->allowedCommands();

        return view('admin.settings.index', compact('commands'));
    }

    public function runCommand(Request $request)
    {
        $commands = $this->allowedCommands();

        $request->validate([
            'command' => ['required', Rule::in(array_keys($commands))],
        ]);

        $key = $request->input('command');
        $definition = $commands[$key];

        try {
            Artisan::call($definition['signature'], $definition['params']);
            $output = Artisan::output();
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Error al ejecutar el comando: ' . $e->getMessage());
        }

        return redirect()->route('admin.settings.index')
            ->with('status', 'Comando ejecutado: ' . $definition['label'])
            ->with('command_output', $output);
    }
}