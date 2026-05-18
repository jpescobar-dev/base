<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class TopnavbarBreadcrumbsComposer
{
    public function compose(View $view): void
    {
        $routeName = Route::currentRouteName() ?? '';
        $route = request()->route();

        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('dashboard')],
        ];

        switch (true) {
            case str_starts_with($routeName, 'usuarios.'):
                $breadcrumbs[] = ['label' => 'Sistema', 'url' => null];
                $breadcrumbs[] = ['label' => 'Usuarios', 'url' => route('usuarios.index')];

                if ($routeName === 'usuarios.create') {
                    $breadcrumbs[] = ['label' => 'Crear', 'url' => null];
                }
                break;

            case str_starts_with($routeName, 'contractual.revisiones.'):
                $breadcrumbs[] = ['label' => 'Sistema', 'url' => null];
                $breadcrumbs[] = ['label' => 'Revisiones Contractuales', 'url' => route('contractual.revisiones.index')];

                $revision = $route?->parameter('revision');
                $snapshot = $route?->parameter('snapshot');
                $documento = $route?->parameter('documento');

                if ($routeName === 'contractual.revisiones.create') {
                    $breadcrumbs[] = ['label' => 'Crear revisión', 'url' => null];
                } elseif ($routeName === 'contractual.revisiones.edit' && $revision) {
                    $breadcrumbs[] = ['label' => 'Revisión #' . $revision->id, 'url' => route('contractual.revisiones.show', $revision)];
                    $breadcrumbs[] = ['label' => 'Editar', 'url' => null];
                } elseif ($routeName === 'contractual.revisiones.show' && $revision) {
                    $breadcrumbs[] = ['label' => 'Revisión #' . $revision->id, 'url' => null];
                } elseif ($routeName === 'contractual.revisiones.documentos.index' && $revision) {
                    $breadcrumbs[] = ['label' => 'Revisión #' . $revision->id, 'url' => route('contractual.revisiones.show', $revision)];
                    $breadcrumbs[] = ['label' => 'Documentos', 'url' => null];
                } elseif ($routeName === 'contractual.revisiones.documentos.show' && $revision && $documento) {
                    $breadcrumbs[] = ['label' => 'Revisión #' . $revision->id, 'url' => route('contractual.revisiones.show', $revision)];
                    $breadcrumbs[] = ['label' => 'Documentos', 'url' => route('contractual.revisiones.documentos.index', $revision)];
                    $breadcrumbs[] = ['label' => 'Documento #' . $documento->id, 'url' => null];
                } elseif ($routeName === 'contractual.revisiones.snapshots.index' && $revision) {
                    $breadcrumbs[] = ['label' => 'Revisión #' . $revision->id, 'url' => route('contractual.revisiones.show', $revision)];
                    $breadcrumbs[] = ['label' => 'Snapshots', 'url' => null];
                } elseif ($routeName === 'contractual.revisiones.snapshots.show' && $revision && $snapshot) {
                    $breadcrumbs[] = ['label' => 'Revisión #' . $revision->id, 'url' => route('contractual.revisiones.show', $revision)];
                    $breadcrumbs[] = ['label' => 'Snapshot v' . $snapshot->numero_version, 'url' => null];
                } elseif ($routeName === 'contractual.revisiones.snapshots.contradicciones.compare' && $revision) {
                    $breadcrumbs[] = ['label' => 'Revisión #' . $revision->id, 'url' => route('contractual.revisiones.show', $revision)];
                    $breadcrumbs[] = ['label' => 'Comparador contradicciones', 'url' => null];
                }
                break;

            default:
                if ($routeName === 'dashboard') {
                    $breadcrumbs = [['label' => 'Inicio', 'url' => null]];
                }
                break;
        }

        $view->with('breadcrumbs', $breadcrumbs);
    }
}
