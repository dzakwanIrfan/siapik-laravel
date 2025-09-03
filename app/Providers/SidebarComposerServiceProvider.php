<?php

namespace App\Providers;

use App\Models\Submission;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SidebarComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        View::composer('includes.sidebar', function ($view) {
            $user = auth()->user();
            $isKaprodi = $user?->hasRole('kaprodi') ?? false;
            $isAkademik = $user?->hasRole('akademik') ?? false;
            $pendingSuratAkademik = 0;
            $pendingUjianAkademik = 0;
            $pendingSuratKaprodi = 0;
            $pendingUjianKaprodi = 0;

            if ($isAkademik) {
                $pendingSuratAkademik = Submission::join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
                                                    ->where('submissions.bitActive', 1)
                                                    ->where('letter_types.bitUjian', 0)
                                                    ->whereIn('submissions.txtStatus', ['Disetujui Kaprodi', 'Disetujui Akademik', 'Sudah dicetak'])
                                                    ->count();

                $pendingUjianAkademik = Submission::join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
                                                    ->where('submissions.bitActive', 1)
                                                    ->where('letter_types.bitUjian', 1)
                                                    ->whereIn('submissions.txtStatus', ['Disetujui Kaprodi', 'Disetujui Akademik', 'Sudah dicetak'])
                                                    ->count();
            }

            if ($isKaprodi) {
                $pendingSuratKaprodi = Submission::join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
                                                    ->where('submissions.bitActive', 1)
                                                    ->where('letter_types.bitUjian', 0)
                                                    ->whereIn('submissions.txtStatus', ['Sedang ditinjau Kaprodi'])
                                                    ->count();

                $pendingUjianKaprodi = Submission::join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
                                                    ->where('submissions.bitActive', 1)
                                                    ->where('letter_types.bitUjian', 1)
                                                    ->whereIn('submissions.txtStatus', ['Sedang ditinjau Kaprodi'])
                                                    ->count();
            }

            $view->with(compact('pendingSuratAkademik', 'pendingUjianAkademik', 'pendingSuratKaprodi', 'pendingUjianKaprodi'));
        });
    }
}
