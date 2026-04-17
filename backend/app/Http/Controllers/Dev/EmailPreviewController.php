<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dev;

use App\Actions\Dev\ListEmailPreviewsAction;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmailPreviewController extends Controller
{
    public function index(ListEmailPreviewsAction $action): View
    {
        return view('dev.email-previews.index', [
            'previews' => $action->execute(),
        ]);
    }

    public function show(string $template, ListEmailPreviewsAction $action): View
    {
        $preview = $action->execute()
            ->toCollection()
            ->firstWhere('template', $template);

        if ($preview === null) {
            throw new NotFoundHttpException(
                __('email_previews.unknown_template', ['template' => $template])
            );
        }

        /** @var view-string $view */
        $view = "emails.{$preview->template}";

        return view($view, $preview->variables);
    }
}
